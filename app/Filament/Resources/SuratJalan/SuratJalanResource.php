<?php

namespace App\Filament\Resources\SuratJalan;

use App\Filament\Resources\SuratJalan\Pages\CreateSuratJalan;
use App\Filament\Resources\SuratJalan\Pages\EditSuratJalan;
use App\Filament\Resources\SuratJalan\Pages\ListSuratJalan;
use App\Filament\Resources\SuratJalan\Pages\ViewSuratJalan;
use App\Filament\Resources\SuratJalan\Schemas\SuratJalanForm;
use App\Filament\Resources\SuratJalan\Tables\SuratJalanTable;
use App\Models\SuratJalan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuratJalanResource extends Resource
{
    protected static ?string $model = SuratJalan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Surat Jalan';
    }

    public static function getModelLabel(): string
    {
        return 'Surat Jalan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Surat Jalan';
    }

    public static function form(Schema $schema): Schema
    {
        return SuratJalanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratJalanTable::configure($table);
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
            'index' => ListSuratJalan::route('/'),
            'create' => CreateSuratJalan::route('/create'),
            'view' => ViewSuratJalan::route('/{record}'),
            'edit' => EditSuratJalan::route('/{record}/edit'),
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