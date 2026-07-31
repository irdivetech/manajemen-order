@extends('layouts.mobile')

@section('title', 'Pengaturan Sistem')

@section('page-header')
    <h1>Pengaturan Sistem</h1>
    <p class="page-sub">Identitas & pengaturan aplikasi</p>
@endsection

@section('content')

@php /** @var array $settings */ @endphp

<form action="{{ route('settings.update') }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="section-title">Identitas Perusahaan</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Perusahaan <span class="text-danger">*</span></label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Email Kontak <span class="text-danger">*</span></label>
                <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" required>
            </div>
            <div>
                <label class="form-label text-sm fw-6">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea name="company_address" class="form-control" rows="3" required>{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                <div class="text-xs text-muted mt-1">Ditampilkan pada faktur / invoice.</div>
            </div>
        </div>
    </div>

    <div class="section-title">Keuangan</div>
    <div class="m-card mb-4">
        <div class="m-card-body">
            <div>
                <label class="form-label text-sm fw-6">Persentase Pajak (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.1" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings['tax_rate'] ?? 11) }}" required min="0" max="100">
                    <span class="input-group-text">%</span>
                </div>
                <div class="text-xs text-muted mt-1">Cth: 11 untuk PPN 11%</div>
            </div>
        </div>
    </div>

    {{-- Spacer --}}
    <div style="height:70px;"></div>

    {{-- ── Sticky Submit ── --}}
    <div class="position-fixed start-0 end-0 p-3"
         style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
        <div class="d-grid">
            <button type="submit" class="btn btn-primary fw-6">
                <i class="bi bi-save me-1"></i> Simpan Pengaturan
            </button>
        </div>
    </div>
</form>

@endsection
