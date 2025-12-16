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

class RuteExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'Rute',
            'Asal',
            'Tujuan',
            'Jenis Muatan',
            'Total Trip',
            'Total Berat (Ton)',
            'Rata-rata Berat/Trip',
            'Total Revenue',
            'Harga/Kg',
        ];
    }
    
    public function map($rute): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            "{$rute->asal} → {$rute->tujuan}",
            $rute->asal,
            $rute->tujuan,
            $rute->item_nama,
            number_format($rute->total_trips, 0, ',', '.'),
            number_format($rute->total_weight, 2, ',', '.'),
            number_format($rute->avg_weight_per_trip, 2, ',', '.'),
            'Rp ' . number_format($rute->total_revenue, 0, ',', '.'),
            'Rp ' . number_format($rute->avg_price_per_kg, 0, ',', '.'),
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
        return 'Laporan Rute';
    }
}