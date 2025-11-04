<?php
namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            \Filament\Widgets\AccountWidget::class,
            \App\Filament\Widgets\DashboardStatsOverview::class,
            \App\Filament\Widgets\RecentOrdersWidget::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
}