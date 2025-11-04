<?php

namespace App\Filament\Resources\Pesanan\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PesananTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_pesanan')
                    ->searchable(),
                TextColumn::make('tanggal_pesanan')
                    ->date()
                    ->sortable(),
                TextColumn::make('pelanggan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kendaraan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sopir_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rute_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jenis_muatan')
                    ->searchable(),
                TextColumn::make('tonase')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('harga_per_ton')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_tagihan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uang_sangu')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sisa_tagihan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
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
                ViewAction::make(),
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
