<?php

namespace App\Filament\Widgets;

use App\Models\BiayaOperasional;
use App\Models\UangSangu;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class BiayaPerKategoriChart extends ChartWidget
{
    protected static bool $isDiscovered = false;
    
    protected ?string $heading = 'Breakdown Biaya Per Kategori';
    
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
        
        // Query biaya per kategori
        $query = BiayaOperasional::whereBetween('tanggal_biaya', [$start, $end])
            ->with('kategoriBiaya');
        
        if ($kendaraanId) {
            $query->where('kendaraan_id', $kendaraanId);
        }
        
        if ($kategoriBiayaId) {
            $query->where('kategori_biaya_id', $kategoriBiayaId);
        }
        
        $biayaPerKategori = $query->get()
            ->groupBy('kategori_biaya_id')
            ->map(function ($items) {
                return [
                    'nama' => $items->first()->kategoriBiaya->nama ?? 'Lainnya',
                    'total' => $items->sum('jumlah'),
                ];
            })
            ->sortByDesc('total');
        
        // Tambah Uang Sangu kalau tidak difilter kategori
        if (!$kategoriBiayaId) {
            $queryUangSangu = UangSangu::whereBetween('tanggal_sangu', [$start, $end]);
            
            if ($kendaraanId) {
                $queryUangSangu->where('kendaraan_id', $kendaraanId);
            }
            
            $totalUangSangu = $queryUangSangu->sum('jumlah');
            
            if ($totalUangSangu > 0) {
                $biayaPerKategori->push([
                    'nama' => 'Uang Sangu',
                    'total' => $totalUangSangu,
                ]);
            }
        }
        
        $labels = $biayaPerKategori->pluck('nama')->toArray();
        $data = $biayaPerKategori->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Biaya',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(239, 68, 68)',   // Red - SOLAR
                        'rgb(251, 146, 60)',  // Orange - TOL
                        'rgb(245, 158, 11)',  // Amber - SPAREPART
                        'rgb(234, 179, 8)',   // Yellow - SERVIS
                        'rgb(132, 204, 22)',  // Lime - BAN
                        'rgb(34, 197, 94)',   // Green - ACCU
                        'rgb(59, 130, 246)',  // Blue - Uang Sangu
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}