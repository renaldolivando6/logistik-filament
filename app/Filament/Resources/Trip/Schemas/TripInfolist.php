<?php

namespace App\Filament\Resources\Trip\Schemas;

use App\Models\Trip;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Trip')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID Trip')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => "Trip #{$state}"),
                            
                        TextEntry::make('tanggal_trip')
                            ->label('Tanggal Trip')
                            ->date('d/m/Y'),
                            
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'berangkat' => 'warning',
                                'selesai' => 'success',
                                'batal' => 'danger',
                                default => 'gray',
                            }),
                            
                        TextEntry::make('sopir.nama')
                            ->label('Sopir'),
                            
                        TextEntry::make('kendaraan.nopol')
                            ->label('Kendaraan')
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(fn ($record) => 
                                "{$record->kendaraan->nopol} - {$record->kendaraan->jenis}"
                            ),
                            
                        TextEntry::make('jumlah_surat_jalan')
                            ->label('Jumlah Surat Jalan')
                            ->state(fn ($record) => $record->suratJalan->count())
                            ->badge()
                            ->color('info'),
                            
                        TextEntry::make('total_berat')
                            ->label('Total Berat')
                            ->numeric(2)
                            ->suffix(' Kg')
                            ->weight('bold')
                            ->color('success'),
                    ]),
                    
                Section::make('Uang Sangu')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('uang_sangu')
                            ->label('Jumlah Uang Sangu')
                            ->money('IDR', locale: 'id')
                            ->weight('bold')
                            ->color('success'),
                            
                        TextEntry::make('catatan_sangu')
                            ->label('Catatan Uang Sangu')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Daftar Surat Jalan')
                    ->schema([
                        RepeatableEntry::make('suratJalan')
                            ->label('')
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID SJ')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn ($state) => "SJ #{$state}"),
                                
                                TextEntry::make('pesanan.pelanggan.nama')
                                    ->label('Pelanggan')
                                    ->weight('bold'),
                                    
                                TextEntry::make('jenis_muatan')
                                    ->label('Muatan')
                                    ->badge()
                                    ->color('success'),
                                    
                                TextEntry::make('rute.asal')
                                    ->label('Asal'),
                                    
                                TextEntry::make('rute.tujuan')
                                    ->label('Tujuan'),
                                    
                                TextEntry::make('alamatPelanggan.alamat_lengkap')
                                    ->label('Alamat Tujuan')
                                    ->limit(40)
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                                    
                                TextEntry::make('berat_dikirim')
                                    ->label('Berat')
                                    ->numeric(2)
                                    ->suffix(' Kg')
                                    ->weight('bold')
                                    ->color('info'),
                                    
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'dikirim' => 'warning',
                                        'diterima' => 'success',
                                        default => 'gray',
                                    }),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(fn ($record) => $record->suratJalan->isEmpty())
                    ->collapsible(),
                    
                Section::make('Catatan Trip')
                    ->schema([
                        TextEntry::make('catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
                    
                Section::make('Metadata')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d/m/Y H:i'),
                            
                        TextEntry::make('updated_at')
                            ->label('Diupdate')
                            ->dateTime('d/m/Y H:i'),
                            
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn (Trip $record): bool => $record->trashed()),
                    ])
                    ->collapsed(),
            ]);
    }
}