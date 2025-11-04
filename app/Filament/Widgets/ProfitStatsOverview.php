<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\BiayaOperasional;
use App\Models\UangSangu;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

class ProfitStatsOverview extends BaseWidget
{
    public $filters = [];

    protected static bool $isDiscovered = false;
    
    #[On('filtersUpdated')]
    public function updateFilters($filters): void
    {
        $this->filters = $filters;
    }
    
    protected function getStats(): array
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        
        // Total Revenue
        $revenue = Pesanan::whereBetween('tanggal_pesanan', [$start, $end])
            ->sum('total_tagihan');
        
        // Total Biaya Operasional
        $biayaOps = BiayaOperasional::whereBetween('tanggal_biaya', [$start, $end])
            ->sum('jumlah');
        
        // Total Uang Sangu
        $uangSangu = UangSangu::whereBetween('tanggal_sangu', [$start, $end])
            ->sum('jumlah');
        
        // Total Biaya
        $totalBiaya = $biayaOps + $uangSangu;
        
        // Net Profit
        $profit = $revenue - $totalBiaya;
        
        // Profit Margin
        $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
        
        return [
            Stat::make('Gross Revenue', 'Rp ' . Number::format($revenue, locale: 'id'))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Total Biaya', 'Rp ' . Number::format($totalBiaya, locale: 'id'))
                ->description('Biaya Ops: Rp ' . Number::format($biayaOps, locale: 'id') . ' + Uang Sangu: Rp ' . Number::format($uangSangu, locale: 'id'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
                
            Stat::make('Net Profit', 'Rp ' . Number::format($profit, locale: 'id'))
                ->description('Profit Margin: ' . number_format($profitMargin, 1) . '%')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($profit >= 0 ? 'success' : 'danger'),
                
            Stat::make('Profit Margin', number_format($profitMargin, 1) . '%')
                ->description($profitMargin >= 20 ? 'Sangat Baik' : ($profitMargin >= 10 ? 'Baik' : 'Perlu Perbaikan'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color($profitMargin >= 20 ? 'success' : ($profitMargin >= 10 ? 'warning' : 'danger')),
        ];
    }
}