<?php

namespace App\Filament\Resources\SuratJalan\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SuratJalanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('trip_id')
                    ->label('Trip')
                    ->relationship('trip', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "Trip #{$record->id} - {$record->sopir->nama} ({$record->kendaraan->nopol}) - " . 
                        $record->tanggal_trip->format('d/m/Y')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        DatePicker::make('tanggal_trip')
                            ->required()
                            ->default(now()),
                        Select::make('sopir_id')
                            ->relationship('sopir', 'nama')
                            ->required(),
                        Select::make('kendaraan_id')
                            ->relationship('kendaraan', 'nopol')
                            ->required(),
                    ]),
                    
                Select::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "ID: {$record->id} - {$record->pelanggan->nama} ({$record->jenis_muatan} - {$record->tonase} Ton)"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $pesanan = \App\Models\Pesanan::find($state);
                            if ($pesanan) {
                                $set('tonase_dikirim', $pesanan->tonase);
                            }
                        }
                    }),
                    
                DatePicker::make('tanggal_kirim')
                    ->label('Tanggal Kirim')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                    
                DatePicker::make('tanggal_terima')
                    ->label('Tanggal Terima')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                    
                TextInput::make('tonase_dikirim')
                    ->label('Tonase Dikirim (Ton)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('Ton')
                    ->helperText('Isi dengan tonase yang dikirim (untuk partial delivery)'),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
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