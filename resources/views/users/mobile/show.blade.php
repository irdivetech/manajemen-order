@extends('layouts.mobile')

@section('title', 'Rincian Pengguna')

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('users.index') }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Profil Pengguna</h1>
            <p class="page-sub">{{ $user->name }}</p>
        </div>
    </div>
@endsection

@section('content')

{{-- ── Profil Card ── --}}
<div class="m-card text-center overflow-hidden position-relative mb-4">
    <div style="height:80px; background:linear-gradient(135deg, var(--primary), var(--primary-light));"></div>
    <div class="m-card-body position-relative pb-4" style="margin-top:-40px;">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow border border-3 border-white" 
             style="width:80px; height:80px; font-size:2rem; background:linear-gradient(135deg, var(--primary), var(--primary-dark)); margin-bottom:1rem;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <h4 class="fw-bold mb-1 fs-5">{{ $user->name }}</h4>
        <p class="text-muted small mb-3">{{ $user->email }}</p>
        
        <div>
            @if($user->isAdmin())
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill">Akun Admin</span>
            @else
                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-3 py-2 rounded-pill">Akun Owner</span>
            @endif
        </div>
    </div>
</div>

{{-- ── Informasi Akun ── --}}
<div class="section-title">Informasi Akun</div>
<div class="m-card mb-4">
    <div class="m-card-body p-0">
        <div class="m-list-item d-flex justify-content-between align-items-center">
            <span class="text-muted text-sm"><i class="bi bi-calendar3 me-2"></i>Tanggal Bergabung</span>
            <span class="fw-6 text-sm">{{ $user->created_at->format('d M Y') }}</span>
        </div>
        <div class="m-list-item d-flex justify-content-between align-items-center border-0">
            <span class="text-muted text-sm"><i class="bi bi-clock-history me-2"></i>Pembaruan Terakhir</span>
            <span class="fw-6 text-sm">{{ $user->updated_at->diffForHumans() }}</span>
        </div>
    </div>
</div>

{{-- ── Pesanan ── --}}
<div class="section-title">Pesanan yang Dibuat ({{ $user->orders->count() }})</div>
<div class="m-card mb-4">
    <div class="m-card-body p-0">
        @if($user->orders->isEmpty())
            <div class="empty-state py-4">
                <i class="bi bi-box-seam"></i>
                <p>Belum ada pesanan yang dibuat.</p>
            </div>
        @else
            @foreach($user->orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="m-list-item d-flex align-items-center justify-content-between text-decoration-none text-dark {{ $loop->last ? 'border-0' : '' }}">
                <div>
                    <div class="fw-7 text-sm mb-1 text-primary">{{ $order->order_number }}</div>
                    <div class="text-xs text-muted">{{ $order->customer_name }}</div>
                </div>
                <div class="text-end">
                    <x-badge :status="$order->current_status" />
                    <div class="text-xs text-muted mt-1">{{ $order->created_at->format('d/m/y') }}</div>
                </div>
            </a>
            @endforeach
        @endif
    </div>
</div>

{{-- Spacer --}}
<div style="height:70px;"></div>

{{-- ── Sticky Action Bar ── --}}
<div class="position-fixed start-0 end-0 p-3"
     style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
    <div class="d-flex gap-2">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-pencil"></i> Ubah
        </a>
        @if(Auth::id() !== $user->id)
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-grow-1" onsubmit="confirmDelete(event, this, 'Apakah Anda yakin ingin menghapus pengguna ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
        @endif
    </div>
</div>



@endsection
