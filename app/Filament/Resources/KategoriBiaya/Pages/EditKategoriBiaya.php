<?php

namespace App\Filament\Resources\KategoriBiaya\Pages;

use App\Filament\Resources\KategoriBiaya\KategoriBiayaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBiaya extends EditRecord
{
    protected static string $resource = KategoriBiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
