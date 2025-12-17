<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Biaya Operasional</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            color: #1a1a1a;
            padding: 15px;
            background: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #dc2626;
        }
        
        .header h1 {
            font-size: 18px;
            color: #dc2626;
            margin-bottom: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .header .subtitle {
            font-size: 10px;
            color: #666666;
            font-weight: 400;
        }
        
        .filter-info {
            background: #fef2f2;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 2px;
            border-left: 3px solid #dc2626;
        }
        
        .filter-info h3 {
            font-size: 9px;
            color: #dc2626;
            margin-bottom: 5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-info p {
            font-size: 8px;
            color: #4a4a4a;
            line-height: 1.5;
        }
        
        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-spacing: 5px 0;
        }
        
        .summary-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #fecaca;
            background: #fef2f2;
            vertical-align: middle;
        }
        
        .summary-box .label {
            font-size: 7px;
            color: #991b1b;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-box .value {
            font-size: 13px;
            font-weight: 700;
            color: #dc2626;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #fecaca;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }
        
        table thead {
            background: #dc2626;
            color: white;
        }
        
        table thead th {
            padding: 6px 4px;
            text-align: left;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #dc2626;
        }
        
        table tbody td {
            padding: 5px 4px;
            border: 1px solid #e5e5e5;
            font-size: 8px;
            color: #2c2c2c;
        }
        
        table tbody tr:nth-child(even) {
            background: #fef2f2;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-trip {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }
        
        .badge-non-trip {
            background: #e5e5e5;
            color: #4a4a4a;
            border: 1px solid #d4d4d4;
        }
        
        .text-red {
            color: #dc2626;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #fecaca;
            text-align: center;
            font-size: 7px;
            color: #666666;
            line-height: 1.6;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Biaya Operasional</h1>
        <div class="subtitle">Detail Pengeluaran Operasional</div>
    </div>
    
    <div class="filter-info">
        <h3>Parameter Laporan</h3>
        <p>
            <strong>Periode:</strong> 
            @if($filters['dari_tanggal'] && $filters['sampai_tanggal'])
                {{ \Carbon\Carbon::parse($filters['dari_tanggal'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['sampai_tanggal'])->format('d/m/Y') }}
            @elseif($filters['dari_tanggal'])
                Dari {{ \Carbon\Carbon::parse($filters['dari_tanggal'])->format('d/m/Y') }}
            @elseif($filters['sampai_tanggal'])
                Sampai {{ \Carbon\Carbon::parse($filters['sampai_tanggal'])->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
            @if($filters['tipe_biaya'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Tipe:</strong> {{ $filters['tipe_biaya'] === 'trip' ? 'Biaya Trip' : 'Biaya Non-Trip' }}
            @endif
            @if($filters['kendaraan'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Kendaraan:</strong> {{ $filters['kendaraan'] }}
            @endif
            @if($filters['sopir'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Sopir:</strong> {{ $filters['sopir'] }}
            @endif
            @if($filters['kategori'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Kategori:</strong> {{ $filters['kategori'] }}
            @endif
            <br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
    
    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total Biaya</div>
            <div class="value">Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Biaya Trip</div>
            <div class="value">Rp {{ number_format($summary['biaya_trip'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Biaya Non-Trip</div>
            <div class="value">Rp {{ number_format($summary['biaya_non_trip'], 0, ',', '.') }}</div>
        </div>
    </div>
    
    <div class="section-title">Detail Biaya Operasional</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 7%;" class="text-center">Tipe</th>
                <th style="width: 6%;">Trip</th>
                <th style="width: 12%;">Sopir</th>
                <th style="width: 10%;">Kendaraan</th>
                <th style="width: 10%;">Kategori</th>
                <th style="width: 12%;" class="text-right">Jumlah</th>
                <th style="width: 32%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $biaya)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $biaya->tanggal_biaya->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $biaya->trip_id ? 'trip' : 'non-trip' }}">
                            {{ $biaya->trip_id ? 'Trip' : 'Non-Trip' }}
                        </span>
                    </td>
                    <td>{{ $biaya->trip_id ? "TRIP-{$biaya->trip_id}" : '-' }}</td>
                    <td>{{ $biaya->trip?->sopir?->nama ?? '-' }}</td>
                    <td>{{ $biaya->kendaraan?->nopol ?? $biaya->trip?->kendaraan?->nopol ?? '-' }}</td>
                    <td><strong>{{ $biaya->kategoribiaya->nama }}</strong></td>
                    <td class="text-right text-red">Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $biaya->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #666666; font-style: italic;">
                        Tidak ada data biaya operasional
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #fef2f2; font-weight: 700;">
                <td colspan="7" class="text-right" style="padding: 8px; border: 1px solid #fecaca;">
                    <strong>TOTAL BIAYA:</strong>
                </td>
                <td class="text-right text-red" style="padding: 8px; border: 1px solid #fecaca; font-size: 10px;">
                    Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}
                </td>
                <td style="border: 1px solid #fecaca;"></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p><strong>Laporan Sistem Logistik</strong></p>
        <p>Dokumen ini dibuat secara otomatis dan valid tanpa tanda tangan</p>
        <p>© {{ date('Y') }} - Confidential & Proprietary</p>
    </div>
</body>
</html>