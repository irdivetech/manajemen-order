<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 40px; background: #fff; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-back { background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-right: 10px; font-weight: bold; }
        .btn-print:hover { background: #4338ca; }
        .btn-back:hover { background: #4b5563; }
        
        @media print { 
            .no-print { display: none; }
            body { padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .invoice-wrapper { margin: 0; max-width: 100%; border: none; box-shadow: none; }
            .header, .footer, .info-box, table.items tr, .note-section { page-break-inside: avoid; }
        }

        .invoice-wrapper { position: relative; max-width: 800px; margin: 0 auto; overflow: hidden; padding-top: 20px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .logo-container {
            background-color: #2b82b1;
            color: white;
            padding: 30px 40px 50px 40px;
            clip-path: polygon(0 0, 100% 0, 100% 75%, 50% 100%, 0 75%);
            width: 220px;
            text-align: center;
            margin-top: -60px;
        }
        .logo-container img { max-width: 100%; height: auto; }
        .logo-container h1 { margin: 0; font-family: 'Brush Script MT', cursive; font-size: 38px; color: #7be0d0; }
        
        .header-title { text-align: right; margin-top: 10px; }
        .header-title h1 { color: #2b82b1; font-size: 70px; font-weight: 900; margin: 0; letter-spacing: 2px; line-height: 1; font-family: 'Arial Black', Arial, sans-serif; }
        .header-title h3 { color: #2b82b1; font-size: 22px; margin: 10px 0 0 0; font-weight: bold; }

        /* Info Box */
        .info-box { background-color: #f69a1f; color: #333; padding: 12px 25px; width: fit-content; margin-bottom: 30px; font-size: 14px; font-weight: 500; }
        .info-box table td { padding: 3px 10px 3px 0; }

        /* Table */
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items th { padding: 15px 12px; text-align: center; font-weight: bold; font-size: 16px; }
        table.items th:nth-child(1), table.items th:nth-child(2), table.items th:nth-child(3), table.items th:nth-child(4) { background-color: #f69a1f; color: #333; }
        table.items th:nth-child(5) { background-color: #b7ce2b; color: #333; }
        
        table.items td { padding: 18px 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        table.items td.text-center { text-align: center; font-weight: bold; font-size: 16px; }
        table.items td.text-right { text-align: right; font-weight: bold; font-size: 16px; }
        table.items td.desc { font-size: 15px; }
        table.items td.desc small { color: #666; display: block; margin-top: 5px; font-weight: normal; font-size: 13px; }

        /* Note */
        .note-section { margin-bottom: 10px; font-size: 12px; }
        .note-section strong { font-size: 11px; text-transform: uppercase; }
        .note-line { border-bottom: 1px solid #999; margin-top: 60px; margin-bottom: 20px; width: 95%; }

        /* Footer */
        .footer { display: flex; justify-content: space-between; align-items: stretch; position: relative; }
        
        .footer-left { width: 50%; padding-left: 15px; display: flex; flex-direction: column; justify-content: space-between; }
        .signature { text-align: center; width: fit-content; margin-bottom: 20px; }
        .signature h4 { margin: 0; font-size: 22px; font-weight: 900; color: #111; letter-spacing: 0.5px; }
        .signature p { margin: 2px 0 0 0; color: #333; font-size: 15px; font-weight: bold; }
        .signature img { max-height: 100px; margin: 10px 0; object-fit: contain; }
        
        .customer-info { font-size: 14px; margin-top: 40px; }
        .customer-info .label { color: #555; font-size: 13px; }
        .customer-info h4 { color: #1e4d7b; margin: 5px 0; font-size: 22px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .customer-info p { margin: 2px 0; color: #333; font-size: 14px; }

        .footer-right { width: 48%; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; }
        
        .total-box { display: flex; flex-direction: column; align-items: flex-end; width: 100%; margin-bottom: 30px; }
        .total-box-row { display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 5px; }
        .total-box-row.sisa { margin-top: 10px; padding-top: 10px; border-top: 2px solid #ddd; }
        .total-box-row span { font-size: 16px; font-weight: bold; font-style: italic; }
        .total-box-row .amount-val { font-size: 18px; font-weight: bold; }
        .total-box-row.sisa span { font-size: 18px; color: #b91c1c; }
        .total-box-row.sisa .amount-val { background-color: #fecaca; color: #b91c1c; padding: 10px 20px; font-size: 22px; font-weight: 900; border-radius: 5px; }

        .payment-info { 
            background-color: #2b82b1; 
            color: white; 
            padding: 35px 30px 25px 30px; 
            width: 100%; 
            box-sizing: border-box;
            clip-path: polygon(50% 0, 100% 15%, 100% 100%, 0 100%, 0 15%);
            margin-top: auto;
        }
        .payment-info h4 { margin: 0 0 12px 0; text-align: center; font-size: 13px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .payment-info table { width: 100%; font-size: 15px; margin: 0 auto; }
        .payment-info table td { padding: 3px 0; }
        
        /* Watermark Lunas */
        .lunas-stamp {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.15);
            border: 8px solid rgba(220, 38, 38, 0.15);
            padding: 10px 40px;
            border-radius: 10px;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body>
    @inject('settings', 'App\Services\SettingService')
    
    <div class="no-print">
        <a href="{{ route('orders.invoice', $order->id) }}" class="btn-back" style="text-decoration: none; display: inline-block;">Kembali</a>
        <button class="btn-print" onclick="window.print()">Cetak Tagihan</button>
    </div>
    
    <div class="invoice-wrapper">
        @if($invoice->isPaid())
            <div class="lunas-stamp">LUNAS</div>
        @endif

        <div class="header">
            <div class="logo-container">
                @if(!empty($settings->get('company_logo')))
                    <img src="{{ asset('storage/' . $settings->get('company_logo')) }}" alt="Logo">
                @else
                    <h1>{{ $settings->get('company_name') }}</h1>
                @endif
            </div>
            <div class="header-title">
                <h1>TAGIHAN.</h1>
                <h3>Billing Statement</h3>
            </div>
        </div>

        <div class="info-box">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td>Nomor Invoice</td>
                    <td>: {{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td>Tanggal Tagihan</td>
                    <td>: {{ strtoupper(date('d F Y')) }}</td>
                </tr>
                @if($order->deadline)
                <tr>
                    <td>Jatuh Tempo</td>
                    <td>: {{ strtoupper(\Carbon\Carbon::parse($order->deadline)->format('d F Y')) }}</td>
                </tr>
                @endif
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>No</th>
                    <th style="text-align: left;">Description</th>
                    <th>Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->sizeDetails as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td class="desc">
                        {{ $order->product_name }}
                        <small>
                            Warna: {{ $detail->color ?? '-' }} <br>
                            Ukuran: {{ $detail->masterSize?->name ?? $detail->size }} 
                            ({{ $detail->masterGender?->name ?? (['male' => 'Laki-laki', 'female' => 'Perempuan', 'child' => 'Anak-anak'][$detail->gender] ?? $detail->gender) }})
                        </small>
                    </td>
                    <td class="text-center" style="font-size: 18px;">{{ $detail->quantity }}</td>
                    <td class="text-right">Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp. {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Riwayat Pembayaran -->
        @if($invoice->payments->count() > 0)
        <div style="margin-bottom: 20px;">
            <strong style="font-size: 12px; text-transform: uppercase;">Riwayat Pembayaran (Cicilan):</strong>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid #ddd;">
                        <th style="text-align: left; padding: 8px 0;">Tanggal</th>
                        <th style="text-align: left; padding: 8px 0;">Metode</th>
                        <th style="text-align: right; padding: 8px 0;">Jumlah Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 0;">{{ $payment->paid_at->format('d M Y, H:i') }}</td>
                        <td style="padding: 8px 0;">{{ $payment->payment_method ?? 'Cash/Transfer' }}</td>
                        <td style="text-align: right; padding: 8px 0; color: #15803d; font-weight: bold;">+ Rp. {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="note-section">
            <strong>NOTE:</strong>
            <p>{{ $order->notes }}</p>
        </div>

        <div class="note-line" style="margin-top: 20px;"></div>

        <div class="footer">
            <div class="footer-left">
                <div class="signature">
                    <h4>{{ $settings->get('owner_name') ?: 'Pimpinan' }}</h4>
                    <p>{{ $settings->get('owner_title') ?: 'Owner' }}</p>
                    @if(!empty($settings->get('signature_image')))
                        <img src="{{ asset('storage/' . $settings->get('signature_image')) }}" alt="Signature">
                    @else
                        <div style="height: 80px;"></div>
                    @endif
                </div>

                <div class="customer-info">
                    <span class="label">Orderan a/n :</span>
                    <h4>
                        {{ $order->customer_name }}
                    </h4>
                    <p>{{ $order->customer_city ?? '-' }}</p>
                </div>
            </div>

            <div class="footer-right">
                <div class="total-box">
                    <div class="total-box-row">
                        <span>Total Orderan :</span>
                        <div class="amount-val">Rp. {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-box-row" style="color: #15803d;">
                        <span>Telah Dibayar :</span>
                        <div class="amount-val">- Rp. {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="total-box-row sisa">
                        <span>Sisa Tagihan :</span>
                        <div class="amount-val">Rp. {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="payment-info">
                    <h4>INFORMASI PEMBAYARAN</h4>
                    <table cellpadding="0" cellspacing="0">
                        @forelse($bankAccounts as $bank)
                        <tr>
                            <td style="width: 100px;">Rekening</td>
                            <td>: {{ strtoupper($bank->bank_name) }}</td>
                        </tr>
                        <tr>
                            <td>Atas Nama</td>
                            <td>: {{ strtoupper($bank->account_name) }}</td>
                        </tr>
                        <tr>
                            <td>No. Rekening</td>
                            <td>: {{ $bank->account_number }}</td>
                        </tr>
                        @if(!$loop->last)
                        <tr><td colspan="2" style="height: 10px;"></td></tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="2" style="text-align: center; font-style: italic;">Belum ada data rekening</td>
                        </tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
