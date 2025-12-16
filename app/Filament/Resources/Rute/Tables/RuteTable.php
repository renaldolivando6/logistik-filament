<?php

namespace App\Filament\Resources\Rute\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RuteTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asal')
                    ->label('Asal')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('item.nama')
                    ->label('Item')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('harga_per_kg')
                    ->label('Harga/Kg')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                    
                IconColumn::make('aktif')
                    ->label('Status')
                    ->boolean(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('item_id')
                    ->label('Item')
                    ->relationship('item', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
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
            ->defaultSort('asal', 'asc');
    }
}