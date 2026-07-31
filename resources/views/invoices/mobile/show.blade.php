@extends('layouts.mobile')

@inject('settings', 'App\Services\SettingService')

@section('title', 'Faktur: ' . $invoice->invoice_number)

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('orders.show', $order) }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Faktur</h1>
            <p class="page-sub">#{{ $invoice->invoice_number }}</p>
        </div>
    </div>
@endsection

@section('content')

{{-- LUNAS Watermark (CSS Overlay) --}}
@if($invoice->isPaid())
<div style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%) rotate(-20deg); font-size:4rem; font-weight:900; color:rgba(220,38,38,0.1); border:5px solid rgba(220,38,38,0.1); padding:10px 30px; border-radius:10px; pointer-events:none; z-index:99;">
    LUNAS
</div>
@endif

{{-- ── Company Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        @if(!empty($settings->get('company_logo')))
            <img src="{{ Storage::url($settings->get('company_logo')) }}" alt="Logo" style="width: 40px; height: 40px; object-fit: contain;" class="rounded bg-white">
        @endif
        <div>
            <div class="fs-5 fw-8 text-primary">{{ $settings->get('company_name') }}</div>
            <div class="text-xs text-muted mt-1">{!! nl2br(e($settings->get('company_address'))) !!}</div>
        </div>
    </div>
    <div class="text-end">
        <a href="{{ route('orders.invoice.print', $order) }}" target="_blank" class="icon-btn bg-primary text-white border-0" style="width:40px;height:40px;">
            <i class="bi bi-printer"></i>
        </a>
    </div>
</div>

{{-- ── Invoice Info ── --}}
<div class="m-card mb-4" style="background:#f8fafc;">
    <div class="m-card-body">
        <div class="row g-3">
            <div class="col-6 border-end">
                <div class="text-xs text-muted text-uppercase fw-6 mb-1">Ditagihkan Kepada</div>
                <div class="fw-7 text-dark text-sm">
                    {{ $order->customer_name }}
                    @if($order->customer_title) <br><span class="fw-normal text-muted">{{ $order->customer_title }}</span> @endif
                </div>
                @if($order->customer_address)
                <div class="text-xs text-muted my-1">{!! nl2br(e($order->customer_address)) !!}</div>
                @endif
                <div class="text-xs text-muted"><i class="bi bi-telephone"></i> {{ $order->customer_phone }}</div>
            </div>
            <div class="col-6 ps-3">
                <div class="text-xs text-muted text-uppercase fw-6 mb-1">Status</div>
                @if($invoice->isPaid())
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                @elseif($invoice->payment_status === 'partial')
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-clock-history me-1"></i> CICILAN/DP</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle me-1"></i> BLM LUNAS</span>
                @endif
                <div class="text-xs text-muted mt-2">Terbit: {{ $invoice->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Item List ── --}}
<div class="section-title">Detail Pesanan</div>
<div class="m-card mb-4">
    <div class="m-card-body p-0">
        @foreach($order->sizeDetails as $detail)
        <div class="m-list-item d-flex justify-content-between align-items-start {{ $loop->last ? 'border-0' : '' }}">
            <div>
                <div class="fw-7 text-sm">{{ $order->product_name }}</div>
                <div class="text-xs text-muted mt-1">
                    {{ $order->product_type }} • {{ $order->color }}
                    @if($order->material) • {{ $order->material }} @endif
                </div>
                <div class="text-xs text-muted">
                    Ukuran: {{ $detail->size }} 
                    ({{ ['male' => 'Laki-laki', 'female' => 'Perempuan', 'child' => 'Anak-anak'][$detail->gender] ?? $detail->gender }})
                    • {{ $detail->quantity }} pcs @ Rp {{ number_format($detail->price, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-end">
                <div class="fw-7 text-sm text-dark">Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Totals ── --}}
<div class="m-card mb-4">
    <div class="m-card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-sm text-muted">Subtotal</span>
            <span class="fw-6 text-sm">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($invoice->tax > 0)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-sm text-muted">Pajak ({{ $settings->get('tax_rate', '11') }}%)</span>
            <span class="fw-6 text-sm">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
            <span class="fw-bold text-dark text-sm">TOTAL TAGIHAN</span>
            <span class="fw-bold text-primary" style="font-size:1.1rem;">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

{{-- ── Notes ── --}}
@if($order->notes)
<div class="m-card mb-4">
    <div class="m-card-body bg-light">
        <div class="text-xs fw-bold text-muted text-uppercase mb-1">Catatan:</div>
        <div class="text-sm">{{ $order->notes }}</div>
    </div>
</div>
@endif

{{-- ── Tanda Tangan ── --}}
<div class="text-center mb-5 pb-3">
    <h6 class="fw-bold text-dark mb-0">{{ $settings->get('owner_name') ?: 'Pimpinan' }}</h6>
    <div class="text-xs text-muted">{{ $settings->get('owner_title') ?: 'Owner' }}</div>
    @if(!empty($settings->get('signature_image')))
        <img src="{{ Storage::url($settings->get('signature_image')) }}" alt="Signature" class="mt-2" style="max-height: 80px;">
    @else
        <div style="height:80px; width:150px; margin:10px auto;" class="border border-dashed text-muted d-flex align-items-center justify-content-center text-xs">TTD Owner</div>
    @endif
</div>

{{-- ── Admin Action ── --}}
@if(Auth::user()?->isAdmin() && !$invoice->isPaid())
<div class="m-card border-primary mb-4">
    <div class="m-card-header bg-primary bg-opacity-10">
        <h2 class="text-primary"><i class="bi bi-wallet2 me-1"></i> Ubah Status Pembayaran</h2>
    </div>
    <div class="m-card-body">
        <form action="{{ route('orders.invoice.payment', $order) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-3">
                <label class="form-label text-xs fw-6 text-muted">Status Pembayaran</label>
                <select name="payment_status" class="form-select" required>
                    <option value="unpaid" {{ $invoice->isUnpaid() ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="partial" {{ $invoice->payment_status === 'partial' ? 'selected' : '' }}>Cicilan / DP</option>
                    <option value="paid" {{ $invoice->isPaid() ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-6">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endif

<div style="height:40px;"></div>
@endsection
