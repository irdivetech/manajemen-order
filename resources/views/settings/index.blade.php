@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <x-card title="Pengaturan Perusahaan & Faktur">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                @php /** @var array $settings */ @endphp

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-2">Identitas Perusahaan</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name']) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Kontak <span class="text-danger">*</span></label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email']) }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone']) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="company_address" class="form-control" rows="3" required>{{ old('company_address', $settings['company_address']) }}</textarea>
                        <small class="text-muted">Akan ditampilkan pada faktur / invoice yang dicetak.</small>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">Pengaturan Keuangan</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Persentase Pajak (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings['tax_rate']) }}" required min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Contoh: 11 untuk PPN 11%.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
