<?php

namespace App\Filament\Resources\Rute\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RuteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('asal')
                    ->label('Asal')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Waru, Negoro, dll'),
                    
                TextInput::make('tujuan')
                    ->label('Tujuan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Gresik, Sidoarjo, dll'),
                    
                Select::make('item_id')
                    ->label('Item Muatan')
                    ->relationship('item', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('nama')
                            ->label('Nama Item')
                            ->required()
                            ->maxLength(255),
                        Select::make('satuan')
                            ->options([
                                'Ton' => 'Ton',
                                'Kg' => 'Kg',
                                'M3' => 'M3',
                            ])
                            ->default('Ton')
                            ->required(),
                    ]),
                    
                TextInput::make('harga_per_ton')
                    ->label('Harga Per Ton')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),
                    
                Toggle::make('aktif')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}