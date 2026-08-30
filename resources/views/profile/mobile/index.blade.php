@extends('layouts.mobile')

@section('title', 'Profil Saya')

@section('page-header')
    <h1>Profil Saya</h1>
    <p class="page-sub">Kelola informasi akun Anda</p>
@endsection

@section('content')

{{-- ── Profil Card ── --}}
<div class="m-card text-center mb-4 border-primary">
    <div class="m-card-body pb-4 pt-4 bg-primary bg-opacity-10" style="border-radius:14px;">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
             style="width:72px; height:72px; font-size:1.8rem; background:linear-gradient(135deg, var(--primary), var(--primary-dark)); margin-bottom:1rem;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <h4 class="fw-bold mb-1 fs-5 text-primary">{{ Auth::user()->name }}</h4>
        <p class="text-primary text-opacity-75 small mb-3">{{ Auth::user()->email }}</p>
        
        <span class="badge bg-primary text-white px-3 py-2 rounded-pill">{{ ucfirst(Auth::user()->role) }}</span>
    </div>
</div>

@if(Auth::user()?->isOwner())
    {{-- ── Form Ubah Profil ── --}}
    <div class="section-title">Pengaturan Profil</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Alamat Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <button type="submit" class="btn btn-outline-primary w-100 fw-6">Simpan Profil</button>
            </form>
        </div>
    </div>

    {{-- ── Form Ganti Password ── --}}
    <div class="section-title">Keamanan</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Kata Sandi Saat Ini</label>
                    <div class="position-relative">
                        <input type="password" name="current_password" id="m_current_password" class="form-control @error('current_password') is-invalid @enderror" required style="padding-right: 2.5rem;">
                        <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('m_current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Kata Sandi Baru</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="m_password" class="form-control @error('password') is-invalid @enderror" required minlength="8" style="padding-right: 2.5rem;">
                        <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('m_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Konfirmasi Kata Sandi Baru</label>
                    <div class="position-relative">
                        <input type="password" name="password_confirmation" id="m_password_confirmation" class="form-control" required minlength="8" style="padding-right: 2.5rem;">
                        <button class="btn border-0 position-absolute end-0 top-50 translate-middle-y" style="color: #9ca3af; background: transparent; box-shadow: none;" type="button" onclick="togglePassword('m_password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-outline-danger w-100 fw-6">Perbarui Kata Sandi</button>
            </form>
        </div>
    </div>
@else
    <div class="section-title">Informasi</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-info-circle text-primary fs-3"></i>
                <p class="mb-0 text-sm">Perubahan profil dan kata sandi hanya dapat dilakukan oleh <strong>Owner</strong>. Silakan hubungi Owner jika Anda perlu mengubah data akun Anda.</p>
            </div>
        </div>
    </div>
@endif

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
