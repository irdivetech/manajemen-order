@extends('layouts.app')

@section('title', 'Best Seller')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Best Seller</li>
@endsection

@push('styles')
<style>
    :root {
        --surface: #ffffff;
        --surface-hover: #f8fafc;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animated-fade-in {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* Premium Summary Cards */
    .premium-stat-card {
        background: var(--surface);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .premium-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
    .premium-stat-card::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--glow-color) 0%, transparent 70%);
        opacity: 0.15;
        z-index: 0;
        transition: transform 0.6s ease;
    }
    .premium-stat-card:hover::before { transform: scale(1.5); }
    .premium-stat-card > * { position: relative; z-index: 1; }

    .stat-icon-wrapper {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; flex-shrink: 0;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.5), 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .card-title-label {
        font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }
    .card-value {
        font-size: 2.25rem; font-weight: 900; color: var(--text-primary); line-height: 1; letter-spacing: -0.03em;
    }

    /* Modern Rank Badges */
    .bs-rank-badge {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        text-shadow: 0 1px 2px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .bs-rank-1 { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .bs-rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; }
    .bs-rank-3 { background: linear-gradient(135deg, #fb7185, #e11d48); color: #fff; }
    .bs-rank-default { background: #f1f5f9; color: #64748b; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); text-shadow: none; border: 1px solid #e2e8f0; }

    /* Sleek Progress Bars */
    .bs-progress-bar {
        height: 6px;
        border-radius: 6px;
        background: #f1f5f9;
        overflow: hidden;
        flex-grow: 1;
        margin-top: 8px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    .bs-progress-fill {
        height: 100%;
        border-radius: 6px;
        transition: width 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        /* Default width 0 for animation */
        width: 0;
    }
    .bs-progress-fill::after {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shine 2s infinite linear;
    }
    @keyframes shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .fill-blue { background: linear-gradient(90deg, #60a5fa, #2563eb); }
    .fill-green { background: linear-gradient(90deg, #34d399, #059669); }
    .fill-orange { background: linear-gradient(90deg, #fbbf24, #ea580c); }

    /* Interactive List Items */
    .bs-item {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem;
        border: 1px solid transparent;
        border-bottom-color: var(--border-color);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
    }
    .bs-item:last-child { border-bottom-color: transparent; }
    .bs-item:hover { 
        background-color: var(--surface); 
        border-color: var(--border-color);
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.025);
        transform: scale(1.02);
        z-index: 10;
        position: relative;
    }

    .bs-item-info { flex-grow: 1; min-width: 0; }
    .bs-item-name { font-weight: 800; font-size: 1.05rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.01em; }
    .bs-item-meta { font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px; display: flex; align-items: center; gap: 6px; font-weight: 500; }
    .bs-item-qty {
        font-weight: 800; font-size: 1.1rem; color: var(--text-primary);
        white-space: nowrap; flex-shrink: 0;
        background: #f8fafc; padding: 6px 12px; border-radius: 10px; border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    /* Premium Empty State */
    .bs-empty {
        text-align: center;
        padding: 5rem 2rem;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        margin: 1.5rem;
    }
    .bs-empty i { font-size: 4rem; margin-bottom: 1.25rem; display: block; color: #94a3b8; opacity: 0.4; }
    .bs-empty-text { font-weight: 700; color: #475569; font-size: 1.25rem; }
    .bs-empty-subtext { font-size: 0.9rem; color: #94a3b8; margin-top: 0.5rem; }

    /* Customizing the Card container */
    .premium-container-card {
        background: var(--surface);
        border-radius: 24px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .premium-container-header {
        padding: 1.5rem 1.5rem 1rem;
        background: #fff;
        display: flex; align-items: center; gap: 0.75rem;
        font-weight: 800; font-size: 1.25rem; color: var(--text-primary);
        letter-spacing: -0.02em;
        border-bottom: 1px solid #f1f5f9;
    }
    .premium-container-header i { font-size: 1.5rem; }
    .premium-container-body {
        padding: 0.75rem;
        flex-grow: 1;
        background: #fafbfd;
    }

    /* Filter UI tweaks */
    .filter-wrapper {
        background: var(--surface);
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -2px rgba(0,0,0,0.02);
        border: 1px solid #e2e8f0;
    }
    .filter-label { font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: block; }
    .custom-select, .custom-input {
        border-radius: 12px; border: 1px solid #cbd5e1; padding: 0.6rem 1.25rem; font-size: 0.95rem; transition: all 0.2s;
        background: #f8fafc; color: #0f172a; font-weight: 600; width: 100%;
    }
    .custom-select:focus, .custom-input:focus {
        border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); outline: none;
    }
    .btn-apply {
        background: #0f172a; color: white; border: none; border-radius: 12px; padding: 0.6rem 1.5rem; font-weight: 700; transition: all 0.2s;
        display: flex; align-items: center; gap: 0.5rem; height: 100%;
    }
    .btn-apply:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(15,23,42,0.2); color: white; }
</style>
@endpush

@section('content')
{{-- Filter Bar --}}
<div class="row mb-4 animated-fade-in">
    <div class="col-12">
        <div class="filter-wrapper">
            <form action="{{ route('best-sellers.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label class="filter-label"><i class="bi bi-calendar3 me-1"></i> Periode Waktu</label>
                    <select name="period" class="custom-select form-select" onchange="
                        if(this.value !== 'custom') this.form.submit();
                        else {
                            document.getElementById('customDateFields').classList.remove('d-none');
                            document.getElementById('customDateFields').classList.add('d-flex');
                        }
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

                <div id="customDateFields" class="col-md-8 col-lg-9 {{ $period === 'custom' ? 'd-flex' : 'd-none' }} flex-wrap gap-3">
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <label class="filter-label">Dari Tanggal</label>
                        <input type="date" name="from" class="custom-input form-control" value="{{ request('from') }}">
                    </div>
                    <div class="flex-grow-1" style="min-width: 150px;">
                        <label class="filter-label">Hingga</label>
                        <input type="date" name="to" class="custom-input form-control" value="{{ request('to') }}">
                    </div>
                    <div class="align-self-end mt-3 mt-md-0">
                        <button type="submit" class="btn-apply"><i class="bi bi-funnel-fill"></i> Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-4 animated-fade-in delay-1">
        <div class="premium-stat-card" style="--glow-color: #3b82f6;">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #2563eb; border: 1px solid #bfdbfe;">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div>
                <div class="card-title-label">Total Jenis Baju</div>
                <div class="card-value">{{ number_format($summary['total_categories']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 animated-fade-in delay-2">
        <div class="premium-stat-card" style="--glow-color: #f59e0b;">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #d97706; border: 1px solid #fde68a;">
                <i class="bi bi-palette-fill"></i>
            </div>
            <div>
                <div class="card-title-label">Total Warna Unik</div>
                <div class="card-value">{{ number_format($summary['total_colors']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 animated-fade-in delay-3">
        <div class="premium-stat-card" style="--glow-color: #10b981;">
            <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); color: #059669; border: 1px solid #a7f3d0;">
                <i class="bi bi-stack"></i>
            </div>
            <div>
                <div class="card-title-label">Total Jenis Bahan</div>
                <div class="card-value">{{ number_format($summary['total_materials']) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Best Seller Tables --}}
<div class="row g-4 mb-5">
    {{-- Top Jenis Baju --}}
    <div class="col-lg-4 animated-fade-in delay-1">
        <div class="premium-container-card">
            <div class="premium-container-header">
                <i class="bi bi-trophy-fill" style="color: #f59e0b; text-shadow: 0 2px 4px rgba(245,158,11,0.3);"></i> Top Jenis Baju
            </div>
            <div class="premium-container-body">
                @if($topClothingCategories->isEmpty())
                    <div class="bs-empty">
                        <i class="bi bi-inbox"></i>
                        <div class="bs-empty-text">Belum Ada Data</div>
                        <div class="bs-empty-subtext">Coba ubah periode waktu untuk melihat hasil.</div>
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
                            <div class="bs-item-meta"><i class="bi bi-cart2"></i> {{ number_format($item->total_orders) }} pesanan</div>
                            <div class="bs-progress-bar">
                                <div class="bs-progress-fill fill-blue" data-width="{{ round(($item->total_quantity / $maxQtyCategory) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="bs-item-qty">{{ number_format($item->total_quantity) }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Top Warna --}}
    <div class="col-lg-4 animated-fade-in delay-2">
        <div class="premium-container-card">
            <div class="premium-container-header">
                <i class="bi bi-palette-fill" style="color: #ec4899; text-shadow: 0 2px 4px rgba(236,72,153,0.3);"></i> Top Warna
            </div>
            <div class="premium-container-body">
                @if($topColors->isEmpty())
                    <div class="bs-empty">
                        <i class="bi bi-inbox"></i>
                        <div class="bs-empty-text">Belum Ada Data</div>
                        <div class="bs-empty-subtext">Coba ubah periode waktu untuk melihat hasil.</div>
                    </div>
                @else
                    @php $maxQtyColor = $topColors->max('total_quantity') ?: 1; @endphp
                    @foreach($topColors as $i => $item)
                    <div class="bs-item">
                        <div class="bs-rank-badge {{ $i < 3 ? 'bs-rank-' . ($i + 1) : 'bs-rank-default' }}">
                            {{ $i + 1 }}
                        </div>
                        <div class="bs-item-info">
                            <div class="bs-item-name">{{ ucfirst($item->color) }}</div>
                            <div class="bs-item-meta"><i class="bi bi-cart2"></i> {{ number_format($item->total_orders) }} pesanan</div>
                            <div class="bs-progress-bar">
                                <div class="bs-progress-fill fill-orange" data-width="{{ round(($item->total_quantity / $maxQtyColor) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="bs-item-qty">{{ number_format($item->total_quantity) }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Top Bahan --}}
    <div class="col-lg-4 animated-fade-in delay-3">
        <div class="premium-container-card">
            <div class="premium-container-header">
                <i class="bi bi-stack" style="color: #10b981; text-shadow: 0 2px 4px rgba(16,185,129,0.3);"></i> Top Bahan
            </div>
            <div class="premium-container-body">
                @if($topMaterials->isEmpty())
                    <div class="bs-empty">
                        <i class="bi bi-inbox"></i>
                        <div class="bs-empty-text">Belum Ada Data</div>
                        <div class="bs-empty-subtext">Coba ubah periode waktu untuk melihat hasil.</div>
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
                            <div class="bs-item-meta"><i class="bi bi-cart2"></i> {{ number_format($item->total_orders) }} pesanan</div>
                            <div class="bs-progress-bar">
                                <div class="bs-progress-fill fill-green" data-width="{{ round(($item->total_quantity / $maxQtyMaterial) * 100) }}%"></div>
                            </div>
                        </div>
                        <div class="bs-item-qty">{{ number_format($item->total_quantity) }}</div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Animate progress bars on load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const progressFills = document.querySelectorAll('.bs-progress-fill');
            progressFills.forEach(fill => {
                const targetWidth = fill.getAttribute('data-width');
                if (targetWidth) {
                    fill.style.width = targetWidth;
                }
            });
        }, 300); // Wait for fade-in animations to finish mostly
    });
</script>
@endpush
@endsection
