@extends('layouts.mobile')

@section('title', 'Pengaturan Sistem')

@section('page-header')
    <div>
        <h1 style="font-size:1.05rem;">Pengaturan</h1>
        <p class="page-sub">Konfigurasi Sistem</p>
    </div>
@endsection

@section('content')

@php /** @var array $settings */ @endphp

<div class="m-card mb-4">
    <div class="m-card-body p-0">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-3">
                <div class="section-title mb-3">Identitas Perusahaan</div>
                
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Nama Perusahaan</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Email Kontak</label>
                    <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Nomor Telepon</label>
                    <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Alamat Lengkap</label>
                    <textarea name="company_address" class="form-control" rows="3" required>{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">WhatsApp Resi</label>
                    <input type="text" name="company_wa" class="form-control" value="{{ old('company_wa', $settings['company_wa'] ?? '') }}" placeholder="08...">
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Instagram Resi</label>
                    <input type="text" name="company_ig" class="form-control" value="{{ old('company_ig', $settings['company_ig'] ?? '') }}" placeholder="@...">
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">TikTok Resi</label>
                    <input type="text" name="company_tiktok" class="form-control" value="{{ old('company_tiktok', $settings['company_tiktok'] ?? '') }}" placeholder="@...">
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Logo Perusahaan (Untuk Faktur)</label>
                    <input type="file" name="company_logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                    @if(!empty($settings['company_logo']))
                        <div class="mt-2">
                            <img src="{{ Storage::url($settings['company_logo']) }}" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="form-label text-xs fw-6 text-muted">Logo Khusus Resi (B/W Disarankan)</label>
                    <input type="file" name="resi_logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                    @if(!empty($settings['resi_logo']))
                        <div class="mt-2">
                            <img src="{{ Storage::url($settings['resi_logo']) }}" alt="Logo Resi" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    @endif
                </div>

                <div class="section-title mb-3 border-top pt-3">Informasi Owner (Faktur)</div>
                
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Nama Owner</label>
                    <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $settings['owner_name'] ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label text-xs fw-6 text-muted">Jabatan Owner</label>
                    <input type="text" name="owner_title" class="form-control" value="{{ old('owner_title', $settings['owner_title'] ?? '') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label text-xs fw-6 text-muted">Tanda Tangan (Signature)</label>
                    <input type="file" name="signature_image" class="form-control" accept="image/png, image/jpeg, image/jpg">
                    @if(!empty($settings['signature_image']))
                        <div class="mt-2">
                            <img src="{{ Storage::url($settings['signature_image']) }}" alt="Signature" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    @endif
                </div>
                
                <button type="submit" class="btn btn-primary w-100 fw-6 py-2">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<div class="section-title mb-3">Rekening Bank Pembayaran</div>
<div class="mb-3">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Rekening
    </button>
</div>

@forelse($bankAccounts as $bank)
<div class="m-card mb-3">
    <div class="m-card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="fw-7 text-dark">{{ $bank->bank_name }}</div>
                <div class="text-sm mt-1">{{ $bank->account_number }}</div>
                <div class="text-xs text-muted">a/n {{ $bank->account_name }}</div>
            </div>
            <div>
                @if($bank->is_active)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle mb-2">Aktif</span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle mb-2">Nonaktif</span>
                @endif
                
                <div class="d-flex justify-content-end gap-1">
                    <form action="{{ route('bank_accounts.toggle', $bank) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="icon-btn {{ $bank->is_active ? 'bg-light text-muted' : 'bg-success bg-opacity-10 text-success' }}" style="width:30px;height:30px;">
                            <i class="bi bi-power"></i>
                        </button>
                    </form>
                    <form action="{{ route('bank_accounts.destroy', $bank) }}" method="POST" onsubmit="return confirm('Hapus?');">
                        @csrf @method('DELETE')
                        <button class="icon-btn bg-danger bg-opacity-10 text-danger" style="width:30px;height:30px;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@empty
<div class="text-center text-muted py-3 text-sm">
    Belum ada rekening bank.
</div>
@endforelse

<!-- Modal Tambah Rekening -->
<div class="modal fade" id="addBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fs-6 fw-7">Tambah Rekening Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-6 text-muted">Nama Bank</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="cth: BCA" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-6 text-muted">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-6 text-muted">Atas Nama</label>
                        <input type="text" name="account_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div style="height:40px;"></div>
@endsection
