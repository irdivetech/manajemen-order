<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Pengiriman — {{ $order->order_number }}</title>
    <style>
        /* ─── Reset & Base ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #111;
            background: #f0f0f0;
            padding: 30px;
        }

        /* ─── Print Controls ─── */
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

        /* ─── Label Container ─── */
        .label-page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 28mm 22mm;
            position: relative;
        }

        /* ─── Header ─── */
        .label-header {
            text-align: center;
            border-bottom: 3px solid #111;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .label-header h1 {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .label-header-meta {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #333;
        }
        .label-header-meta span {
            font-weight: 600;
        }

        /* ─── Sections ─── */
        .section {
            margin-bottom: 22px;
            border: 2px solid #111;
            border-radius: 4px;
            overflow: hidden;
        }
        .section-title {
            background: #111;
            color: #fff;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .section-body {
            padding: 18px 20px;
        }

        /* ─── Recipient (largest / most prominent) ─── */
        .recipient-name {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        .recipient-phone {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 14px;
        }
        .recipient-location {
            display: flex;
            gap: 24px;
            margin-bottom: 12px;
        }
        .recipient-location .loc-item {
            flex: 1;
        }
        .loc-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 2px;
        }
        .loc-value {
            font-size: 15px;
            font-weight: 700;
            color: #111;
        }
        .recipient-address-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 4px;
        }
        .recipient-address {
            font-size: 15px;
            font-weight: 500;
            line-height: 1.55;
            color: #111;
        }

        /* ─── Package Details ─── */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }
        .detail-item .detail-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 3px;
        }
        .detail-item .detail-value {
            font-size: 16px;
            font-weight: 700;
            color: #111;
        }

        /* ─── Sender ─── */
        .sender-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
        }
        .sender-item .sender-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 2px;
        }
        .sender-item .sender-value {
            font-size: 14px;
            font-weight: 600;
            color: #111;
        }
        .sender-item.full-width {
            grid-column: 1 / -1;
        }
        .sender-address {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
            color: #333;
        }

        /* ─── Shipping Info (manual fill) ─── */
        .shipping-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .shipping-field .field-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 8px;
        }
        .shipping-field .field-line {
            border-bottom: 2px dashed #999;
            height: 32px;
        }

        /* ─── Footer Note ─── */
        .label-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 11px;
            color: #999;
            font-style: italic;
        }

        /* ─── Dashed cut line ─── */
        .cut-line {
            border: none;
            border-top: 2px dashed #ccc;
            margin: 20px 0 0;
        }
        .cut-label {
            text-align: center;
            font-size: 10px;
            color: #bbb;
            margin-top: 4px;
            letter-spacing: 2px;
            text-transform: uppercase;
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
                min-height: auto;
                padding: 18mm 20mm;
            }
            .section { page-break-inside: avoid; }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>
    @inject('settings', 'App\Services\SettingService')

    <div class="no-print">
        <a href="{{ route('orders.tracking', $order->id) }}" class="btn btn-back">← Kembali</a>
        <button class="btn btn-print" onclick="window.print()">🖨 Cetak Resi</button>
    </div>

    <div class="label-page">

        {{-- ── Header ── --}}
        <div class="label-header">
            <h1>Resi / Label Pengiriman</h1>
            <div class="label-header-meta">
                <span>Nomor Pesanan: {{ $order->order_number }}</span>
                <span>Tanggal: {{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        {{-- ── Penerima ── --}}
        <div class="section">
            <div class="section-title">Penerima</div>
            <div class="section-body">
                <div class="recipient-name">{{ $order->customer_name }}</div>
                <div class="recipient-phone">{{ $order->customer_phone }}</div>

                @if($order->customer_district || $order->customer_city)
                <div class="recipient-location">
                    @if($order->customer_district)
                    <div class="loc-item">
                        <div class="loc-label">Kecamatan</div>
                        <div class="loc-value">{{ $order->customer_district }}</div>
                    </div>
                    @endif
                    @if($order->customer_city)
                    <div class="loc-item">
                        <div class="loc-label">Kabupaten / Kota</div>
                        <div class="loc-value">{{ $order->customer_city }}</div>
                    </div>
                    @endif
                </div>
                @endif

                @if($order->customer_address)
                <div style="margin-top: 6px;">
                    <div class="recipient-address-label">Alamat Lengkap</div>
                    <div class="recipient-address">{!! nl2br(e($order->customer_address)) !!}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Detail Paket ── --}}
        <div class="section">
            <div class="section-title">Detail Paket</div>
            <div class="section-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nama Produk</div>
                        <div class="detail-value">{{ $order->product_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total Quantity</div>
                        <div class="detail-value">{{ $order->totalQuantity() }} PCS</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Nomor Pesanan</div>
                        <div class="detail-value">{{ $order->order_number }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Pengirim ── --}}
        <div class="section">
            <div class="section-title">Pengirim</div>
            <div class="section-body">
                <div class="sender-grid">
                    <div class="sender-item">
                        <div class="sender-label">Nama Pengirim / Konveksi</div>
                        <div class="sender-value">{{ $settings->get('company_name') ?: '-' }}</div>
                    </div>
                    <div class="sender-item">
                        <div class="sender-label">Nomor HP</div>
                        <div class="sender-value">{{ $settings->get('company_phone') ?: '-' }}</div>
                    </div>
                    <div class="sender-item full-width">
                        <div class="sender-label">Alamat Pengirim</div>
                        <div class="sender-address">{{ $settings->get('company_address') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Informasi Pengiriman (diisi manual) ── --}}
        <div class="section">
            <div class="section-title">Informasi Pengiriman</div>
            <div class="section-body">
                <div class="shipping-fields">
                    <div class="shipping-field">
                        <div class="field-label">Jasa Pengiriman</div>
                        <div class="field-line"></div>
                    </div>
                    <div class="shipping-field">
                        <div class="field-label">Nomor Resi Ekspedisi</div>
                        <div class="field-line"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Footer ── --}}
        <div class="label-footer">
            Dokumen ini dicetak oleh sistem POMS &mdash; {{ now()->translatedFormat('d F Y, H:i') }}
        </div>

        <hr class="cut-line">
        <div class="cut-label">✂ garis potong</div>
    </div>

</body>
</html>
