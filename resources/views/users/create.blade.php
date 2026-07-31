@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Pengguna</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <x-card title="Informasi Pengguna">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Pilih Peran --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner (Hanya Baca)</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4 border-top pt-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                    <div class="col-12 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Kata sandi minimal harus 8 karakter.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
