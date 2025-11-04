<?php

namespace App\Filament\Widgets;

use App\Models\BiayaOperasional;
use App\Models\UangSangu;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class BiayaBreakdownChart extends ChartWidget
{
    protected ?string $heading = 'Breakdown Biaya';
    
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
        $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        
        // Biaya per kategori
        $biayaPerKategori = BiayaOperasional::whereBetween('tanggal_biaya', [$start, $end])
            ->with('kategoriBiaya')
            ->get()
            ->groupBy('kategori_biaya_id')
            ->map(function ($items) {
                return [
                    'nama' => $items->first()->kategoriBiaya->nama ?? 'Lainnya',
                    'total' => $items->sum('jumlah'),
                ];
            });
        
        // Uang Sangu
        $totalSangu = UangSangu::whereBetween('tanggal_sangu', [$start, $end])
            ->sum('jumlah');
        
        $labels = $biayaPerKategori->pluck('nama')->toArray();
        $data = $biayaPerKategori->pluck('total')->toArray();
        
        // Tambah Uang Sangu
        $labels[] = 'Uang Sangu';
        $data[] = $totalSangu;

        return [
            'datasets' => [
                [
                    'label' => 'Biaya',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(251, 146, 60)',
                        'rgb(245, 158, 11)',
                        'rgb(234, 179, 8)',
                        'rgb(132, 204, 22)',
                        'rgb(34, 197, 94)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}