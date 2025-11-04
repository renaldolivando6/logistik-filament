<?php

namespace App\Filament\Resources\KategoriBiaya\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KategoriBiayaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Toggle::make('aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}