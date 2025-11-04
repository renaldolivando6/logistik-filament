<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FilterPeriodeWidget extends Widget
{
    // ✅ Hapus "static"
    protected string $view = 'filament.widgets.filter-periode-widget';

    protected static bool $isDiscovered = false;
    
    protected int | string | array $columnSpan = 'full';
    
    public $start_date;
    public $end_date;
    
    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }
    
    public function updatedStartDate()
    {
        $this->dispatch('filtersUpdated', filters: [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }
    
    public function updatedEndDate()
    {
        $this->dispatch('filtersUpdated', filters: [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }
}