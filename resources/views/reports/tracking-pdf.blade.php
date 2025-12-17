<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tracking Pesanan & Trip</title>
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
            border-bottom: 2px solid #2c2c2c;
        }
        
        .header h1 {
            font-size: 18px;
            color: #2c2c2c;
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
            background: #f8f8f8;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 2px;
            border-left: 3px solid #2c2c2c;
        }
        
        .filter-info h3 {
            font-size: 9px;
            color: #2c2c2c;
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
            width: 20%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e0e0e0;
            background: #fafafa;
            vertical-align: middle;
        }
        
        .summary-box .label {
            font-size: 7px;
            color: #666666;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-box .value {
            font-size: 13px;
            font-weight: 700;
            color: #2c2c2c;
        }
        
        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #2c2c2c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #d4d4d4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }
        
        table thead {
            background: #2c2c2c;
            color: white;
        }
        
        table thead th {
            padding: 6px 4px;
            text-align: left;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #2c2c2c;
        }
        
        table tbody td {
            padding: 5px 4px;
            border: 1px solid #e5e5e5;
            font-size: 8px;
            color: #2c2c2c;
        }
        
        table tbody tr:nth-child(even) {
            background: #fafafa;
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
        
        .badge-draft {
            background: #e5e5e5;
            color: #4a4a4a;
            border: 1px solid #d4d4d4;
        }
        
        .badge-dikirim {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }
        
        .badge-diterima {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }
        
        .badge-selesai {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
        }
        
        .badge-dalam-perjalanan {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }
        
        .text-red {
            color: #dc2626;
            font-weight: 700;
        }
        
        .text-green {
            color: #059669;
            font-weight: 700;
        }
        
        .text-blue {
            color: #2563eb;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #d4d4d4;
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
        <h1>Laporan Tracking Pesanan & Trip</h1>
        <div class="subtitle">Detail Surat Jalan & Outstanding Pesanan</div>
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
            @if($filters['pelanggan'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Pelanggan:</strong> {{ $filters['pelanggan'] }}
            @endif
            <br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
    
    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total SJ</div>
            <div class="value">{{ number_format($summary['total_sj']) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Berat</div>
            <div class="value">{{ number_format($summary['total_berat'], 0, ',', '.') }} Kg</div>
        </div>
        <div class="summary-box">
            <div class="label">Draft</div>
            <div class="value">{{ number_format($summary['sj_draft']) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Dikirim</div>
            <div class="value">{{ number_format($summary['sj_dikirim']) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Diterima</div>
            <div class="value">{{ number_format($summary['sj_diterima']) }}</div>
        </div>
    </div>
    
    <div class="section-title">Outstanding Pesanan</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 6%;">ID</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 18%;">Pelanggan</th>
                <th style="width: 10%;">Item</th>
                <th style="width: 9%;" class="text-right">Total</th>
                <th style="width: 9%;" class="text-right">SJ Dibuat</th>
                <th style="width: 9%;" class="text-right">Terkirim</th>
                <th style="width: 9%;" class="text-right">Sisa</th>
                <th style="width: 7%;" class="text-right">%</th>
                <th style="width: 5%;" class="text-center">SJ</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['outstanding'] as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item['id'] }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($item['tanggal_pesanan'])->format('d/m/Y') }}</td>
                    <td>{{ $item['pelanggan'] }}</td>
                    <td>{{ $item['item'] }}</td>
                    <td class="text-right">{{ number_format($item['total_berat'], 2, ',', '.') }}</td>
                    <td class="text-right text-blue">{{ number_format($item['berat_sj_dibuat'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['berat_terkirim'], 2, ',', '.') }}</td>
                    <td class="text-right {{ $item['sisa_berat'] > 0 ? 'text-red' : 'text-green' }}">
                        {{ number_format($item['sisa_berat'], 2, ',', '.') }}
                    </td>
                    <td class="text-right"><strong>{{ $item['persen_terkirim'] }}%</strong></td>
                    <td class="text-center">{{ $item['jumlah_sj'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ str_replace('_', '-', $item['status']) }}">
                            {{ ucfirst(str_replace('_', ' ', $item['status'])) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px; color: #666666; font-style: italic;">
                        Tidak ada data pesanan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="page-break"></div>
    
    <div class="section-title">Detail Surat Jalan</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 5%;">ID SJ</th>
                <th style="width: 6%;">ID PO</th>
                <th style="width: 7%;">Tgl PO</th>
                <th style="width: 15%;">Pelanggan</th>
                <th style="width: 8%;">Item</th>
                <th style="width: 7%;" class="text-right">Berat</th>
                <th style="width: 7%;">Trip</th>
                <th style="width: 10%;">Sopir</th>
                <th style="width: 7%;">Nopol</th>
                <th style="width: 7%;">Tgl Kirim</th>
                <th style="width: 7%;">Tgl Terima</th>
                <th style="width: 8%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['surat_jalan'] as $index => $sj)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $sj->id }}</strong></td>
                    <td>{{ $sj->pesanan_id }}</td>
                    <td>{{ $sj->pesanan->tanggal_pesanan->format('d/m/Y') }}</td>
                    <td>{{ $sj->pesanan->pelanggan->nama }}</td>
                    <td>{{ $sj->pesanan->rute->item->nama }}</td>
                    <td class="text-right">{{ number_format($sj->berat_dikirim, 2, ',', '.') }}</td>
                    <td>{{ $sj->trip_id ? 'TRIP-' . $sj->trip_id : '-' }}</td>
                    <td>{{ $sj->trip?->sopir?->nama ?? '-' }}</td>
                    <td>{{ $sj->trip?->kendaraan?->nopol ?? '-' }}</td>
                    <td>{{ $sj->tanggal_kirim ? $sj->tanggal_kirim->format('d/m/Y') : '-' }}</td>
                    <td>{{ $sj->tanggal_terima ? $sj->tanggal_terima->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-{{ $sj->status }}">
                            {{ ucfirst($sj->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 15px; color: #666666; font-style: italic;">
                        Tidak ada data surat jalan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Laporan Sistem Logistik</strong></p>
        <p>Dokumen ini dibuat secara otomatis dan valid tanpa tanda tangan</p>
        <p>© {{ date('Y') }} - Confidential & Proprietary</p>
    </div>
</body>
</html>