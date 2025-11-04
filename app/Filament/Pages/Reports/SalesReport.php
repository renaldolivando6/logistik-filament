<?php
namespace App\Filament\Pages\Reports;

use BackedEnum;
use Filament\Pages\Page;

class SalesReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';
    
    protected string $view = 'filament.pages.reports.sales-report';
    
    protected static ?int $navigationSort = 1;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Penjualan';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Penjualan';
    }
    
    // ✅ Widgets: Filter di atas, lalu Stats & Chart
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FilterPeriodeWidget::class,
            \App\Filament\Widgets\SalesStatsOverview::class,
            \App\Filament\Widgets\RevenueChart::class,
        ];
    }
}