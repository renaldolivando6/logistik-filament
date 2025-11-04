<?php

namespace App\Filament\Resources\KategoriBiaya\Pages;

use App\Filament\Resources\KategoriBiaya\KategoriBiayaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBiaya extends ListRecords
{
    protected static string $resource = KategoriBiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
