@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dasbor Admin</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Wawasan Produksi</h2>
        <p class="text-muted mb-0">Ringkasan waktu nyata (real-time) dari area produksi pakaian Anda.</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        Pesanan Baru
    </a>
</div>

<!-- KPI Grid -->
<div class="row g-3 mb-4">
    <!-- Total Pesanan -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100">
            <p class="text-muted small text-uppercase fw-semibold mb-2">Total Pesanan</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0">{{ number_format($summary['total_orders'], 0, ',', '.') }}</h3>
                <span class="{{ $summary['orders_growth'] >= 0 ? 'text-success' : 'text-danger' }} small fw-bold">
                    {{ $summary['orders_growth'] > 0 ? '+' : '' }}{{ $summary['orders_growth'] }}%
                </span>
            </div>
        </div>
    </div>
    
    <!-- Diproses -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100">
            <p class="text-muted small text-uppercase fw-semibold mb-2">Diproses</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0">{{ number_format($summary['in_progress'], 0, ',', '.') }}</h3>
                <span class="text-primary small fw-bold">{{ $summary['active_progress'] }} Aktif</span>
            </div>
        </div>
    </div>
    
    <!-- Selesai -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100">
            <p class="text-muted small text-uppercase fw-semibold mb-2">Selesai</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0">{{ number_format($summary['completed'], 0, ',', '.') }}</h3>
                <span class="text-success small fw-bold">{{ $summary['completed_rate'] }}% Selesai</span>
            </div>
        </div>
    </div>
    
    <!-- Diarsipkan -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100">
            <p class="text-muted small text-uppercase fw-semibold mb-2">Diarsipkan</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0">{{ number_format($summary['archived'], 0, ',', '.') }}</h3>
                <span class="text-muted small fw-bold">Total Arsip</span>
            </div>
        </div>
    </div>
    
    <!-- Pendapatan Bulanan -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100">
            <p class="text-muted small text-uppercase fw-semibold mb-2">Pendapatan Bulan Ini</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0 fs-5">Rp {{ number_format($summary['monthly_revenue'], 0, ',', '.') }}</h3>
                <span class="{{ $summary['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }} small fw-bold">
                    {{ $summary['revenue_growth'] > 0 ? '+' : '' }}{{ $summary['revenue_growth'] }}%
                </span>
            </div>
        </div>
    </div>
    
    <!-- Batas Waktu Depan -->
    <div class="col-sm-6 col-lg-4 col-xl-2">
        <div class="stat-card h-100 border-start border-4 border-danger">
            <p class="text-danger small text-uppercase fw-semibold mb-2">Batas Waktu Depan</p>
            <div class="d-flex justify-content-between align-items-end">
                <h3 class="fw-bold mb-0">{{ $summary['pending_deadlines'] }}</h3>
                <span class="text-danger small fw-bold">7 Hari Kedepan</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart: Pertumbuhan Pendapatan (Line) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Statistik Pendapatan Bulanan (Rp)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Distribusi Status Produksi (Bar) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Analisis Status Produksi</h5>
            </div>
            <div class="card-body">
                <canvas id="productionStatusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Center Column: Tables -->
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fs-6 fw-bold">Pesanan Terbaru</h5>
                <a href="{{ route('orders.index') }}" class="text-primary text-decoration-none small fw-bold">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Pesanan #</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="ps-4 fw-medium">
                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">#{{ $order->order_number }}</a>
                            </td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td><x-badge :status="$order->current_status" /></td>
                            <td class="text-end pe-4 fw-semibold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Widgets -->
    <div class="col-lg-4 space-y-4 d-flex flex-column gap-4">
        
        <!-- Doughnut Chart Area -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Distribusi Pesanan</h5>
            </div>
            <div class="card-body d-flex align-items-center">
                <div class="w-50">
                    <ul class="list-unstyled mb-0 small space-y-2 gap-2 d-flex flex-column">
                        <li class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-success d-inline-block" style="width:10px;height:10px;"></span> 
                            Selesai ({{ $statusBreakdownUI['pengiriman']['percent'] ?? 0 }}%)
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-primary d-inline-block" style="width:10px;height:10px;"></span> 
                            Diproses ({{ 100 - ($statusBreakdownUI['pengiriman']['percent'] ?? 0) - ($summary['total_orders'] > 0 ? round(($summary['archived']/$summary['total_orders'])*100) : 0) }}%)
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-secondary d-inline-block" style="width:10px;height:10px;"></span> 
                            Arsip ({{ $summary['total_orders'] > 0 ? round(($summary['archived']/$summary['total_orders'])*100) : 0 }}%)
                        </li>
                    </ul>
                </div>
                <div class="w-50 d-flex justify-content-center">
                    <canvas id="orderDistributionChart" style="max-height: 120px; max-width: 120px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="card border-0 shadow-sm flex-grow-1">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Batas Waktu Mendekat (Aktif)</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
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
                        <a href="{{ route('orders.show', $order) }}" class="list-group-item list-group-item-action border-start border-4 {{ $borderClass }} py-3">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-semibold">#{{ $order->order_number }}</h6>
                                <span class="badge bg-{{ str_replace('text-', '', $textClass) }} bg-opacity-10 {{ $textClass }}">{{ $badgeText }}</span>
                            </div>
                            <p class="mb-0 small text-muted d-flex justify-content-between">
                                <span>{{ $order->customer_name }}</span>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}</span>
                            </p>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted small">Tidak ada pesanan aktif dengan batas waktu.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Shared Chart Options
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    // 1. Production Status Chart (Bar)
    const ctxProd = document.getElementById('productionStatusChart').getContext('2d');
    new Chart(ctxProd, {
        type: 'bar',
        data: {
            labels: ['Penerimaan', 'Persiapan', 'Produksi', 'Finishing', 'Pengiriman'],
            datasets: [{
                label: 'Jumlah Pesanan',
                data: [
                    {{ $statusBreakdownUI['penerimaan']['count'] ?? 0 }},
                    {{ $statusBreakdownUI['persiapan']['count'] ?? 0 }},
                    {{ $statusBreakdownUI['produksi']['count'] ?? 0 }},
                    {{ $statusBreakdownUI['finishing']['count'] ?? 0 }},
                    {{ $statusBreakdownUI['pengiriman']['count'] ?? 0 }}
                ],
                backgroundColor: [
                    '#94a3b8', // secondary
                    '#818cf8', // indigo/primary light
                    '#4f46e5', // primary
                    '#f59e0b', // warning
                    '#10b981'  // success
                ],
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Order Distribution Chart (Doughnut)
    const ctxDist = document.getElementById('orderDistributionChart').getContext('2d');
    const pctShipping = {{ $statusBreakdownUI['pengiriman']['percent'] ?? 0 }};
    const pctArchived = {{ $summary['total_orders'] > 0 ? round(($summary['archived']/$summary['total_orders'])*100) : 0 }};
    const pctInProgress = 100 - pctShipping - pctArchived;

    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Diproses', 'Arsip'],
            datasets: [{
                data: [pctShipping, pctInProgress, pctArchived],
                backgroundColor: ['#10b981', '#4f46e5', '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });

    // 3. Mock Revenue Chart (Line) - In a real scenario, we'd pull monthly data from a service
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    
    // Using current month revenue as the last data point, mock previous months for visual
    const currentRev = {{ $summary['monthly_revenue'] }};
    const mockData = [
        currentRev * 0.7, currentRev * 0.8, currentRev * 0.75, 
        currentRev * 0.9, currentRev * 0.85, currentRev
    ];
    
    const months = [];
    for(let i=5; i>=0; i--) {
        const d = new Date();
        d.setMonth(d.getMonth() - i);
        months.push(d.toLocaleString('id-ID', { month: 'short' }));
    }

    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: mockData,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush