<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;
    
    public function table(Table $table): Table
    {
        return $table
            ->heading('Pesanan Terbaru')
            ->description('10 pesanan terakhir')
            ->query(
                Pesanan::query()
                    ->with(['pelanggan', 'kendaraan', 'sopir', 'rute.item'])
                    ->orderBy('id', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->weight('bold')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('rute.item.nama')
                    ->label('Muatan')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('rute_display')
                    ->label('Rute')
                    ->getStateUsing(fn($record) => "{$record->rute->asal} → {$record->rute->tujuan}")
                    ->limit(25),
                    
                Tables\Columns\TextColumn::make('berat')
                    ->label('Berat')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.') . ' KG')
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->alignEnd(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam_perjalanan' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'dalam_perjalanan' => 'Dalam Perjalanan',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => $state,
                    }),
            ])
            ->paginated(false);
    }
}