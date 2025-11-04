<?php

namespace App\Filament\Resources\BiayaOperasional\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BiayaOperasionalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_biaya')
                    ->label('Tanggal Biaya')
                    ->required()
                    ->default(now()),
                    
                Select::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'nomor_pesanan')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('kategori_biaya_id')
                    ->label('Kategori Biaya')
                    ->relationship('kategoriBiaya', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}