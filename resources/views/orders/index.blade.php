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

    <div class="row mb-3">
        <div class="col-md-6">
            <form action="{{ route('orders.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari no pesanan, nama..." value="{{ request('search') }}">
                <select name="status" class="form-select form-select-sm" style="width:auto;">
                    <option value="">Semua Status</option>
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
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-search"></i></button>
                @if(request('search') || request('status'))
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
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
                        <div class="fw-medium text-dark">{{ $order->customer_name }}</div>
                        <div class="small text-muted">{{ $order->customer_phone }}</div>
                    </td>
                    <td>
                        <div class="text-dark">{{ $order->product_name }}</div>
                        <div class="small text-muted">{{ $order->totalQuantity() }} pcs - {{ $order->color }}</div>
                    </td>
                    <td><x-badge :status="$order->current_status" /></td>
                    <td class="fw-semibold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="small {{ \Carbon\Carbon::parse($order->deadline)->isPast() ? 'text-danger fw-bold' : '' }}">
                            {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}
                        </span>
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
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pesanan aktif.</td>
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
