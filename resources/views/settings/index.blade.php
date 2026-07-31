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
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @php /** @var array $settings */ @endphp

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-2">Identitas Perusahaan</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Kontak <span class="text-danger">*</span></label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="company_address" class="form-control" rows="3" required>{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Logo Perusahaan</label>
                        <input type="file" name="company_logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        @if(!empty($settings['company_logo']))
                            <div class="mt-2">
                                <img src="{{ Storage::url($settings['company_logo']) }}" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">Informasi Owner (Untuk Faktur)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Owner</label>
                        <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $settings['owner_name'] ?? '') }}">
                        <small class="text-muted">Misal: Rini Eka Maulani</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan Owner</label>
                        <input type="text" name="owner_title" class="form-control" value="{{ old('owner_title', $settings['owner_title'] ?? '') }}">
                        <small class="text-muted">Misal: Owner Shaleea</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tanda Tangan (Signature)</label>
                        <input type="file" name="signature_image" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted">Upload gambar tanda tangan (disarankan latar belakang transparan / PNG).</small>
                        @if(!empty($settings['signature_image']))
                            <div class="mt-2">
                                <img src="{{ Storage::url($settings['signature_image']) }}" alt="Signature" class="img-thumbnail" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </x-card>

        <!-- Bank Accounts Management -->
        <x-card title="Rekening Bank Pembayaran" class="mt-4">
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Rekening
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Bank</th>
                            <th>No. Rekening</th>
                            <th>Atas Nama</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bankAccounts as $bank)
                        <tr>
                            <td class="fw-semibold">{{ $bank->bank_name }}</td>
                            <td>{{ $bank->account_number }}</td>
                            <td>{{ $bank->account_name }}</td>
                            <td class="text-center">
                                @if($bank->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('bank_accounts.toggle', $bank) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $bank->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="Ubah Status">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('bank_accounts.destroy', $bank) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rekening ini?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada rekening bank yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>

<!-- Modal Tambah Rekening -->
<div class="modal fade" id="addBankModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bank_accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rekening Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank (cth: BCA, MANDIRI)</label>
                        <input type="text" name="bank_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" name="account_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Rekening</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
