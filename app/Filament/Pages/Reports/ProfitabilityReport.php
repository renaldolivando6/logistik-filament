<?php
namespace App\Filament\Pages\Reports;

use BackedEnum;
use Filament\Pages\Page;

class ProfitabilityReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    
    protected string $view = 'filament.pages.reports.profitability-report';
    
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Profitabilitas';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Profitabilitas';
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FilterPeriodeWidget::class,
            \App\Filament\Widgets\ProfitStatsOverview::class,
            \App\Filament\Widgets\ProfitChart::class,
            \App\Filament\Widgets\BiayaBreakdownChart::class,
        ];
    }
}