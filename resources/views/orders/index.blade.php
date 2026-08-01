@extends('layouts.app')

@section('title', 'Data Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pesanan</li>
@endsection

@section('content')
<x-card>
    <x-slot name="title">Daftar Pesanan</x-slot>
    <x-slot name="actions">
        @if(Auth::user()?->isAdmin())
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Baru
        </a>
        @endif
    </x-slot>

    <div class="bg-light border rounded-3 p-3 mb-4 shadow-sm">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari no pesanan, nama pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status Produksi</option>
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
                    @foreach(\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $statusLabels[$status] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-medium"><i class="bi bi-funnel me-1"></i> Terapkan</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-danger btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Status</th>
                    <th>Total Harga</th>
                    <th>Tenggat Waktu</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); flex-shrink: 0;">
                                {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                                <div class="small text-muted mb-1"><i class="bi bi-telephone-fill me-1 opacity-50"></i>{{ $order->customer_phone }}</div>
                                @if($order->customer_title || $order->customer_address)
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @if($order->customer_title)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle fw-medium">{{ $order->customer_title }}</span>
                                        @endif
                                        @if($order->customer_address)
                                            <span class="badge bg-light text-dark border text-truncate fw-normal" style="max-width: 120px;" title="{{ $order->customer_address }}"><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $order->customer_address }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-dark fw-bold mb-1">{{ $order->product_name }}</div>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">{{ $order->totalQuantity() }} pcs</span>
                            <span class="badge bg-light text-dark border fw-normal">{{ $order->color }}</span>
                            @if($order->material)
                                <span class="badge bg-light text-dark border fw-normal"><i class="bi bi-tag-fill text-muted me-1"></i>{{ $order->material }}</span>
                            @endif
                        </div>
                    </td>
                    <td><x-badge :status="$order->current_status" /></td>
                    <td class="fw-semibold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-event {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-danger' : 'text-muted' }}"></i>
                            <span class="fw-medium {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-danger' : 'text-dark' }}">
                                {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                            </span>
                        </div>
                        @if(\Carbon\Carbon::parse($order->deadline)->isPast())
                            <div class="mt-1"><span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1" style="font-size:0.65rem;">Terlewat</span></div>
                        @elseif(\Carbon\Carbon::parse($order->deadline)->isToday())
                            <div class="mt-1"><span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1" style="font-size:0.65rem;">Hari Ini</span></div>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light border" title="Lihat"><i class="bi bi-eye"></i></a>
                            @if(Auth::user()?->isAdmin() && $order->isEditable())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-light border" title="Ubah"><i class="bi bi-pencil"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center opacity-75">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Pesanan</h6>
                            <p class="text-muted small mb-4">Belum ada pesanan yang sesuai dengan kriteria filter Anda.</p>
                            @if(Auth::user()?->isAdmin() && !request('search') && !request('status'))
                                <a href="{{ route('orders.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-plus-lg me-1"></i> Buat Pesanan Pertama
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-end mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</x-card>
@endsection
