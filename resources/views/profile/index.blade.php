@extends('layouts.app')

@section('title', 'Profil Saya')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Profil</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <x-card class="text-center h-100">
            <div class="d-flex justify-content-center mb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow" style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
            <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 border border-primary">{{ ucfirst(Auth::user()->role) }}</span>
            
            <hr class="my-4 text-muted opacity-25">
            
            <div class="d-flex justify-content-between text-start small">
                <span class="text-muted">Akun Dibuat Pada</span>
                <span class="fw-semibold">{{ Auth::user()->created_at->format('d M Y') }}</span>
            </div>
        </x-card>
    </div>

    <div class="col-md-8">
        @if(Auth::user()?->isOwner())
            <x-card title="Pengaturan Profil" class="mb-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Profil</button>
                        </div>
                    </div>
                </form>
            </x-card>

            <x-card title="Ubah Kata Sandi">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Kata Sandi Saat Ini</label>
                            <div class="position-relative">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required style="padding-right: 2.5rem;">
                                <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('current_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kata Sandi Baru</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" style="padding-right: 2.5rem;">
                                <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8" style="padding-right: 2.5rem;">
                                <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
                        </div>
                    </div>
                </form>
            </x-card>
        @else
            <x-card title="Informasi">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i> Perubahan profil dan kata sandi hanya dapat dilakukan oleh <strong>Owner</strong>. Silakan hubungi Owner jika Anda perlu mengubah data akun Anda.
                </div>
            </x-card>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        icon.style.color = 'var(--primary)'; // Changed color to make it distinct
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
        icon.style.color = ''; // Reset color
    }
}
</script>
@endpush
