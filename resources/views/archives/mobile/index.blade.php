@extends('layouts.mobile')

@section('title', 'Arsip Pesanan')

@section('page-header')
    <h1>Arsip Pesanan</h1>
    <p class="page-sub">Pesanan yang telah selesai & dikirim</p>
@endsection

@section('content')

{{-- ── Search ── --}}
<form action="{{ route('archives.index') }}" method="GET" class="mb-3" id="searchForm">
    <div class="m-search">
        <i class="bi bi-search search-icon"></i>
        <input type="text" name="search" id="searchInput" placeholder="Cari arsip..." value="{{ request('search') }}" autocomplete="off">
        @if(request('search'))
            <a href="{{ route('archives.index') }}" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:var(--muted);"><i class="bi bi-x-circle-fill"></i></a>
        @endif
    </div>
</form>

{{-- ── Archive List ── --}}
@forelse($orders as $order)
<a href="{{ route('orders.show', $order) }}" class="m-list-item d-flex align-items-center gap-3 text-decoration-none text-dark">
    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary"
         style="width:40px;height:40px;flex-shrink:0;">
        <i class="bi bi-archive-fill"></i>
    </div>
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-7 text-sm text-truncate">{{ $order->order_number }}</span>
            <span class="badge bg-light text-secondary border text-xs">{{ $order->archived_at->format('d/m/y') }}</span>
        </div>
        <div class="text-xs text-muted text-truncate">{{ $order->customer_name }} • {{ $order->product_name }}</div>
    </div>
</a>
@empty
<div class="empty-state">
    <i class="bi bi-archive"></i>
    <p>
        @if(request('search'))
            Tidak ada arsip yang cocok.
        @else
            Belum ada pesanan yang diarsipkan.
        @endif
    </p>
</div>
@endforelse

{{-- ── Pagination ── --}}
@if($orders->hasPages())
<div class="m-pagination mt-3">
    {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
