@extends('layouts.app')

@section('title', 'Best Seller')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Best Seller</li>
@endsection

@push('styles')
<style>
    .bs-rank-badge {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem;
        flex-shrink: 0;
    }
    .bs-rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .bs-rank-2 { background: linear-gradient(135deg, #9ca3af, #6b7280); color: #fff; }
    .bs-rank-3 { background: linear-gradient(135deg, #b45309, #92400e); color: #fff; }
    .bs-rank-default { background: #f3f4f6; color: #6b7280; }

    .bs-progress-bar {
        height: 8px;
        border-radius: 4px;
        background: #f3f4f6;
        overflow: hidden;
        flex-grow: 1;
    }
    .bs-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bs-progress-fill.fill-primary { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
    .bs-progress-fill.fill-success { background: linear-gradient(90deg, #10b981, #34d399); }
    .bs-progress-fill.fill-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .color-swatch {
        width: 20px; height: 20px;
        border-radius: 6px;
        border: 2px solid #e5e7eb;
        flex-shrink: 0;
    }

    .bs-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        transition: background-color 0.15s ease;
    }
    .bs-item:last-child { border-bottom: none; }
    .bs-item:hover { background-color: #fafbfd; margin: 0 -1rem; padding-left: 1rem; padding-right: 1rem; border-radius: 8px; }

    .bs-item-info { flex-grow: 1; min-width: 0; }
    .bs-item-name { font-weight: 600; font-size: 0.875rem; color: #1f2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bs-item-meta { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }
    .bs-item-qty {
        font-weight: 700; font-size: 0.9rem; color: #1f2937;
        white-space: nowrap; flex-shrink: 0;
    }

    .bs-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #9ca3af;
    }
    .bs-empty i { font-size: 2.5rem; margin-bottom: 0.5rem; display: block; }

    .summary-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
</style>
@endpush

@section('content')
{{-- Filter Bar --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <x-card>
            <form action="{{ route('best-sellers.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Periode Waktu</label>
                    <select name="period" class="form-select form-select-sm" onchange="
                        if(this.value !== 'custom') this.form.submit();
                        else document.getElementById('customDateFields').classList.remove('d-none');
                    ">
                        <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="3_months" {{ $period === '3_months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                        <option value="6_months" {{ $period === '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                        <option value="1_year" {{ $period === '1_year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Pilih Tanggal Manual</option>
                    </select>
                </div>

                <div id="customDateFields" class="{{ $period === 'custom' ? '' : 'd-none' }} col-md-6">
                    <div class="row g-2 align-items-end">
                        <div class="col">
                            <label class="form-label small text-muted">Dari Tanggal</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                        </div>
                        <div class="col">
                            <label class="form-label small text-muted">Hingga</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="summary-icon" style="background: #eef2ff; color: var(--primary);">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Jenis Baju</div>
                <div class="fw-bold fs-4">{{ $summary['total_categories'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="summary-icon" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-palette-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Warna Unik</div>
                <div class="fw-bold fs-4">{{ $summary['total_colors'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="summary-icon" style="background: #d1fae5; color: #059669;">
                <i class="bi bi-stack"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold">Jenis Bahan</div>
                <div class="fw-bold fs-4">{{ $summary['total_materials'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Best Seller Tables --}}
<div class="row g-4">
    {{-- Top Jenis Baju --}}
    <div class="col-lg-4">
        <x-card>
            <x-slot name="title">
                <i class="bi bi-trophy-fill text-warning me-1"></i> Top Jenis Baju
            </x-slot>

            @if($topClothingCategories->isEmpty())
                <div class="bs-empty">
                    <i class="bi bi-inbox"></i>
                    <div>Belum ada data untuk periode ini</div>
                </div>
            @else
                @php $maxQtyCategory = $topClothingCategories->max('total_quantity') ?: 1; @endphp
                @foreach($topClothingCategories as $i => $item)
                <div class="bs-item">
                    <div class="bs-rank-badge {{ $i < 3 ? 'bs-rank-' . ($i + 1) : 'bs-rank-default' }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="bs-item-info">
                        <div class="bs-item-name">{{ $item->name }}</div>
                        <div class="bs-item-meta">{{ $item->total_orders }} pesanan</div>
                        <div class="bs-progress-bar mt-1">
                            <div class="bs-progress-fill fill-primary" style="width: {{ round(($item->total_quantity / $maxQtyCategory) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="bs-item-qty">{{ number_format($item->total_quantity) }} pcs</div>
                </div>
                @endforeach
            @endif
        </x-card>
    </div>

    {{-- Top Warna --}}
    <div class="col-lg-4">
        <x-card>
            <x-slot name="title">
                <i class="bi bi-palette-fill text-warning me-1"></i> Top Warna
            </x-slot>

            @if($topColors->isEmpty())
                <div class="bs-empty">
                    <i class="bi bi-inbox"></i>
                    <div>Belum ada data untuk periode ini</div>
                </div>
            @else
                @php $maxQtyColor = $topColors->max('total_quantity') ?: 1; @endphp
                @foreach($topColors as $i => $item)
                <div class="bs-item">
                    <div class="bs-rank-badge {{ $i < 3 ? 'bs-rank-' . ($i + 1) : 'bs-rank-default' }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="bs-item-info">
                        <div class="bs-item-name d-flex align-items-center gap-2">
                            {{ ucfirst($item->color) }}
                        </div>
                        <div class="bs-item-meta">{{ $item->total_orders }} pesanan</div>
                        <div class="bs-progress-bar mt-1">
                            <div class="bs-progress-fill fill-warning" style="width: {{ round(($item->total_quantity / $maxQtyColor) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="bs-item-qty">{{ number_format($item->total_quantity) }} pcs</div>
                </div>
                @endforeach
            @endif
        </x-card>
    </div>

    {{-- Top Bahan --}}
    <div class="col-lg-4">
        <x-card>
            <x-slot name="title">
                <i class="bi bi-stack text-warning me-1"></i> Top Bahan
            </x-slot>

            @if($topMaterials->isEmpty())
                <div class="bs-empty">
                    <i class="bi bi-inbox"></i>
                    <div>Belum ada data untuk periode ini</div>
                </div>
            @else
                @php $maxQtyMaterial = $topMaterials->max('total_quantity') ?: 1; @endphp
                @foreach($topMaterials as $i => $item)
                <div class="bs-item">
                    <div class="bs-rank-badge {{ $i < 3 ? 'bs-rank-' . ($i + 1) : 'bs-rank-default' }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="bs-item-info">
                        <div class="bs-item-name">{{ $item->name }}</div>
                        <div class="bs-item-meta">{{ $item->total_orders }} pesanan</div>
                        <div class="bs-progress-bar mt-1">
                            <div class="bs-progress-fill fill-success" style="width: {{ round(($item->total_quantity / $maxQtyMaterial) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="bs-item-qty">{{ number_format($item->total_quantity) }} pcs</div>
                </div>
                @endforeach
            @endif
        </x-card>
    </div>
</div>
@endsection
