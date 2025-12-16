<?php

namespace App\Filament\Resources\SuratJalan\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SuratJalanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID SJ')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                    
                TextColumn::make('trip.id')
                    ->label('ID Trip')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->url(fn ($record) => $record->trip ? 
                        route('filament.admin.resources.trip.view', ['record' => $record->trip]) : null
                    ),
                    
                TextColumn::make('pesanan.id')
                    ->label('ID Pesanan')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->description(fn ($record) => $record->pesanan?->pelanggan?->nama),
                    
                TextColumn::make('trip.sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('trip.kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                TextColumn::make('pesanan.rute.asal')
                    ->label('Rute')
                    ->formatStateUsing(fn ($record) => 
                        ($record->pesanan?->rute?->asal ?? '-') . ' → ' . 
                        ($record->pesanan?->rute?->tujuan ?? '-')
                    )
                    ->wrap(),
                    
                TextColumn::make('tonase_dikirim')
                    ->label('Tonase')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->sortable(),
                    
                TextColumn::make('tanggal_kirim')
                    ->label('Tgl Kirim')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                    
                TextColumn::make('tanggal_terima')
                    ->label('Tgl Terima')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dikirim' => 'warning',
                        'diterima' => 'success',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('trip_id')
                    ->label('Trip')
                    ->relationship('trip', 'id')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'id')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
                    ])
                    ->multiple(),
                    
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}