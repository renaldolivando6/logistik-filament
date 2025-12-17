<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Profitabilitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            padding: 20px;
            background: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c2c2c;
        }
        
        .header h1 {
            font-size: 22px;
            color: #2c2c2c;
            margin-bottom: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .header .subtitle {
            font-size: 11px;
            color: #666666;
            font-weight: 400;
        }
        
        .filter-info {
            background: #f8f8f8;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 2px;
            border-left: 3px solid #2c2c2c;
        }
        
        .filter-info h3 {
            font-size: 10px;
            color: #2c2c2c;
            margin-bottom: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-info p {
            font-size: 9px;
            color: #4a4a4a;
            line-height: 1.6;
        }
        
        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 8px 0;
        }
        
        .summary-box {
            display: table-cell;
            width: 20%;
            padding: 14px;
            text-align: center;
            border: 1px solid #e0e0e0;
            background: #fafafa;
            vertical-align: middle;
        }
        
        .summary-box .label {
            font-size: 9px;
            color: #666666;
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-box .value {
            font-size: 15px;
            font-weight: 700;
            color: #2c2c2c;
        }
        
        /* Accent colors only on specific values */
        .summary-box.profit .value.positive { color: #059669; }
        .summary-box.profit .value.negative { color: #dc2626; }
        .summary-box.margin .value { color: #2563eb; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        table thead {
            background: #2c2c2c;
            color: white;
        }
        
        table thead th {
            padding: 9px 7px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #2c2c2c;
        }
        
        table tbody td {
            padding: 8px 7px;
            border: 1px solid #e5e5e5;
            font-size: 9px;
            color: #2c2c2c;
        }
        
        table tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        table tbody tr:hover {
            background: #f5f5f5;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #e5e5e5;
            color: #2c2c2c;
            border: 1px solid #d4d4d4;
        }
        
        .profit-positive {
            color: #059669;
            font-weight: 700;
        }
        
        .profit-negative {
            color: #dc2626;
            font-weight: 700;
        }
        
        .footer-summary {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #2c2c2c;
        }
        
        .footer-summary table {
            width: 100%;
            margin: 0;
            border: none;
        }
        
        .footer-summary td {
            padding: 10px 12px;
            font-weight: 700;
            font-size: 11px;
            background: #fafafa !important;
            border: 1px solid #e0e0e0 !important;
            color: #2c2c2c;
        }
        
        .footer-summary .total-row td {
            background: #2c2c2c !important;
            color: white;
            font-size: 12px;
        }
        
        .footer-summary .value-revenue { color: #059669; }
        .footer-summary .value-cost { color: #4a4a4a; }
        .footer-summary .value-profit-pos { color: #059669; }
        .footer-summary .value-profit-neg { color: #dc2626; }
        
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #d4d4d4;
            text-align: center;
            font-size: 8px;
            color: #666666;
            line-height: 1.6;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Separator line */
        .separator {
            height: 1px;
            background: #e0e0e0;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Profitabilitas</h1>
        <div class="subtitle">Analisis Profit & Performance per Trip</div>
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
            @if($filters['kendaraan'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Kendaraan:</strong> {{ $filters['kendaraan'] }}
            @endif
            @if($filters['sopir'])
                &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Sopir:</strong> {{ $filters['sopir'] }}
            @endif
            &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Status Trip:</strong> Selesai
            <br>
            <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </p>
    </div>
    
    <div class="summary-boxes">
        <div class="summary-box">
            <div class="label">Total Trip</div>
            <div class="value">{{ number_format($summary['total_trips']) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Revenue</div>
            <div class="value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Biaya</div>
            <div class="value">Rp {{ number_format($summary['total_costs'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-box profit">
            <div class="label">Profit</div>
            <div class="value {{ $summary['total_profit'] >= 0 ? 'positive' : 'negative' }}">
                Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-box margin">
            <div class="label">Margin</div>
            <div class="value">{{ number_format($summary['profit_margin'], 1) }}%</div>
        </div>
    </div>
    
    <div class="separator"></div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 7%;">ID Trip</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 13%;">Sopir</th>
                <th style="width: 10%;">Kendaraan</th>
                <th style="width: 5%;" class="text-center">SJ</th>
                <th style="width: 8%;" class="text-right">Berat</th>
                <th style="width: 12%;" class="text-right">Revenue</th>
                <th style="width: 11%;" class="text-right">Biaya</th>
                <th style="width: 12%;" class="text-right">Profit</th>
                <th style="width: 6%;" class="text-right">Margin</th>
                <th style="width: 7%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $trip)
                @php
                    $profit = $trip->total_revenue - $trip->total_cost;
                    $margin = $trip->total_revenue > 0 ? round(($profit / $trip->total_revenue) * 100, 2) : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $trip->id }}</strong></td>
                    <td>{{ $trip->tanggal_trip->format('d/m/Y') }}</td>
                    <td>{{ $trip->sopir->nama }}</td>
                    <td>{{ $trip->kendaraan->nopol }}</td>
                    <td class="text-center">{{ $trip->suratJalan_count }}</td>
                    <td class="text-right">{{ number_format($trip->total_berat, 2, ',', '.') }} Kg</td>
                    <td class="text-right">Rp {{ number_format($trip->total_revenue, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($trip->total_cost, 0, ',', '.') }}</td>
                    <td class="text-right {{ $profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        Rp {{ number_format($profit, 0, ',', '.') }}
                    </td>
                    <td class="text-right"><strong>{{ $margin }}%</strong></td>
                    <td class="text-center">
                        <span class="badge">Selesai</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 25px; color: #666666; font-style: italic;">
                        Tidak ada data trip yang selesai untuk periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($data->isNotEmpty())
    <div class="footer-summary">
        <table>
            <tr>
                <td style="width: 70%; text-align: right; font-weight: 600;">TOTAL REVENUE:</td>
                <td style="width: 30%; text-align: right;" class="value-revenue">
                    Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="width: 70%; text-align: right; font-weight: 600;">TOTAL BIAYA OPERASIONAL:</td>
                <td style="width: 30%; text-align: right;" class="value-cost">
                    Rp {{ number_format($summary['total_costs'], 0, ',', '.') }}
                </td>
            </tr>
            <tr class="total-row">
                <td style="width: 70%; text-align: right; font-size: 13px;">TOTAL PROFIT:</td>
                <td style="width: 30%; text-align: right; font-size: 13px;">
                    Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
    @endif
    
    <div class="footer">
        <p><strong>Laporan Sistem Logistik</strong></p>
        <p>Dokumen ini dibuat secara otomatis dan valid tanpa tanda tangan</p>
        <p>© {{ date('Y') }} - Confidential & Proprietary</p>
    </div>
</body>
</html>