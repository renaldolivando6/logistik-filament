<?php

namespace App\Filament\Resources\Pelanggan;

use App\Filament\Resources\Pelanggan\Pages\CreatePelanggan;
use App\Filament\Resources\Pelanggan\Pages\EditPelanggan;
use App\Filament\Resources\Pelanggan\Pages\ListPelanggan;
use App\Filament\Resources\Pelanggan\Schemas\PelangganForm;
use App\Filament\Resources\Pelanggan\Tables\PelangganTable;
use App\Models\Pelanggan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

        public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama';

    
    public static function getNavigationLabel(): string
    {
        return 'Pelanggan';
    }

        public static function getModelLabel(): string
    {
        return 'Pelanggan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pelanggan';
    }
    
    public static function form(Schema $schema): Schema
    {
        return PelangganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelangganTable::configure($table);
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
            'index' => ListPelanggan::route('/'),
            'create' => CreatePelanggan::route('/create'),
            'edit' => EditPelanggan::route('/{record}/edit'),
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
