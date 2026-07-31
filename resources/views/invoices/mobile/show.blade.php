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

{{-- ── Company Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="fs-5 fw-8 text-primary">{{ $settings->get('company_name') }}</div>
        <div class="text-xs text-muted mt-1">{!! nl2br(e($settings->get('company_address'))) !!}</div>
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
                <div class="fw-7 text-dark text-sm">{{ $order->customer_name }}</div>
                <div class="text-xs text-muted">{{ $order->customer_phone }}</div>
            </div>
            <div class="col-6 ps-3">
                <div class="text-xs text-muted text-uppercase fw-6 mb-1">Status Pembayaran</div>
                @if($invoice->isPaid())
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                @elseif($invoice->payment_status === \App\Models\Invoice::PAYMENT_PARTIAL)
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-clock-history me-1"></i> SEBAGIAN</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle me-1"></i> BELUM LUNAS</span>
                @endif
                <div class="text-xs text-muted mt-2">Tgl: {{ $invoice->created_at->format('d M Y') }}</div>
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

{{-- ── Total Summary ── --}}
<div class="m-card mb-5">
    <div class="m-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-8 text-dark" style="font-size:1.1rem;">Total Tagihan</span>
            <span class="fw-8 text-success" style="font-size:1.3rem;">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

{{-- ── Admin Action ── --}}
@if(Auth::user()?->isAdmin())
<div class="m-card border-primary mb-4">
    <div class="m-card-header bg-primary bg-opacity-10">
        <h2 class="text-primary"><i class="bi bi-wallet2 me-1"></i> Update Pembayaran</h2>
    </div>
    <div class="m-card-body">
        <form action="{{ route('orders.invoice.payment', $order) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <select name="payment_status" class="form-select">
                    <option value="unpaid" {{ $invoice->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="partial" {{ $invoice->payment_status === 'partial' ? 'selected' : '' }}>Cicilan / Sebagian</option>
                    <option value="paid" {{ $invoice->payment_status === 'paid' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-6">Simpan Status</button>
        </form>
    </div>
</div>
@endif

<div style="height:40px;"></div>
@endsection
