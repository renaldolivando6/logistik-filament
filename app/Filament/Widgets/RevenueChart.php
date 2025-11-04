<?php
namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Trend';

    protected static bool $isDiscovered = false;
    
    protected ?string $maxHeight = '300px';
    
    public $filters = [];
    
    #[On('filtersUpdated')]
    public function updateFilters($filters): void
    {
        $this->filters = $filters;
    }
    
    protected function getData(): array
    {
        $start = $this->filters['start_date'] ?? now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);
        $monthsDiff = max(1, $startDate->diffInMonths($endDate) + 1);
        
        $months = collect();
        for ($i = $monthsDiff - 1; $i >= 0; $i--) {
            $date = \Carbon\Carbon::parse($end)->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            if ($startOfMonth < $startDate) $startOfMonth = $startDate;
            if ($endOfMonth > $endDate) $endOfMonth = $endDate;
            
            $revenue = Pesanan::whereBetween('tanggal_pesanan', [$startOfMonth, $endOfMonth])
                ->sum('total_tagihan');
            
            $months->push([
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $months->pluck('revenue')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}