<?php

namespace App\Filament\Resources\BiayaOperasional\Pages;

use App\Filament\Resources\BiayaOperasional\BiayaOperasionalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBiayaOperasional extends ListRecords
{
    protected static string $resource = BiayaOperasionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
