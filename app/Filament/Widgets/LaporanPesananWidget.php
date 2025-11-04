<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Sopir;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LaporanPesananWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static bool $isDiscovered = false;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = '📊 Laporan Pesanan Logistik';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pesanan::query()
                    ->with(['pelanggan', 'sopir', 'kendaraan', 'rute'])
            )
            ->columns([
                TextColumn::make('nomor_pesanan')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                    
                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                    
                TextColumn::make('pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->limit(30),
                    
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->icon('heroicon-o-user-circle')
                    ->limit(25),
                    
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->icon('heroicon-o-truck')
                    ->badge()
                    ->color('gray'),
                    
                TextColumn::make('jenis_muatan')
                    ->label('Muatan')
                    ->icon('heroicon-o-cube')
                    ->limit(20),
                    
                TextColumn::make('tonase')
                    ->label('Tonase')
                    ->suffix(' Ton')
                    ->alignEnd()
                    ->sortable(),
                    
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->alignEnd()
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                    
                TextColumn::make('uang_sangu')
                    ->label('Uang Sangu')
                    ->money('IDR')
                    ->alignEnd()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                    
                TextColumn::make('sisa_tagihan')
                    ->label('Sisa Tagihan')
                    ->money('IDR')
                    ->alignEnd()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total'),
                    ]),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dalam_perjalanan' => 'warning',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'dalam_perjalanan' => 'heroicon-o-truck',
                        'selesai' => 'heroicon-o-check-circle',
                        'batal' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()->startOfMonth()),
                            
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => 
                                    $query->whereDate('tanggal_pesanan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => 
                                    $query->whereDate('tanggal_pesanan', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y');
                        }
                        
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                        }
                        
                        return $indicators;
                    }),
                    
                SelectFilter::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->options(Pelanggan::where('aktif', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('sopir_id')
                    ->label('Sopir')
                    ->options(Sopir::where('aktif', true)->pluck('nama', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'dalam_perjalanan' => 'Dalam Perjalanan',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ])
                    ->multiple(),
            ])
            ->defaultSort('tanggal_pesanan', 'desc')
            ->paginated([10, 25, 50, 100])
            ->striped();
    }
}