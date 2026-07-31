<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Eksekutif - StitchFlow</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #334155; margin: 0; padding: 20px; }
        .header { border-bottom: 3px solid #4f46e5; padding-bottom: 15px; margin-bottom: 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; color: #0f172a; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #64748b; }
        
        .section-title { font-size: 14px; font-weight: bold; color: #1e293b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-top: 35px; margin-bottom: 20px; letter-spacing: 0.5px; }
        
        .kpi-row { width: 100%; margin-bottom: 20px; table-layout: fixed; border: none; background: transparent; }
        .kpi-col { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; vertical-align: top; }
        .kpi-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 8px; }
        .kpi-value { font-size: 20px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .kpi-growth { font-size: 11px; }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
        .text-muted { color: #94a3b8; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; background-color: #fff; border: 1px solid #e2e8f0; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 12px 15px; text-align: left; }
        th { background-color: #f1f5f9; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; }
        td { font-size: 12px; color: #334155; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 15px; font-style: italic; }
        .badge { background-color: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kinerja Eksekutif</h1>
        <p>StitchFlow Production Order Management System</p>
        <p><strong>Periode:</strong> {{ $rangeLabel }}</p>
        <p><strong>Dicetak pada:</strong> {{ $generatedAt }}</p>
    </div>

    <div class="section-title">Ikhtisar Performa ({{ $rangeLabel }})</div>
    <table class="kpi-row">
        <tr>
            <td class="kpi-col" style="width: 32%;">
                <div class="kpi-title">Pesanan Diterima</div>
                <div class="kpi-value">{{ $ownerSummary['kpi_total_orders'] }}</div>
                <div class="kpi-growth {{ str_contains($ownerSummary['kpi_orders_growth'], '-') ? 'text-danger' : 'text-success' }}">
                    {{ $ownerSummary['kpi_orders_growth'] }} vs periode sebelumnya
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td class="kpi-col" style="width: 32%;">
                <div class="kpi-title">Pesanan Diselesaikan</div>
                <div class="kpi-value">{{ $ownerSummary['kpi_completed_orders'] }}</div>
                <div class="kpi-growth {{ str_contains($ownerSummary['kpi_completed_growth'], '-') ? 'text-danger' : 'text-success' }}">
                    {{ $ownerSummary['kpi_completed_growth'] }} vs periode sebelumnya
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td class="kpi-col" style="width: 32%;">
                <div class="kpi-title">Omset (Pendapatan)</div>
                <div class="kpi-value">{{ $ownerSummary['kpi_monthly_revenue'] }}</div>
                <div class="kpi-growth {{ str_contains($ownerSummary['kpi_revenue_growth'], '-') ? 'text-danger' : 'text-success' }}">
                    {{ $ownerSummary['kpi_revenue_growth'] }} vs periode sebelumnya
                </div>
            </td>
        </tr>
    </table>
    
    <table class="kpi-row">
        <tr>
            <td class="kpi-col" style="width: 48%;">
                <div class="kpi-title">Distribusi: B2B (Grosir)</div>
                <div class="kpi-value">{{ $ownerSummary['b2b_percentage'] }}%</div>
                <div class="kpi-growth text-muted">Rp {{ number_format(str_replace(['Rp ', '.'], '', $ownerSummary['rev_b2b']), 0, ',', '.') }}</div>
            </td>
            <td style="width: 4%;"></td>
            <td class="kpi-col" style="width: 48%;">
                <div class="kpi-title">Distribusi: Retail (Eceran)</div>
                <div class="kpi-value">{{ $ownerSummary['retail_percentage'] }}%</div>
                <div class="kpi-growth text-muted">Rp {{ number_format(str_replace(['Rp ', '.'], '', $ownerSummary['rev_retail']), 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Klien Teratas Berdasarkan Omset</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Klien</th>
                <th>Jumlah Pesanan</th>
                <th>Tingkat Penyelesaian</th>
                <th>Total Omset</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topClients as $index => $client)
            <tr>
                <td style="width: 5%; text-align: center;">{{ $index + 1 }}</td>
                <td style="width: 35%;"><strong>{{ $client['name'] }}</strong></td>
                <td style="width: 15%; text-align: center;">{{ $client['orders'] }}</td>
                <td style="width: 20%; text-align: center;">{{ $client['rate'] }}</td>
                <td style="width: 25%; text-align: right;">{{ $client['revenue'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data klien pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Bauran Produk (Product Mix)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe Produk</th>
                <th>Persentase Permintaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productMix as $index => $mix)
            <tr>
                <td style="width: 10%; text-align: center;">{{ $index + 1 }}</td>
                <td style="width: 60%;">{{ $mix['name'] }}</td>
                <td style="width: 30%; text-align: center;">{{ $mix['percentage'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 20px;">Tidak ada data bauran produk pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh StitchFlow System pada {{ $generatedAt }} - Rahasia Perusahaan
    </div>
</body>
</html>
