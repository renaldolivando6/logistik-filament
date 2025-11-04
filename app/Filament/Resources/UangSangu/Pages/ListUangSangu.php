<?php

namespace App\Filament\Resources\UangSangu\Pages;

use App\Filament\Resources\UangSangu\UangSanguResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUangSangu extends ListRecords
{
    protected static string $resource = UangSanguResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
