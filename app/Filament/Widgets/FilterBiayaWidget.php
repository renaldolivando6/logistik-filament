<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FilterBiayaWidget extends Widget
{
    protected static bool $isDiscovered = false;
    
    protected string $view = 'filament.widgets.filter-biaya-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public $start_date;
    public $end_date;
    public $kendaraan_id = null;
    public $kategori_biaya_id = null;
    
    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }
    
    public function updatedStartDate()
    {
        $this->updateFilters();
    }
    
    public function updatedEndDate()
    {
        $this->updateFilters();
    }
    
    public function updatedKendaraanId()
    {
        $this->updateFilters();
    }
    
    public function updatedKategoriBiayaId()
    {
        $this->updateFilters();
    }
    
    public function updateFilters(): void
    {
        $this->dispatch('biayaFiltersUpdated', filters: [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'kendaraan_id' => $this->kendaraan_id,
            'kategori_biaya_id' => $this->kategori_biaya_id,
        ]);
    }
    
    public function resetFilters(): void
    {
        $this->kendaraan_id = null;
        $this->kategori_biaya_id = null;
        $this->updateFilters();
    }
}