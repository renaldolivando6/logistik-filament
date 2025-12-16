<?php

namespace App\Filament\Resources\SuratJalan\Schemas;

use App\Models\SuratJalan;
use App\Filament\Resources\Trip\TripResource;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SuratJalanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Surat Jalan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID Surat Jalan')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => "SJ #{$state}"),
                            
                        TextEntry::make('trip.id')
                            ->label('Trip')
                            ->badge()
                            ->color(fn ($state) => $state ? 'info' : 'gray')
                            ->formatStateUsing(fn ($state) => $state ? "Trip #{$state}" : 'Belum ada trip')
                            ->url(fn ($record) => $record->trip ? 
                                TripResource::getUrl('view', ['record' => $record->trip]) : null
                            ),
                            
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'dikirim' => 'warning',
                                'diterima' => 'success',
                                default => 'gray',
                            }),
                    ]),
                    
                Section::make('Informasi Pesanan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pesanan.id')
                            ->label('ID Pesanan')
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(fn ($state) => "Pesanan #{$state}"),
                            
                        TextEntry::make('pesanan.pelanggan.nama')
                            ->label('Pelanggan'),
                            
                        TextEntry::make('jenis_muatan')
                            ->label('Jenis Muatan')
                            ->badge()
                            ->color('success'),
                            
                        TextEntry::make('rute.asal')
                            ->label('Asal'),
                            
                        TextEntry::make('rute.tujuan')
                            ->label('Tujuan'),
                    ]),
                    
                // ✅ NEW Section: Alamat Tujuan
                Section::make('Alamat Tujuan Pengiriman')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('alamatPelanggan.label')
                            ->label('Label Alamat')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),
                            
                        TextEntry::make('alamatPelanggan.alamat_lengkap')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                            
                        TextEntry::make('alamatPelanggan.kota')
                            ->label('Kota')
                            ->placeholder('-'),
                            
                        TextEntry::make('alamatPelanggan.provinsi')
                            ->label('Provinsi')
                            ->placeholder('-'),
                            
                        TextEntry::make('alamatPelanggan.kode_pos')
                            ->label('Kode Pos')
                            ->placeholder('-'),
                            
                        TextEntry::make('alamatPelanggan.kontak_person')
                            ->label('Kontak Person')
                            ->placeholder('-'),
                            
                        TextEntry::make('alamatPelanggan.telepon')
                            ->label('Telepon')
                            ->placeholder('-'),
                    ])
                    ->visible(fn ($record) => $record->alamat_pelanggan_id !== null),
                    
                Section::make('Detail Pengiriman')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('berat_dikirim')
                            ->label('Berat Dikirim')
                            ->numeric(2)
                            ->suffix(' Kg')
                            ->weight('bold')
                            ->color('info'),
                            
                        TextEntry::make('tanggal_kirim')
                            ->label('Tanggal Kirim')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                            
                        TextEntry::make('tanggal_terima')
                            ->label('Tanggal Terima')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                    ]),
                    
                Section::make('Detail Trip (jika ada)')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('trip.tanggal_trip')
                            ->label('Tanggal Trip')
                            ->date('d/m/Y')
                            ->placeholder('-'),
                            
                        TextEntry::make('trip.sopir.nama')
                            ->label('Sopir')
                            ->placeholder('-'),
                            
                        TextEntry::make('trip.kendaraan.nopol')
                            ->label('Kendaraan')
                            ->badge()
                            ->color('gray')
                            ->placeholder('-'),
                    ])
                    ->visible(fn ($record) => $record->trip_id !== null),
                    
                Section::make('Catatan')
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
                            ->visible(fn (SuratJalan $record): bool => $record->trashed()),
                    ])
                    ->collapsed(),
            ]);
    }
}