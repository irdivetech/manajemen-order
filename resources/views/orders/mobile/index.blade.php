@extends('layouts.mobile')

@section('title', 'Data Pesanan')

@section('page-header')
    <h1>Data Pesanan</h1>
    <p class="page-sub">{{ $orders->total() }} pesanan aktif ditemukan</p>
@endsection

@section('content')

{{-- ── Search ── --}}
<form action="{{ route('orders.index') }}" method="GET" id="searchForm">
    <div class="m-search">
        <i class="bi bi-search search-icon"></i>
        <input type="text" name="search" id="searchInput"
               placeholder="Cari no pesanan, nama pelanggan..."
               value="{{ request('search') }}"
               autocomplete="off">
    </div>

    {{-- ── Status Filter Chips ── --}}
    <div class="chips-scroll mb-3">
        <a href="{{ route('orders.index', array_merge(request()->except(['status', 'page']), ['search' => request('search')])) }}"
           class="chip {{ !request('status') ? 'active' : '' }}">
            Semua
        </a>
        @php
            $statusChips = [
                'order_received'      => 'Diterima',
                'fabric_cutting'      => 'Potong Kain',
                'sewing'              => 'Jahit',
                'embroidery'          => 'Bordir',
                'button_installation' => 'Kancing',
                'shipping'            => 'Pengiriman',
            ];
        @endphp
        @foreach($statusChips as $val => $label)
        <a href="{{ route('orders.index', array_merge(request()->except(['status', 'page']), ['status' => $val, 'search' => request('search')])) }}"
           class="chip {{ request('status') === $val ? 'active' : '' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</form>

{{-- ── Order Cards ── --}}
@forelse($orders as $order)
<a href="{{ route('orders.show', $order) }}" class="order-card">
    <div class="oc-header">
        <span class="oc-num">{{ $order->order_number }}</span>
        <x-badge :status="$order->current_status" />
    </div>
    <div class="oc-body">
        <div class="oc-name">{{ $order->customer_name }}</div>
        <div class="oc-product">
            {{ $order->product_name }} ·
            {{ $order->totalQuantity() }} pcs ·
            {{ $order->color }}
        </div>
    </div>
    <div class="oc-footer">
        <span class="oc-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        <span class="oc-deadline {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'overdue' : '' }}">
            <i class="bi bi-calendar3"></i>
            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
        </span>
    </div>
</a>
@empty
<div class="empty-state">
    <i class="bi bi-clipboard2-x"></i>
    <p>
        @if(request('search') || request('status'))
            Tidak ada pesanan yang cocok dengan filter ini.
        @else
            Belum ada pesanan aktif.
        @endif
    </p>
    @if(request('search') || request('status'))
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm mt-2">Reset Filter</a>
    @endif
</div>
@endforelse

{{-- ── Pagination ── --}}
@if($orders->hasPages())
<div class="m-pagination mt-2">
    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

{{-- ── FAB: Buat Pesanan ── --}}
@if(Auth::user()?->isAdmin())
<a href="{{ route('orders.create') }}" class="fab" title="Buat Pesanan Baru">
    <i class="bi bi-plus-lg"></i>
</a>
@endif

@endsection

@push('scripts')
<script>
    // Live search on type (debounced)
    let timer;
    document.getElementById('searchInput')?.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 500);
    });
</script>
@endpush
