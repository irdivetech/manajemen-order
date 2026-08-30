@extends('layouts.mobile')

@section('title', 'Dasbor Admin')

@section('page-header')
    <h1>Wawasan Produksi</h1>
    <p class="page-sub">Ringkasan real-time area produksi pakaian Anda.</p>
@endsection

@section('content')

{{-- ── KPI Stats 2x2 Grid ── --}}
<div class="m-stat-grid">
    {{-- Total Pesanan --}}
    <div class="m-stat">
        <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;">
            <i class="bi bi-cart-fill"></i>
        </div>
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-value">{{ number_format($summary['total_orders'], 0, ',', '.') }}</div>
        <span class="stat-badge {{ $summary['orders_growth'] >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
            <i class="bi {{ $summary['orders_growth'] >= 0 ? 'bi-graph-up' : 'bi-graph-down' }}"></i>
            {{ $summary['orders_growth'] > 0 ? '+' : '' }}{{ $summary['orders_growth'] }}%
        </span>
    </div>

    {{-- Diproses --}}
    <div class="m-stat">
        <div class="stat-icon" style="background:#eef2ff; color:#6366f1;">
            <i class="bi bi-gear-fill"></i>
        </div>
        <div class="stat-label">Diproses</div>
        <div class="stat-value">{{ number_format($summary['in_progress'], 0, ',', '.') }}</div>
        <span class="stat-badge bg-primary bg-opacity-10 text-primary">
            {{ $summary['active_progress'] }} Aktif
        </span>
    </div>

    {{-- Selesai --}}
    <div class="m-stat">
        <div class="stat-icon" style="background:#d1fae5; color:#10b981;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-label">Selesai</div>
        <div class="stat-value">{{ number_format($summary['completed'], 0, ',', '.') }}</div>
        <span class="stat-badge bg-success bg-opacity-10 text-success">
            {{ $summary['completed_rate'] }}% Rate
        </span>
    </div>

    {{-- Deadline Mendekat --}}
    <div class="m-stat" style="border-color:#fca5a5; background:#fff5f5;">
        <div class="stat-icon" style="background:#fee2e2; color:#ef4444;">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="stat-label" style="color:#ef4444;">Batas Waktu</div>
        <div class="stat-value" style="color:#ef4444;">{{ $summary['pending_deadlines'] }}</div>
        <span class="stat-badge bg-danger bg-opacity-10 text-danger">7 Hari ke depan</span>
    </div>
</div>

{{-- ── Pendapatan Bulan Ini ── --}}
<div class="m-card">
    <div class="m-card-body" style="background: linear-gradient(135deg, #4f46e5, #6366f1); border-radius:14px;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div style="font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:0.05em;">
                    Pendapatan Bulan Ini
                </div>
                <div style="font-size:1.35rem; font-weight:700; color:#fff; margin-top:0.25rem;">
                    Rp {{ number_format($summary['monthly_revenue'], 0, ',', '.') }}
                </div>
                <span class="stat-badge mt-2 d-inline-flex" style="background:rgba(255,255,255,0.2); color:#fff;">
                    <i class="bi {{ $summary['revenue_growth'] >= 0 ? 'bi-graph-up' : 'bi-graph-down' }}"></i>
                    {{ $summary['revenue_growth'] > 0 ? '+' : '' }}{{ $summary['revenue_growth'] }}% dari bulan lalu
                </span>
            </div>
            <div style="font-size:2.5rem; opacity:0.3; color:#fff;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

{{-- ── Distribusi Status Produksi ── --}}
<div class="m-card">
    <div class="m-card-header">
        <h2>Status Produksi</h2>
        <a href="{{ route('orders.index') }}" class="text-xs text-primary fw-6 text-decoration-none">Lihat Semua →</a>
    </div>
    <div class="m-card-body">
        @php
            $statusItems = [
                ['label' => 'Penerimaan', 'key' => 'penerimaan', 'color' => '#94a3b8'],
                ['label' => 'Persiapan',  'key' => 'persiapan',  'color' => '#818cf8'],
                ['label' => 'Produksi',   'key' => 'produksi',   'color' => '#4f46e5'],
                ['label' => 'Finishing',  'key' => 'finishing',  'color' => '#f59e0b'],
                ['label' => 'Pengiriman', 'key' => 'pengiriman', 'color' => '#10b981'],
            ];
        @endphp
        <div class="d-flex flex-column gap-2">
            @foreach($statusItems as $item)
            <div>
                <div class="d-flex justify-content-between text-sm mb-1">
                    <span class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:{{ $item['color'] }};flex-shrink:0;"></span>
                        {{ $item['label'] }}
                    </span>
                    <span class="fw-6">
                        {{ $statusBreakdownUI[$item['key']]['count'] ?? 0 }}
                        <span class="text-muted fw-4">({{ $statusBreakdownUI[$item['key']]['percent'] ?? 0 }}%)</span>
                    </span>
                </div>
                <div class="progress" style="height:5px; border-radius:3px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ $statusBreakdownUI[$item['key']]['percent'] ?? 0 }}%; background:{{ $item['color'] }};">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Pesanan Terbaru ── --}}
<div class="section-title">Pesanan Terbaru</div>

@forelse($recentOrders as $order)
<a href="{{ route('orders.show', $order) }}" class="order-card">
    <div class="oc-header">
        <span class="oc-num">{{ $order->order_number }}</span>
        <x-badge :status="$order->current_status" />
    </div>
    <div class="oc-body">
        <div class="oc-name">{{ $order->customer_name }}</div>
        <div class="oc-product">{{ $order->product_name }}</div>
    </div>
    <div class="oc-footer">
        <span class="oc-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        <span class="oc-deadline {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'overdue' : '' }}">
            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
        </span>
    </div>
</a>
@empty
<div class="empty-state">
    <i class="bi bi-clipboard2-x"></i>
    <p>Belum ada pesanan terbaru.</p>
</div>
@endforelse

{{-- ── Batas Waktu Mendekat ── --}}
<div class="section-title mt-4">Batas Waktu Mendekat (Aktif)</div>
<div class="m-card">
    <div class="m-card-body p-0">
        @php
            $upcoming = \App\Models\Order::active()
                        ->whereNotNull('deadline')
                        ->orderBy('deadline', 'asc')
                        ->limit(5)
                        ->get();
        @endphp
        @forelse($upcoming as $order)
            @php
                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($order->deadline), false);
                if ($daysLeft < 0) {
                    $borderClass = 'border-danger';
                    $textClass = 'text-danger';
                    $badgeText = 'Terlambat';
                } elseif ($daysLeft <= 3) {
                    $borderClass = 'border-danger';
                    $textClass = 'text-danger';
                    $badgeText = 'Mendesak';
                } elseif ($daysLeft <= 7) {
                    $borderClass = 'border-warning';
                    $textClass = 'text-warning';
                    $badgeText = 'Segera';
                } else {
                    $borderClass = 'border-info';
                    $textClass = 'text-muted';
                    $badgeText = 'Aman';
                }
            @endphp
            <a href="{{ route('orders.show', $order) }}" class="m-list-item d-flex align-items-center justify-content-between text-decoration-none border-start border-4 {{ $borderClass }}">
                <div>
                    <h6 class="mb-0 fw-semibold text-dark">#{{ $order->order_number }}</h6>
                    <p class="mb-0 text-xs text-muted">{{ $order->customer_name }}</p>
                    <p class="mb-0 text-xs fw-medium mt-1"><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}</p>
                </div>
                <div>
                    <span class="badge bg-{{ str_replace('text-', '', $textClass) }} bg-opacity-10 {{ $textClass }}">{{ $badgeText }}</span>
                </div>
            </a>
        @empty
            <div class="text-center py-4 text-muted small">Tidak ada pesanan aktif dengan batas waktu.</div>
        @endforelse
    </div>
</div>

{{-- Spacer for FAB --}}
<div style="height: 5rem;"></div>

{{-- FAB: Tambah Pesanan --}}
<a href="{{ route('orders.create') }}" class="fab" title="Buat Pesanan Baru">
    <i class="bi bi-plus-lg"></i>
</a>

@endsection
