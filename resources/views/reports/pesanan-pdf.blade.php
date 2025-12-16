<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
        }
        th { 
            background-color: #4472C4; 
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-draft { background: #e5e7eb; color: #374151; }
        .badge-dalam_perjalanan { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-batal { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PESANAN</h2>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Pesanan</div>
            <div class="summary-value">{{ number_format($summary['total_pesanan']) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Revenue</div>
            <div class="summary-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Rata-rata Order</div>
            <div class="summary-value">Rp {{ number_format($summary['avg_order_value'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Completion Rate</div>
            <div class="summary-value">{{ $summary['completion_rate'] }}%</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="20%">Pelanggan</th>
                <th width="15%">Rute</th>
                <th width="10%">Muatan</th>
                <th class="text-right" width="10%">Berat</th>
                <th class="text-right" width="15%">Total</th>
                <th class="text-center" width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $pesanan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pesanan->tanggal_pesanan->format('d/m/Y') }}</td>
                <td>{{ $pesanan->pelanggan->nama }}</td>
                <td>{{ $pesanan->rute->asal }} → {{ $pesanan->rute->tujuan }}</td>
                <td>{{ $pesanan->rute->item->nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($pesanan->berat, 2) }} Ton</td>
                <td class="text-right">Rp {{ number_format($pesanan->total_tagihan, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $pesanan->status }}">
                        {{ match($pesanan->status) {
                            'draft' => 'Draft',
                            'dalam_perjalanan' => 'Dalam Perjalanan',
                            'selesai' => 'Selesai',
                            'batal' => 'Batal',
                            default => $pesanan->status
                        } }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem</p>
    </div>
</body>
</html>