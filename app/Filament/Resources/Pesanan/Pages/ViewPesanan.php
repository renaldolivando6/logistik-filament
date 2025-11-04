<?php

namespace App\Filament\Resources\Pesanan\Pages;

use App\Filament\Resources\Pesanan\PesananResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPesanan extends ViewRecord
{
    protected static string $resource = PesananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
