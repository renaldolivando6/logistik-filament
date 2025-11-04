<?php
namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

class SalesStatsOverview extends BaseWidget
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
        
        $revenue = Pesanan::whereBetween('tanggal_pesanan', [$start, $end])
            ->sum('total_tagihan');
            
        $orders = Pesanan::whereBetween('tanggal_pesanan', [$start, $end])
            ->count();
        
        return [
            Stat::make('Total Revenue', 'Rp ' . Number::format($revenue, locale: 'id'))
                ->description('Periode: ' . date('d/m/Y', strtotime($start)) . ' - ' . date('d/m/Y', strtotime($end)))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
                
            Stat::make('Jumlah Pesanan', number_format($orders))
                ->description('Total pesanan')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
                
            Stat::make('Rata-rata Per Order', 'Rp ' . Number::format($orders > 0 ? $revenue / $orders : 0, locale: 'id'))
                ->description('Average order value')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),
        ];
    }
}