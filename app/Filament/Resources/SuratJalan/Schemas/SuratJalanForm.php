<?php

namespace App\Filament\Resources\SuratJalan\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SuratJalanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Pesanan')
                    ->columns(2)
                    ->schema([
                        Select::make('pesanan_id')
                            ->label('Pesanan')
                            ->relationship(
                                'pesanan', 
                                'id',
                                fn ($query) => $query->where('status', 'draft')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "#{$record->id} - {$record->pelanggan->nama} | " .
                                "{$record->rute->asal} → {$record->rute->tujuan} | " .
                                "{$record->rute->item->nama} ({$record->berat} Kg)"
                            )
                            ->searchable(['id'])
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $pesanan = \App\Models\Pesanan::with(['rute.item', 'pelanggan'])->find($state);
                                    if ($pesanan) {
                                        $totalDikirim = $pesanan->suratJalan()->sum('berat_dikirim');
                                        $sisaBerat = $pesanan->berat - $totalDikirim;
                                        
                                        $set('berat_dikirim', $sisaBerat > 0 ? $sisaBerat : 0);
                                        
                                        // ✅ Reset alamat saat pesanan berubah
                                        $set('alamat_pelanggan_id', null);
                                    }
                                }
                            })
                            ->required(),
                            
                        Placeholder::make('info_pelanggan')
                            ->label('Pelanggan')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::with('pelanggan')->find($pesananId);
                                    return $pesanan?->pelanggan?->nama ?? '-';
                                }
                                return '-';
                            }),
                            
                        Placeholder::make('info_rute')
                            ->label('Rute')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::with('rute')->find($pesananId);
                                    return $pesanan ? 
                                        "{$pesanan->rute->asal} → {$pesanan->rute->tujuan}" : '-';
                                }
                                return '-';
                            }),
                            
                        Placeholder::make('info_muatan')
                            ->label('Jenis Muatan')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::with('rute.item')->find($pesananId);
                                    return $pesanan?->rute?->item?->nama ?? '-';
                                }
                                return '-';
                            }),
                            
                        Placeholder::make('info_berat_total')
                            ->label('Total Berat Pesanan')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::find($pesananId);
                                    return $pesanan ? number_format($pesanan->berat, 2) . ' Kg' : '-';
                                }
                                return '-';
                            }),
                            
                        Placeholder::make('info_berat_terkirim')
                            ->label('Sudah Dikirim')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::find($pesananId);
                                    $totalDikirim = $pesanan->suratJalan()->sum('berat_dikirim');
                                    return number_format($totalDikirim, 2) . ' Kg';
                                }
                                return '0.00 Kg';
                            }),
                            
                        Placeholder::make('info_sisa_berat')
                            ->label('Sisa Berat')
                            ->content(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if ($pesananId) {
                                    $pesanan = \App\Models\Pesanan::find($pesananId);
                                    $totalDikirim = $pesanan->suratJalan()->sum('berat_dikirim');
                                    $sisaBerat = $pesanan->berat - $totalDikirim;
                                    return number_format($sisaBerat, 2) . ' Kg';
                                }
                                return '-';
                            }),
                    ]),
                    
                Section::make('Alamat & Detail Pengiriman')
                    ->columns(2)
                    ->schema([
                        // ✅ Pilih alamat pelanggan
                        Select::make('alamat_pelanggan_id')
                            ->label('Alamat Tujuan')
                            ->options(function (Get $get) {
                                $pesananId = $get('pesanan_id');
                                if (!$pesananId) {
                                    return [];
                                }
                                
                                $pesanan = \App\Models\Pesanan::with('pelanggan.alamatAktif')->find($pesananId);
                                if (!$pesanan || !$pesanan->pelanggan) {
                                    return [];
                                }
                                
                                return $pesanan->pelanggan->alamatAktif->mapWithKeys(function ($alamat) {
                                    $label = $alamat->label ? "{$alamat->label} - " : '';
                                    $label .= $alamat->alamat_lengkap;
                                    if ($alamat->kota) {
                                        $label .= ", {$alamat->kota}";
                                    }
                                    if ($alamat->is_default) {
                                        $label = "⭐ {$label}";
                                    }
                                    return [$alamat->id => $label];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Pilih alamat tujuan pengiriman')
                            ->disabled(fn (Get $get) => !$get('pesanan_id'))
                            ->columnSpanFull(),
                            
                        TextInput::make('berat_dikirim')
                            ->label('Berat Dikirim (Kg)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->suffix('Kg')
                            ->helperText('Isi dengan berat yang akan dikirim (bisa partial/bertahap)')
                            ->columnSpanFull(),
                            
                        // ❌ REMOVED: Status input (auto-managed)
                        // Status will be:
                        // - 'draft' on create (default)
                        // - 'dikirim' when added to trip (auto)
                        // - 'diterima' via button in view page (manual)
                    ]),
                    
                Section::make('Trip (Opsional)')
                    ->description('Trip akan diisi nanti saat membuat Trip yang mencakup surat jalan ini')
                    ->collapsed()
                    ->schema([
                        Select::make('trip_id')
                            ->label('Trip')
                            ->relationship('trip', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "Trip #{$record->id} - {$record->sopir->nama} ({$record->kendaraan->nopol}) - " . 
                                $record->tanggal_trip->format('d/m/Y')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Belum ada trip')
                            ->helperText('Kosongkan jika belum membuat trip'),
                    ]),
                    
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}