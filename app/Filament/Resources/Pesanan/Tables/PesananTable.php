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
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->weight('bold'),
                    
                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                    
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                    
                // ✅ Via relasi rute->item
                TextColumn::make('rute.item.nama')
                    ->label('Muatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('berat')
                    ->label('Berat')
                    ->numeric(2)
                    ->sortable()
                    ->suffix(' Kg'),
                    
                // ✅ NEW: Total berat dikirim via accessor
                TextColumn::make('total_berat_dikirim')
                    ->label('Terkirim')
                    ->numeric(2)
                    ->suffix(' Kg')
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                // ✅ NEW: Sisa berat via accessor
                TextColumn::make('sisa_berat')
                    ->label('Sisa')
                    ->numeric(2)
                    ->suffix(' Kg')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('harga_per_kg')
                    ->label('Harga/Kg')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam_perjalanan' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    }),
                    
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