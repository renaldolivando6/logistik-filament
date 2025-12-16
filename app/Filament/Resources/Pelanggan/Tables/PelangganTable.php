<?php

namespace App\Filament\Resources\Pelanggan\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PelangganTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('telepon')
                    ->searchable(),
                    
                TextColumn::make('kontak_person')
                    ->searchable(),
                    
                // ✅ Show jumlah alamat
                TextColumn::make('alamat_count')
                    ->label('Jumlah Alamat')
                    ->counts('alamat')
                    ->badge()
                    ->color('info'),
                    
                // ✅ Show alamat default
                TextColumn::make('alamatDefault.alamat_lengkap')
                    ->label('Alamat Utama')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(),
                    
                IconColumn::make('aktif')
                    ->boolean(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}