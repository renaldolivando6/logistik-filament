<?php

namespace App\Filament\Resources\UangSangu\Schemas;

use App\Models\UangSangu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UangSanguForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_sangu')
                    ->label('Tanggal Sangu')
                    ->required()
                    ->default(now()),
                    
                Select::make('pesanan_id')
                    ->label('Pesanan (Opsional)')
                    ->relationship('pesanan', 'nomor_pesanan')
                    ->searchable()
                    ->preload(),
                    
                Select::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),
                    
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'selesai' => 'Selesai',
                    ])
                    ->default('menunggu')
                    ->required(),
            ]);
    }
}