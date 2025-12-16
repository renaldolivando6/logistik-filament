<?php

namespace App\Filament\Resources\Sopir\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SopirTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ HAPUS kolom "kode"
                
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable(),
                    
                TextColumn::make('no_sim')
                    ->label('No. SIM')
                    ->searchable(),
                    
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alamat)
                    ->toggleable(),
                    
                IconColumn::make('aktif')
                    ->label('Status')
                    ->boolean(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama', 'asc');
    }
}