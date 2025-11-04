<?php

namespace App\Filament\Resources\UangSangu;

use App\Filament\Resources\UangSangu\Pages\CreateUangSangu;
use App\Filament\Resources\UangSangu\Pages\EditUangSangu;
use App\Filament\Resources\UangSangu\Pages\ListUangSangu;
use App\Filament\Resources\UangSangu\Schemas\UangSanguForm;
use App\Filament\Resources\UangSangu\Tables\UangSanguTable;
use App\Models\UangSangu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UangSanguResource extends Resource
{
    protected static ?string $model = UangSangu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Uang Sangu';
    }

        public static function getModelLabel(): string
    {
        return 'Uang Sangu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Uang Sangu';
    }

    protected static ?string $recordTitleAttribute = 'nomor_sangu';

    public static function form(Schema $schema): Schema
    {
        return UangSanguForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UangSanguTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUangSangu::route('/'),
            //'create' => CreateUangSangu::route('/create'),
            //'edit' => EditUangSangu::route('/{record}/edit'),
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
