<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    // ❌ SALAH (Penyebab Error):
    // protected static string $view = 'filament.widgets.quick-actions';

    // ✅ BENAR (Hapus 'static'):
    protected string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
}