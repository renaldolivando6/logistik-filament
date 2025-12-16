<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\ExcelReports\RuteExcelReport;
use BackedEnum;

class LaporanRute extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-map';
    
    protected static ?int $navigationSort = 4;
    
    protected string $view = 'filament.pages.reports.laporan-rute';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Rute';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Rute';
    }
    
    public function getSummaryData(): array
    {
        $routes = $this->getRouteAnalysis();
        
        $mostProfitable = $routes->sortByDesc('total_revenue')->first();
        $mostFrequent = $routes->sortByDesc('total_trips')->first();
        $highestVolume = $routes->sortByDesc('total_weight')->first();
        
        return [
            'total_routes' => $routes->count(),
            'most_profitable' => $mostProfitable ? "{$mostProfitable->asal} → {$mostProfitable->tujuan}" : '-',
            'most_frequent' => $mostFrequent ? "{$mostFrequent->asal} → {$mostFrequent->tujuan}" : '-',
            'highest_volume' => $highestVolume ? "{$highestVolume->asal} → {$highestVolume->tujuan}" : '-',
        ];
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                
                TextColumn::make('route')
                    ->label('Rute')
                    ->formatStateUsing(fn ($record) => 
                        "{$record->asal} → {$record->tujuan}"
                    )
                    ->searchable(['asal', 'tujuan'])
                    ->sortable(),
                
                TextColumn::make('item_nama')
                    ->label('Jenis Muatan')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('total_trips')
                    ->label('Total Trip')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                
                TextColumn::make('total_weight')
                    ->label('Total Berat')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->alignEnd()
                    ->sortable(),
                
                TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                    ]),
                
                TextColumn::make('avg_price_per_kg')
                    ->label('Harga/Kg')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable(),
                
                TextColumn::make('avg_weight_per_trip')
                    ->label('Rata-rata Berat/Trip')
                    ->numeric(2)
                    ->suffix(' Ton')
                    ->alignEnd(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], 
                                fn (Builder $query, $date) => $query->havingRaw('MIN(tanggal_pesanan) >= ?', [$date])
                            )
                            ->when($data['sampai'], 
                                fn (Builder $query, $date) => $query->havingRaw('MAX(tanggal_pesanan) <= ?', [$date])
                            );
                    }),
                
                \Filament\Tables\Filters\SelectFilter::make('item_id')
                    ->label('Jenis Muatan')
                    ->options(function () {
                        return \App\Models\Item::pluck('nama', 'id');
                    }),
            ])
            ->defaultSort('total_revenue', 'desc');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $data = $this->getRouteAnalysis();
                    return Excel::download(
                        new RuteExcelReport($data), 
                        'laporan-rute-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
            
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger'),
        ];
    }
    
    private function getBaseQuery()
    {
        return Pesanan::query()
            ->join('rute', 'pesanan.rute_id', '=', 'rute.id')
            ->join('item', 'rute.item_id', '=', 'item.id')
            ->select([
                DB::raw('CONCAT(rute.id, "-", item.id) as id'), // ✅ ADD PRIMARY KEY
                'rute.id as rute_id',
                'rute.asal',
                'rute.tujuan',
                'item.nama as item_nama',
                DB::raw('COUNT(pesanan.id) as total_trips'),
                DB::raw('SUM(pesanan.berat) as total_weight'),
                DB::raw('SUM(pesanan.total_tagihan) as total_revenue'),
                DB::raw('AVG(pesanan.harga_per_kg) as avg_price_per_kg'),
                DB::raw('AVG(pesanan.berat) as avg_weight_per_trip'),
                DB::raw('MIN(pesanan.tanggal_pesanan) as first_date'),
                DB::raw('MAX(pesanan.tanggal_pesanan) as last_date'),
            ])
            ->where('pesanan.status', '!=', 'batal')
            ->groupBy('rute.id', 'rute.asal', 'rute.tujuan', 'item.id', 'item.nama');
    }
    
    private function getRouteAnalysis()
    {
        return $this->getBaseQuery()->get();
    }
}