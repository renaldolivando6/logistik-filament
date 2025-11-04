<?php

namespace App\Filament\Resources\BiayaOperasional;

use App\Filament\Resources\BiayaOperasional\Pages\CreateBiayaOperasional;
use App\Filament\Resources\BiayaOperasional\Pages\EditBiayaOperasional;
use App\Filament\Resources\BiayaOperasional\Pages\ListBiayaOperasional;
use App\Filament\Resources\BiayaOperasional\Schemas\BiayaOperasionalForm;
use App\Filament\Resources\BiayaOperasional\Tables\BiayaOperasionalTable;
use App\Models\BiayaOperasional;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BiayaOperasionalResource extends Resource
{
    protected static ?string $model = BiayaOperasional::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Biaya Operasional';
    }

    public static function getModelLabel(): string
    {
        return 'Biaya Operasional';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Biaya Operasional';
    }

    public static function form(Schema $schema): Schema
    {
        return BiayaOperasionalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BiayaOperasionalTable::configure($table);
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
            'index' => ListBiayaOperasional::route('/'),
            'create' => CreateBiayaOperasional::route('/create'),
            'edit' => EditBiayaOperasional::route('/{record}/edit'),
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
