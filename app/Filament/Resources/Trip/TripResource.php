<?php

namespace App\Filament\Resources\Trip;

use App\Filament\Resources\Trip\Pages\CreateTrip;
use App\Filament\Resources\Trip\Pages\EditTrip;
use App\Filament\Resources\Trip\Pages\ListTrip;
use App\Filament\Resources\Trip\Pages\ViewTrip;
use App\Filament\Resources\Trip\Schemas\TripForm;
use App\Filament\Resources\Trip\Tables\TripTable;
use App\Models\Trip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Trip';
    }

    public static function getModelLabel(): string
    {
        return 'Trip';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Trip';
    }

    public static function form(Schema $schema): Schema
    {
        return TripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripTable::configure($table);
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
            'index' => ListTrip::route('/'),
            'create' => CreateTrip::route('/create'),
            'view' => ViewTrip::route('/{record}'),
            'edit' => EditTrip::route('/{record}/edit'),
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