<?php

namespace App\Filament\Resources\Item\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Item')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('Contoh: Asbes, Besi, Semen'),
                    
                Select::make('satuan')
                    ->label('Satuan')
                    ->options([
                        'Ton' => 'Ton',
                        'Kg' => 'Kg',
                        'M3' => 'M3',
                        'Unit' => 'Unit',
                    ])
                    ->default('Ton')
                    ->required(),
                    
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