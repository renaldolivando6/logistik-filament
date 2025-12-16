<?php

namespace App\Filament\Resources\Trip\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_trip')
                    ->label('Tanggal Trip')
                    ->required()
                    ->default(now())
                    ->native(false),
                    
                Select::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                    ]),
                    
                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nopol} - {$record->jenis}")
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'berangkat' => 'Berangkat',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ])
                    ->default('draft')
                    ->required(),
                    
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}