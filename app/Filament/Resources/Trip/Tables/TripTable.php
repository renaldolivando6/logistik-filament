<?php

namespace App\Filament\Resources\Trip\Tables;

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

class TripTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Trip')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                    
                TextColumn::make('tanggal_trip')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->description(fn ($record) => $record->kendaraan?->jenis),
                    
                TextColumn::make('suratJalan')
                    ->label('Jumlah SJ')
                    ->counts('suratJalan')
                    ->badge()
                    ->color('info')
                    ->suffix(' SJ'),
                    
                TextColumn::make('uangSangu.jumlah')
                    ->label('Uang Sangu')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-')
                    ->color('success'),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'berangkat' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->catatan)
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'berangkat' => 'Berangkat',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
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