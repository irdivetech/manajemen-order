@extends('layouts.mobile')

@section('title', 'Laporan & Analitik')

@section('page-header')
    <h1>Laporan Keuangan</h1>
    <p class="page-sub">Analisis & ringkasan bisnis Anda</p>
@endsection

@section('content')

{{-- ── Filter Modal Trigger ── --}}
@php
    $periodLabels = ['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan', 'custom' => 'Kustom'];
    $periodName = $periodLabels[$period ?? 'monthly'] ?? ucfirst($period ?? 'monthly');
@endphp
<div class="mb-3">
    <button type="button" class="btn btn-light w-100 border text-start d-flex justify-content-between align-items-center"
            data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" style="border-radius:12px; padding:12px 16px;">
        <div>
            <div class="text-xs text-muted mb-1">Periode Waktu</div>
            <div class="fw-7 text-dark">{{ $periodName }}</div>
        </div>
        <i class="bi bi-chevron-down text-muted"></i>
    </button>
</div>

{{-- ── Key Metrics ── --}}
<div class="m-card mb-3" style="background:linear-gradient(135deg,#10b981,#059669); border:none;">
    <div class="m-card-body text-white">
        <div class="text-xs fw-6 text-uppercase mb-1" style="color:rgba(255,255,255,0.8); letter-spacing:0.05em;">Total Pendapatan</div>
        <div style="font-size:1.5rem; font-weight:700;">Rp {{ number_format((float) $totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="row g-2 mb-4">
    <div class="col-6">
        <div class="m-card h-100 border-primary">
            <div class="m-card-body py-3">
                <div class="text-xs text-muted fw-6 mb-1">Pesanan Aktif</div>
                <div class="fs-4 fw-7 text-primary">{{ $orders->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="m-card h-100 border-info">
            <div class="m-card-body py-3">
                <div class="text-xs text-muted fw-6 mb-1">Selesai/Dikirim</div>
                <div class="fs-4 fw-7 text-info">{{ $statusBreakdown['shipping'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Order List ── --}}
<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="section-title mb-0">Rincian Pesanan</div>
    <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-file-earmark-excel"></i> Excel
    </a>
</div>

<div class="m-card">
    <div class="m-card-body p-0">
        @forelse($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="m-list-item d-flex align-items-center gap-3 text-decoration-none text-dark">
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="fw-7 text-sm">{{ $order->order_number }}</span>
                    <span class="fw-7 text-sm text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="text-xs text-muted text-truncate mb-1">{{ $order->customer_name }} • {{ $order->product_name }}</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-xs text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $order->order_date?->format('d/m/y') }}</span>
                    <x-badge :status="$order->current_status" />
                </div>
            </div>
        </a>
        @empty
        <div class="empty-state py-4">
            <i class="bi bi-file-bar-graph"></i>
            <p>Tidak ada data untuk periode ini.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ── Filter Offcanvas (Bottom Sheet) ── --}}
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="filterOffcanvas" style="border-radius:20px 20px 0 0; min-height:50vh; height:auto;">
    <div class="d-flex justify-content-center pt-3 pb-1">
        <div style="width:36px;height:4px;background:#e5e7eb;border-radius:2px;"></div>
    </div>
    <div class="offcanvas-header pb-0 pt-2 px-4">
        <h5 class="offcanvas-title fw-7" style="font-size:1rem;">Pilih Periode Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-4" style="padding-bottom: calc(var(--nav-height, 64px) + 1.5rem) !important;">
                <form action="{{ route('reports.index') }}" method="GET" id="reportFilterForm">
                    <div class="d-flex flex-column gap-2 mb-4">
                        @foreach($periodLabels as $key => $label)
                        <label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer {{ request('period', 'monthly') === $key ? 'border-primary bg-primary bg-opacity-5' : '' }}"
                               style="cursor:pointer; transition:all 0.15s;">
                            <input type="radio" name="period" value="{{ $key }}" class="form-check-input m-0 flex-shrink-0"
                                   {{ request('period', 'monthly') === $key ? 'checked' : '' }}
                                   onchange="toggleCustomDates(this.value)" style="width:1.1em;height:1.1em;">
                            <div>
                                <div class="fw-6 {{ request('period', 'monthly') === $key ? 'text-primary' : '' }}" style="font-size:0.9rem;">{{ $label }}</div>
                                @php
                                    $periodDescs = ['daily'=>'Data hari ini','weekly'=>'7 hari terakhir','monthly'=>'30 hari terakhir','yearly'=>'365 hari terakhir','custom'=>'Tentukan rentang tanggal sendiri'];
                                @endphp
                                <div class="text-muted" style="font-size:0.75rem;">{{ $periodDescs[$key] ?? '' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-sm fw-6">Filter Berdasarkan</label>
                        <select name="date_column" class="form-select">
                            <option value="order_date" {{ request('date_column', 'order_date') === 'order_date' ? 'selected' : '' }}>Tanggal Pesan</option>
                            <option value="deadline" {{ request('date_column') === 'deadline' ? 'selected' : '' }}>Tenggat Waktu</option>
                        </select>
                    </div>

                    <div id="customDates" class="mb-4" style="display:{{ request('period') === 'custom' ? 'block' : 'none' }};">
                        <div class="mb-3">
                            <label class="form-label text-sm fw-6">Dari Tanggal</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div>
                            <label class="form-label text-sm fw-6">Hingga Tanggal</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-6" style="border-radius:12px; padding:0.8rem;">
                            <i class="bi bi-check2-circle me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleCustomDates(val) {
    const customDiv = document.getElementById('customDates');
    if (val === 'custom') {
        customDiv.style.display = 'block';
    } else {
        customDiv.style.display = 'none';
    }
}
</script>
@endpush
