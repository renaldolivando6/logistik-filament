<?php

namespace App\Filament\Resources\Sopir;

use App\Filament\Resources\Sopir\Pages\CreateSopir;
use App\Filament\Resources\Sopir\Pages\EditSopir;
use App\Filament\Resources\Sopir\Pages\ListSopir;
use App\Filament\Resources\Sopir\Schemas\SopirForm;
use App\Filament\Resources\Sopir\Tables\SopirTable;
use App\Models\Sopir;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SopirResource extends Resource
{
    protected static ?string $model = Sopir::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 4;


    public static function getNavigationLabel(): string
    {
        return 'Sopir';
    }

        public static function getModelLabel(): string
    {
        return 'Sopir';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sopir';
    }

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return SopirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SopirTable::configure($table);
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
            'index' => ListSopir::route('/'),
            'create' => CreateSopir::route('/create'),
            'edit' => EditSopir::route('/{record}/edit'),
        ];
    }
}
