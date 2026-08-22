@extends('layouts.mobile')

@section('title', 'Rincian Pesanan: ' . $order->order_number)

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('orders.index') }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">{{ $order->order_number }}</h1>
            <p class="page-sub">Detail & Riwayat Produksi</p>
        </div>
    </div>
@endsection

@section('content')

{{-- ── Status Banner ── --}}
<div class="m-card mb-3" style="background:linear-gradient(135deg,#4f46e5,#6366f1); border:none;">
    <div class="m-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div style="font-size:0.7rem; color:rgba(255,255,255,0.7); font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Status Saat Ini</div>
                <div style="color:#fff; font-weight:700; font-size:1rem; margin-top:0.25rem;">
                    {{ Str::title(str_replace('_', ' ', $order->current_status)) }}
                </div>
            </div>
            <x-badge :status="$order->current_status" />
        </div>
    </div>
</div>

{{-- ── Info Pelanggan ── --}}
<div class="section-title">Informasi Pelanggan</div>
<div class="m-card">
    <div class="m-card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-7"
                 style="width:44px;height:44px;flex-shrink:0;font-size:1.1rem;background:var(--primary)!important;">
                {{ substr($order->customer_name, 0, 1) }}
            </div>
            <div>
                <div class="fw-7">{{ $order->customer_name }}</div>
                @if($order->customer_title)
                <div class="text-xs text-secondary fw-6">{!! nl2br(e($order->customer_title)) !!}</div>
                @endif
                <div class="text-sm text-muted">{{ $order->customer_phone }}</div>
            </div>
            @if($order->customer_category === 'b2b')
                <span class="ms-auto badge bg-primary bg-opacity-10 text-primary border border-primary-subtle text-xs">B2B</span>
            @else
                <span class="ms-auto badge bg-info bg-opacity-10 text-info border border-info-subtle text-xs">Retail</span>
            @endif
        </div>
        @if($order->customer_address || $order->customer_city || $order->customer_district)
        <div class="mb-3 p-2 bg-light rounded">
            <div class="text-xs text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>Alamat Lengkap</div>
            <div class="text-sm fw-6">
                {{ $order->customer_address }}<br>
                @if($order->customer_district || $order->customer_city || $order->customer_province)
                    <span class="text-muted small">{{ collect([$order->customer_district, $order->customer_city, $order->customer_province])->filter()->join(', ') }}</span>
                @endif
            </div>
        </div>
        @endif
        <div class="row g-2">
            <div class="col-6">
                <div class="text-xs text-muted">Tanggal Pesan</div>
                <div class="text-sm fw-6">{{ $order->order_date?->format('d M Y') }}</div>
            </div>
            <div class="col-6">
                <div class="text-xs text-muted">Tenggat Waktu</div>
                <div class="text-sm fw-6 {{ $order->deadline?->isPast() ? 'text-danger' : 'text-warning' }}">
                    {{ $order->deadline?->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Spesifikasi Produk ── --}}
<div class="section-title">Spesifikasi Produk</div>
<div class="m-card">
    <div class="m-card-body">
        <div class="row g-2 mb-3">
            <div class="col-7">
                <div class="text-xs text-muted">Produk</div>
                <div class="text-sm fw-6">{{ $order->product_name }}</div>
                <div class="text-xs text-muted">{{ $order->clothingCategory?->name ?? '-' }} - {{ $order->product_type }}</div>
            </div>
            <div class="col-3">
                @if($order->material)
                <div class="text-xs text-muted">Bahan</div>
                <div class="text-sm fw-6">{{ $order->masterMaterial->name }}</div>
                @endif
                @if($order->has_embroidery)
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle mt-1 text-xs">Bordir / Sablon</span>
                @endif
            </div>
            <div class="col-2 text-end">
                <div class="text-xs text-muted">Total</div>
                <div class="text-sm fw-7 text-primary">{{ $order->totalQuantity() }}</div>
                <div class="text-xs text-muted">pcs</div>
            </div>
        </div>

        {{-- Size breakdown --}}
        <div class="mb-2">
            <div class="d-flex flex-column gap-2">
                @foreach($order->sizeDetails as $detail)
                <div class="d-flex align-items-center justify-content-between border rounded p-2" style="font-size:0.75rem;">
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary text-xs me-1">{{ $detail->color }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary text-xs me-1">{{ $detail->gender?->label ?? '-' }}</span>
                        <span class="fw-7 ms-1">{{ $detail->size?->label ?? '-' }}</span>
                        <span class="text-muted ms-1">({{ ucfirst($detail->size_type) }})</span>
                    </div>
                    <div class="fw-6 text-dark">{{ $detail->quantity }} pcs</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Harga & Profit ── --}}
<div class="section-title">Harga & Keuangan</div>
<div class="m-card">
    <div class="m-card-body">
        <div class="d-flex flex-column gap-2">
            <div class="d-flex justify-content-between text-sm">
                <span class="text-muted">Pendapatan (Omset)</span>
                <span class="fw-6">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            @if(Auth::user()?->isAdmin() || Auth::user()?->isOwner())
            <div class="d-flex justify-content-between text-sm">
                <span class="text-muted">Total HPP (Modal)</span>
                <span class="fw-6 text-danger">- Rp {{ number_format($order->total_cost, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:2px solid var(--border);">
                <span class="fw-7">Profit Bersih</span>
                <div class="text-end">
                    <div class="fw-7 text-success" style="font-size:1.1rem;">Rp {{ number_format($order->getProfit(), 0, ',', '.') }}</div>
                    <span class="badge bg-success text-xs">{{ $order->getProfitMargin() }}% margin</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Catatan ── --}}
@if($order->notes)
<div class="section-title">Catatan</div>
<div class="m-card">
    <div class="m-card-body">
        <p class="text-sm mb-0" style="line-height:1.6;">{{ $order->notes }}</p>
    </div>
</div>
@endif

{{-- ── Foto Desain ── --}}
@if($order->designFiles->isNotEmpty())
<div class="section-title">Foto Desain</div>
<div class="m-card">
    <div class="m-card-body">
        <div class="row g-2">
            @foreach($order->designFiles as $file)
            <div class="col-4">
                <a href="{{ $file->url }}" class="design-lightbox-trigger"
                   data-bs-toggle="offcanvas" data-bs-target="#designLightboxOffcanvas"
                   data-src="{{ $file->url }}" data-name="{{ $file->original_name }}">
                    <div class="rounded-3 overflow-hidden" style="aspect-ratio:1; background:#1a1a2e;">
                        <img src="{{ $file->url }}" class="w-100 h-100 object-fit-cover" alt="{{ $file->original_name }}">
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Riwayat Tracking ── --}}
<div class="section-title">Riwayat Produksi</div>
<div class="m-card">
    <div class="m-card-body">
        @if($order->trackingHistories->isNotEmpty())
        <div class="timeline">
            @foreach($order->trackingHistories->sortByDesc('created_at') as $track)
            <div class="timeline-item">
                <div class="timeline-dot done"></div>
                <div>
                    <div class="fw-6 text-sm">{{ Str::title(str_replace('_', ' ', $track->status)) }}</div>
                    <div class="text-xs text-muted mt-1">{{ $track->description }}</div>
                    <div class="d-flex gap-2 mt-1">
                        <span class="text-xs text-muted">{{ $track->created_at->format('d M Y, H:i') }}</span>
                        @if($track->updatedBy)
                            <span class="text-xs text-muted">· {{ $track->updatedBy->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state py-3">
            <i class="bi bi-clock-history"></i>
            <p>Belum ada riwayat tracking.</p>
        </div>
        @endif
    </div>
</div>

{{-- Spacer for sticky actions --}}
<div style="height:70px;"></div>

{{-- ── Sticky Bottom Actions ── --}}
<div class="position-fixed start-0 end-0 p-3"
     style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom, 0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
    <div class="d-flex gap-2">
        <a href="{{ route('orders.tracking', $order) }}" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-clock-history"></i> Tracking
        </a>
        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-receipt"></i> Faktur
        </a>
        @if(Auth::user()?->isAdmin() && $order->isEditable())
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-light border d-flex align-items-center justify-content-center" style="width:44px;">
            <i class="bi bi-pencil"></i>
        </a>
        @endif
        @if(Auth::user()?->isAdmin() && $order->isDeletable())
        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="confirmDelete(event, this, 'Hapus pesanan {{ $order->order_number }}? Tindakan ini tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center" style="width:44px;">
                <i class="bi bi-trash"></i>
            </button>
        </form>
        @endif
    </div>
</div>

{{-- ── Lightbox Offcanvas ── --}}
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="designLightboxOffcanvas" style="border-radius:20px 20px 0 0; height:85vh; background:#1a1a2e;">
    <div class="d-flex justify-content-center pt-3 pb-1">
        <div style="width:36px;height:4px;background:#4a4a5a;border-radius:2px;"></div>
    </div>
    <div class="offcanvas-header border-0 pb-0 pt-2 px-4 d-flex justify-content-between align-items-center">
        <h6 class="offcanvas-title text-white text-xs text-truncate pe-3" id="lightbox-filename" style="max-width:80%;"></h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" style="flex-shrink:0;"></button>
    </div>
    <div class="offcanvas-body text-center p-4 d-flex flex-column align-items-center justify-content-center">
        <img id="lightbox-img" src="" alt="" class="img-fluid mb-4" style="border-radius:10px; max-height:60vh; object-fit:contain; box-shadow:0 8px 32px rgba(0,0,0,0.5);">
        <a id="lightbox-download" href="" download class="btn btn-light btn-sm px-4 fw-6" style="border-radius:20px;">
            <i class="bi bi-download me-1"></i> Unduh Desain
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.design-lightbox-trigger').forEach(function(el) {
    el.addEventListener('click', function(e) {
        document.getElementById('lightbox-img').src = this.dataset.src;
        document.getElementById('lightbox-filename').textContent = this.dataset.name;
        document.getElementById('lightbox-download').href = this.dataset.src;
    });
});
</script>
@endpush
