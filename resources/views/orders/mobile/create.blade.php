@extends('layouts.mobile')

@section('title', 'Buat Pesanan Baru')

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('orders.index') }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Pesanan Baru</h1>
            <p class="page-sub">Isi detail pesanan di bawah ini</p>
        </div>
    </div>
@endsection

@section('content')

<form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="createOrderForm">
    @csrf

    {{-- ── Informasi Pelanggan ── --}}
    <div class="section-title">Informasi Pelanggan</div>
    <div class="m-card">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="customer_name"
                       class="form-control @error('customer_name') is-invalid @enderror"
                       value="{{ old('customer_name') }}" required placeholder="Masukkan nama pelanggan">
                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="customer_phone"
                       class="form-control @error('customer_phone') is-invalid @enderror"
                       value="{{ old('customer_phone') }}" required placeholder="08xx-xxxx-xxxx">
                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Kategori Pelanggan <span class="text-danger">*</span></label>
                <select name="customer_category" class="form-select @error('customer_category') is-invalid @enderror" required>
                    <option value="retail" {{ old('customer_category') === 'retail' ? 'selected' : '' }}>Retail (Eceran)</option>
                    <option value="b2b"    {{ old('customer_category') === 'b2b'    ? 'selected' : '' }}>B2B (Grosir / Instansi)</option>
                </select>
                @error('customer_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ── Rincian Produk ── --}}
    <div class="section-title">Rincian Produk</div>
    <div class="m-card">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_name"
                       class="form-control @error('product_name') is-invalid @enderror"
                       value="{{ old('product_name') }}" required placeholder="Cth: Kaos Oblong, Jaket, dll.">
                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Tipe / Model Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_type"
                       class="form-control @error('product_type') is-invalid @enderror"
                       value="{{ old('product_type') }}" required placeholder="Cth: Polo Shirt, Hoodie, dll.">
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Warna <span class="text-danger">*</span></label>
                <input type="text" name="color"
                       class="form-control @error('color') is-invalid @enderror"
                       value="{{ old('color') }}" required placeholder="Cth: Merah, Navy, Hitam-Putih">
                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ── Ukuran & Jumlah ── --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="section-title mb-0">Ukuran & Jumlah</div>
        <button type="button" class="btn btn-primary btn-sm" id="add-size-row" data-bs-toggle="offcanvas" data-bs-target="#addSizeModal">
            <i class="bi bi-plus-lg"></i> Tambah
        </button>
    </div>
    @error('size_details') <div class="text-danger text-xs mb-2">{{ $message }}</div> @enderror

    <div class="m-card">
        <div class="m-card-body p-0" id="size-details-container">
            <div class="empty-state py-3" id="size-empty-msg">
                <i class="bi bi-rulers"></i>
                <p>Belum ada ukuran. Ketuk "Tambah" untuk menambahkan.</p>
            </div>
        </div>
    </div>

    {{-- ── Summary ── --}}
    <div class="m-card" id="price-summary-card" style="display:none;">
        <div class="m-card-body">
            <div class="d-flex justify-content-between text-sm mb-1">
                <span class="text-muted">Total Kuantitas</span>
                <span class="fw-7" id="total-qty-display">0 pcs</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="fw-7">Total Harga</span>
                <span class="fw-7 text-success" id="total-price-display">Rp 0</span>
            </div>
        </div>
    </div>

    {{-- ── Jadwal ── --}}
    <div class="section-title">Jadwal</div>
    <div class="m-card">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Tanggal Pesanan <span class="text-danger">*</span></label>
                <input type="date" name="order_date"
                       class="form-control @error('order_date') is-invalid @enderror"
                       value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Tenggat Waktu <span class="text-danger">*</span></label>
                <input type="date" name="deadline"
                       class="form-control @error('deadline') is-invalid @enderror"
                       value="{{ old('deadline') }}" required>
                @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ── HPP & Catatan ── --}}
    <div class="section-title">Keuangan & Catatan</div>
    <div class="m-card">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Total HPP / Modal (Rp)</label>
                <input type="number" name="total_cost"
                       class="form-control @error('total_cost') is-invalid @enderror"
                       value="{{ old('total_cost', 0) }}" min="0" step="1000" placeholder="0">
                <div class="text-xs text-muted mt-1">Total modal produksi keseluruhan</div>
                @error('total_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Catatan Tambahan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                          rows="3" placeholder="Catatan khusus untuk order ini...">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ── Upload Foto Desain ── --}}
    <div class="section-title">Foto Desain (Opsional)</div>
    <div class="m-card">
        <div class="m-card-body">
            <label class="form-label text-sm fw-6">Upload Foto / File Desain</label>
            <input type="file" name="design_files[]" id="designFilesInput"
                   class="form-control @error('design_files') is-invalid @enderror @error('design_files.*') is-invalid @enderror"
                   multiple accept="image/*,.pdf">
            <div class="text-xs text-muted mt-1">Format: JPG, PNG, PDF. Maks 5MB per file.</div>
            @error('design_files')   <div class="invalid-feedback">{{ $message }}</div> @enderror
            @error('design_files.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Spacer --}}
    <div style="height:80px;"></div>

    {{-- ── Sticky Submit ── --}}
    <div class="position-fixed start-0 end-0 p-3"
         style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
        <div class="d-flex gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-light border flex-shrink-0">Batal</a>
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-check-lg me-1"></i> Simpan Pesanan
            </button>
        </div>
    </div>
</form>

{{-- ── Add Size Row Offcanvas ── --}}
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="addSizeModal" style="border-radius:20px 20px 0 0; min-height:60vh; height:auto;">
    <div class="d-flex justify-content-center pt-3 pb-1">
        <div style="width:36px;height:4px;background:#e5e7eb;border-radius:2px;"></div>
    </div>
    <div class="offcanvas-header border-0 pb-0 pt-2 px-4">
        <h5 class="offcanvas-title fw-7" style="font-size:1rem;">Tambah Ukuran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-4 pb-4">
        <div class="mb-3">
            <label class="form-label text-sm fw-6">Kategori</label>
            <select id="modal-gender" class="form-select">
                <option value="male">Laki-laki (Dewasa)</option>
                <option value="female">Perempuan (Dewasa)</option>
                <option value="child">Anak-anak (Unisex)</option>
            </select>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-4">
                <label class="form-label text-sm fw-6">Ukuran</label>
                <input type="text" id="modal-size" class="form-control" placeholder="S, M...">
            </div>
            <div class="col-4">
                <label class="form-label text-sm fw-6">Harga (Rp)</label>
                <input type="number" id="modal-price" class="form-control" placeholder="50000" min="0">
            </div>
            <div class="col-4">
                <label class="form-label text-sm fw-6">Jumlah</label>
                <input type="number" id="modal-qty" class="form-control" placeholder="10" min="1">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border flex-grow-1" data-bs-dismiss="offcanvas">Batal</button>
            <button type="button" class="btn btn-primary flex-grow-1" id="confirmAddSize">Tambahkan</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let rowIndex = 0;

document.getElementById('addSizeModal').addEventListener('show.bs.offcanvas', () => {
    document.getElementById('modal-gender').value = 'male';
    document.getElementById('modal-size').value   = '';
    document.getElementById('modal-price').value  = '';
    document.getElementById('modal-qty').value    = '';
    setTimeout(() => document.getElementById('modal-size').focus(), 400);
});

document.getElementById('confirmAddSize').addEventListener('click', () => {
    const gender = document.getElementById('modal-gender').value;
    const size   = document.getElementById('modal-size').value.trim();
    const price  = parseInt(document.getElementById('modal-price').value) || 0;
    const qty    = parseInt(document.getElementById('modal-qty').value) || 0;

    if (!size || qty <= 0) {
        alert('Harap isi ukuran dan jumlah dengan benar.');
        return;
    }

    const container  = document.getElementById('size-details-container');
    const emptyMsg   = document.getElementById('size-empty-msg');
    const subtotal   = price * qty;

    const genderLabel = { male: 'Laki-laki', female: 'Perempuan', child: 'Anak-anak' }[gender];
    const genderColor = { male: 'primary',   female: 'danger',    child: 'success' }[gender];

    const rowHtml = `
        <div class="m-list-item size-row" data-price="${price}" data-qty="${qty}" data-row="${rowIndex}">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-${genderColor} bg-opacity-10 text-${genderColor} text-xs">${genderLabel}</span>
                    <span class="fw-7">${size}</span>
                </div>
                <div class="text-xs text-muted">Rp ${price.toLocaleString('id-ID')} × ${qty} pcs = <strong>Rp ${subtotal.toLocaleString('id-ID')}</strong></div>
            </div>
            <button type="button" class="btn btn-sm btn-light border text-danger remove-row" style="flex-shrink:0;">
                <i class="bi bi-trash"></i>
            </button>
            <input type="hidden" name="size_details[${rowIndex}][gender]"   value="${gender}">
            <input type="hidden" name="size_details[${rowIndex}][size]"     value="${size}">
            <input type="hidden" name="size_details[${rowIndex}][price]"    value="${price}">
            <input type="hidden" name="size_details[${rowIndex}][quantity]" value="${qty}">
        </div>`;

    if (emptyMsg) emptyMsg.style.display = 'none';
    container.insertAdjacentHTML('beforeend', rowHtml);
    rowIndex++;

    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('addSizeModal')).hide();
    updateSummary();
});

document.getElementById('size-details-container').addEventListener('click', e => {
    const btn = e.target.closest('.remove-row');
    if (btn) {
        btn.closest('.size-row').remove();
        if (!document.querySelector('.size-row')) {
            document.getElementById('size-empty-msg').style.display = '';
        }
        updateSummary();
    }
});

function updateSummary() {
    let totalQty = 0, totalPrice = 0;
    document.querySelectorAll('.size-row').forEach(row => {
        totalQty   += parseInt(row.dataset.qty);
        totalPrice += parseInt(row.dataset.price) * parseInt(row.dataset.qty);
    });
    const card = document.getElementById('price-summary-card');
    card.style.display = totalQty > 0 ? '' : 'none';
    document.getElementById('total-qty-display').textContent   = totalQty + ' pcs';
    document.getElementById('total-price-display').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
}
</script>
@endpush
