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
                // ✅ Tampilkan ID saja, bukan nomor_pesanan
                TextColumn::make('id')
                    ->label('No. Pesanan')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                    
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
                    
                TextColumn::make('jenis_muatan')
                    ->label('Muatan')
                    ->searchable()
                    ->limit(20),
                    
                TextColumn::make('tonase')
                    ->label('Tonase')
                    ->numeric()
                    ->sortable()
                    ->suffix(' Ton'),
                    
                TextColumn::make('harga_per_ton')
                    ->label('Harga/Ton')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                    
                TextColumn::make('uang_sangu')
                    ->label('Uang Sangu')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('sisa_tagihan')
                    ->label('Sisa')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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