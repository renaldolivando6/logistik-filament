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

class UangSanguExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'Uang Sangu',
            'Total Biaya',
            'Dikembalikan',
            'Selisih',
            'Status',
            'Hari Outstanding',
            'Tgl Pengembalian',
        ];
    }
    
    public function map($trip): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            'TRIP-' . str_pad($trip->id, 5, '0', STR_PAD_LEFT),
            $trip->tanggal_trip->format('d/m/Y'),
            $trip->sopir->nama,
            $trip->kendaraan->nopol,
            'Rp ' . number_format($trip->uang_sangu, 0, ',', '.'),
            'Rp ' . number_format($trip->total_expenses, 0, ',', '.'),
            'Rp ' . number_format($trip->uang_kembali, 0, ',', '.'),
            'Rp ' . number_format($trip->outstanding, 0, ',', '.'),
            match($trip->status_sangu) {
                'belum_selesai' => 'Belum Selesai',
                'selesai' => 'Selesai',
                default => $trip->status_sangu,
            },
            $trip->days_outstanding . ' hari',
            $trip->tanggal_pengembalian ? $trip->tanggal_pengembalian->format('d/m/Y') : '-',
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
        return 'Laporan Uang Sangu';
    }
}