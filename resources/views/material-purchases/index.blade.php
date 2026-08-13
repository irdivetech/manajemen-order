@extends('layouts.app')

@section('title', 'Belanja Bahan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Belanja Bahan</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <x-card>
            <form action="{{ route('material-purchases.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Filter Berdasarkan</label>
                    <select name="date_column" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="order_date" {{ request('date_column', 'order_date') === 'order_date' ? 'selected' : '' }}>Tanggal Pesan</option>
                        <option value="deadline" {{ request('date_column') === 'deadline' ? 'selected' : '' }}>Tenggat Waktu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Periode Waktu</label>
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ request('period', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Pilih Tanggal Manual</option>
                    </select>
                </div>
                
                @if(request('period') === 'custom')
                <div class="col-md-2">
                    <label class="form-label small text-muted">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Hingga</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                </div>
                @endif
            </form>
        </x-card>
    </div>
</div>

@php
    $periodLabels = [
        'daily' => 'Harian',
        'weekly' => 'Mingguan',
        'monthly' => 'Bulanan',
        'yearly' => 'Tahunan',
        'custom' => 'Kustom'
    ];
    $periodStr = (string) $period;
    $periodName = $periodLabels[$periodStr] ?? ucfirst($periodStr);
@endphp

<div class="row g-4 mb-4">
    <!-- Total Cost Card -->
    <div class="col-md-6">
        <div class="stat-card border-warning">
            <h6 class="text-muted mb-3 fw-semibold">Estimasi Uang Cash Disiapkan ({{ $periodName }})</h6>
            <h3 class="fw-bold text-warning mb-0">Rp {{ number_format((float) $totalEstimatedCash, 0, ',', '.') }}</h3>
        </div>
    </div>
    <!-- Variances Card -->
    <div class="col-md-6">
        <div class="stat-card {{ $totalVariance > 0 ? 'border-danger' : 'border-success' }}">
            <h6 class="text-muted mb-3 fw-semibold">Pesanan Terdampak Kenaikan Harga</h6>
            <h3 class="fw-bold {{ $totalVariance > 0 ? 'text-danger' : 'text-success' }} mb-0">{{ $totalVariance }} Pesanan</h3>
            @if($totalVariance > 0)
                <p class="text-danger small mt-2 mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Terdapat pesanan yang HPP-nya di bawah harga pasar saat ini.</p>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <x-card>
            <x-slot name="title">Daftar Bahan Yang Harus Dibeli (Belum Dibeli)</x-slot>

            <div class="accordion mt-3" id="materialAccordion">
                @forelse($summary as $index => $item)
                    @php 
                        $materialOrders = $ordersByMaterial->get($item->material_id, collect()); 
                    @endphp
                    <div class="accordion-item border-0 mb-3 rounded shadow-sm">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $item->material_name }}</h6>
                                        <small class="text-muted">Total Kebutuhan: {{ (float) $item->total_usage_meter }} meter</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-warning">Rp {{ number_format($item->total_estimated_cost, 0, ',', '.') }}</div>
                                        <small class="text-muted">@ Rp {{ number_format($item->current_price, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#materialAccordion">
                            <div class="accordion-body bg-light">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle bg-white mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No Pesanan</th>
                                                <th>Pelanggan</th>
                                                <th>Produk</th>
                                                <th>Estimasi (Meter)</th>
                                                <th>Status Harga</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                                    
                                                    // Determine what the old price likely was for display
                                                    $displayOldPrice = $order->material_price_snapshot;
                                                    if (!$displayOldPrice && $usageForThisOrder > 0) {
                                                        $displayOldPrice = $order->total_cost / $usageForThisOrder;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                                                    <td>{{ $order->customer_name }}</td>
                                                    <td>{{ $order->product_name }}</td>
                                                    <td>{{ (float) $usageForThisOrder }} m</td>
                                                    <td>
                                                        @if($isPriceHigher)
                                                            <span class="badge bg-danger"><i class="bi bi-arrow-up-circle"></i> Naik (Dulu: Rp {{ number_format($displayOldPrice, 0, ',', '.') }})</span>
                                                        @else
                                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aman</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex gap-2 justify-content-end">
                                                            @if($isPriceHigher)
                                                            <form action="{{ route('material-purchases.sync-hpp', $order) }}" method="POST" onsubmit="return confirm('Update HPP order ini menggunakan harga bahan pasar saat ini? Margin profit di laporan akan berkurang menyesuaikan modal baru.');">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Sync HPP ke Harga Pasar">
                                                                    <i class="bi bi-arrow-repeat"></i> Sync HPP
                                                                </button>
                                                            </form>
                                                            @endif
                                                            
                                                            <form action="{{ route('material-purchases.mark-purchased', $order) }}" method="POST" onsubmit="return confirm('Tandai bahan untuk pesanan ini sudah dibeli?');">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-check2-square"></i> Tandai Dibeli
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bag-check fs-1"></i>
                        <p class="mt-3">Semua bahan pesanan sudah dibeli atau tidak ada pesanan baru.</p>
                    </div>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
@endsection
