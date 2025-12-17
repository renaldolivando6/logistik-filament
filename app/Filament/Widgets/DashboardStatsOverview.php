<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Kendaraan;
use App\Models\Sopir;
use App\Models\Trip;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
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
            ->where('status', '!=', 'batal')
            ->sum('total_tagihan');
        
        // Trip aktif (berangkat/belum selesai)
        $tripAktif = Trip::whereIn('status', ['draft', 'berangkat'])->count();
        
        return [
            Stat::make('Pesanan Bulan Ini', $pesananThisMonth)
                ->description(($pesananGrowth >= 0 ? '+' : '') . number_format($pesananGrowth, 1) . '% dari bulan lalu')
                ->descriptionIcon($pesananGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pesananGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Revenue Bulan Ini', 'Rp ' . Number::format($revenueThisMonth, locale: 'id'))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Trip Aktif', $tripAktif)
                ->description('Sedang berjalan')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
                
            Stat::make('Armada', Kendaraan::where('aktif', true)->count())
                ->description('Kendaraan tersedia')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),
                
            Stat::make('Sopir', Sopir::where('aktif', true)->count())
                ->description('Sopir aktif')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info'),
                
            Stat::make('Pelanggan', Pelanggan::where('aktif', true)->count())
                ->description('Total pelanggan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}