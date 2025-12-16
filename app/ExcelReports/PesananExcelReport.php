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

class PesananExcelReport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'Pelanggan',
            'Muatan',
            'Rute',
            'Berat Total (Ton)',
            'Terkirim (Ton)',
            'Sisa (Ton)',
            'Harga/Kg',
            'Total Tagihan',
            'Status',
        ];
    }
    
    public function map($pesanan): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $pesanan->tanggal_pesanan->format('d/m/Y'),
            $pesanan->pelanggan->nama,
            $pesanan->rute->item->nama ?? '-',
            "{$pesanan->rute->asal} → {$pesanan->rute->tujuan}",
            number_format($pesanan->berat, 2, ',', '.'),
            number_format($pesanan->total_berat_dikirim, 2, ',', '.'),
            number_format($pesanan->sisa_berat, 2, ',', '.'),
            number_format($pesanan->harga_per_kg, 0, ',', '.'),
            number_format($pesanan->total_tagihan, 0, ',', '.'),
            match($pesanan->status) {
                'draft' => 'Draft',
                'dalam_perjalanan' => 'Dalam Perjalanan',
                'selesai' => 'Selesai',
                'batal' => 'Batal',
                default => $pesanan->status,
            },
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
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
        return 'Laporan Pesanan';
    }
}