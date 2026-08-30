@extends('layouts.mobile')

@section('title', 'Dasbor Eksekutif')

@section('page-header')
    <h1>Tinjauan Bisnis</h1>
    <p class="page-sub">Performa real-time — <strong>{{ $rangeLabel }}</strong></p>
@endsection

@section('content')

{{-- ── Range Filter ── --}}
<div class="mb-3">
    <form method="GET" action="{{ route('dashboard.index') }}">
        <div class="chips-scroll">
            <button name="range" value="30_days"    type="submit" class="chip {{ $range == '30_days'    ? 'active' : '' }}">30 Hari</button>
            <button name="range" value="this_month" type="submit" class="chip {{ $range == 'this_month' ? 'active' : '' }}">Bulan Ini</button>
            <button name="range" value="this_year"  type="submit" class="chip {{ $range == 'this_year'  ? 'active' : '' }}">Tahun Ini</button>
            <button name="range" value="all_time"   type="submit" class="chip {{ $range == 'all_time'   ? 'active' : '' }}">Semua</button>
        </div>
    </form>
</div>

{{-- ── Ekspor PDF ── --}}
<div class="mb-3">
    <a href="{{ route('dashboard.export', ['range' => $range]) }}"
       class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius:12px; font-weight: 600;">
        <i class="bi bi-download"></i> Ekspor Laporan PDF
    </a>
</div>

{{-- ── KPI Cards 2x2 Grid ── --}}
<div class="m-stat-grid">
    {{-- Total Pesanan --}}
    <div class="m-stat">
        <div class="stat-icon" style="background:#eef2ff; color:#4f46e5;">
            <i class="bi bi-cart-fill"></i>
        </div>
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-value">{{ $ownerSummary['kpi_total_orders'] }}</div>
        <span class="stat-badge {{ str_contains($ownerSummary['kpi_orders_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
            <i class="bi {{ str_contains($ownerSummary['kpi_orders_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i>
            {{ $ownerSummary['kpi_orders_growth'] }}
        </span>
    </div>

    {{-- Selesai --}}
    <div class="m-stat">
        <div class="stat-icon" style="background:#d1fae5; color:#10b981;">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="stat-label">Selesai</div>
        <div class="stat-value">{{ $ownerSummary['kpi_completed_orders'] }}</div>
        <span class="stat-badge {{ str_contains($ownerSummary['kpi_completed_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }}">
            <i class="bi {{ str_contains($ownerSummary['kpi_completed_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i>
            {{ $ownerSummary['kpi_completed_growth'] }}
        </span>
    </div>

    {{-- Pendapatan --}}
    <div class="m-stat" style="grid-column: span 2;">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-label">Pendapatan Periode Ini</div>
                <div class="stat-value" style="font-size:1.3rem;">{{ $ownerSummary['kpi_monthly_revenue'] }}</div>
                <span class="stat-badge {{ str_contains($ownerSummary['kpi_revenue_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} mt-1">
                    <i class="bi {{ str_contains($ownerSummary['kpi_revenue_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i>
                    {{ $ownerSummary['kpi_revenue_growth'] }}
                </span>
            </div>
            <div class="stat-icon" style="background:#eef2ff; color:#4f46e5; width:44px; height:44px;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>


</div>

{{-- ── Grafik Pendapatan ── --}}
<div class="m-card">
    <div class="m-card-header">
        <h2>Pendapatan & Profit Margin</h2>
    </div>
    <div class="m-card-body">
        <canvas id="revenueTrendChart" style="height:200px; width:100%;"></canvas>
    </div>
</div>

{{-- ── Distribusi Pasar ── --}}
<div class="m-card">
    <div class="m-card-header">
        <h2>Distribusi Pasar</h2>
    </div>
    <div class="m-card-body">
        <div class="row align-items-center g-3">
            <div class="col-5 d-flex justify-content-center">
                <canvas id="marketSplitChart" style="height:140px; width:140px;"></canvas>
            </div>
            <div class="col-7 d-flex flex-column gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="rounded-circle bg-primary" style="width:10px;height:10px;flex-shrink:0;"></span>
                        <span class="text-sm fw-6">B2B (Grosir)</span>
                    </div>
                    <div style="font-size:1.5rem; font-weight:700; color:var(--primary);">{{ $ownerSummary['b2b_percentage'] }}</div>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="rounded-circle" style="width:10px;height:10px;background:#0ea5e9;flex-shrink:0;"></span>
                        <span class="text-sm fw-6">Retail</span>
                    </div>
                    <div style="font-size:1.5rem; font-weight:700; color:#0ea5e9;">{{ $ownerSummary['retail_percentage'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Top Klien ── --}}
<div class="section-title">Klien Teratas</div>
<div class="m-card">
    <div class="m-card-body p-0">
        @forelse($topClients as $client)
        <div class="m-list-item">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-7"
                 style="width:36px;height:36px;flex-shrink:0;font-size:0.85rem;">
                {{ substr($client['name'], 0, 1) }}
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-6 text-sm text-truncate">{{ $client['name'] }}</div>
                <div class="text-xs text-muted">{{ $client['orders'] }} Pesanan</div>
                <div class="progress mt-1" style="height:3px;">
                    <div class="progress-bar bg-primary" style="width:{{ $client['contribution_percentage'] }}%;"></div>
                </div>
            </div>
            <div class="text-end" style="flex-shrink:0;">
                <div class="fw-7 text-sm">{{ $client['revenue'] }}</div>
            </div>
        </div>
        @empty
        <div class="empty-state py-4">
            <i class="bi bi-people"></i>
            <p>Belum ada data klien.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ── Batch Terkini ── --}}
<div class="section-title">Batch Selesai Terkini</div>
@forelse($recentBatches as $batch)
<a href="{{ route('orders.show', $batch->id) }}" class="order-card">
    <div class="oc-header">
        <span class="oc-num">{{ $batch->order_number }}</span>
        <span class="badge bg-success bg-opacity-10 text-success text-xs">{{ Str::title(str_replace('_', ' ', $batch->current_status)) }}</span>
    </div>
    <div class="oc-body">
        <div class="oc-name">{{ $batch->product_type ?? 'Lainnya' }}</div>
        <div class="oc-product">{{ number_format($batch->quantity, 0, ',', '.') }} pcs</div>
    </div>
    <div class="oc-footer">
        <span class="text-xs text-muted">Selesai {{ $batch->updated_at->diffForHumans() }}</span>
        <span class="text-xs text-primary fw-6">Lihat →</span>
    </div>
</a>
@empty
<div class="empty-state">
    <i class="bi bi-inbox"></i>
    <p>Belum ada batch selesai.</p>
</div>
@endforelse

{{-- Spacer to prevent content cut-off by bottom nav --}}
<div style="height: 3rem;"></div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Revenue Trend Chart
    const ctxRev = document.getElementById('revenueTrendChart').getContext('2d');
    const revenueTrendData = {!! json_encode($revenueTrend) !!};
    new Chart(ctxRev, {
        type: 'bar',
        data: {
            labels: revenueTrendData.labels,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: revenueTrendData.revenue_data,
                    backgroundColor: '#4f46e5',
                    borderRadius: 4,
                    order: 2
                },
                {
                    label: 'Profit Margin (%)',
                    data: revenueTrendData.margin_data,
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: '#10b981',
                    borderWidth: 2,
                    pointRadius: 3,
                    yAxisID: 'y1',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { font: { size: 10 } } } },
            scales: {
                y:  { ticks: { callback: v => 'Rp '+(v/1000000).toFixed(0)+'M', font: { size: 10 } } },
                y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: v => v+'%', font: { size: 10 } } },
                x:  { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // 2. Market Split Doughnut
    const ctxMarket = document.getElementById('marketSplitChart').getContext('2d');
    new Chart(ctxMarket, {
        type: 'doughnut',
        data: {
            labels: ['B2B', 'Retail'],
            datasets: [{ data: [parseFloat('{{ $ownerSummary['b2b_percentage'] }}'), parseFloat('{{ $ownerSummary['retail_percentage'] }}')], backgroundColor: ['#4f46e5', '#0ea5e9'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
    });
});
</script>
@endpush
