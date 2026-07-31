@extends('layouts.mobile')

@section('title', 'Ubah Pengguna')

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('users.show', $user) }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Ubah Pengguna</h1>
            <p class="page-sub">{{ $user->name }}</p>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="section-title">Informasi Pribadi</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-sm fw-6">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label text-sm fw-6">Peran (Role) <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh & Ubah Data)</option>
                    <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Owner (Hanya Baca Laporan)</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Spacer --}}
    <div style="height:70px;"></div>

    {{-- ── Sticky Submit ── --}}
    <div class="position-fixed start-0 end-0 p-3"
         style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
        <div class="d-flex gap-2">
            <a href="{{ route('users.show', $user) }}" class="btn btn-light border flex-shrink-0">Batal</a>
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection
