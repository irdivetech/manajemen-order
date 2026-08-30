@extends('layouts.mobile')

@section('title', 'Best Seller')

@section('page-header')
    <h1 class="mb-0">Best Seller</h1>
    <p class="page-sub">Produk terlaris berdasarkan {{ strtolower($periodLabel) }}</p>
@endsection

@section('content')
<div class="mb-3 sticky-top bg-white py-2 z-1">
    <form action="{{ route('best-sellers.index') }}" method="GET" id="filterForm">
        <label class="form-label text-xs mb-1 text-muted">Periode Waktu</label>
        <select name="period" class="form-select form-select-sm" onchange="if(this.value !== 'custom') document.getElementById('filterForm').submit()">
            <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
            <option value="3_months" {{ $period === '3_months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
            <option value="6_months" {{ $period === '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
            <option value="1_year" {{ $period === '1_year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
        </select>
    </form>
</div>

{{-- Summary --}}
<div class="row g-2 mb-4">
    <div class="col-4">
        <div class="m-card text-center mb-0 p-2 shadow-sm border-primary border-opacity-25" style="background-color: var(--bs-primary-bg-subtle);">
            <div class="text-xs text-muted mb-1">Kategori</div>
            <div class="fw-bold fs-5 text-primary">{{ number_format($summary['total_categories']) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="m-card text-center mb-0 p-2 shadow-sm border-warning border-opacity-25" style="background-color: var(--bs-warning-bg-subtle);">
            <div class="text-xs text-muted mb-1">Warna</div>
            <div class="fw-bold fs-5 text-warning">{{ number_format($summary['total_colors']) }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="m-card text-center mb-0 p-2 shadow-sm border-success border-opacity-25" style="background-color: var(--bs-success-bg-subtle);">
            <div class="text-xs text-muted mb-1">Bahan</div>
            <div class="fw-bold fs-5 text-success">{{ number_format($summary['total_materials']) }}</div>
        </div>
    </div>
</div>

{{-- Top Jenis Baju --}}
<div class="section-title"><i class="bi bi-trophy-fill text-warning me-1"></i> Top Jenis Baju</div>
<div class="m-card mb-4 shadow-sm">
    <div class="m-card-body p-0">
        @if($topClothingCategories->isEmpty())
            <div class="text-center p-4 text-muted text-sm">Belum Ada Data</div>
        @else
            @php $maxQtyCategory = $topClothingCategories->max('total_quantity') ?: 1; @endphp
            @foreach($topClothingCategories as $i => $item)
            <div class="m-list-item d-flex align-items-center gap-3">
                <div class="fw-bold" style="width:24px; text-align:center; color:{{ $i < 3 ? '#f59e0b' : '#9ca3af' }}">{{ $i + 1 }}</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-sm text-truncate">{{ $item->name }}</div>
                    <div class="text-xs text-muted">{{ number_format($item->total_orders) }} pesanan</div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: {{ round(($item->total_quantity / $maxQtyCategory) * 100) }}%"></div>
                    </div>
                </div>
                <div class="fw-bold text-sm">{{ number_format($item->total_quantity) }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Top Warna --}}
<div class="section-title"><i class="bi bi-palette-fill text-danger me-1"></i> Top Warna</div>
<div class="m-card mb-4 shadow-sm">
    <div class="m-card-body p-0">
        @if($topColors->isEmpty())
            <div class="text-center p-4 text-muted text-sm">Belum Ada Data</div>
        @else
            @php $maxQtyColor = $topColors->max('total_quantity') ?: 1; @endphp
            @foreach($topColors as $i => $item)
            <div class="m-list-item d-flex align-items-center gap-3">
                <div class="fw-bold" style="width:24px; text-align:center; color:{{ $i < 3 ? '#ef4444' : '#9ca3af' }}">{{ $i + 1 }}</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-sm text-truncate">{{ ucfirst($item->color) }}</div>
                    <div class="text-xs text-muted">{{ number_format($item->total_orders) }} pesanan</div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: {{ round(($item->total_quantity / $maxQtyColor) * 100) }}%"></div>
                    </div>
                </div>
                <div class="fw-bold text-sm">{{ number_format($item->total_quantity) }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Top Bahan --}}
<div class="section-title"><i class="bi bi-stack text-success me-1"></i> Top Bahan</div>
<div class="m-card mb-4 shadow-sm">
    <div class="m-card-body p-0">
        @if($topMaterials->isEmpty())
            <div class="text-center p-4 text-muted text-sm">Belum Ada Data</div>
        @else
            @php $maxQtyMaterial = $topMaterials->max('total_quantity') ?: 1; @endphp
            @foreach($topMaterials as $i => $item)
            <div class="m-list-item d-flex align-items-center gap-3">
                <div class="fw-bold" style="width:24px; text-align:center; color:{{ $i < 3 ? '#10b981' : '#9ca3af' }}">{{ $i + 1 }}</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-sm text-truncate">{{ $item->name }}</div>
                    <div class="text-xs text-muted">{{ number_format($item->total_orders) }} pesanan</div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ round(($item->total_quantity / $maxQtyMaterial) * 100) }}%"></div>
                    </div>
                </div>
                <div class="fw-bold text-sm">{{ number_format($item->total_quantity) }}</div>
            </div>
            @endforeach
        @endif
    </div>
</div>

<div style="height: 40px;"></div>
@endsection
