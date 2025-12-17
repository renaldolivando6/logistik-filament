<?php

namespace App\Filament\Resources\User\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama lengkap'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('user@example.com'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->placeholder('Minimal 8 karakter')
                    ->helperText(fn (string $context) => 
                        $context === 'edit' 
                            ? 'Kosongkan jika tidak ingin mengubah password' 
                            : 'Password minimal 8 karakter'
                    ),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrated(false)
                    ->same('password')
                    ->placeholder('Ulangi password'),
            ])
            ->columns(2);
    }
}