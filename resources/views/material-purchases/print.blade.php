<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Belanja Bahan</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111;
            background: #f0f0f0;
            padding: 20px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        
        .btn:hover { opacity: 0.85; }
        .btn-back { background: #6b7280; color: #fff; margin-right: 8px; }
        .btn-print { background: #111; color: #fff; }

        .print-page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 20mm 15mm;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            color: #555;
        }

        .summary-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .summary-box h3 {
            margin-top: 5px;
            font-size: 22px;
            color: #d97706; /* Warning color */
        }

        .material-group {
            margin-bottom: 30px;
        }

        .material-title {
            background: #111;
            color: #fff;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        table th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        @media print {
            .no-print { display: none !important; }
            body {
                background: #fff;
                padding: 0;
            }
            .print-page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 0;
            }
            .material-title {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    @php
        $periodLabels = [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
            'custom' => 'Kustom'
        ];
        $periodStr = (string) $period;
        $periodName = $periodLabels[$periodStr] ?? ucfirst($periodStr);
    @endphp

    <div class="no-print">
        <a href="{{ route('material-purchases.index', request()->query()) }}" class="btn btn-back">← Kembali</a>
        <button class="btn btn-print" onclick="window.print()">🖨 Cetak PDF</button>
    </div>

    <div class="print-page">
        <div class="header">
            <h1>Daftar Belanja Bahan</h1>
            <p>Periode: <strong>{{ $periodName }}</strong> 
                @if($period === 'custom' && $from && $to)
                    ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})
                @endif
            </p>
        </div>

        <div class="summary-box">
            <div>Estimasi Total Uang Belanja:</div>
            <h3>Rp {{ number_format((float) $totalEstimatedCash, 0, ',', '.') }}</h3>
        </div>

        @if($summary->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="45%">Nama Bahan</th>
                        <th width="20%" class="text-end">Total Kebutuhan</th>
                        <th width="20%" class="text-end">Estimasi Harga</th>
                        <th width="10%" class="text-center">Ceklis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ $item->material_name }}</strong></td>
                            <td class="text-end" style="font-weight: bold; color: #d97706;">{{ (float) $item->total_usage_meter }} meter</td>
                            <td class="text-end">Rp {{ number_format($item->total_estimated_cost, 0, ',', '.') }}</td>
                            <td></td> <!-- Empty box for physical checking -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 50px; color: #666;">
                Semua bahan pesanan sudah dibeli atau tidak ada data pada periode ini.
            </div>
        @endif
    </div>
</body>
</html>
