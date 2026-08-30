@extends('layouts.app')

@section('title', 'Dasbor Eksekutif (Pemilik)')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dasbor Pemilik</li>
@endsection

@section('content')
<!-- Welcome Header -->
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark mb-1">Tinjauan Bisnis Eksekutif</h2>
        <p class="text-muted mb-0">Metrik performa real-time untuk pusat manufaktur StitchFlow Anda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form method="GET" action="{{ route('dashboard.index') }}" class="m-0 d-flex align-items-center">
            <select name="range" class="form-select bg-white shadow-sm border-0" onchange="this.form.submit()" style="cursor:pointer; min-width: 160px; height: 38px;">
                <option value="30_days" {{ $range == '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="this_month" {{ $range == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="this_year" {{ $range == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                <option value="all_time" {{ $range == 'all_time' ? 'selected' : '' }}>Sepanjang Waktu</option>
            </select>
        </form>
        <a href="{{ route('dashboard.export', ['range' => $range]) }}" target="_blank" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" style="height: 38px;">
            <i class="bi bi-download"></i> Ekspor PDF
        </a>
    </div>
</div>

<!-- KPI Cards Row -->
<div class="row g-4 mb-4">
    <!-- Total Orders -->
    <div class="col-md-4 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                        <i class="bi bi-cart fs-5"></i>
                    </div>
                    <span class="badge {{ str_contains($ownerSummary['kpi_orders_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} px-2 py-1 rounded-pill">
                        <i class="bi {{ str_contains($ownerSummary['kpi_orders_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i> 
                        {{ $ownerSummary['kpi_orders_growth'] }}
                    </span>
                </div>
                <p class="text-muted small text-uppercase fw-semibold mb-1">Total Pesanan</p>
                <h3 class="fw-bold mb-0">{{ $ownerSummary['kpi_total_orders'] }}</h3>
                <p class="text-muted small mt-2 mb-0 fst-italic">Mencerminkan siklus produksi bulan ini</p>
            </div>
        </div>
    </div>
    
    <!-- Completed Orders -->
    <div class="col-md-4 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded p-2">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <span class="badge {{ str_contains($ownerSummary['kpi_completed_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} px-2 py-1 rounded-pill">
                        <i class="bi {{ str_contains($ownerSummary['kpi_completed_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i> 
                        {{ $ownerSummary['kpi_completed_growth'] }}
                    </span>
                </div>
                <p class="text-muted small text-uppercase fw-semibold mb-1">Pesanan Selesai</p>
                <h3 class="fw-bold mb-0">{{ $ownerSummary['kpi_completed_orders'] }}</h3>
                <p class="text-muted small mt-2 mb-0 fst-italic">Tingkat pemenuhan pesanan terus dipantau</p>
            </div>
        </div>
    </div>
    
    <!-- Monthly Revenue -->
    <div class="col-md-4 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded p-2">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                    <span class="badge {{ str_contains($ownerSummary['kpi_revenue_growth'], '-') ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} px-2 py-1 rounded-pill">
                        <i class="bi {{ str_contains($ownerSummary['kpi_revenue_growth'], '-') ? 'bi-graph-down' : 'bi-graph-up' }}"></i> 
                        {{ $ownerSummary['kpi_revenue_growth'] }}
                    </span>
                </div>
                <p class="text-muted small text-uppercase fw-semibold mb-1">Pendapatan Bulan Ini</p>
                <h3 class="fw-bold fs-4 mb-0">{{ $ownerSummary['kpi_monthly_revenue'] }}</h3>
                <p class="text-muted small mt-2 mb-0 fst-italic">Proyeksi pemasukan aktif</p>
            </div>
        </div>
    </div>
    

</div>

<div class="row g-4 mb-4">
    <!-- Chart: Pendapatan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Grafik Pendapatan & Profit Margin</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" style="height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Chart: Market Split -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Distribusi Pasar (B2B vs Retail)</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div class="position-relative w-100 d-flex justify-content-center" style="height: 200px;">
                    <canvas id="marketSplitChart"></canvas>
                </div>
                <div class="d-flex w-100 justify-content-center gap-4 mt-4">
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="rounded-circle bg-primary" style="width:12px;height:12px;"></span>
                            <span class="small fw-semibold">B2B (Grosir)</span>
                        </div>
                        <h4 class="fw-bold mb-0">{{ $ownerSummary['b2b_percentage'] }}</h4>
                    </div>
                    <div class="text-center">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="rounded-circle bg-info" style="width:12px;height:12px;"></span>
                            <span class="small fw-semibold">Retail</span>
                        </div>
                        <h4 class="fw-bold mb-0">{{ $ownerSummary['retail_percentage'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Center Column: Data & Lists -->
    <div class="col-lg-8 d-flex flex-column gap-4">
        
        <!-- Top Clients Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fs-6 fw-bold">Klien Teratas (Berdasarkan Volume)</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topClients as $client)
                    <li class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 40px; height: 40px;">
                                {{ substr($client['name'], 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $client['name'] }}</h6>
                                <p class="mb-0 small text-muted">{{ $client['orders'] }} Pesanan Aktif</p>
                            </div>
                        </div>
                        <div class="text-end w-25">
                            <h6 class="mb-1 fw-bold">{{ $client['revenue'] }}</h6>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $client['contribution_percentage'] }}%" aria-valuenow="{{ $client['contribution_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted">Belum ada data klien teratas.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Recent Batches Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Batch Produksi Selesai Terkini</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">No. Batch</th>
                            <th>Produk</th>
                            <th>Kuantitas</th>
                            <th>Status QC</th>
                            <th class="text-end pe-4">Waktu Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($recentBatches as $batch)
                        <tr>
                            <td class="ps-4 fw-medium text-primary">
                                <a href="{{ route('orders.show', $batch->id) }}" class="text-decoration-none">{{ $batch->order_number }}</a>
                            </td>
                            <td>{{ $batch->product_type ?? 'Lainnya' }}</td>
                            <td>{{ number_format($batch->quantity, 0, ',', '.') }} pcs</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ Str::title(str_replace('_', ' ', $batch->current_status)) }}</span></td>
                            <td class="text-end pe-4 text-muted small">{{ $batch->updated_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada batch produksi yang selesai baru-baru ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Right Sidebar Widgets -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        
        <!-- Product Mix Distribution -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fs-6 fw-bold">Bauran Produk (Product Mix)</h5>
            </div>
            <div class="card-body">
                <div class="position-relative d-flex justify-content-center mb-4" style="height: 180px;">
                    <canvas id="productMixChart"></canvas>
                </div>
                <div class="d-flex flex-column gap-3">
                    @php 
                        $colors = ['bg-primary', 'bg-info', 'bg-warning', 'bg-secondary', 'bg-dark']; 
                        $i = 0;
                    @endphp
                    @forelse($productMix as $mix)
                    <div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="d-flex align-items-center gap-2 fw-medium">
                                <span class="rounded-circle {{ $colors[$i % count($colors)] }}" style="width:10px;height:10px;"></span>
                                {{ $mix['name'] }}
                            </span>
                            <span class="fw-bold">{{ $mix['percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $colors[$i % count($colors)] }}" role="progressbar" style="width: {{ $mix['percentage'] }}%"></div>
                        </div>
                    </div>
                    @php $i++; @endphp
                    @empty
                    <p class="text-center text-muted small mb-0">Belum ada data bauran produk.</p>
                    @endforelse
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

    // 1. Real Revenue Trend Chart
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
                    pointRadius: 4,
                    yAxisID: 'y1',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000000).toFixed(0) + 'M';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Market Split Chart (Doughnut)
    const ctxMarket = document.getElementById('marketSplitChart').getContext('2d');
    const b2bPct = parseFloat('{{ $ownerSummary['b2b_percentage'] }}');
    const retailPct = parseFloat('{{ $ownerSummary['retail_percentage'] }}');

    new Chart(ctxMarket, {
        type: 'doughnut',
        data: {
            labels: ['B2B', 'Retail'],
            datasets: [{
                data: [b2bPct, retailPct],
                backgroundColor: ['#4f46e5', '#0ea5e9'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 3. Product Mix Chart (Pie)
    const ctxMix = document.getElementById('productMixChart').getContext('2d');
    
    // Parse Product Mix Data from PHP
    const productMixData = {!! json_encode($productMix) !!};
    const mixLabels = productMixData.map(item => item.name);
    const mixValues = productMixData.map(item => item.percentage);
    
    new Chart(ctxMix, {
        type: 'pie',
        data: {
            labels: mixLabels,
            datasets: [{
                data: mixValues,
                backgroundColor: ['#4f46e5', '#0ea5e9', '#f59e0b', '#64748b', '#0f172a'],
                borderWidth: 1,
                borderColor: '#ffffff'
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
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>