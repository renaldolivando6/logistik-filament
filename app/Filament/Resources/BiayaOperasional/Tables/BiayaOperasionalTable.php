<?php

namespace App\Filament\Resources\BiayaOperasional\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Trip\TripResource;

class BiayaOperasionalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_biaya')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                
                // ✅ Show tipe
                TextColumn::make('tipe_biaya')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TRIP' => 'info',
                        'NON-TRIP' => 'warning',
                        default => 'gray',
                    }),
                
                // ✅ Trip (if exists)
                TextColumn::make('trip.id')
                    ->label('Trip')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? "Trip #{$state}" : '-')
                    ->url(fn ($record) => $record->trip ? 
                        TripResource::getUrl('view', ['record' => $record->trip]) : null
                    )
                    ->placeholder('-')
                    ->sortable(),
                
                // ✅ Sopir (via trip or direct)
                TextColumn::make('trip.sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->placeholder('-'),
                    
                // ✅ Kendaraan (via trip or direct)
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->getStateUsing(fn ($record) => $record->trip?->kendaraan?->nopol ?? $record->kendaraan?->nopol)
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->description(fn ($record) => 
                        $record->trip?->kendaraan?->jenis ?? $record->kendaraan?->jenis
                    )
                    ->placeholder('-'),
                    
                TextColumn::make('kategoriBiaya.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SOLAR' => 'danger',
                        'TOL' => 'warning',
                        'SPAREPART' => 'info',
                        'SERVIS' => 'success',
                        'BAN' => 'primary',
                        'ACCU' => 'gray',
                        default => 'secondary',
                    }),
                    
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger')
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                            ->label('Total'),
                    ]),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->wrap()
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Filter by Tipe
                SelectFilter::make('tipe')
                    ->label('Tipe Biaya')
                    ->options([
                        'trip' => 'Biaya Trip',
                        'non-trip' => 'Biaya Umum',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'trip') {
                            return $query->whereNotNull('trip_id');
                        } elseif ($data['value'] === 'non-trip') {
                            return $query->whereNull('trip_id');
                        }
                        return $query;
                    }),
                
                SelectFilter::make('trip_id')
                    ->label('Trip')
                    ->relationship('trip', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Trip #{$record->id} - {$record->sopir->nama}")
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Trip'),
                
                SelectFilter::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                
                SelectFilter::make('kategori_biaya_id')
                    ->label('Kategori Biaya')
                    ->relationship('kategoriBiaya', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kategori'),
                    
                Filter::make('tanggal_biaya')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_biaya', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_biaya', '<=', $date),
                            );
                    }),
                    
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_biaya', 'desc');
    }
}