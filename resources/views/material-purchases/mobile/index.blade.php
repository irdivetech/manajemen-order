@extends('layouts.mobile')

@section('title', 'Belanja Bahan')

@section('content')
<div class="p-3 mb-2 bg-white sticky-top shadow-sm z-1">
    <form action="{{ route('material-purchases.index') }}" method="GET" id="filterForm">
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label text-xs mb-1 text-muted">Filter By</label>
                <select name="date_column" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                    <option value="order_date" {{ request('date_column', 'order_date') === 'order_date' ? 'selected' : '' }}>Tgl Pesan</option>
                    <option value="deadline" {{ request('date_column') === 'deadline' ? 'selected' : '' }}>Tenggat</option>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label text-xs mb-1 text-muted">Periode</label>
                <select name="period" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                    <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ request('period', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
        </div>
    </form>
</div>

<div class="px-3 pb-3">
    <!-- Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-12">
            <div class="card border-warning shadow-sm">
                <div class="card-body p-3">
                    <p class="text-xs text-muted mb-1">Estimasi Cash Disiapkan</p>
                    <h4 class="mb-0 fw-bold text-warning">Rp {{ number_format((float) $totalEstimatedCash, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card {{ $totalVariance > 0 ? 'border-danger' : 'border-success' }} shadow-sm">
                <div class="card-body p-3">
                    <p class="text-xs text-muted mb-1">Status Harga Bahan</p>
                    @if($totalVariance > 0)
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle text-danger"></i>
                            <span class="text-danger fw-semibold text-sm">{{ $totalVariance }} Pesanan Naik Harga</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <span class="text-success fw-semibold text-sm">Semua Harga Aman</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion List -->
    <div class="accordion" id="materialAccordionMobile">
        @forelse($summary as $index => $item)
            @php 
                $materialOrders = $ordersByMaterial->get($item->material_id, collect()); 
            @endphp
            <div class="accordion-item mb-2 border rounded overflow-hidden shadow-sm">
                <h2 class="accordion-header" id="headingMob{{ $index }}">
                    <button class="accordion-button collapsed p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMob{{ $index }}" aria-expanded="false" aria-controls="collapseMob{{ $index }}">
                        <div class="w-100 pe-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark">{{ $item->material_name }}</span>
                                <span class="fw-bold text-warning text-sm">Rp {{ number_format($item->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark border">{{ (float) $item->total_usage_meter }} m</span>
                                <span class="text-muted text-xs">@ Rp {{ number_format($item->current_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapseMob{{ $index }}" class="accordion-collapse collapse" aria-labelledby="headingMob{{ $index }}" data-bs-parent="#materialAccordionMobile">
                    <div class="accordion-body p-0 bg-light">
                        <ul class="list-group list-group-flush">
                            @foreach($materialOrders as $order)
                                @php
                                    $usageForThisOrder = 0;
                                    foreach($order->sizeDetails as $detail) {
                                        $est = \App\Models\MaterialUsageEstimate::where('material_id', $order->material_id)
                                            ->where('clothing_category_id', $order->clothing_category_id)
                                            ->where('size_id', $detail->size_id)
                                            ->first();
                                        if($est) {
                                            $usageForThisOrder += ($est->estimated_usage * $detail->quantity);
                                        }
                                    }
                                    
                                    $newEstimatedCost = $usageForThisOrder * ($order->masterMaterial ? $order->masterMaterial->price_per_unit : 0);
                                    $isPriceHigher = $newEstimatedCost > ($order->total_cost + 1);
                                    
                                    $displayOldPrice = $order->material_price_snapshot;
                                    if (!$displayOldPrice && $usageForThisOrder > 0) {
                                        $displayOldPrice = $order->total_cost / $usageForThisOrder;
                                    }
                                @endphp
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-decoration-none d-block">{{ $order->order_number }}</a>
                                            <span class="text-xs text-muted">{{ $order->customer_name }}</span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ (float) $usageForThisOrder }} m</span>
                                    </div>
                                    
                                    @if($isPriceHigher)
                                    <div class="alert alert-danger py-2 px-3 mb-3 text-xs d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-arrow-up-short"></i> Naik dr Rp {{ number_format($displayOldPrice, 0, ',', '.') }}</span>
                                        <form action="{{ route('material-purchases.sync-hpp', $order) }}" method="POST" onsubmit="return confirm('Update HPP?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-xs border-0 py-0 text-decoration-underline">Sync</button>
                                        </form>
                                    </div>
                                    @endif

                                    <form action="{{ route('material-purchases.mark-purchased', $order) }}" method="POST" onsubmit="return confirm('Tandai sudah dibeli?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-check2-square"></i> Tandai Sudah Dibeli
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                <p class="mt-2 text-muted text-sm">Semua bahan sudah dibeli.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
