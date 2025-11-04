<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Kendaraan;
use App\Models\Sopir;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $bulanIni = now()->startOfMonth();
        $bulanLalu = now()->subMonth()->startOfMonth();
        
        // Pesanan bulan ini
        $pesananThisMonth = Pesanan::where('tanggal_pesanan', '>=', $bulanIni)->count();
        $pesananLastMonth = Pesanan::whereBetween('tanggal_pesanan', [
            $bulanLalu, 
            $bulanLalu->copy()->endOfMonth()
        ])->count();
        
        $pesananGrowth = $pesananLastMonth > 0 
            ? (($pesananThisMonth - $pesananLastMonth) / $pesananLastMonth) * 100 
            : 0;
        
        // Revenue bulan ini
        $revenueThisMonth = Pesanan::where('tanggal_pesanan', '>=', $bulanIni)
            ->sum('total_tagihan');
        
        return [
            Stat::make('Pesanan Bulan Ini', $pesananThisMonth)
                ->description(($pesananGrowth >= 0 ? '+' : '') . number_format($pesananGrowth, 1) . '% dari bulan lalu')
                ->descriptionIcon($pesananGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pesananGrowth >= 0 ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
                
            Stat::make('Revenue Bulan Ini', 'Rp ' . Number::format($revenueThisMonth, locale: 'id'))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([3, 5, 4, 6, 7, 5, 6, 8]),
                
            Stat::make('Armada Aktif', Kendaraan::where('aktif', true)->count() . '/' . Kendaraan::count())
                ->description('Kendaraan tersedia')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
                
            Stat::make('Pelanggan Aktif', Pelanggan::where('aktif', true)->count())
                ->description('Total pelanggan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}