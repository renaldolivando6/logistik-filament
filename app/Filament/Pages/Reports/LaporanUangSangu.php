<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Trip;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;

class LaporanUangSangu extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.reports.laporan-uang-sangu';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Uang Sangu';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Uang Sangu';
    }
    
    // Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $sopir_id = null;
    public ?int $kendaraan_id = null;
    public ?string $status_sangu = null; // 'belum_selesai' atau 'selesai'
    public bool $hasAppliedFilter = false;
    
    // Apply Filter Action
    public function applyFilters(): void
    {
        $this->hasAppliedFilter = true;
        $this->resetTable();
        $this->dispatch('initCharts');
    }
    
    // Reset Filter Action
    public function resetFilters(): void
    {
        $this->dari_tanggal = null;
        $this->sampai_tanggal = null;
        $this->sopir_id = null;
        $this->kendaraan_id = null;
        $this->status_sangu = null;
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // Get Filtered Query
    private function getFilteredQuery(): Builder
    {
        $query = Trip::query()
            ->with(['sopir', 'kendaraan'])
            ->withSum('biayaOperasional', 'jumlah')
            ->where('status', '!=', 'batal'); // Exclude cancelled trips
        
        if ($this->dari_tanggal) {
            $query->whereDate('tanggal_trip', '>=', $this->dari_tanggal);
        }
        
        if ($this->sampai_tanggal) {
            $query->whereDate('tanggal_trip', '<=', $this->sampai_tanggal);
        }
        
        if ($this->sopir_id) {
            $query->where('sopir_id', $this->sopir_id);
        }
        
        if ($this->kendaraan_id) {
            $query->where('kendaraan_id', $this->kendaraan_id);
        }
        
        if ($this->status_sangu) {
            $query->where('status_sangu', $this->status_sangu);
        }
        
        return $query;
    }
    
    // Summary data
    public function getSummaryData(): array
    {
        if (!$this->hasAppliedFilter) {
            return [
                'total_sangu_diberikan' => 0,
                'total_biaya_operasional' => 0,
                'total_harus_kembali' => 0,
                'total_sudah_kembali' => 0,
            ];
        }
        
        $trips = $this->getFilteredQuery()->get();
        
        $totalSangu = $trips->sum('uang_sangu');
        $totalBiaya = $trips->sum('biaya_operasional_sum_jumlah');
        $totalHarusKembali = $trips->sum(function($trip) {
            return $trip->uang_sangu - ($trip->biaya_operasional_sum_jumlah ?? 0);
        });
        $totalSudahKembali = $trips->sum('uang_kembali');
        
        return [
            'total_sangu_diberikan' => $totalSangu,
            'total_biaya_operasional' => $totalBiaya,
            'total_harus_kembali' => $totalHarusKembali,
            'total_sudah_kembali' => $totalSudahKembali,
        ];
    }
    
    // Get Top 10 Outstanding Sangu (Bar Chart)
    public function getOutstandingSanguData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'belum_kembali' => [], 'sudah_kembali' => []];
        }
        
        $trips = $this->getFilteredQuery()
            ->orderByDesc('uang_sangu')
            ->limit(10)
            ->get();
        
        $labels = [];
        $belumKembali = [];
        $sudahKembali = [];
        
        foreach ($trips as $trip) {
            $sisaSangu = $trip->uang_sangu - ($trip->biaya_operasional_sum_jumlah ?? 0);
            $labels[] = "TRIP-{$trip->id}\n{$trip->sopir->nama}";
            
            if ($trip->status_sangu === 'belum_selesai') {
                $belumKembali[] = (float) $sisaSangu;
                $sudahKembali[] = 0;
            } else {
                $belumKembali[] = 0;
                $sudahKembali[] = (float) $trip->uang_kembali;
            }
        }
        
        return [
            'labels' => $labels,
            'belum_kembali' => $belumKembali,
            'sudah_kembali' => $sudahKembali,
        ];
    }
    
    // Get options for selects
    public function getSopirOptions(): array
    {
        return \App\Models\Sopir::pluck('nama', 'id')->toArray();
    }
    
    public function getKendaraanOptions(): array
    {
        return \App\Models\Kendaraan::pluck('nopol', 'id')->toArray();
    }
    
    public function getStatusSanguOptions(): array
    {
        return [
            'belum_selesai' => 'Belum Dikembalikan',
            'selesai' => 'Sudah Dikembalikan',
        ];
    }
    
    // Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('id')
                    ->label('Trip ID')
                    ->formatStateUsing(fn($state) => "TRIP-{$state}")
                    ->sortable(),
                
                TextColumn::make('tanggal_trip')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('uang_sangu')
                    ->label('Sangu Diberikan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('biaya_operasional_sum_jumlah')
                    ->label('Total Biaya')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd()
                    ->default(0),
                
                TextColumn::make('sisa_sangu')
                    ->label('Sisa (Harus Kembali)')
                    ->getStateUsing(function($record) {
                        return $record->uang_sangu - ($record->biaya_operasional_sum_jumlah ?? 0);
                    })
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->color('warning'),
                
                TextColumn::make('uang_kembali')
                    ->label('Sudah Dikembalikan')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd()
                    ->default(0),
                
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->getStateUsing(function($record) {
                        $harusKembali = $record->uang_sangu - ($record->biaya_operasional_sum_jumlah ?? 0);
                        return $record->uang_kembali - $harusKembali;
                    })
                    ->formatStateUsing(function($state) {
                        if ($state == 0) return '-';
                        $prefix = $state > 0 ? '+' : '';
                        return $prefix . 'Rp ' . number_format(abs($state), 0, ',', '.');
                    })
                    ->color(fn($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->alignEnd(),
                
                TextColumn::make('status_sangu')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_selesai' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_selesai' => 'Belum Dikembalikan',
                        'selesai' => 'Sudah Dikembalikan',
                        default => $state,
                    }),
                
                TextColumn::make('tanggal_pengembalian')
                    ->label('Tgl Pengembalian')
                    ->date('d/m/Y')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
    
    // Export methods
    public function exportToExcel()
    {
        $data = $this->getFilteredQuery()->get();
            
        return Excel::download(
            new \App\ExcelReports\UangSanguExcelReport($data), 
            'laporan-uang-sangu-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportToPdf()
    {
        $data = $this->getFilteredQuery()->get();
        $summary = $this->getSummaryData();
        
        // Prepare filter info
        $filters = [
            'dari_tanggal' => $this->dari_tanggal,
            'sampai_tanggal' => $this->sampai_tanggal,
            'sopir' => $this->sopir_id ? \App\Models\Sopir::find($this->sopir_id)?->nama : null,
            'kendaraan' => $this->kendaraan_id ? \App\Models\Kendaraan::find($this->kendaraan_id)?->nopol : null,
            'status_sangu' => $this->status_sangu ? $this->getStatusSanguOptions()[$this->status_sangu] : null,
        ];
        
        $pdf = Pdf::loadView('reports.uang-sangu-pdf', [
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-uang-sangu-' . now()->format('Y-m-d') . '.pdf');
    }
}