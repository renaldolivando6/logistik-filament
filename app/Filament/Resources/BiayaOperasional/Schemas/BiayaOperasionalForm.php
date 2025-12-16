<?php

namespace App\Filament\Resources\BiayaOperasional\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BiayaOperasionalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tipe Biaya')
                    ->description('Pilih apakah biaya ini terkait dengan trip tertentu atau biaya umum')
                    ->schema([
                        Radio::make('tipe_biaya')
                            ->label('Tipe Biaya')
                            ->options([
                                'trip' => 'Biaya Trip (Solar, Tol, dll dalam perjalanan)',
                                'non-trip' => 'Biaya Umum (Servis rutin, Listrik, dll)',
                            ])
                            ->default('trip')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Reset fields when type changes
                                $set('trip_id', null);
                                $set('kendaraan_id', null);
                                $set('pesanan_id', null);
                            })
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Informasi Biaya')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_biaya')
                            ->label('Tanggal Biaya')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        
                        // ✅ TRIP: Trip selector
                        Select::make('trip_id')
                            ->label('Trip')
                            ->relationship('trip', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "Trip #{$record->id} - {$record->sopir->nama} ({$record->kendaraan->nopol}) - " . 
                                $record->tanggal_trip->format('d/m/Y')
                            )
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('tipe_biaya') === 'trip')
                            ->visible(fn (Get $get) => $get('tipe_biaya') === 'trip')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $trip = \App\Models\Trip::with(['kendaraan', 'suratJalan.pesanan'])->find($state);
                                    if ($trip) {
                                        // Auto-fill kendaraan & pesanan
                                        $set('kendaraan_id', $trip->kendaraan_id);
                                        $firstPesanan = $trip->suratJalan->first()?->pesanan;
                                        if ($firstPesanan) {
                                            $set('pesanan_id', $firstPesanan->id);
                                        }
                                    }
                                }
                            })
                            ->helperText('Pilih trip yang terkait dengan biaya ini'),
                        
                        // ✅ NON-TRIP: Kendaraan selector (optional)
                        Select::make('kendaraan_id')
                            ->label('Kendaraan')
                            ->relationship('kendaraan', 'nopol')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nopol} - {$record->jenis}")
                            ->searchable()
                            ->preload()
                            ->required(false)
                            ->visible(fn (Get $get) => $get('tipe_biaya') === 'non-trip')
                            ->helperText('Opsional - kosongkan jika biaya tidak terkait kendaraan tertentu (misal: listrik kantor)'),
                        
                        // ✅ Info Trip (if selected)
                        Placeholder::make('trip_info')
                            ->label('Detail Trip')
                            ->content(function (Get $get) {
                                $tripId = $get('trip_id');
                                if (!$tripId) {
                                    return '-';
                                }
                                
                                $trip = \App\Models\Trip::with([
                                    'sopir', 
                                    'kendaraan', 
                                    'suratJalan.pesanan.pelanggan'
                                ])->find($tripId);
                                
                                if (!$trip) {
                                    return '-';
                                }
                                
                                $pelangganList = $trip->suratJalan
                                    ->pluck('pesanan.pelanggan.nama')
                                    ->unique()
                                    ->join(', ');
                                
                                return sprintf(
                                    "Sopir: %s | Kendaraan: %s %s | Pelanggan: %s",
                                    $trip->sopir->nama,
                                    $trip->kendaraan->nopol,
                                    $trip->kendaraan->jenis,
                                    $pelangganList ?: 'Belum ada surat jalan'
                                );
                            })
                            ->visible(fn (Get $get) => $get('tipe_biaya') === 'trip' && $get('trip_id'))
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Detail Biaya')
                    ->columns(2)
                    ->schema([
                        Select::make('kategori_biaya_id')
                            ->label('Kategori Biaya')
                            ->relationship('kategoriBiaya', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Pilih jenis biaya (Solar, Tol, Servis, dll)'),
                            
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),
                            
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Detail tambahan tentang biaya ini')
                            ->columnSpanFull(),
                    ]),
                
                // ✅ Hidden fields (auto-filled, but still saved)
                Select::make('pesanan_id')
                    ->relationship('pesanan', 'id')
                    ->hidden()
                    ->dehydrated(),
                
                Select::make('kendaraan_id')
                    ->relationship('kendaraan', 'nopol')
                    ->hidden(fn (Get $get) => $get('tipe_biaya') === 'trip')
                    ->dehydrated(),
            ]);
    }
}