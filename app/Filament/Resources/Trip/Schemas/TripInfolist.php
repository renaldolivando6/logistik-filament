<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Models\Trip;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal_trip')
                    ->date(),
                TextEntry::make('sopir_id')
                    ->numeric(),
                TextEntry::make('kendaraan_id')
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
                    ->visible(fn (Trip $record): bool => $record->trashed()),
            ]);
    }
}
