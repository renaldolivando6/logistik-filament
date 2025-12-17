<?php

namespace App\ExcelReports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UangSanguExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'Trip ID',
            'Tanggal',
            'Sopir',
            'Kendaraan',
            'Sangu Diberikan',
            'Total Biaya',
            'Sisa (Harus Kembali)',
            'Sudah Dikembalikan',
            'Selisih',
            'Status',
            'Tgl Pengembalian',
        ];
    }
    
    public function map($trip): array
    {
        static $no = 0;
        $no++;
        
        $harusKembali = $trip->uang_sangu - ($trip->biaya_operasional_sum_jumlah ?? 0);
        $selisih = $trip->uang_kembali - $harusKembali;
        
        return [
            $no,
            "TRIP-{$trip->id}",
            $trip->tanggal_trip->format('d/m/Y'),
            $trip->sopir->nama,
            $trip->kendaraan->nopol,
            number_format($trip->uang_sangu, 0, ',', '.'),
            number_format($trip->biaya_operasional_sum_jumlah ?? 0, 0, ',', '.'),
            number_format($harusKembali, 0, ',', '.'),
            number_format($trip->uang_kembali, 0, ',', '.'),
            $selisih == 0 ? '-' : ($selisih > 0 ? '+' : '') . number_format($selisih, 0, ',', '.'),
            match($trip->status_sangu) {
                'belum_selesai' => 'Belum Dikembalikan',
                'selesai' => 'Sudah Dikembalikan',
                default => $trip->status_sangu,
            },
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
                    'startColor' => ['rgb' => '16A34A']
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