<?php

// ==========================================
// ListTrip.php
// ==========================================
namespace App\Filament\Resources\Trip\Pages;

use App\Filament\Resources\Trip\TripResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrip extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}