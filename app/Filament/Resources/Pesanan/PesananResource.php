<?php

namespace App\Filament\Resources\Pesanan;

use App\Filament\Resources\Pesanan\Pages\CreatePesanan;
use App\Filament\Resources\Pesanan\Pages\EditPesanan;
use App\Filament\Resources\Pesanan\Pages\ListPesanan;
use App\Filament\Resources\Pesanan\Pages\ViewPesanan;
use App\Filament\Resources\Pesanan\Schemas\PesananForm;
use App\Filament\Resources\Pesanan\Schemas\PesananInfolist;
use App\Filament\Resources\Pesanan\Tables\PesananTable;
use App\Models\Pesanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Transaksi';
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Pesanan';
    }

        public static function getModelLabel(): string
    {
        return 'Pesanan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pesanan';
    }

    protected static ?string $recordTitleAttribute = 'nomor_pesanan';

    public static function form(Schema $schema): Schema
    {
        return PesananForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PesananInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PesananTable::configure($table);
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
            'index' => ListPesanan::route('/'),
            'create' => CreatePesanan::route('/create'),
            'view' => ViewPesanan::route('/{record}'),
            'edit' => EditPesanan::route('/{record}/edit'),
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
