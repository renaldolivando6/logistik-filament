<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SuratJalan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;

class LaporanTracking extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.reports.laporan-tracking';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Tracking Pesanan & Trip';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Tracking Pesanan & Trip';
    }
    
    // Public properties untuk filter
    public ?string $dari_tanggal = null;
    public ?string $sampai_tanggal = null;
    public ?int $pelanggan_id = null;
    public ?int $pesanan_id = null;
    public ?int $trip_id = null;
    public array $status_sj = [];
    public bool $tampilkan_selesai = false; // ✅ NEW: Default OFF
    public bool $hasAppliedFilter = false;
    
    // Apply Filter Action
    public function applyFilters(): void
    {
        $this->hasAppliedFilter = true;
        $this->resetTable();
    }
    
    // Reset Filter Action
    public function resetFilters(): void
    {
        $this->dari_tanggal = null;
        $this->sampai_tanggal = null;
        $this->pelanggan_id = null;
        $this->pesanan_id = null;
        $this->trip_id = null;
        $this->status_sj = [];
        $this->tampilkan_selesai = false;
        $this->hasAppliedFilter = false;
        $this->resetTable();
    }
    
    // Get Filtered Query
    private function getFilteredQuery(): Builder
    {
        $query = SuratJalan::query()
            ->with([
                'pesanan.pelanggan',
                'pesanan.rute.item',
                'alamatPelanggan',
                'trip.sopir',
                'trip.kendaraan'
            ]);
        
        if ($this->dari_tanggal) {
            $query->whereHas('pesanan', function($q) {
                $q->whereDate('tanggal_pesanan', '>=', $this->dari_tanggal);
            });
        }
        
        if ($this->sampai_tanggal) {
            $query->whereHas('pesanan', function($q) {
                $q->whereDate('tanggal_pesanan', '<=', $this->sampai_tanggal);
            });
        }
        
        if ($this->pelanggan_id) {
            $query->whereHas('pesanan', function($q) {
                $q->where('pelanggan_id', $this->pelanggan_id);
            });
        }
        
        if ($this->pesanan_id) {
            $query->where('pesanan_id', $this->pesanan_id);
        }
        
        if ($this->trip_id) {
            $query->where('trip_id', $this->trip_id);
        }
        
        if (!empty($this->status_sj)) {
            $query->whereIn('status', $this->status_sj);
        }
        
        return $query;
    }
    
    // Summary data
    public function getSummaryData(): array
    {
        if (!$this->hasAppliedFilter) {
            return [
                'total_sj' => 0,
                'total_berat' => 0,
                'sj_draft' => 0,
                'sj_dikirim' => 0,
                'sj_diterima' => 0,
            ];
        }
        
        $query = $this->getFilteredQuery();
        
        return [
            'total_sj' => $query->count(),
            'total_berat' => $query->sum('berat_dikirim'),
            'sj_draft' => (clone $query)->where('status', 'draft')->count(),
            'sj_dikirim' => (clone $query)->where('status', 'dikirim')->count(),
            'sj_diterima' => (clone $query)->where('status', 'diterima')->count(),
        ];
    }
    
    // Get Outstanding Pesanan Summary
    public function getOutstandingPesanan(): array
    {
        if (!$this->hasAppliedFilter) {
            return [];
        }
        
        // Build base query for pesanan
        $query = \App\Models\Pesanan::query()->with(['pelanggan', 'rute.item', 'suratJalan']);
        
        // Apply same filters as surat jalan
        if ($this->dari_tanggal) {
            $query->whereDate('tanggal_pesanan', '>=', $this->dari_tanggal);
        }
        
        if ($this->sampai_tanggal) {
            $query->whereDate('tanggal_pesanan', '<=', $this->sampai_tanggal);
        }
        
        if ($this->pelanggan_id) {
            $query->where('pelanggan_id', $this->pelanggan_id);
        }
        
        if ($this->pesanan_id) {
            $query->where('id', $this->pesanan_id);
        }
        
        $pesananList = $query->get()->map(function ($pesanan) {
            $totalBerat = $pesanan->berat;
            
            // Hitung total berat SJ yang sudah dibuat (berapapun statusnya)
            $beratSJDibuat = $pesanan->suratJalan->sum('berat_dikirim');
            
            // Hitung berat yang BENAR-BENAR terkirim (status = dikirim atau diterima, punya tanggal_kirim)
            $beratTerkirim = $pesanan->suratJalan
                ->filter(function ($sj) {
                    return in_array($sj->status, ['dikirim', 'diterima']) && $sj->tanggal_kirim !== null;
                })
                ->sum('berat_dikirim');
            
            $sisaBerat = $totalBerat - $beratTerkirim;
            $persenTerkirim = $totalBerat > 0 ? round(($beratTerkirim / $totalBerat) * 100, 1) : 0;
            
            return [
                'id' => $pesanan->id,
                'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                'pelanggan' => $pesanan->pelanggan->nama,
                'item' => $pesanan->rute->item->nama,
                'total_berat' => $totalBerat,
                'berat_sj_dibuat' => $beratSJDibuat,
                'berat_terkirim' => $beratTerkirim,
                'sisa_berat' => $sisaBerat,
                'persen_terkirim' => $persenTerkirim,
                'jumlah_sj' => $pesanan->suratJalan->count(),
                'status' => $pesanan->status,
            ];
        });
        
        // ✅ Filter: Default hanya tampilkan yang outstanding (< 100%)
        if (!$this->tampilkan_selesai) {
            $pesananList = $pesananList->filter(fn($item) => $item['persen_terkirim'] < 100);
        }
        
        return $pesananList->values()->toArray();
    }
    
    // Get options for selects
    public function getPelangganOptions(): array
    {
        return \App\Models\Pelanggan::pluck('nama', 'id')->toArray();
    }
    
    public function getPesananOptions(): array
    {
        return \App\Models\Pesanan::query()
            ->with('pelanggan')
            ->get()
            ->mapWithKeys(function ($pesanan) {
                return [$pesanan->id => 'ID-' . $pesanan->id . ' - ' . $pesanan->pelanggan->nama . ' (' . $pesanan->tanggal_pesanan->format('d/m/Y') . ')'];
            })
            ->toArray();
    }
    
    public function getTripOptions(): array
    {
        return \App\Models\Trip::query()
            ->get()
            ->mapWithKeys(function ($trip) {
                return [$trip->id => 'ID-' . $trip->id . ' (' . $trip->tanggal_trip->format('d/m/Y') . ')'];
            })
            ->toArray();
    }
    
    public function getStatusSJOptions(): array
    {
        return [
            'draft' => 'Draft',
            'dikirim' => 'Dikirim',
            'diterima' => 'Diterima',
            'batal' => 'Batal',
        ];
    }
    
    // Table configuration
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                
                TextColumn::make('id')
                    ->label('ID SJ')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                
                TextColumn::make('pesanan_id')
                    ->label('ID Pesanan')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('pesanan.tanggal_pesanan')
                    ->label('Tgl Pesanan')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('pesanan.pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                
                TextColumn::make('alamatPelanggan.label')
                    ->label('Tujuan')
                    ->formatStateUsing(function ($record) {
                        if (!$record->alamatPelanggan) return '-';
                        $label = $record->alamatPelanggan->label;
                        $kota = $record->alamatPelanggan->kota;
                        return $label . ($kota ? ' - ' . $kota : '');
                    })
                    ->limit(25)
                    ->tooltip(function ($record) {
                        if (!$record->alamatPelanggan) return null;
                        return $record->alamatPelanggan->alamat_lengkap;
                    }),
                
                TextColumn::make('pesanan.rute.item.nama')
                    ->label('Item')
                    ->badge()
                    ->color('gray'),
                
                TextColumn::make('berat_dikirim')
                    ->label('Berat')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.') . ' Kg')
                    ->alignEnd(),
                
                TextColumn::make('trip_id')
                    ->label('Trip')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'Belum assign';
                        return 'TRIP-' . $state;
                    })
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->sortable(),
                
                TextColumn::make('trip.sopir.nama')
                    ->label('Sopir')
                    ->placeholder('Belum ada trip')
                    ->searchable(),
                
                TextColumn::make('trip.kendaraan.nopol')
                    ->label('Kendaraan')
                    ->placeholder('Belum ada trip')
                    ->searchable(),
                
                TextColumn::make('tanggal_kirim')
                    ->label('Tgl Kirim')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'Belum dikirim')
                    ->color(fn ($state) => $state ? null : 'gray')
                    ->sortable(),
                
                TextColumn::make('tanggal_terima')
                    ->label('Tgl Terima')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : 'Belum diterima')
                    ->color(fn ($state) => $state ? null : 'gray')
                    ->sortable(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'dikirim' => 'warning',
                        'diterima' => 'success',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'dikirim' => 'Dikirim',
                        'diterima' => 'Diterima',
                        'batal' => 'Batal',
                        default => $state,
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
    
    // Export methods
    public function exportToExcel()
    {
        $suratJalan = $this->getFilteredQuery()->get();
        $outstandingPesanan = $this->getOutstandingPesanan();
            
        return Excel::download(
            new \App\ExcelReports\TrackingExcelReport($suratJalan, $outstandingPesanan), 
            'laporan-tracking-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
    
    public function exportToPdf()
    {
        $suratJalan = $this->getFilteredQuery()->get();
        $outstandingPesanan = $this->getOutstandingPesanan();
        $summary = $this->getSummaryData();
        
        $pdf = Pdf::loadView('reports.tracking-pdf', [
            'data' => [
                'surat_jalan' => $suratJalan,
                'outstanding' => $outstandingPesanan,
            ],
            'summary' => $summary,
            'filters' => [
                'dari_tanggal' => $this->dari_tanggal,
                'sampai_tanggal' => $this->sampai_tanggal,
                'pelanggan' => $this->pelanggan_id ? \App\Models\Pelanggan::find($this->pelanggan_id)?->nama : null,
            ],
        ])->setPaper('a4', 'landscape');
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-tracking-' . now()->format('Y-m-d') . '.pdf');
    }
}