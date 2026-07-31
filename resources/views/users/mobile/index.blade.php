@extends('layouts.mobile')

@section('title', 'Manajemen Pengguna')

@section('page-header')
    <h1>Pengguna Sistem</h1>
    <p class="page-sub">Kelola akses admin dan owner</p>
@endsection

@section('content')

{{-- ── Users List ── --}}
<div class="m-card">
    <div class="m-card-body p-0">
        @forelse($users as $user)
        <a href="{{ route('users.show', $user) }}" class="m-list-item d-flex align-items-center gap-3 text-decoration-none text-dark">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold bg-primary shadow-sm flex-shrink-0" 
                 style="width: 46px; height: 46px; font-size: 1.1rem; background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-7 text-sm text-truncate pe-2">
                        {{ $user->name }}
                        @if(Auth::id() === $user->id)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle ms-1" style="font-size:0.6rem;">Anda</span>
                        @endif
                    </span>
                    @if($user->isAdmin())
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle text-xs" style="flex-shrink:0;">Admin</span>
                    @else
                        <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle text-xs" style="flex-shrink:0;">Owner</span>
                    @endif
                </div>
                <div class="text-xs text-muted text-truncate">{{ $user->email }}</div>
            </div>
            <i class="bi bi-chevron-right text-muted opacity-50 ms-2"></i>
        </a>
        @empty
        <div class="empty-state py-4">
            <i class="bi bi-people"></i>
            <p>Belum ada data pengguna.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ── FAB: Tambah Pengguna ── --}}
<a href="{{ route('users.create') }}" class="fab" title="Tambah Pengguna Baru">
    <i class="bi bi-person-plus"></i>
</a>

@endsection
