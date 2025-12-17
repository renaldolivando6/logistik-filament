<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BiayaOperasional;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;

class LaporanBiayaOperasional extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.reports.laporan-biaya-operasional';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Biaya Operasional';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Biaya Operasional';
    }
    
    // Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $kendaraan_id = null;
    public ?int $sopir_id = null;
    public ?int $kategori_biaya_id = null;
    public ?string $tipe_biaya = null; // 'trip' atau 'non_trip'
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
        $this->kendaraan_id = null;
        $this->sopir_id = null;
        $this->kategori_biaya_id = null;
        $this->tipe_biaya = null;
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // Get Filtered Query
    private function getFilteredQuery(): Builder
    {
        $query = BiayaOperasional::query()
            ->with(['trip.sopir', 'trip.kendaraan', 'kendaraan', 'kategoribiaya']);
        
        if ($this->dari_tanggal) {
            $query->whereDate('tanggal_biaya', '>=', $this->dari_tanggal);
        }
        
        if ($this->sampai_tanggal) {
            $query->whereDate('tanggal_biaya', '<=', $this->sampai_tanggal);
        }
        
        if ($this->kendaraan_id) {
            $query->where(function($q) {
                $q->where('kendaraan_id', $this->kendaraan_id)
                  ->orWhereHas('trip', function($t) {
                      $t->where('kendaraan_id', $this->kendaraan_id);
                  });
            });
        }
        
        if ($this->sopir_id) {
            $query->whereHas('trip', function($q) {
                $q->where('sopir_id', $this->sopir_id);
            });
        }
        
        if ($this->kategori_biaya_id) {
            $query->where('kategori_biaya_id', $this->kategori_biaya_id);
        }
        
        if ($this->tipe_biaya === 'trip') {
            $query->whereNotNull('trip_id');
        } elseif ($this->tipe_biaya === 'non_trip') {
            $query->whereNull('trip_id');
        }
        
        return $query;
    }
    
    // Summary data
    public function getSummaryData(): array
    {
        if (!$this->hasAppliedFilter) {
            return [
                'total_biaya' => 0,
                'biaya_trip' => 0,
                'biaya_non_trip' => 0,
            ];
        }
        
        $query = $this->getFilteredQuery();
        
        return [
            'total_biaya' => $query->sum('jumlah'),
            'biaya_trip' => (clone $query)->whereNotNull('trip_id')->sum('jumlah'),
            'biaya_non_trip' => (clone $query)->whereNull('trip_id')->sum('jumlah'),
        ];
    }
    
    // Get Breakdown by Category (Pie Chart)
    public function getCategoryBreakdownData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'data' => []];
        }
        
        $breakdown = $this->getFilteredQuery()
            ->join('kategori_biaya', 'biaya_operasional.kategori_biaya_id', '=', 'kategori_biaya.id')
            ->select('kategori_biaya.nama')
            ->selectRaw('SUM(biaya_operasional.jumlah) as total')
            ->groupBy('kategori_biaya.id', 'kategori_biaya.nama')
            ->orderByDesc('total')
            ->get();
        
        return [
            'labels' => $breakdown->pluck('nama')->toArray(),
            'data' => $breakdown->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];
    }
    
    // Get Monthly Trend (6 months)
    public function getMonthlyTrendData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'data' => []];
        }
        
        $endDate = $this->sampai_tanggal 
            ? \Carbon\Carbon::parse($this->sampai_tanggal) 
            : now();
        
        $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
        
        $labels = [];
        $monthKeys = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = $endDate->copy()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $monthKeys[] = $monthKey;
        }
        
        $monthlyData = BiayaOperasional::query()
            ->whereDate('tanggal_biaya', '>=', $startDate)
            ->whereDate('tanggal_biaya', '<=', $endDate)
            ->when($this->kendaraan_id, function($q) {
                $q->where(function($sub) {
                    $sub->where('kendaraan_id', $this->kendaraan_id)
                        ->orWhereHas('trip', fn($t) => $t->where('kendaraan_id', $this->kendaraan_id));
                });
            })
            ->when($this->sopir_id, fn($q) => $q->whereHas('trip', fn($t) => $t->where('sopir_id', $this->sopir_id)))
            ->when($this->kategori_biaya_id, fn($q) => $q->where('kategori_biaya_id', $this->kategori_biaya_id))
            ->when($this->tipe_biaya === 'trip', fn($q) => $q->whereNotNull('trip_id'))
            ->when($this->tipe_biaya === 'non_trip', fn($q) => $q->whereNull('trip_id'))
            ->selectRaw('DATE_FORMAT(tanggal_biaya, "%Y-%m") as month')
            ->selectRaw('SUM(jumlah) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
        
        $data = [];
        foreach ($monthKeys as $monthKey) {
            $data[] = isset($monthlyData[$monthKey]) ? (float) $monthlyData[$monthKey] : 0;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }
    
    // Get options for selects
    public function getKendaraanOptions(): array
    {
        return \App\Models\Kendaraan::pluck('nopol', 'id')->toArray();
    }
    
    public function getSopirOptions(): array
    {
        return \App\Models\Sopir::pluck('nama', 'id')->toArray();
    }
    
    public function getKategoriBiayaOptions(): array
    {
        return \App\Models\KategoriBiaya::pluck('nama', 'id')->toArray();
    }
    
    public function getTipeBiayaOptions(): array
    {
        return [
            'trip' => 'Biaya Trip',
            'non_trip' => 'Biaya Non-Trip',
        ];
    }
    
    // Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->sortable(),
                
                TextColumn::make('tanggal_biaya')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('tipe_biaya')
                    ->label('Tipe')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->trip_id ? 'Trip' : 'Non-Trip')
                    ->color(fn($record) => $record->trip_id ? 'success' : 'gray'),
                
                TextColumn::make('trip.id')
                    ->label('Trip')
                    ->formatStateUsing(fn($state) => $state ? "TRIP-{$state}" : '-')
                    ->sortable(),
                
                TextColumn::make('trip.sopir.nama')
                    ->label('Sopir')
                    ->default('-')
                    ->searchable(),
                
                TextColumn::make('kendaraan_nopol')
                    ->label('Kendaraan')
                    ->getStateUsing(function($record) {
                        return $record->kendaraan?->nopol ?? $record->trip?->kendaraan?->nopol ?? '-';
                    })
                    ->searchable(),
                
                TextColumn::make('kategoribiaya.nama')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                            ->label('Total Biaya'),
                    ]),
                
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
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
            new \App\ExcelReports\BiayaOperasionalExcelReport($data), 
            'laporan-biaya-operasional-' . now()->format('Y-m-d') . '.xlsx'
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
            'tipe_biaya' => $this->tipe_biaya,
            'kendaraan' => $this->kendaraan_id ? \App\Models\Kendaraan::find($this->kendaraan_id)?->nopol : null,
            'sopir' => $this->sopir_id ? \App\Models\Sopir::find($this->sopir_id)?->nama : null,
            'kategori' => $this->kategori_biaya_id ? \App\Models\KategoriBiaya::find($this->kategori_biaya_id)?->nama : null,
        ];
        
        $pdf = Pdf::loadView('reports.biaya-operasional-pdf', [
            'data' => $data,
            'summary' => $summary,
            'filters' => $filters,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-biaya-operasional-' . now()->format('Y-m-d') . '.pdf');
    }
}