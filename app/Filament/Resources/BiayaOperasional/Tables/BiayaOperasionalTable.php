<?php
namespace App\Filament\Resources\BiayaOperasional\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BiayaOperasionalTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_biaya')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('pesanan.nomor_pesanan')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->pesanan?->nomor_pesanan),
                    
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->description(fn ($record) => $record->kendaraan?->jenis),
                    
                TextColumn::make('kategoriBiaya.nama')
                    ->label('Kategori Biaya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SOLAR' => 'danger',
                        'TOL' => 'warning',
                        'SPAREPART' => 'info',
                        'SERVIS' => 'success',
                        'BAN' => 'primary',
                        'ACCU' => 'gray',
                        default => 'secondary',
                    }),
                    
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger')
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                            ->label('Total'),
                    ]),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->wrap()
                    ->toggleable(),
                    
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
                // Filter by Kendaraan
                SelectFilter::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kendaraan'),
                    
                // Filter by Kategori Biaya
                SelectFilter::make('kategori_biaya_id')
                    ->label('Kategori Biaya')
                    ->relationship('kategoriBiaya', 'nama')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kategori'),
                    
                // Filter by Pesanan
                SelectFilter::make('pesanan_id')
                    ->label('Pesanan')
                    ->relationship('pesanan', 'nomor_pesanan')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Pesanan'),
                    
                // Filter by Tanggal
                Filter::make('tanggal_biaya')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_biaya', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_biaya', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['dari'] ?? null) {
                            $indicators['dari'] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d/m/Y');
                        }
                        
                        if ($data['sampai'] ?? null) {
                            $indicators['sampai'] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d/m/Y');
                        }
                        
                        return $indicators;
                    }),
                    
                // Filter by Range Jumlah
                Filter::make('jumlah')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min')
                            ->label('Min. Jumlah')
                            ->numeric()
                            ->prefix('Rp'),
                        \Filament\Forms\Components\TextInput::make('max')
                            ->label('Max. Jumlah')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min'],
                                fn (Builder $query, $amount): Builder => $query->where('jumlah', '>=', $amount),
                            )
                            ->when(
                                $data['max'],
                                fn (Builder $query, $amount): Builder => $query->where('jumlah', '<=', $amount),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['min'] ?? null) {
                            $indicators['min'] = 'Min: Rp ' . number_format($data['min'], 0, ',', '.');
                        }
                        
                        if ($data['max'] ?? null) {
                            $indicators['max'] = 'Max: Rp ' . number_format($data['max'], 0, ',', '.');
                        }
                        
                        return $indicators;
                    }),
                    
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
            ->defaultSort('tanggal_biaya', 'desc');
    }
}