<?php

namespace App\Filament\Resources\Pesanan\Schemas;

use App\Models\Pesanan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PesananInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pesanan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tanggal_pesanan')
                            ->label('Tanggal')
                            ->date('d/m/Y'),
                            
                        TextEntry::make('pelanggan.nama')
                            ->label('Pelanggan'),
                            
                        TextEntry::make('rute.asal')
                            ->label('Asal'),
                            
                        TextEntry::make('rute.tujuan')
                            ->label('Tujuan'),
                            
                        // ✅ Via accessor
                        TextEntry::make('jenis_muatan')
                            ->label('Jenis Muatan')
                            ->badge()
                            ->color('info'),
                            
                        TextEntry::make('kendaraan.nopol')
                            ->label('Kendaraan')
                            ->badge()
                            ->color('warning'),
                            
                        TextEntry::make('sopir.nama')
                            ->label('Sopir'),
                            
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'dalam_perjalanan' => 'warning',
                                'selesai' => 'success',
                                'batal' => 'danger',
                                default => 'gray',
                            }),
                    ]),
                    
                Section::make('Detail Berat & Harga')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('berat')
                            ->label('Total Berat Order')
                            ->numeric(2)
                            ->suffix(' Kg'),
                            
                        // ✅ Via accessor
                        TextEntry::make('total_berat_dikirim')
                            ->label('Berat Terkirim')
                            ->numeric(2)
                            ->suffix(' Kg')
                            ->color('success'),
                            
                        // ✅ Via accessor
                        TextEntry::make('sisa_berat')
                            ->label('Sisa Berat')
                            ->numeric(2)
                            ->suffix(' Kg')
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
                            
                        TextEntry::make('harga_per_kg')
                            ->label('Harga Per Kg')
                            ->money('IDR', locale: 'id'),
                            
                        TextEntry::make('total_tagihan')
                            ->label('Total Tagihan')
                            ->money('IDR', locale: 'id')
                            ->weight('bold')
                            ->color('success'),
                    ]),
                    
                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
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
                            ->visible(fn (Pesanan $record): bool => $record->trashed()),
                    ])
                    ->collapsed(),
            ]);
    }
}