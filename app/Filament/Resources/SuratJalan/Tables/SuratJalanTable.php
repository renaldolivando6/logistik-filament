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
use App\Filament\Resources\Trip\TripResource;

class SuratJalanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->weight('bold'),
                    
                // ✅ FIXED: Remove route, use resource URL
                TextColumn::make('trip_status')
                    ->label('Trip')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->trip_id ? "Trip #{$record->trip_id}" : 'Belum jalan')
                    ->color(fn ($state) => str_contains($state, 'Trip') ? 'info' : 'gray')
                    ->url(fn ($record) => $record->trip ? 
                        TripResource::getUrl('view', ['record' => $record->trip]) : null
                    ),
                    
                TextColumn::make('pesanan.id')
                    ->label('Pesanan')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "Pesanan #{$state}")
                    ->badge()
                    ->color('warning'),
                    
                TextColumn::make('pesanan.pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                    
                // ✅ NEW: Alamat tujuan
                TextColumn::make('alamatPelanggan.alamat_lengkap')
                    ->label('Alamat Tujuan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alamatPelanggan?->alamat_lengkap_format)
                    ->placeholder('-')
                    ->toggleable(),
                    
                TextColumn::make('jenis_muatan')
                    ->label('Muatan')
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('rute.asal')
                    ->label('Asal')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                    
                TextColumn::make('rute.tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                    
                TextColumn::make('berat_dikirim')
                    ->label('Berat')
                    ->numeric(2)
                    ->suffix(' Kg')
                    ->sortable(),
                    
                TextColumn::make('tanggal_kirim')
                    ->label('Tgl Kirim')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                    
                TextColumn::make('tanggal_terima')
                    ->label('Tgl Terima')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                    
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
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Trip #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Pesanan #{$record->id} - {$record->pelanggan->nama}")
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