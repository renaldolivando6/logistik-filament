<?php

namespace App\Filament\Resources\Pelanggan\Pages;

use App\Filament\Resources\Pelanggan\PelangganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPelanggan extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
