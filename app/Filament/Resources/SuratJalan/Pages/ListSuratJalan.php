<?php


// ==========================================
// ListSuratJalan.php
// ==========================================
namespace App\Filament\Resources\SuratJalan\Pages;

use App\Filament\Resources\SuratJalan\SuratJalanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuratJalan extends ListRecords
{
    protected static string $resource = SuratJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}