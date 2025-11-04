<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome-widget';

    protected static bool $isDiscovered = false;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = false;
}