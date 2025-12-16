<?php

namespace App\ExcelReports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProfitabilitasExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function collection()
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return [
            'No',
            'ID Trip',
            'Tanggal',
            'Sopir',
            'Kendaraan',
            'Jumlah SJ',
            'Total Berat',
            'Revenue',
            'Biaya Operasional',
            'Profit',
            'Margin (%)',
            'Status',
        ];
    }
    
    public function map($trip): array
    {
        static $no = 0;
        $no++;
        
        $revenue = $trip->total_revenue ?? 0;
        $cost = $trip->total_cost ?? 0;
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
        
        return [
            $no,
            'TRIP-' . str_pad($trip->id, 5, '0', STR_PAD_LEFT),
            $trip->tanggal_trip->format('d/m/Y'),
            $trip->sopir->nama,
            $trip->kendaraan->nopol,
            $trip->suratJalan_count ?? 0,
            number_format($trip->total_berat ?? 0, 2, ',', '.') . ' Ton',
            'Rp ' . number_format($revenue, 0, ',', '.'),
            'Rp ' . number_format($cost, 0, ',', '.'),
            'Rp ' . number_format($profit, 0, ',', '.'),
            $margin . '%',
            match($trip->status) {
                'draft' => 'Draft',
                'berangkat' => 'Berangkat',
                'selesai' => 'Selesai',
                'batal' => 'Batal',
                default => $trip->status,
            },
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
    
    public function title(): string
    {
        return 'Laporan Profitabilitas';
    }
}