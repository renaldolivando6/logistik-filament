<?php

namespace App\Filament\Resources\Sopir\Pages;

use App\Filament\Resources\Sopir\SopirResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSopir extends EditRecord
{
    protected static string $resource = SopirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
