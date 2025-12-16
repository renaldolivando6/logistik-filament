<?php

namespace App\Filament\Resources\Trip\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TripTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->weight('bold'),
                    
                TextColumn::make('id')
                    ->label('ID Trip')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "Trip #{$state}")
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('tanggal_trip')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                    
                TextColumn::make('suratJalan_count')
                    ->counts('suratJalan')
                    ->label('Jml SJ')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "{$state} SJ"),
                    
                TextColumn::make('total_berat')
                    ->label('Total Berat')
                    ->numeric(2)
                    ->suffix(' Kg')
                    ->sortable(false)
                    ->color('success'),
                    
                TextColumn::make('uang_sangu')
                    ->label('Uang Sangu')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
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
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'berangkat' => 'Berangkat',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ]),
                    
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
                    
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // ❌ NO BULK DELETE - only restore
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_trip', 'desc');
    }
}