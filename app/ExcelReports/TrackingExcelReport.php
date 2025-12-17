<?php

namespace App\ExcelReports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrackingExcelReport implements WithMultipleSheets
{
    protected $suratJalan;
    protected $outstandingPesanan;
    
    public function __construct($suratJalan, $outstandingPesanan)
    {
        $this->suratJalan = $suratJalan;
        $this->outstandingPesanan = $outstandingPesanan;
    }
    
    public function sheets(): array
    {
        return [
            new OutstandingPesananSheet($this->outstandingPesanan),
            new DetailSuratJalanSheet($this->suratJalan),
        ];
    }
}

class OutstandingPesananSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = collect($data);
    }
    
    public function collection()
    {
        return $this->data;
    }
    
    public function headings(): array
    {
        return [
            'No',
            'ID Pesanan',
            'Tanggal',
            'Pelanggan',
            'Item',
            'Total Berat (Kg)',
            'SJ Dibuat (Kg)',
            'Terkirim (Kg)',
            'Sisa (Kg)',
            'Progress (%)',
            'Jumlah SJ',
            'Status',
        ];
    }
    
    public function map($item): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $item['id'],
            \Carbon\Carbon::parse($item['tanggal_pesanan'])->format('d/m/Y'),
            $item['pelanggan'],
            $item['item'],
            number_format($item['total_berat'], 2, ',', '.'),
            number_format($item['berat_sj_dibuat'], 2, ',', '.'),
            number_format($item['berat_terkirim'], 2, ',', '.'),
            number_format($item['sisa_berat'], 2, ',', '.'),
            $item['persen_terkirim'],
            $item['jumlah_sj'],
            ucfirst(str_replace('_', ' ', $item['status'])),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2c2c2c']
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
        return 'Outstanding Pesanan';
    }
}

class DetailSuratJalanSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'ID SJ',
            'ID Pesanan',
            'Tanggal Pesanan',
            'Pelanggan',
            'Tujuan',
            'Item',
            'Berat (Kg)',
            'Trip',
            'Sopir',
            'Kendaraan',
            'Tgl Kirim',
            'Tgl Terima',
            'Status',
        ];
    }
    
    public function map($sj): array
    {
        static $no = 0;
        $no++;
        
        $tujuan = $sj->alamatPelanggan 
            ? $sj->alamatPelanggan->label . ($sj->alamatPelanggan->kota ? ' - ' . $sj->alamatPelanggan->kota : '')
            : '-';
        
        return [
            $no,
            $sj->id,
            $sj->pesanan_id,
            $sj->pesanan->tanggal_pesanan->format('d/m/Y'),
            $sj->pesanan->pelanggan->nama,
            $tujuan,
            $sj->pesanan->rute->item->nama,
            number_format($sj->berat_dikirim, 2, ',', '.'),
            $sj->trip_id ? 'TRIP-' . $sj->trip_id : 'Belum assign',
            $sj->trip?->sopir?->nama ?? 'Belum ada trip',
            $sj->trip?->kendaraan?->nopol ?? 'Belum ada trip',
            $sj->tanggal_kirim ? $sj->tanggal_kirim->format('d/m/Y') : 'Belum dikirim',
            $sj->tanggal_terima ? $sj->tanggal_terima->format('d/m/Y') : 'Belum diterima',
            match($sj->status) {
                'draft' => 'Draft',
                'dikirim' => 'Dikirim',
                'diterima' => 'Diterima',
                'batal' => 'Batal',
                default => $sj->status,
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
                    'startColor' => ['rgb' => '2c2c2c']
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
        return 'Detail Surat Jalan';
    }
}