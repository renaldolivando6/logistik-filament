<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pesanan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\ExcelReports\PesananExcelReport;
use BackedEnum;

class LaporanPesanan extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?int $navigationSort = 1;
    
    protected string $view = 'filament.pages.reports.laporan-pesanan';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Pesanan';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Pesanan';
    }
    
    // ✅ Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $pelanggan_id = null;
    public array $status = [];
    public bool $hasAppliedFilter = false;
    
    // ✅ Apply Filter Action
    public function applyFilters(): void
    {
        $this->hasAppliedFilter = true;
        $this->resetTable();
    }
    
    // ✅ Reset Filter Action
    public function resetFilters(): void
    {
        $this->dari_tanggal = null;
        $this->sampai_tanggal = null;
        $this->pelanggan_id = null;
        $this->status = [];
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // ✅ Get Filtered Query
    private function getFilteredQuery(): Builder
    {
        $query = Pesanan::query()->with(['pelanggan', 'rute.item', 'sopir', 'kendaraan', 'suratJalan']);
        
        if ($this->dari_tanggal) {
            $query->whereDate('tanggal_pesanan', '>=', $this->dari_tanggal);
        }
        
        if ($this->sampai_tanggal) {
            $query->whereDate('tanggal_pesanan', '<=', $this->sampai_tanggal);
        }
        
        if ($this->pelanggan_id) {
            $query->where('pelanggan_id', $this->pelanggan_id);
        }
        
        if (!empty($this->status)) {
            $query->whereIn('status', $this->status);
        }
        
        return $query;
    }
    
    // ✅ Summary data (only after filter applied)
    public function getSummaryData(): array
    {
        if (!$this->hasAppliedFilter) {
            return [
                'total_pesanan' => 0,
                'total_revenue' => 0,
                'avg_order_value' => 0,
                'completion_rate' => 0,
            ];
        }
        
        $query = $this->getFilteredQuery();
        $total = $query->count();
        
        return [
            'total_pesanan' => $total,
            'total_revenue' => $query->sum('total_tagihan'),
            'avg_order_value' => $total > 0 ? $query->avg('total_tagihan') : 0,
            'completion_rate' => $this->calculateCompletionRate($query),
        ];
    }
    
    // ✅ Get pelanggan options for select
    public function getPelangganOptions(): array
    {
        return \App\Models\Pelanggan::pluck('nama', 'id')->toArray();
    }
    
    // ✅ Get status options for select
    public function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'dalam_perjalanan' => 'Dalam Perjalanan',
            'selesai' => 'Selesai',
            'batal' => 'Batal',
        ];
    }
    
    // ✅ Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                
                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                
                TextColumn::make('rute.item.nama')
                    ->label('Muatan')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('berat')
                    ->label('Berat Total')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->alignEnd(),
                
                TextColumn::make('total_berat_dikirim')
                    ->label('Terkirim')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->color('success')
                    ->alignEnd()
                    ->toggleable(),
                
                TextColumn::make('sisa_berat')
                    ->label('Sisa')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->alignEnd()
                    ->toggleable(),
                
                TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                            ->label('Total Revenue'),
                    ]),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam_perjalanan' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'dalam_perjalanan' => 'Dalam Perjalanan',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => $state,
                    }),
            ])
            ->defaultSort('tanggal_pesanan', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
    
    // ✅ Actions (visible only after filter)
    protected function getHeaderActions(): array
    {
        if (!$this->hasAppliedFilter) {
            return [];
        }
        
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => $this->exportToExcel()),
            
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(fn () => $this->exportToPdf()),
        ];
    }
    
    // ✅ Helper methods
    private function calculateCompletionRate($query): float
    {
        $total = $query->count();
        if ($total === 0) return 0;
        
        $completed = (clone $query)->where('status', 'selesai')->count();
        return round(($completed / $total) * 100, 2);
    }
    
    private function exportToExcel()
    {
        $data = $this->getFilteredQuery()->get();
            
        return Excel::download(
            new PesananExcelReport($data), 
            'laporan-pesanan-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    private function exportToPdf()
    {
        $data = $this->getFilteredQuery()->get();
        $summary = $this->getSummaryData();
        
        $pdf = Pdf::loadView('reports.pesanan-pdf', [
            'data' => $data,
            'summary' => $summary,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-pesanan-' . now()->format('Y-m-d') . '.pdf');
    }
}