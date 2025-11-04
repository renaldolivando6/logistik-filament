<?php

namespace App\Filament\Resources\Rute;

use App\Filament\Resources\Rute\Pages\CreateRute;
use App\Filament\Resources\Rute\Pages\EditRute;
use App\Filament\Resources\Rute\Pages\ListRute;
use App\Filament\Resources\Rute\Schemas\RuteForm;
use App\Filament\Resources\Rute\Tables\RuteTable;
use App\Models\Rute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RuteResource extends Resource
{
    protected static ?string $model = Rute::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Master Data';
    }

    protected static ?int $navigationSort = 5;


    public static function getNavigationLabel(): string
    {
        return 'Rute';
    }

        public static function getModelLabel(): string
    {
        return 'Rute';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rute';
    }

    protected static ?string $recordTitleAttribute = 'asal';

    public static function form(Schema $schema): Schema
    {
        return RuteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuteTable::configure($table);
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
            'index' => ListRute::route('/'),
            'create' => CreateRute::route('/create'),
            'edit' => EditRute::route('/{record}/edit'),
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
