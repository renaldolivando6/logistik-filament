<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Uang Sangu</title>
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
            border-bottom: 2px solid #16a34a;
        }
        
        .header h1 {
            font-size: 18px;
            color: #16a34a;
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
            background: #f0fdf4;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 2px;
            border-left: 3px solid #16a34a;
        }
        
        .filter-info h3 {
            font-size: 9px;
            color: #16a34a;
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
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            vertical-align: middle;
        }
        
        .summary-box .label {
            font-size: 7px;
            color: #166534;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-box .value {
            font-size: 13px;
            font-weight: 700;
            color: #16a34a;
        }
        
        .summary-box.warning .value {
            color: #f59e0b;
        }
        
        .summary-box.danger .value {
            color: #dc2626;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #bbf7d0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }
        
        table thead {
            background: #16a34a;
            color: white;
        }
        
        table thead th {
            padding: 6px 4px;
            text-align: left;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #16a34a;
        }
        
        table tbody td {
            padding: 5px 4px;
            border: 1px solid #e5e5e5;
            font-size: 8px;
            color: #2c2c2c;
        }
        
        table tbody tr:nth-child(even) {
            background: #f0fdf4;
        }
        
        table tfoot tr {
            background: #dcfce7;
            font-weight: 700;
        }
        
        table tfoot td {
            padding: 8px 4px;
            border: 1px solid #bbf7d0;
            font-size: 9px;
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
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }
        
        .badge-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }
        
        .text-blue {
            color: #2563eb;
            font-weight: 700;
        }
        
        .text-red {
            color: #dc2626;
            font-weight: 700;
        }
        
        .text-orange {
            color: #f59e0b;
            font-weight: 700;
        }
        
        .text-green {
            color: #16a34a;
            font-weight: 700;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #bbf7d0;
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
        <h1>Laporan Uang Sangu</h1>
        <div class="subtitle">Monitoring Pengembalian Uang Sangu Sopir</div>
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
            @if($filters['status_sangu'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Status:</strong> {{ $filters['status_sangu'] }}
            @endif
            @if($filters['sopir'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Sopir:</strong> {{ $filters['sopir'] }}
            @endif
            @if($filters['kendaraan'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Kendaraan:</strong> {{ $filters['kendaraan'] }}
            @endif
            <br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
    
    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Sangu Diberikan</div>
            <div class="value">Rp {{ number_format($summary['total_sangu_diberikan'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box danger">
            <div class="label">Total Biaya</div>
            <div class="value">Rp {{ number_format($summary['total_biaya_operasional'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box warning">
            <div class="label">Harus Dikembalikan</div>
            <div class="value">Rp {{ number_format($summary['total_harus_kembali'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Sudah Dikembalikan</div>
            <div class="value">Rp {{ number_format($summary['total_sudah_kembali'], 0, ',', '.') }}</div>
        </div>
    </div>
    
    <div class="section-title">Detail Uang Sangu Per Trip</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 6%;">Trip</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 13%;">Sopir</th>
                <th style="width: 10%;">Kendaraan</th>
                <th style="width: 11%;" class="text-right">Sangu</th>
                <th style="width: 11%;" class="text-right">Biaya</th>
                <th style="width: 11%;" class="text-right">Harus Kembali</th>
                <th style="width: 11%;" class="text-right">Sudah Kembali</th>
                <th style="width: 7%;" class="text-right">Selisih</th>
                <th style="width: 9%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $trip)
                @php
                    $harusKembali = $trip->uang_sangu - ($trip->biaya_operasional_sum_jumlah ?? 0);
                    $selisih = $trip->uang_kembali - $harusKembali;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>TRIP-{{ $trip->id }}</strong></td>
                    <td>{{ $trip->tanggal_trip->format('d/m/Y') }}</td>
                    <td>{{ $trip->sopir->nama }}</td>
                    <td>{{ $trip->kendaraan->nopol }}</td>
                    <td class="text-right text-blue">Rp {{ number_format($trip->uang_sangu, 0, ',', '.') }}</td>
                    <td class="text-right text-red">Rp {{ number_format($trip->biaya_operasional_sum_jumlah ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right text-orange">Rp {{ number_format($harusKembali, 0, ',', '.') }}</td>
                    <td class="text-right text-green">Rp {{ number_format($trip->uang_kembali, 0, ',', '.') }}</td>
                    <td class="text-right {{ $selisih > 0 ? 'text-green' : ($selisih < 0 ? 'text-red' : '') }}">
                        @if($selisih == 0)
                            -
                        @else
                            {{ $selisih > 0 ? '+' : '' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $trip->status_sangu === 'selesai' ? 'success' : 'warning' }}">
                            {{ $trip->status_sangu === 'selesai' ? 'Selesai' : 'Belum' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px; color: #666666; font-style: italic;">
                        Tidak ada data uang sangu
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td>
                <td class="text-right text-blue"><strong>Rp {{ number_format($summary['total_sangu_diberikan'], 0, ',', '.') }}</strong></td>
                <td class="text-right text-red"><strong>Rp {{ number_format($summary['total_biaya_operasional'], 0, ',', '.') }}</strong></td>
                <td class="text-right text-orange"><strong>Rp {{ number_format($summary['total_harus_kembali'], 0, ',', '.') }}</strong></td>
                <td class="text-right text-green"><strong>Rp {{ number_format($summary['total_sudah_kembali'], 0, ',', '.') }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
    
    <div class="footer">
        <p><strong>Laporan Sistem Logistik</strong></p>
        <p>Dokumen ini dibuat secara otomatis dan valid tanpa tanda tangan</p>
        <p>© {{ date('Y') }} - Confidential & Proprietary</p>
    </div>
</body>
</html>