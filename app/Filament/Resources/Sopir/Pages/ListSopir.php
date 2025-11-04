<?php

namespace App\Filament\Resources\Sopir\Pages;

use App\Filament\Resources\Sopir\SopirResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSopir extends ListRecords
{
    protected static string $resource = SopirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
