<?php

namespace App\Filament\Resources\Sopir\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SopirForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ✅ HAPUS field "kode" sopir
                
                TextInput::make('nama')
                    ->label('Nama Sopir')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('telepon')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20),
                    
                TextInput::make('no_sim')
                    ->label('Nomor SIM')
                    ->maxLength(50),
                    
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Toggle::make('aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}