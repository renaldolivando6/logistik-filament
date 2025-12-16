<?php

namespace App\Filament\Resources\Item;

use App\Filament\Resources\Item\Pages\CreateItem;
use App\Filament\Resources\Item\Pages\EditItem;
use App\Filament\Resources\Item\Pages\ListItem;
use App\Filament\Resources\Item\Schemas\ItemForm;
use App\Filament\Resources\Item\Tables\ItemTable;
use App\Models\Item;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationLabel(): string
    {
        return 'Item';
    }

    public static function getModelLabel(): string
    {
        return 'Item';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Item';
    }

    public static function form(Schema $schema): Schema
    {
        return ItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ItemTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItem::route('/'),
            'create' => CreateItem::route('/create'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}