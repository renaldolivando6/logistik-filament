<?php

namespace App\Filament\Resources\UangSangu\Pages;

use App\Filament\Resources\UangSangu\UangSanguResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUangSangu extends EditRecord
{
    protected static string $resource = UangSanguResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
