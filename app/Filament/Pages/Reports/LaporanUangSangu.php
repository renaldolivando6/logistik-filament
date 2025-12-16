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
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\ExcelReports\UangSanguExcelReport;
use BackedEnum;

class LaporanUangSangu extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?int $navigationSort = 5;
    
    protected string $view = 'filament.pages.reports.laporan-uang-sangu';
    
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Laporan Uang Sangu';
    }
    
    public function getTitle(): string
    {
        return 'Laporan Uang Sangu';
    }
    
    public function getSummaryData(): array
    {
        $trips = Trip::query()
            ->with(['sopir', 'kendaraan'])
            ->select([
                'trip.*',
                DB::raw('COALESCE((SELECT SUM(jumlah) FROM biaya_operasional WHERE trip_id = trip.id), 0) as total_expenses'),
                DB::raw('(uang_sangu - COALESCE((SELECT SUM(jumlah) FROM biaya_operasional WHERE trip_id = trip.id), 0) - uang_kembali) as outstanding'),
            ])
            ->where('status', '!=', 'batal')
            ->get();
        
        return [
            'total_sangu' => $trips->sum('uang_sangu'),
            'total_expenses' => $trips->sum('total_expenses'),
            'total_returned' => $trips->sum('uang_kembali'),
            'outstanding' => $trips->where('status_sangu', 'belum_selesai')->sum('outstanding'),
            'overdue_count' => $trips->where('status_sangu', 'belum_selesai')
                ->filter(function ($trip) {
                    return $trip->tanggal_trip && $trip->tanggal_trip->diffInDays(now()) > 7;
                })
                ->count(),
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
                
                TextColumn::make('id')
                    ->label('ID Trip')
                    ->formatStateUsing(fn ($state) => 'TRIP-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_trip')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('sopir.nama')
                    ->label('Sopir')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kendaraan.nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('uang_sangu')
                    ->label('Uang Sangu')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                    ]),
                
                TextColumn::make('total_expenses')
                    ->label('Total Biaya')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable()
                    ->color('danger')
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                    ]),
                
                TextColumn::make('uang_kembali')
                    ->label('Dikembalikan')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->sortable()
                    ->color('success')
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                    ]),
                
                TextColumn::make('outstanding')
                    ->label('Selisih')
                    ->money('IDR', locale: 'id')
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'warning' : ($state < 0 ? 'danger' : 'success'))
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', locale: 'id')
                    ]),
                
                TextColumn::make('status_sangu')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_selesai' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_selesai' => 'Belum Selesai',
                        'selesai' => 'Selesai',
                        default => $state,
                    }),
                
                TextColumn::make('days_outstanding')
                    ->label('Hari')
                    ->numeric()
                    ->suffix(' hari')
                    ->alignCenter()
                    ->color(fn ($state) => $state > 7 ? 'danger' : ($state > 3 ? 'warning' : 'gray')),
                
                TextColumn::make('tanggal_pengembalian')
                    ->label('Tgl Kembali')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),
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
                                fn (Builder $query, $date) => $query->whereDate('tanggal_trip', '>=', $date)
                            )
                            ->when($data['sampai'], 
                                fn (Builder $query, $date) => $query->whereDate('tanggal_trip', '<=', $date)
                            );
                    }),
                
                \Filament\Tables\Filters\SelectFilter::make('sopir_id')
                    ->label('Sopir')
                    ->relationship('sopir', 'nama')
                    ->searchable()
                    ->preload(),
                
                \Filament\Tables\Filters\SelectFilter::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol')
                    ->searchable()
                    ->preload(),
                
                \Filament\Tables\Filters\SelectFilter::make('status_sangu')
                    ->label('Status')
                    ->options([
                        'belum_selesai' => 'Belum Selesai',
                        'selesai' => 'Selesai',
                    ]),
                
                Filter::make('overdue')
                    ->label('Lewat Waktu (>7 hari)')
                    ->query(fn (Builder $query) => 
                        $query->where('status_sangu', 'belum_selesai')
                            ->whereRaw('DATEDIFF(COALESCE(tanggal_pengembalian, CURDATE()), tanggal_trip) > 7')
                    )
                    ->toggle(),
            ])
            ->defaultSort('tanggal_trip', 'desc');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $data = $this->getBaseQuery()->get();
                    return Excel::download(
                        new UangSanguExcelReport($data), 
                        'laporan-uang-sangu-' . now()->format('Y-m-d') . '.xlsx'
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
        return Trip::query()
            ->with(['sopir', 'kendaraan'])
            ->select([
                'trip.*',
                DB::raw('COALESCE((SELECT SUM(jumlah) FROM biaya_operasional WHERE trip_id = trip.id), 0) as total_expenses'),
                DB::raw('(uang_sangu - COALESCE((SELECT SUM(jumlah) FROM biaya_operasional WHERE trip_id = trip.id), 0) - uang_kembali) as outstanding'),
                DB::raw('DATEDIFF(COALESCE(tanggal_pengembalian, CURDATE()), tanggal_trip) as days_outstanding'),
            ])
            ->where('status', '!=', 'batal');
    }
}