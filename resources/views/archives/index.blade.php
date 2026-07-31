@extends('layouts.app')

@section('title', 'Arsip Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Arsip</li>
@endsection

@section('content')
<x-card title="Arsip Pesanan (Telah Dikirim)">
    <div class="row mb-3">
        <div class="col-md-6">
            <form action="{{ route('archives.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pesanan dalam arsip..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('archives.index') }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-muted">
            <thead class="table-light">
                <tr>
                    <th>No Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Tanggal Diarsipkan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-secondary text-decoration-none">{{ $order->order_number }}</a></td>
                    <td>
                        <div class="fw-medium">{{ $order->customer_name }}</div>
                        <div class="small">{{ $order->customer_phone }}</div>
                    </td>
                    <td>
                        <div>{{ $order->product_name }}</div>
                        <div class="small">{{ $order->totalQuantity() }} pcs - {{ $order->color }}</div>
                    </td>
                    <td>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                            <i class="bi bi-archive me-1"></i> {{ $order->archived_at->format('d M Y') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light border" title="Lihat Detail"><i class="bi bi-eye"></i> Lihat</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Belum ada pesanan yang diarsipkan.</td>
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
