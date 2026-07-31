<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 0; padding: 40px; background: #fff; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        
        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }

        .invoice-wrapper { position: relative; max-width: 800px; margin: 0 auto; overflow: hidden; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .logo-container {
            background-color: #2b82b1;
            color: white;
            padding: 30px 40px;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 50% 100%, 0 80%);
            width: 200px;
            text-align: center;
        }
        .logo-container img { max-width: 150px; }
        .logo-container h1 { margin: 0; font-family: 'Brush Script MT', cursive; font-size: 32px; color: #7be0d0; }
        
        .header-title { text-align: right; }
        .header-title h1 { color: #2b82b1; font-size: 55px; font-weight: 800; margin: 0; letter-spacing: 2px; }
        .header-title h3 { color: #2b82b1; font-size: 20px; margin: 5px 0 0 0; font-weight: normal; }

        /* Info Box */
        .info-box { background-color: #f69a1f; color: #333; padding: 10px 20px; width: fit-content; margin-bottom: 30px; font-size: 14px; }
        .info-box table td { padding: 2px 10px 2px 0; font-weight: 500; }

        /* Table */
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        table.items th { padding: 12px; text-align: center; font-weight: bold; }
        table.items th:nth-child(1), table.items th:nth-child(2), table.items th:nth-child(3), table.items th:nth-child(4) { background-color: #f69a1f; color: #333; }
        table.items th:nth-child(5) { background-color: #b7ce2b; color: #333; }
        
        table.items td { padding: 15px 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        table.items td.text-center { text-align: center; }
        table.items td.text-right { text-align: right; font-weight: bold; }
        table.items td.desc { font-size: 14px; }
        table.items td.desc small { color: #666; display: block; margin-top: 5px; font-weight: normal; }

        /* Note */
        .note-section { margin-bottom: 50px; font-size: 12px; }
        .note-section strong { font-size: 10px; }
        .note-line { border-bottom: 1px solid #333; margin-top: 50px; margin-bottom: 20px; }

        /* Footer */
        .footer { display: flex; justify-content: space-between; align-items: stretch; position: relative; }
        
        .footer-left { width: 50%; }
        .signature { text-align: center; width: fit-content; margin-bottom: 40px; }
        .signature h4 { margin: 0; font-size: 18px; }
        .signature p { margin: 0; color: #555; font-size: 14px; }
        .signature img { max-height: 100px; margin: 10px 0; }
        
        .customer-info { font-size: 14px; }
        .customer-info .label { color: #555; }
        .customer-info h4 { color: #204176; margin: 5px 0; font-size: 18px; text-transform: uppercase; }
        .customer-info p { margin: 0; color: #333; }

        .footer-right { width: 45%; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; }
        
        .total-box { display: flex; align-items: center; justify-content: flex-end; width: 100%; margin-bottom: 30px; }
        .total-box span { font-size: 18px; font-weight: bold; font-style: italic; margin-right: 15px; }
        .total-box .amount { background-color: #b7ce2b; padding: 10px 20px; font-size: 22px; font-weight: bold; }

        .payment-info { 
            background-color: #2b82b1; 
            color: white; 
            padding: 30px; 
            width: 100%; 
            box-sizing: border-box;
            clip-path: polygon(50% 0, 100% 15%, 100% 100%, 0 100%, 0 15%);
            margin-top: auto;
        }
        .payment-info h4 { margin: 0 0 15px 0; text-align: center; font-size: 14px; }
        .payment-info table { width: 100%; font-size: 14px; }
        .payment-info table td { padding: 4px 0; }
        
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
        <button class="btn-print" onclick="window.print()">Cetak Faktur</button>
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
                <h1>INVOICE.</h1>
                <h3>Account Receivable</h3>
            </div>
        </div>

        <div class="info-box">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td>Nomor Invoice</td>
                    <td>: {{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td>Tanggal Invoice</td>
                    <td>: {{ $invoice->created_at->format('d M Y') }}</td>
                </tr>
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
                        {{ $order->product_name }} - {{ $order->product_type }}
                        <small>
                            Ukuran: {{ $detail->size }} 
                            ({{ ['male' => 'Laki-laki', 'female' => 'Perempuan', 'child' => 'Anak-anak'][$detail->gender] ?? $detail->gender }}) 
                            | Warna: {{ $order->color }}
                            @if($order->material) | Bahan: {{ $order->material }} @endif
                        </small>
                    </td>
                    <td class="text-center fw-bold" style="font-weight: bold; font-size: 18px;">{{ $detail->quantity }}</td>
                    <td class="text-right">Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp. {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note-section">
            <strong>NOTE:</strong>
            <p>{{ $order->notes }}</p>
        </div>

        <div class="note-line"></div>

        <div class="footer">
            <div class="footer-left">
                <div class="signature">
                    <h4>{{ $settings->get('owner_name') ?: 'Pimpinan' }}</h4>
                    <p>{{ $settings->get('owner_title') ?: 'Owner' }}</p>
                    @if(!empty($settings->get('signature_image')))
                        <img src="{{ asset('storage/' . $settings->get('signature_image')) }}" alt="Signature">
                    @else
                        <div style="height: 100px;"></div>
                    @endif
                </div>

                <div class="customer-info">
                    <span class="label">Orderan a/n :</span>
                    <h4>
                        {{ $order->customer_name }}
                        @if($order->customer_title) - {{ $order->customer_title }} @endif
                    </h4>
                    <p>{!! nl2br(e($order->customer_address)) !!}</p>
                    <p>{{ $order->customer_phone }}</p>
                </div>
            </div>

            <div class="footer-right">
                <div class="total-box">
                    <span>Total Orderan :</span>
                    <div class="amount">Rp. {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                </div>

                <div class="payment-info">
                    <h4>INFORMASI PEMBAYARAN</h4>
                    <table cellpadding="0" cellspacing="0">
                        @forelse($bankAccounts as $bank)
                        <tr>
                            <td>Rekening</td>
                            <td>: {{ $bank->bank_name }}</td>
                        </tr>
                        <tr>
                            <td>Atas Nama</td>
                            <td>: {{ $bank->account_name }}</td>
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
