<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pesanan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\ExcelReports\PesananExcelReport;
use BackedEnum;
use Illuminate\Support\Facades\DB;

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
    
    // Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $pelanggan_id = null;
    public ?int $item_id = null;
    public ?int $rute_id = null;
    public array $status = [];
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
        $this->pelanggan_id = null;
        $this->item_id = null;
        $this->rute_id = null;
        $this->status = [];
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // Get Filtered Query
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
        
        if ($this->item_id) {
            $query->whereHas('rute', function($q) {
                $q->where('item_id', $this->item_id);
            });
        }
        
        if ($this->rute_id) {
            $query->where('rute_id', $this->rute_id);
        }
        
        if (!empty($this->status)) {
            $query->whereIn('status', $this->status);
        }
        
        return $query;
    }
    
    // Summary data
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
    
    // Get Top 5 Customers Data
    public function getTopCustomersData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'revenue' => [], 'count' => []];
        }
        
        $topCustomers = $this->getFilteredQuery()
            ->select('pelanggan_id')
            ->selectRaw('COUNT(*) as total_pesanan')
            ->selectRaw('SUM(total_tagihan) as total_revenue')
            ->groupBy('pelanggan_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->with('pelanggan')
            ->get();
        
        return [
            'labels' => $topCustomers->map(fn($item) => $item->pelanggan->nama)->toArray(),
            'revenue' => $topCustomers->map(fn($item) => (float) $item->total_revenue)->toArray(),
            'count' => $topCustomers->map(fn($item) => (int) $item->total_pesanan)->toArray(),
        ];
    }
    
    // Get Monthly Revenue Trend
    public function getMonthlyRevenueData(): array
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
        
        $monthlyData = Pesanan::query()
            ->whereDate('tanggal_pesanan', '>=', $startDate)
            ->whereDate('tanggal_pesanan', '<=', $endDate)
            ->when($this->pelanggan_id, fn($q) => $q->where('pelanggan_id', $this->pelanggan_id))
            ->when($this->item_id, fn($q) => $q->whereHas('rute', fn($r) => $r->where('item_id', $this->item_id)))
            ->when($this->rute_id, fn($q) => $q->where('rute_id', $this->rute_id))
            ->when(!empty($this->status), fn($q) => $q->whereIn('status', $this->status))
            ->selectRaw('DATE_FORMAT(tanggal_pesanan, "%Y-%m") as month')
            ->selectRaw('SUM(total_tagihan) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month');
        
        $data = [];
        foreach ($monthKeys as $monthKey) {
            $data[] = isset($monthlyData[$monthKey]) ? (float) $monthlyData[$monthKey] : 0;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }
    
    // Get Top 5 Items Data (by COUNT)
    public function getTopItemsData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'count' => []];
        }
        
        $topItems = $this->getFilteredQuery()
            ->join('rute', 'pesanan.rute_id', '=', 'rute.id')
            ->join('item', 'rute.item_id', '=', 'item.id')
            ->select('item.id', 'item.nama')
            ->selectRaw('COUNT(*) as total_order')
            ->groupBy('item.id', 'item.nama')
            ->orderByDesc('total_order')
            ->limit(5)
            ->get();
        
        return [
            'labels' => $topItems->pluck('nama')->toArray(),
            'count' => $topItems->pluck('total_order')->map(fn($v) => (int) $v)->toArray(),
        ];
    }
    
    // Get Top 5 Routes Data (by COUNT)
    public function getTopRoutesData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'count' => []];
        }
        
        $topRoutes = $this->getFilteredQuery()
            ->join('rute', 'pesanan.rute_id', '=', 'rute.id')
            ->select('rute.id', 'rute.asal', 'rute.tujuan')
            ->selectRaw('COUNT(*) as total_trip')
            ->groupBy('rute.id', 'rute.asal', 'rute.tujuan')
            ->orderByDesc('total_trip')
            ->limit(5)
            ->get();
        
        return [
            'labels' => $topRoutes->map(fn($r) => $r->asal . ' - ' . $r->tujuan)->toArray(),
            'count' => $topRoutes->pluck('total_trip')->map(fn($v) => (int) $v)->toArray(),
        ];
    }
    
    // Get options for selects
    public function getPelangganOptions(): array
    {
        return \App\Models\Pelanggan::pluck('nama', 'id')->toArray();
    }
    
    public function getItemOptions(): array
    {
        return \App\Models\Item::pluck('nama', 'id')->toArray();
    }
    
    public function getRuteOptions(): array
    {
        return \App\Models\Rute::selectRaw("id, CONCAT(asal, ' → ', tujuan) as label")
            ->pluck('label', 'id')
            ->toArray();
    }
    
    public function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'dalam_perjalanan' => 'Dalam Perjalanan',
            'selesai' => 'Selesai',
            'batal' => 'Batal',
        ];
    }
    
    // Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('id')
                    ->label('No ID')
                    ->sortable(),
                
                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                
                TextColumn::make('rute.asal')
                    ->label('Asal')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('rute.tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('rute.item.nama')
                    ->label('Muatan')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('berat')
                    ->label('Berat')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' KG')
                    ->alignEnd(),
                
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
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
    
    // Helper methods
    private function calculateCompletionRate($query): float
    {
        $total = $query->count();
        if ($total === 0) return 0;
        
        $completed = (clone $query)->where('status', 'selesai')->count();
        return round(($completed / $total) * 100, 2);
    }
    
    // Export methods
    public function exportToExcel()
    {
        $data = $this->getFilteredQuery()->get();
            
        return Excel::download(
            new PesananExcelReport($data), 
            'laporan-pesanan-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportToPdf()
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