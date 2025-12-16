<?php

namespace App\Filament\Resources\Trip\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Trip')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_trip')
                            ->label('Tanggal Trip')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                            
                        Select::make('sopir_id')
                            ->label('Sopir')
                            ->relationship('sopir', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Select::make('kendaraan_id')
                            ->label('Kendaraan')
                            ->relationship('kendaraan', 'nopol')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nopol} - {$record->jenis}")
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        // ✅ Status only visible on EDIT (not on create)
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'berangkat' => 'Berangkat',
                                'selesai' => 'Selesai',
                                'batal' => 'Batal',
                            ])
                            ->default('draft')
                            ->required()
                            ->visible(fn ($record) => $record !== null) // Only show on edit
                            ->helperText('Update status trip sesuai progress pengiriman'),
                    ]),
                    
                Section::make('Uang Sangu')
                    ->columns(2)
                    ->schema([
                        TextInput::make('uang_sangu')
                            ->label('Jumlah Uang Sangu')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),
                            
                        Textarea::make('catatan_sangu')
                            ->label('Catatan Uang Sangu')
                            ->rows(2)
                            ->placeholder('Catatan khusus untuk uang sangu...')
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Surat Jalan')
                    ->description('Pilih surat jalan yang akan masuk dalam trip ini')
                    ->schema([
                        Select::make('surat_jalan_ids')
                            ->label('Daftar Surat Jalan')
                            ->multiple()
                            ->relationship(
                                'suratJalan',
                                'id',
                                fn ($query, $record) => $query
                                    ->with(['pesanan.pelanggan', 'pesanan.rute.item', 'alamatPelanggan'])
                                    ->where(function ($q) use ($record) {
                                        $q->whereNull('trip_id');
                                        // ✅ On edit, include current trip's SJ
                                        if ($record) {
                                            $q->orWhere('trip_id', $record->id);
                                        }
                                    })
                                    ->where('status', '!=', 'batal')
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $alamat = $record->alamatPelanggan 
                                    ? ($record->alamatPelanggan->label ?: $record->alamatPelanggan->kota)
                                    : '-';
                                
                                return sprintf(
                                    "SJ #%d | %s | %s → %s | %s (%.2f Kg) | 📍 %s",
                                    $record->id,
                                    $record->pesanan->pelanggan->nama,
                                    $record->rute->asal,
                                    $record->rute->tujuan,
                                    $record->jenis_muatan,
                                    $record->berat_dikirim,
                                    $alamat
                                );
                            })
                            ->searchable()
                            ->preload()
                            ->helperText('Hanya menampilkan surat jalan yang belum masuk trip atau sudah ada di trip ini')
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && count($state) > 0) {
                                    $suratJalans = \App\Models\SuratJalan::whereIn('id', $state)->get();
                                    $totalBerat = $suratJalans->sum('berat_dikirim');
                                    $set('preview_total_berat', $totalBerat);
                                } else {
                                    $set('preview_total_berat', 0);
                                }
                            }),
                            
                        Placeholder::make('preview_total_berat')
                            ->label('Preview Total Berat')
                            ->content(function (Get $get) {
                                $sjIds = $get('surat_jalan_ids');
                                if ($sjIds && count($sjIds) > 0) {
                                    $suratJalans = \App\Models\SuratJalan::whereIn('id', $sjIds)->get();
                                    $totalBerat = $suratJalans->sum('berat_dikirim');
                                    $jumlahSJ = count($sjIds);
                                    
                                    // ✅ Group by pelanggan
                                    $grouped = $suratJalans->groupBy('pesanan.pelanggan.nama');
                                    $summary = [];
                                    foreach ($grouped as $pelanggan => $items) {
                                        $summary[] = "{$pelanggan}: " . number_format($items->sum('berat_dikirim'), 2) . " Kg";
                                    }
                                    
                                    return number_format($totalBerat, 2) . " Kg dari {$jumlahSJ} Surat Jalan\n" 
                                        . implode("\n", $summary);
                                }
                                return '-';
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record !== null), // Collapse on edit
                    
                Textarea::make('catatan')
                    ->label('Catatan Trip')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}