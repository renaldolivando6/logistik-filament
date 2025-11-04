<?php

namespace App\Filament\Widgets;

use App\Models\BiayaOperasional;
use App\Models\UangSangu;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

class BiayaStatsOverview extends BaseWidget
{
    protected static bool $isDiscovered = false;
    
    public $filters = [];
    
    #[On('biayaFiltersUpdated')]
    public function updateFilters($filters): void
    {
        $this->filters = $filters;
    }
    
    protected function getStats(): array
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        $kendaraanId = $this->filters['kendaraan_id'] ?? null;
        $kategoriBiayaId = $this->filters['kategori_biaya_id'] ?? null;
        
        // Query Biaya Operasional
        $queryBiaya = BiayaOperasional::whereBetween('tanggal_biaya', [$start, $end]);
        
        if ($kendaraanId) {
            $queryBiaya->where('kendaraan_id', $kendaraanId);
        }
        
        if ($kategoriBiayaId) {
            $queryBiaya->where('kategori_biaya_id', $kategoriBiayaId);
        }
        
        $totalBiayaOps = $queryBiaya->sum('jumlah');
        $jumlahTransaksi = $queryBiaya->count();
        
        // Query Uang Sangu
        $queryUangSangu = UangSangu::whereBetween('tanggal_sangu', [$start, $end]);
        
        if ($kendaraanId) {
            $queryUangSangu->where('kendaraan_id', $kendaraanId);
        }
        
        $totalUangSangu = $queryUangSangu->sum('jumlah');
        
        // Total Keseluruhan
        $grandTotal = $totalBiayaOps + $totalUangSangu;
        
        // Rata-rata per transaksi
        $avgPerTransaksi = $jumlahTransaksi > 0 ? $totalBiayaOps / $jumlahTransaksi : 0;
        
        return [
            Stat::make('Total Biaya Operasional', 'Rp ' . Number::format($totalBiayaOps, locale: 'id'))
                ->description($jumlahTransaksi . ' transaksi')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),
                
            Stat::make('Total Uang Sangu', 'Rp ' . Number::format($totalUangSangu, locale: 'id'))
                ->description('Uang perjalanan sopir')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
                
            Stat::make('Grand Total', 'Rp ' . Number::format($grandTotal, locale: 'id'))
                ->description('Total semua biaya')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('danger'),
                
            Stat::make('Rata-rata Per Transaksi', 'Rp ' . Number::format($avgPerTransaksi, locale: 'id'))
                ->description('Biaya ops per transaksi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}