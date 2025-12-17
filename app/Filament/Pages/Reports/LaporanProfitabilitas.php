<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Trip;
use App\Models\Pesanan;
use App\Models\BiayaOperasional;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use Illuminate\Support\Facades\DB;

class LaporanProfitabilitas extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.reports.laporan-profitabilitas';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Profitabilitas';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Profitabilitas';
    }
    
    // Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $kendaraan_id = null;
    public ?int $sopir_id = null;
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
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // Get Filtered Query for Trips
    private function getFilteredQuery(): Builder
    {
        $query = Trip::query()
            ->with(['sopir', 'kendaraan', 'suratJalan.pesanan', 'biayaOperasional.kategoriBiaya'])
            ->where('status', 'selesai'); // ✅ HANYA trip yang sudah selesai
        
        if ($this->dari_tanggal) {
            $query->whereDate('tanggal_trip', '>=', $this->dari_tanggal);
        }
        
        if ($this->sampai_tanggal) {
            $query->whereDate('tanggal_trip', '<=', $this->sampai_tanggal);
        }
        
        if ($this->kendaraan_id) {
            $query->where('kendaraan_id', $this->kendaraan_id);
        }
        
        if ($this->sopir_id) {
            $query->where('sopir_id', $this->sopir_id);
        }
        
        return $query;
    }
    
    // Summary data
    public function getSummaryData(): array
    {
        if (!$this->hasAppliedFilter) {
            return [
                'total_trips' => 0,
                'total_revenue' => 0,
                'total_costs' => 0,
                'total_profit' => 0,
                'profit_margin' => 0,
                'avg_profit_per_trip' => 0,
            ];
        }
        
        $trips = $this->getFilteredQuery()->get();
        
        $totalRevenue = 0;
        $totalCosts = 0;
        
        foreach ($trips as $trip) {
            // Revenue dari semua pesanan di trip ini
            $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
            $revenue = Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
            $totalRevenue += $revenue;
            
            // Costs dari biaya operasional trip ini
            $costs = $trip->biayaOperasional->sum('jumlah');
            $totalCosts += $costs;
        }
        
        $totalProfit = $totalRevenue - $totalCosts;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        $avgProfitPerTrip = $trips->count() > 0 ? $totalProfit / $trips->count() : 0;
        
        return [
            'total_trips' => $trips->count(),
            'total_revenue' => $totalRevenue,
            'total_costs' => $totalCosts,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'avg_profit_per_trip' => $avgProfitPerTrip,
        ];
    }
    
    // Get Monthly Profit Trend
    public function getMonthlyProfitData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'revenue' => [], 'costs' => [], 'profit' => []];
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
        
        $revenueData = [];
        $costsData = [];
        $profitData = [];
        
        foreach ($monthKeys as $monthKey) {
            $trips = Trip::query()
                ->whereRaw('DATE_FORMAT(tanggal_trip, "%Y-%m") = ?', [$monthKey])
                ->where('status', 'selesai') // ✅ HANYA trip selesai
                ->when($this->kendaraan_id, fn($q) => $q->where('kendaraan_id', $this->kendaraan_id))
                ->when($this->sopir_id, fn($q) => $q->where('sopir_id', $this->sopir_id))
                ->with(['suratJalan.pesanan', 'biayaOperasional'])
                ->get();
            
            $monthRevenue = 0;
            $monthCosts = 0;
            
            foreach ($trips as $trip) {
                $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
                $monthRevenue += Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                $monthCosts += $trip->biayaOperasional->sum('jumlah');
            }
            
            $revenueData[] = (float) $monthRevenue;
            $costsData[] = (float) $monthCosts;
            $profitData[] = (float) ($monthRevenue - $monthCosts);
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'costs' => $costsData,
            'profit' => $profitData,
        ];
    }
    
    // Get Top 5 Profitable Drivers
    public function getTopDriversData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'profit' => [], 'trips' => []];
        }
        
        $drivers = [];
        
        $trips = $this->getFilteredQuery()->get()->groupBy('sopir_id');
        
        foreach ($trips as $sopirId => $sopirTrips) {
            $revenue = 0;
            $costs = 0;
            
            foreach ($sopirTrips as $trip) {
                $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
                $revenue += Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                $costs += $trip->biayaOperasional->sum('jumlah');
            }
            
            $drivers[] = [
                'nama' => $sopirTrips->first()->sopir->nama,
                'profit' => $revenue - $costs,
                'trips' => $sopirTrips->count(),
            ];
        }
        
        usort($drivers, fn($a, $b) => $b['profit'] <=> $a['profit']);
        $topDrivers = array_slice($drivers, 0, 5);
        
        return [
            'labels' => array_column($topDrivers, 'nama'),
            'profit' => array_column($topDrivers, 'profit'),
            'trips' => array_column($topDrivers, 'trips'),
        ];
    }
    
    // Get Cost Breakdown by Category
    public function getCostBreakdownData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'data' => []];
        }
        
        $costs = BiayaOperasional::query()
            ->whereIn('trip_id', $this->getFilteredQuery()->pluck('id'))
            ->with('kategoriBiaya')
            ->selectRaw('kategori_biaya_id, SUM(jumlah) as total')
            ->groupBy('kategori_biaya_id')
            ->orderByDesc('total')
            ->get();
        
        return [
            'labels' => $costs->map(fn($c) => $c->kategoriBiaya->nama)->toArray(),
            'data' => $costs->map(fn($c) => (float) $c->total)->toArray(),
        ];
    }
    
    // Get Top 5 Profitable Vehicles
    public function getTopVehiclesData(): array
    {
        if (!$this->hasAppliedFilter) {
            return ['labels' => [], 'profit' => []];
        }
        
        $vehicles = [];
        
        $trips = $this->getFilteredQuery()->get()->groupBy('kendaraan_id');
        
        foreach ($trips as $kendaraanId => $vehicleTrips) {
            $revenue = 0;
            $costs = 0;
            
            foreach ($vehicleTrips as $trip) {
                $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
                $revenue += Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                $costs += $trip->biayaOperasional->sum('jumlah');
            }
            
            $vehicles[] = [
                'nopol' => $vehicleTrips->first()->kendaraan->nopol,
                'profit' => $revenue - $costs,
            ];
        }
        
        usort($vehicles, fn($a, $b) => $b['profit'] <=> $a['profit']);
        $topVehicles = array_slice($vehicles, 0, 5);
        
        return [
            'labels' => array_column($topVehicles, 'nopol'),
            'profit' => array_column($topVehicles, 'profit'),
        ];
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
    
    // Helper method untuk calculate total profit (untuk summarizer)
    private function calculateTotalProfit(): float
    {
        $trips = $this->getFilteredQuery()->get();
        $totalRevenue = 0;
        $totalCosts = 0;
        
        foreach ($trips as $trip) {
            $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
            $totalRevenue += Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
            $totalCosts += $trip->biayaOperasional->sum('jumlah');
        }
        
        return $totalRevenue - $totalCosts;
    }
    
    // Get table footer summary for display
    public function getTableFooterSummary(): array
    {
        if (!$this->hasAppliedFilter) {
            return [];
        }
        
        $summary = $this->getSummaryData();
        
        return [
            'total_profit' => $summary['total_profit'],
            'total_revenue' => $summary['total_revenue'],
            'total_costs' => $summary['total_costs'],
        ];
    }
    
    // Export methods
    public function exportToExcel()
    {
        $trips = $this->getFilteredQuery()->get();
        
        // Calculate revenue & costs for each trip
        $data = $trips->map(function ($trip) {
            $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
            $revenue = Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
            $costs = $trip->biayaOperasional->sum('jumlah');
            
            $trip->total_revenue = $revenue;
            $trip->total_cost = $costs;
            $trip->suratJalan_count = $trip->suratJalan->count();
            
            return $trip;
        });
            
        return Excel::download(
            new \App\ExcelReports\ProfitabilitasExcelReport($data), 
            'laporan-profitabilitas-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportToPdf()
    {
        $trips = $this->getFilteredQuery()->get();
        
        // Calculate revenue & costs for each trip
        $data = $trips->map(function ($trip) {
            $pesananIds = $trip->suratJalan->pluck('pesanan_id')->unique();
            $revenue = Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
            $costs = $trip->biayaOperasional->sum('jumlah');
            
            $trip->total_revenue = $revenue;
            $trip->total_cost = $costs;
            $trip->total_berat = $trip->suratJalan->sum('berat_dikirim');
            $trip->suratJalan_count = $trip->suratJalan->count();
            
            return $trip;
        });
        
        $summary = $this->getSummaryData();
        
        $pdf = Pdf::loadView('reports.profitabilitas-pdf', [
            'data' => $data,
            'summary' => $summary,
            'filters' => [
                'dari_tanggal' => $this->dari_tanggal,
                'sampai_tanggal' => $this->sampai_tanggal,
                'kendaraan' => $this->kendaraan_id ? \App\Models\Kendaraan::find($this->kendaraan_id)?->nopol : null,
                'sopir' => $this->sopir_id ? \App\Models\Sopir::find($this->sopir_id)?->nama : null,
            ],
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-profitabilitas-' . now()->format('Y-m-d') . '.pdf');
    }
    
    // Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                
                TextColumn::make('id')
                    ->label('ID Trip')
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
                
                TextColumn::make('suratJalan_count')
                    ->label('Jml SJ')
                    ->counts('suratJalan')
                    ->alignCenter(),
                
                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('IDR', locale: 'id')
                    ->getStateUsing(function ($record) {
                        $pesananIds = $record->suratJalan->pluck('pesanan_id')->unique();
                        return Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                    })
                    ->alignEnd(),
                
                TextColumn::make('costs')
                    ->label('Biaya')
                    ->money('IDR', locale: 'id')
                    ->getStateUsing(function ($record) {
                        return $record->biayaOperasional->sum('jumlah');
                    })
                    ->alignEnd(),
                
                TextColumn::make('profit')
                    ->label('Profit')
                    ->money('IDR', locale: 'id')
                    ->getStateUsing(function ($record) {
                        $pesananIds = $record->suratJalan->pluck('pesanan_id')->unique();
                        $revenue = Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                        $costs = $record->biayaOperasional->sum('jumlah');
                        return $revenue - $costs;
                    })
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->weight('bold')
                    ->alignEnd(),
                
                TextColumn::make('margin')
                    ->label('Margin')
                    ->getStateUsing(function ($record) {
                        $pesananIds = $record->suratJalan->pluck('pesanan_id')->unique();
                        $revenue = Pesanan::whereIn('id', $pesananIds)->sum('total_tagihan');
                        $costs = $record->biayaOperasional->sum('jumlah');
                        $profit = $revenue - $costs;
                        
                        return $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
                    })
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->color(fn ($state) => match(true) {
                        $state >= 30 => 'success',
                        $state >= 15 => 'warning',
                        default => 'danger',
                    })
                    ->weight('bold')
                    ->alignEnd(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'berangkat' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'berangkat' => 'Berangkat',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => $state,
                    }),
            ])
            ->defaultSort('tanggal_trip', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}