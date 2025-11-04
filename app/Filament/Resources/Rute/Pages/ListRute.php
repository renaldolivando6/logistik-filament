<?php

namespace App\Filament\Resources\Rute\Pages;

use App\Filament\Resources\Rute\RuteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRute extends ListRecords
{
    protected static string $resource = RuteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
