<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\Pages\CreateUser;
use App\Filament\Resources\User\Pages\EditUser;
use App\Filament\Resources\User\Pages\ListUser;
use App\Filament\Resources\User\Schemas\UserForm;
use App\Filament\Resources\User\Tables\UserTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Pengaturan';
    }

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return 'User Login';
    }

    public static function getModelLabel(): string
    {
        return 'User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'User';
    }

    // ✅ Hide menu dari sidebar jika bukan owner
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->email === 'owner@tan.com';
    }

    // ✅ Block akses langsung via URL jika bukan owner
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->email === 'owner@tan.com';
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserTable::configure($table);
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
            'index' => ListUser::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}