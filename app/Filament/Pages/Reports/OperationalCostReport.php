<?php
namespace App\Filament\Pages\Reports;

use BackedEnum;
use Filament\Pages\Page;

class OperationalCostReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected string $view = 'filament.pages.reports.operational-cost-report';
    
    protected static ?int $navigationSort = 3;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Biaya Operasional';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Biaya Operasional';
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FilterBiayaWidget::class,
            \App\Filament\Widgets\BiayaStatsOverview::class,
            \App\Filament\Widgets\BiayaPerKendaraanChart::class,
            \App\Filament\Widgets\BiayaPerKategoriChart::class,
            \App\Filament\Widgets\DetailBiayaTable::class,
        ];
    }
}