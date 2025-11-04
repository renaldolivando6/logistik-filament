<?php

namespace App\Filament\Resources\Pelanggan\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->label('Kode Pelanggan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('CUST001'),
                    
                TextInput::make('nama')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),
                    
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                    
                TextInput::make('telepon')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20),
                    
                TextInput::make('kontak_person')
                    ->label('Kontak Person')
                    ->maxLength(255),
                    
                Toggle::make('aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}