<?php

namespace App\Filament\Resources\Pesanan\Pages;

use App\Filament\Resources\Pesanan\PesananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPesanan extends ListRecords
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
