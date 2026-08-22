<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pengiriman — {{ $order->order_number }}</title>
    <style>
        /* ─── Reset & Base ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            background: #f0f0f0;
            padding: 30px;
        }

        /* ─── Print Controls ─── */
        .no-print {
            text-align: center;
            margin-bottom: 24px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
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

        /* ─── Label Container ─── */
        .label-page {
            width: 210mm;
            height: 148mm; /* A5 Landscape */
            background: #fff;
            margin: 0 auto;
            padding: 12mm 15mm;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* ─── Header ─── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header-left {
            flex-shrink: 0;
            text-align: center;
        }
        
        .header-left img {
            max-width: 100%;
            max-height: 95px;
            object-fit: contain;
        }
        
        .header-right {
            flex-grow: 1;
            max-width: 380px;
            text-align: right;
            font-size: 12.5px;
            line-height: 1.45;
            color: #000;
        }
        
        .header-right .company-name {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 3px;
        }
        
        .divider {
            border: none;
            border-top: 1.5px solid #000;
            margin-bottom: 18px;
        }

        /* ─── Main Content ─── */
        .content {
            font-family: "Times New Roman", Times, serif;
            color: #000;
        }
        
        table.resi-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table.resi-table td {
            vertical-align: top;
            padding: 6px 0;
        }
        
        table.resi-table td.label-col {
            width: 175px;
            font-weight: bold;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        
        table.resi-table td.colon-col {
            width: 25px;
            font-weight: bold;
            font-size: 24px;
            text-align: center;
        }
        
        table.resi-table td.value-col {
            font-size: 24px;
            line-height: 1.4;
        }
        
        .phone-bullet {
            padding-left: 18px;
            position: relative;
        }
        .phone-bullet::before {
            content: "•";
            position: absolute;
            left: 0;
            font-size: 26px;
            top: -2px;
        }

        /* ─── Print Styles ─── */
        @media print {
            .no-print { display: none !important; }
            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }
            .label-page {
                margin: 0;
                border: none;
                box-shadow: none;
                width: 100%;
                height: 100%;
                padding: 10mm 15mm;
            }
            @page {
                size: A5 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @inject('settings', 'App\Services\SettingService')

    @php
        $companyName = $settings->get('company_name') ?: 'SHALEEA';
        $companyAddress = $settings->get('company_address') ?: "Gg. Pangrango No.2 No. 7, RT.01/RW.07, Sawah Gede,\nKec. Cianjur, Kabupaten Cianjur, Jawa Barat 43211";
        $companyEmail = $settings->get('company_email') ?: 'shaleea.official@gmail.com';
        $companyPhone = $settings->get('company_phone') ?: '0815-1519-2525';
        
        // Cek jika ada di settings, jika tidak fallback ke hardcode sesuai referensi
        $companyWa = $settings->get('company_wa') ?: $companyPhone;
        $companyIg = $settings->get('company_ig') ?: '@shaleea.konveksi';
        $companyTiktok = $settings->get('company_tiktok') ?: '@shaleea.official';
    @endphp

    <div class="no-print">
        <a href="{{ route('orders.tracking', $order->id) }}" class="btn btn-back">← Kembali</a>
        <button class="btn btn-print" onclick="window.print()">🖨 Cetak Resi</button>
    </div>

    <div class="label-page">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                @if($settings->get('resi_logo'))
                    <img src="{{ Storage::url($settings->get('resi_logo')) }}" alt="Logo Resi">
                @elseif($settings->get('company_logo'))
                    <img src="{{ Storage::url($settings->get('company_logo')) }}" alt="Logo">
                @else
                    <h2 style="font-family: 'Times New Roman', serif; font-size: 26px; font-weight: bold; margin-top: 15px;">
                        {{ $companyName }}
                    </h2>
                @endif
            </div>
            <div class="header-right">
                <div class="company-name">{{ strtoupper($companyName) }}</div>
                <div>{!! nl2br(e($companyAddress)) !!}</div>
                <div>
                    Email: {{ $companyEmail }} | WA: {{ $companyWa }}
                </div>
                <div>
                    Instagram: {{ $companyIg }} | TikTok: {{ $companyTiktok }}
                </div>
            </div>
        </div>

        <hr class="divider">

        <!-- CONTENT -->
        <div class="content">
            <table class="resi-table">
                <tr>
                    <td class="label-col">PENGIRIM</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $companyName }} ({{ $companyWa }})</td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-top: 12px;">KEPADA</td>
                    <td class="colon-col" style="padding-top: 12px;">:</td>
                    <td class="value-col" style="padding-top: 12px;">
                        NAMA : {{ strtoupper($order->customer_name) }}<br>
                        <div class="phone-bullet">NO. HP : {{ $order->customer_phone }}</div>
                    </td>
                </tr>
                <tr>
                    <td class="label-col" style="padding-top: 16px;">ALAMAT</td>
                    <td class="colon-col" style="padding-top: 16px;">:</td>
                    <td class="value-col" style="padding-top: 16px; padding-right: 20px; text-align: justify;">
                        @php
                            $addressParts = [];
                            if ($order->customer_address) $addressParts[] = $order->customer_address;
                            if ($order->customer_district) $addressParts[] = 'Kec. ' . $order->customer_district;
                            if ($order->customer_city) $addressParts[] = $order->customer_city;
                        @endphp
                        {{ implode(', ', $addressParts) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
