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

class BiayaOperasionalExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'Tanggal',
            'Tipe',
            'Trip',
            'Sopir',
            'Kendaraan',
            'Kategori',
            'Jumlah',
            'Keterangan',
        ];
    }
    
    public function map($biaya): array
    {
        static $no = 0;
        $no++;
        
        $tipe = $biaya->trip_id ? 'Trip' : 'Non-Trip';
        $trip = $biaya->trip_id ? 'TRIP-' . str_pad($biaya->trip_id, 5, '0', STR_PAD_LEFT) : '-';
        $sopir = $biaya->trip?->sopir?->nama ?? '-';
        
        return [
            $no,
            $biaya->tanggal_biaya->format('d/m/Y'),
            $tipe,
            $trip,
            $sopir,
            $biaya->kendaraan?->nopol ?? '-',
            $biaya->kategoriBiaya->nama,
            'Rp ' . number_format($biaya->jumlah, 0, ',', '.'),
            $biaya->keterangan ?? '-',
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
        return 'Laporan Biaya Operasional';
    }
}