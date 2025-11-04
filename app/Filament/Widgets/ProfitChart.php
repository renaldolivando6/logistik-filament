<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\BiayaOperasional;
use App\Models\UangSangu;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class ProfitChart extends ChartWidget
{
    protected ?string $heading = 'Trend Profit';
    
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
            
            $biaya = BiayaOperasional::whereBetween('tanggal_biaya', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');
            
            $sangu = UangSangu::whereBetween('tanggal_sangu', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');
            
            $profit = $revenue - ($biaya + $sangu);
            
            $months->push([
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'biaya' => $biaya + $sangu,
                'profit' => $profit,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $months->pluck('revenue')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Biaya',
                    'data' => $months->pluck('biaya')->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
                [
                    'label' => 'Profit',
                    'data' => $months->pluck('profit')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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