<?php

namespace App\Filament\Resources\Kendaraan\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KendaraanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nopol')
                    ->label('Nomor Polisi')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                    
                TextInput::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->placeholder('Truk, Pickup, dll')
                    ->maxLength(100),
                    
                TextInput::make('kapasitas')
                    ->label('Kapasitas (Ton)')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('Ton'),
                    
                TextInput::make('merk')
                    ->label('Merk')
                    ->maxLength(100),
                    
                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 1)
                    ->length(4),
                    
                Toggle::make('aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}