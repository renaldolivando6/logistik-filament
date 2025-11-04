<?php

namespace App\Filament\Widgets;

use App\Models\BiayaOperasional;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;
use Livewire\Attributes\On;

class DetailBiayaTable extends BaseWidget
{
    protected static bool $isDiscovered = false;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 5;
    
    public $filters = [];
    
    #[On('biayaFiltersUpdated')]
    public function updateFilters($filters): void
    {
        $this->filters = $filters;
    }
    
    public function table(Table $table): Table
    {
        $start = $this->filters['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->filters['end_date'] ?? now()->format('Y-m-d');
        $kendaraanId = $this->filters['kendaraan_id'] ?? null;
        $kategoriBiayaId = $this->filters['kategori_biaya_id'] ?? null;
        
        $query = BiayaOperasional::query()
            ->with(['kendaraan', 'kategoriBiaya', 'pesanan'])
            ->whereBetween('tanggal_biaya', [$start, $end])
            ->orderBy('tanggal_biaya', 'desc');
        
        if ($kendaraanId) {
            $query->where('kendaraan_id', $kendaraanId);
        }
        
        if ($kategoriBiayaId) {
            $query->where('kategori_biaya_id', $kategoriBiayaId);
        }
        
        return $table
            ->heading('Detail Transaksi Biaya Operasional')
            ->description('Daftar lengkap biaya operasional berdasarkan filter')
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_biaya')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->badge()
                    ->color('warning'),
                    
                Tables\Columns\TextColumn::make('kategoriBiaya.nama')
                    ->label('Kategori')
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
                    
                Tables\Columns\TextColumn::make('pesanan.nomor_pesanan')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->pesanan?->nomor_pesanan),
                    
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('danger')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                            ->label('Total'),
                    ]),
                    
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->wrap(),
            ])
            ->defaultSort('tanggal_biaya', 'desc')
            ->filters([
                // Bisa tambah filter tambahan di sini kalau perlu
            ]);
    }
}