<?php

namespace App\Filament\Resources\KategoriBiaya;

use App\Filament\Resources\KategoriBiaya\Pages\CreateKategoriBiaya;
use App\Filament\Resources\KategoriBiaya\Pages\EditKategoriBiaya;
use App\Filament\Resources\KategoriBiaya\Pages\ListKategoriBiaya;
use App\Filament\Resources\KategoriBiaya\Schemas\KategoriBiayaForm;
use App\Filament\Resources\KategoriBiaya\Tables\KategoriBiayaTable;
use App\Models\KategoriBiaya;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriBiayaResource extends Resource
{
    protected static ?string $model = KategoriBiaya::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 3;


    public static function getNavigationLabel(): string
    {
        return 'Kategori Biaya';
    }

        public static function getModelLabel(): string
    {
        return 'Kategori Biaya';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Biaya';
    }

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return KategoriBiayaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriBiayaTable::configure($table);
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
            'index' => ListKategoriBiaya::route('/'),
            'create' => CreateKategoriBiaya::route('/create'),
            'edit' => EditKategoriBiaya::route('/{record}/edit'),
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
