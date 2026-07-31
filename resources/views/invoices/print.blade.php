<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        
        @media print { 
            .no-print { display: none; }
            body { padding: 0; }
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            position: relative;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .lunas-stamp {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.12);
            border: 6px solid rgba(220, 38, 38, 0.12);
            padding: 10px 30px;
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
    
    <div class="invoice-box">
        @if($invoice->isPaid())
            <div class="lunas-stamp">LUNAS</div>
        @endif

        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                @if(!empty($settings->get('company_logo')))
                                    <img src="{{ asset('storage/' . $settings->get('company_logo')) }}" style="width:100%; max-width:150px;">
                                @else
                                    <span style="font-size: 24px; font-weight: bold;">{{ $settings->get('company_name') }}</span>
                                @endif
                            </td>
                            <td>
                                Faktur #: {{ $invoice->invoice_number }}<br>
                                Dibuat: {{ $invoice->created_at->format('d M Y') }}<br>
                                @if($order->deadline)
                                Jatuh Tempo: {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                {{ $settings->get('company_name') }}<br>
                                {!! nl2br(e($settings->get('company_address'))) !!}<br>
                                {{ $settings->get('company_phone') }}
                            </td>
                            <td>
                                {{ $order->customer_name }}
                                @if($order->customer_title) - {{ $order->customer_title }} @endif<br>
                                @if($order->customer_address){!! nl2br(e($order->customer_address)) !!}<br>@endif
                                {{ $order->customer_phone }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Status Pembayaran</td>
                <td>
                    @if($invoice->isPaid())
                        LUNAS
                    @elseif($invoice->payment_status === 'partial')
                        CICILAN / DP
                    @else
                        BELUM LUNAS
                    @endif
                </td>
            </tr>

            <tr class="details">
                <td>Total Tagihan</td>
                <td>Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
            </tr>

            <tr class="heading">
                <td>Deskripsi Produk</td>
                <td>Total Harga</td>
            </tr>

            @foreach($order->sizeDetails as $detail)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td>
                    {{ $order->product_name }} ({{ $order->product_type }})<br>
                    <small>
                        Ukuran: {{ $detail->size }} 
                        ({{ ['male' => 'Laki-laki', 'female' => 'Perempuan', 'child' => 'Anak-anak'][$detail->gender] ?? $detail->gender }}) 
                        | Warna: {{ $order->color }} 
                        @if($order->material) | Bahan: {{ $order->material }} @endif 
                        | Qty: {{ $detail->quantity }} @ Rp {{ number_format($detail->price, 0, ',', '.') }}
                    </small>
                </td>
                <td>Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td>
                    Subtotal: Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}<br>
                    @if($invoice->tax > 0)
                    Pajak ({{ $settings->get('tax_rate', '11') }}%): Rp {{ number_format($invoice->tax, 0, ',', '.') }}<br>
                    @endif
                    <br>
                    <strong>Total Tagihan: Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>

        @if($order->notes)
        <div style="margin-top: 30px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
            <strong>Catatan:</strong><br>
            {{ $order->notes }}
        </div>
        @endif

        <div style="margin-top: 40px; text-align: right;">
            <div>
                <strong>{{ $settings->get('owner_name') ?: 'Pimpinan' }}</strong><br>
                <span style="color: #666;">{{ $settings->get('owner_title') ?: 'Owner' }}</span>
            </div>
            @if(!empty($settings->get('signature_image')))
                <img src="{{ asset('storage/' . $settings->get('signature_image')) }}" alt="Signature" style="max-height: 80px; margin-top: 10px;">
            @else
                <div style="height: 80px;"></div>
            @endif
        </div>
    </div>
</body>
</html>
