<?php

namespace App\Filament\Widgets;

use App\Models\BiayaOperasional;
use App\Models\Kendaraan;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class BiayaPerKendaraanChart extends ChartWidget
{
    protected static bool $isDiscovered = false;
    
    protected ?string $heading = 'Biaya Operasional Per Kendaraan';
    
    protected ?string $maxHeight = '300px';
    
    public $filters = [];
    
    #[On('biayaFiltersUpdated')]
    public function updateFilters($filters): void
    {
        $this->filters = $filters;
    }
    
    protected function getData(): array
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        $kendaraanId = $this->filters['kendaraan_id'] ?? null;
        $kategoriBiayaId = $this->filters['kategori_biaya_id'] ?? null;
        
        // Query biaya per kendaraan
        $query = BiayaOperasional::whereBetween('tanggal_biaya', [$start, $end])
            ->with('kendaraan');
        
        if ($kendaraanId) {
            $query->where('kendaraan_id', $kendaraanId);
        }
        
        if ($kategoriBiayaId) {
            $query->where('kategori_biaya_id', $kategoriBiayaId);
        }
        
        $biayaPerKendaraan = $query->get()
            ->groupBy('kendaraan_id')
            ->map(function ($items) {
                return [
                    'nopol' => $items->first()->kendaraan->nopol ?? 'Unknown',
                    'total' => $items->sum('jumlah'),
                ];
            })
            ->sortByDesc('total')
            ->take(10); // Top 10 kendaraan
        
        return [
            'datasets' => [
                [
                    'label' => 'Biaya (Rp)',
                    'data' => $biayaPerKendaraan->pluck('total')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $biayaPerKendaraan->pluck('nopol')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}