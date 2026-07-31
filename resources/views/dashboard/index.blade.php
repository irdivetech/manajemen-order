@extends('layouts.app')

@section('title', 'Dasbor')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dasbor</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Total Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="text-muted mb-0 fw-semibold" style="font-size:0.85rem;">Total Pesanan</h6>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($summary['total_orders'], 0, ',', '.') }}</h3>
            <span class="text-success small fw-medium"><i class="bi bi-arrow-up-short"></i> Seluruh Waktu</span>
        </div>
    </div>

    <!-- Active Production -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="text-muted mb-0 fw-semibold" style="font-size:0.85rem;">Produksi Aktif</h6>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-gear-fill"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ number_format($summary['active_orders'], 0, ',', '.') }}</h3>
            <span class="text-muted small fw-medium">Sedang Diproses</span>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="text-muted mb-0 fw-semibold" style="font-size:0.85rem;">Total Pendapatan</h6>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</h3>
            <span class="text-success small fw-medium">Dari Faktur Lunas</span>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="text-muted mb-0 fw-semibold" style="font-size:0.85rem;">Menunggu Pembayaran</h6>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">Rp {{ number_format($summary['pending_payments'], 0, ',', '.') }}</h3>
            <span class="text-warning small fw-medium">Faktur Belum Lunas</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-card title="Pesanan Terbaru">
            <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0">
                    <thead class="border-bottom">
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-end">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-primary">{{ $order->order_number }}</a>
                            </td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td><x-badge :status="$order->current_status" /></td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada pesanan terbaru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <div class="col-lg-4">
        <x-card title="Mendekati Tenggat Waktu">
            <div class="d-flex flex-column gap-3">
                @forelse($nearingDeadline as $order)
                <div class="p-3 border border-warning border-opacity-50 bg-warning bg-opacity-10 rounded-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <a href="{{ route('orders.show', $order) }}" class="fw-semibold text-dark text-decoration-none">{{ $order->order_number }}</a>
                        <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($order->deadline)->diffForHumans() }}</span>
                    </div>
                    <div class="text-muted small">{{ $order->customer_name }}</div>
                </div>
                @empty
                <div class="text-center text-muted py-4">Tidak ada pesanan mendekati tenggat waktu.</div>
                @endforelse
            </div>
        </x-card>
        
        <x-card title="Status Produksi">
            <div class="d-flex flex-column gap-3">
                @foreach($summary['status_breakdown'] as $status => $count)
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary" style="width:8px;height:8px;"></div>
                        <span class="text-dark small">
                            @php
                                $statusLabels = [
                                    'order_received' => 'Pesanan Diterima',
                                    'fabric_cutting' => 'Pemotongan Kain',
                                    'sewing' => 'Penjahitan',
                                    'embroidery' => 'Bordir',
                                    'button_installation' => 'Pemasangan Kancing',
                                    'shipping' => 'Pengiriman',
                                ];
                            @endphp
                            {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </div>
                    <span class="fw-semibold">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </x-card>
    </div>
</div>
@endsection
