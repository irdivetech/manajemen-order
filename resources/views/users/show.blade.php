@extends('layouts.app')

@section('title', 'Rincian Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Pengguna</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <x-card class="text-center">
            <div class="d-flex justify-content-center mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold bg-primary shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ $user->email }}</p>
            
            <div class="mb-4">
                @if($user->isAdmin())
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">Akun Admin</span>
                @else
                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2">Akun Owner</span>
                @endif
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i> Ubah Data Pengguna</a>
                @if(Auth::id() !== $user->id)
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="confirmDelete(event, this, 'Hapus pengguna ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i> Hapus Pengguna</button>
                </form>
                @endif
            </div>
        </x-card>
        
        <x-card title="Informasi Akun" class="mt-4">
            <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                <li>
                    <span class="text-muted d-block small">Tanggal Bergabung</span>
                    <span class="fw-medium"><i class="bi bi-calendar3 me-2 text-muted"></i>{{ $user->created_at->format('d M Y') }}</span>
                </li>
                <li>
                    <span class="text-muted d-block small">Terakhir Diperbarui</span>
                    <span class="fw-medium"><i class="bi bi-clock-history me-2 text-muted"></i>{{ $user->updated_at->diffForHumans() }}</span>
                </li>
            </ul>
        </x-card>
    </div>

    <div class="col-lg-8">
        <x-card title="Pesanan yang Dibuat Oleh {{ explode(' ', $user->name)[0] }}">
            @if($user->orders->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                    Pengguna ini belum membuat pesanan apapun.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders as $order)
                            <tr>
                                <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><x-badge :status="$order->current_status" /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
