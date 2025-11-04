<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class RecentOrdersWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;
    
    public function table(Table $table): Table
    {
        return $table
            ->heading('Pesanan Terbaru')
            ->description('5 pesanan terakhir')
            ->query(
                Pesanan::query()
                    ->with(['pelanggan', 'kendaraan', 'sopir'])
                    ->orderBy('tanggal_pesanan', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pesanan')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->icon('heroicon-m-user')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->icon('heroicon-m-truck')
                    ->badge()
                    ->color('warning'),
                    
                Tables\Columns\TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->icon('heroicon-m-user-circle')
                    ->limit(15),
                    
                Tables\Columns\TextColumn::make('total_tagihan')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('success'),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'proses',
                        'success' => 'selesai',
                        'danger' => 'batal',
                    ]),
            ])
            ->paginated(false);
    }
}