<?php

namespace App\Filament\Resources\Pesanan\Schemas;

use App\Models\Pesanan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PesananInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nomor_pesanan'),
                TextEntry::make('tanggal_pesanan')
                    ->date(),
                TextEntry::make('pelanggan_id')
                    ->numeric(),
                TextEntry::make('kendaraan_id')
                    ->numeric(),
                TextEntry::make('sopir_id')
                    ->numeric(),
                TextEntry::make('rute_id')
                    ->numeric(),
                TextEntry::make('jenis_muatan'),
                TextEntry::make('tonase')
                    ->numeric(),
                TextEntry::make('harga_per_ton')
                    ->numeric(),
                TextEntry::make('total_tagihan')
                    ->numeric(),
                TextEntry::make('uang_sangu')
                    ->numeric(),
                TextEntry::make('sisa_tagihan')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('catatan')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Pesanan $record): bool => $record->trashed()),
            ]);
    }
}
