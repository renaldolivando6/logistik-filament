<?php

namespace App\Filament\Resources\Pesanan\Schemas;

use App\Models\Pesanan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->default(fn () => 'ORD-' . date('Ymd') . '-' . str_pad(Pesanan::count() + 1, 4, '0', STR_PAD_LEFT))
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ignoreRecord: true),
                    
                DatePicker::make('tanggal_pesanan')
                    ->label('Tanggal Pesanan')
                    ->required()
                    ->default(now()),
                    
                Select::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->relationship('pelanggan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Select::make('rute_id')
                    ->label('Rute')
                    ->relationship('rute', 'asal')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->asal} → {$record->tujuan} ({$record->jenis_muatan})")
                    ->searchable(['asal', 'tujuan', 'jenis_muatan'])
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $rute = \App\Models\Rute::find($state);
                            if ($rute) {
                                $set('harga_per_ton', $rute->harga_per_ton);
                                $set('jenis_muatan', $rute->jenis_muatan);
                            }
                        }
                    })
                    ->required(),
                    
                TextInput::make('jenis_muatan')
                    ->label('Jenis Muatan')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('tonase')
                    ->label('Tonase (Ton)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->suffix('Ton')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => 
                        self::calculateTotal($state, $get, $set)
                    ),
                    
                TextInput::make('harga_per_ton')
                    ->label('Harga Per Ton')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
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
                    
                TextInput::make('uang_sangu')
                    ->label('Uang Sangu')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => 
                        self::calculateRemaining($state, $get, $set)
                    ),
                    
                TextInput::make('sisa_tagihan')
                    ->label('Sisa Tagihan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'dalam_perjalanan' => 'Dalam Perjalanan',
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
    
    private static function calculateTotal($state, Get $get, Set $set): void
    {
        $tonase = $get('tonase') ?? 0;
        $hargaPerTon = $get('harga_per_ton') ?? 0;
        $total = $tonase * $hargaPerTon;
        
        $set('total_tagihan', $total);
        
        // Hitung sisa tagihan
        $uangSangu = $get('uang_sangu') ?? 0;
        $set('sisa_tagihan', $total - $uangSangu);
    }
    
    private static function calculateRemaining($state, Get $get, Set $set): void
    {
        $totalTagihan = $get('total_tagihan') ?? 0;
        $uangSangu = $state ?? 0;
        
        $set('sisa_tagihan', $totalTagihan - $uangSangu);
    }
}