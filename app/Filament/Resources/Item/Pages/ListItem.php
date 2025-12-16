<?php
// ==========================================
// ListItem.php
// ==========================================
namespace App\Filament\Resources\Item\Pages;

use App\Filament\Resources\Item\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListItem extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}