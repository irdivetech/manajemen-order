@extends('layouts.mobile')

@section('title', 'Tambah Pengguna Baru')

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('users.index') }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Tambah Pengguna</h1>
            <p class="page-sub">Buat akun baru untuk staf/owner</p>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('users.store') }}" method="POST">
    @csrf
    
    <div class="section-title">Informasi Pribadi</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Nama pengguna baru">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-sm fw-6">Alamat Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="email@contoh.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="form-label text-sm fw-6">Peran (Role) <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="section-title">Keamanan</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Kata Sandi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="mPasswordInput" class="form-control @error('password') is-invalid @enderror" required minlength="8" placeholder="Minimal 8 karakter">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mPasswordInput', this)"><i class="bi bi-eye"></i></button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label text-sm fw-6">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="mPasswordConfirmInput" class="form-control" required minlength="8" placeholder="Ketik ulang kata sandi">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mPasswordConfirmInput', this)"><i class="bi bi-eye"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Spacer --}}
    <div style="height:70px;"></div>

    {{-- ── Sticky Submit ── --}}
    <div class="position-fixed start-0 end-0 p-3"
         style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
        <div class="d-flex gap-2">
            <a href="{{ route('users.index') }}" class="btn btn-light border flex-shrink-0">Batal</a>
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-person-plus me-1"></i> Simpan Pengguna
            </button>
        </div>
    </div>
</form>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endsection
