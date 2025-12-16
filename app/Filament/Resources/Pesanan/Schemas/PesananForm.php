<?php

namespace App\Filament\Resources\Pesanan\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('tanggal_pesanan')
                    ->label('Tanggal Pesanan')
                    ->required()
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                    
                Select::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->relationship('pelanggan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('rute_id')
                    ->label('Rute')
                    ->relationship('rute', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        "{$record->asal} → {$record->tujuan} ({$record->item->nama})"
                    )
                    ->searchable(['asal', 'tujuan'])
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $rute = \App\Models\Rute::with('item')->find($state);
                            if ($rute) {
                                $set('harga_per_kg', $rute->harga_per_kg);
                            }
                        }
                    })
                    ->required(),
                    
                // ✅ Display jenis muatan via rute->item
                Placeholder::make('jenis_muatan_display')
                    ->label('Jenis Muatan')
                    ->content(function (Get $get) {
                        $ruteId = $get('rute_id');
                        if ($ruteId) {
                            $rute = \App\Models\Rute::with('item')->find($ruteId);
                            return $rute?->item?->nama ?? '-';
                        }
                        return '-';
                    }),
                    
                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nopol} - {$record->jenis}")
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                TextInput::make('berat')
                    ->label('Berat (Kg)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('Kg')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => 
                        self::calculateTotal($state, $get, $set)
                    ),
                    
                TextInput::make('harga_per_kg')
                    ->label('Harga Per Kg')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => 
                        self::calculateTotal($state, $get, $set)
                    ),
                    
                TextInput::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
                    
                TextInput::make('status')
                    ->default('draft')
                    ->hidden()
                    ->dehydrated(),
                    
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
    
    private static function calculateTotal($state, Get $get, Set $set): void
    {
        $berat = $get('berat') ?? 0;
        $hargaPerKg = $get('harga_per_kg') ?? 0;
        $total = $berat * $hargaPerKg;
        
        $set('total_tagihan', $total);
    }
}
