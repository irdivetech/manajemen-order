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

<form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="order-form">
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
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Kategori Pelanggan <span class="text-danger">*</span></label>
                <select name="customer_category" class="form-select @error('customer_category') is-invalid @enderror" required>
                    <option value="retail" {{ old('customer_category') === 'retail' ? 'selected' : '' }}>Retail (Eceran)</option>
                    <option value="b2b"    {{ old('customer_category') === 'b2b'    ? 'selected' : '' }}>B2B (Grosir / Instansi)</option>
                </select>
                @error('customer_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Jabatan / Custom Nama</label>
                <textarea name="customer_title"
                       class="form-control @error('customer_title') is-invalid @enderror"
                       rows="3"
                       style="resize:vertical;">{{ old('customer_title') }}</textarea>
                <div class="text-xs text-muted mt-1">Opsional — bisa lebih dari satu baris</div>
                @error('customer_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Alamat Lengkap</label>
                <input type="text" name="customer_address"
                       class="form-control @error('customer_address') is-invalid @enderror"
                       value="{{ old('customer_address') }}">
                @error('customer_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Provinsi</label>
                <select name="customer_province" id="customer_province" class="form-select @error('customer_province') is-invalid @enderror">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                <input type="hidden" id="old_province" value="{{ old('customer_province') }}">
                @error('customer_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label text-sm fw-6">Kota / Kab.</label>
                    <select name="customer_city" id="customer_city" class="form-select @error('customer_city') is-invalid @enderror" disabled>
                        <option value="">-- Pilih Kota/Kab --</option>
                    </select>
                    <input type="hidden" id="old_city" value="{{ old('customer_city') }}">
                    @error('customer_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6">
                    <label class="form-label text-sm fw-6">Kecamatan</label>
                    <select name="customer_district" id="customer_district" class="form-select @error('customer_district') is-invalid @enderror" disabled>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                    <input type="hidden" id="old_district" value="{{ old('customer_district') }}">
                    @error('customer_district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
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
                <label class="form-label text-sm fw-6">Kategori Baju <span class="text-danger">*</span></label>
                <select name="clothing_category_id" class="form-select @error('clothing_category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($clothingCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('clothing_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('clothing_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Bahan / Material <span class="text-danger">*</span></label>
                <select name="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ old('material_id') == $mat->id ? 'selected' : '' }}>{{ $mat->name }}</option>
                    @endforeach
                </select>
                @error('material_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Tipe Lengan / Spesifikasi <span class="text-danger">*</span></label>
                <input type="text" name="product_type"
                       class="form-control @error('product_type') is-invalid @enderror"
                       value="{{ old('product_type') }}" required>
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Model Produk</label>
                <input type="text" name="model_product"
                       class="form-control @error('model_product') is-invalid @enderror"
                       value="{{ old('model_product') }}">
                @error('model_product') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <div class="form-check form-switch mt-3">
                    <input type="hidden" name="has_embroidery" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_embroidery" name="has_embroidery" value="1" {{ old('has_embroidery') ? 'checked' : '' }}>
                    <label class="form-check-label text-sm fw-6" for="has_embroidery">Dengan Bordir / Sablon</label>
                </div>
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
                <p>Belum ada rincian ukuran.</p>
            </div>
        </div>
    </div>

    {{-- ── Summary ── --}}
    <div class="m-card mt-3" id="price-summary-card" style="display:none;">
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
                <label class="form-label text-sm fw-6">Total HPP / Modal Produksi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="total_cost"
                           class="form-control @error('total_cost') is-invalid @enderror"
                           value="{{ old('total_cost', 0) }}" min="0" step="1000" required>
                    <button class="btn btn-outline-secondary" type="button" id="btn-calculate-hpp">
                        <i class="bi bi-calculator"></i>
                    </button>
                </div>
                <div class="text-xs text-muted mt-1">Estimasi biaya bahan & tenaga kerja</div>
                @error('total_cost') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
            <div class="text-xs text-muted mt-1">Format: JPG, PNG, WEBP. Maks 5 foto.</div>
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
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="addSizeModal" style="border-radius:20px 20px 0 0; min-height:75vh; height:auto;">
    <div class="d-flex justify-content-center pt-3 pb-1">
        <div style="width:36px;height:4px;background:#e5e7eb;border-radius:2px;"></div>
    </div>
    <div class="offcanvas-header border-0 pb-0 pt-2 px-4">
        <h5 class="offcanvas-title fw-7" style="font-size:1rem;">Tambah Ukuran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-4 pb-4">
        <div class="mb-3">
            <label class="form-label text-sm fw-6">Warna</label>
            <input type="text" id="modal-color" class="form-control" placeholder="Merah...">
        </div>
        <div class="mb-3">
            <label class="form-label text-sm fw-6">Gender</label>
            <select id="modal-gender" class="form-select">
                @foreach($genders as $g)
                    <option value="{{ $g->id }}">{{ $g->label }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-12">
                <label class="form-label text-sm fw-6">Tipe Ukuran</label>
                <select id="modal-size-type" class="form-select">
                    <option value="standard" selected>Standard</option>
                    <option value="big">Big Size</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label text-sm fw-6">Ukuran</label>
            <select id="modal-size-id" class="form-select">
                <option value="">Pilih Ukuran...</option>
            </select>
        </div>
        <div class="row g-2 mb-4">
            <div class="col-6">
                <label class="form-label text-sm fw-6">Harga (Rp)</label>
                <input type="number" id="modal-price" class="form-control" placeholder="50000" min="0">
            </div>
            <div class="col-6">
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

const masterSizes = @json($sizes);
const modalSizeType = document.getElementById('modal-size-type');
const modalSizeId = document.getElementById('modal-size-id');

modalSizeType.addEventListener('change', function() {
    modalSizeId.innerHTML = '<option value="">Pilih Ukuran...</option>';
    const type = this.value;
    if (type) {
        masterSizes.filter(s => s.size_type == type).forEach(size => {
            const option = document.createElement('option');
            option.value = size.id;
            option.textContent = size.label;
            modalSizeId.appendChild(option);
        });
    }
});

// Trigger change event to populate sizes initially
modalSizeType.dispatchEvent(new Event('change'));

document.getElementById('addSizeModal').addEventListener('show.bs.offcanvas', () => {
    document.getElementById('modal-color').value  = '';
    document.getElementById('modal-price').value  = '';
    document.getElementById('modal-qty').value    = '';
    setTimeout(() => document.getElementById('modal-color').focus(), 400);
});

document.getElementById('confirmAddSize').addEventListener('click', () => {
    const color = document.getElementById('modal-color').value.trim();
    const genderId = document.getElementById('modal-gender').value;
    const genderText = document.getElementById('modal-gender').options[document.getElementById('modal-gender').selectedIndex].text;
    const sizeTypeId = document.getElementById('modal-size-type').value;
    const sizeId = document.getElementById('modal-size-id').value;
    const sizeText = modalSizeId.options[modalSizeId.selectedIndex]?.text || '';
    const price  = parseInt(document.getElementById('modal-price').value) || 0;
    const qty    = parseInt(document.getElementById('modal-qty').value) || 0;

    if (!color || !sizeId || qty <= 0 || price < 0) {
        alert('Harap isi warna, ukuran, harga, dan jumlah dengan benar.');
        return;
    }

    const container  = document.getElementById('size-details-container');
    const emptyMsg   = document.getElementById('size-empty-msg');
    const subtotal   = price * qty;

    const rowHtml = `
        <div class="m-list-item size-row p-3 border-bottom" data-price="${price}" data-qty="${qty}" data-row="${rowIndex}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary text-xs">${color}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary text-xs">${genderText}</span>
                        <span class="fw-7">${sizeText}</span>
                        <span class="text-xs text-muted">(${sizeTypeId})</span>
                    </div>
                    <div class="text-xs text-muted">Rp ${price.toLocaleString('id-ID')} × ${qty} pcs = <strong class="text-dark">Rp ${subtotal.toLocaleString('id-ID')}</strong></div>
                </div>
                <button type="button" class="btn btn-sm btn-light border text-danger remove-row" style="flex-shrink:0;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <input type="hidden" name="size_details[${rowIndex}][color]"   value="${color}">
            <input type="hidden" name="size_details[${rowIndex}][gender_id]" value="${genderId}">
            <input type="hidden" name="size_details[${rowIndex}][size_type]" value="${sizeTypeId}">
            <input type="hidden" name="size_details[${rowIndex}][size_id]" value="${sizeId}">
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

// Hitung Estimasi HPP
document.getElementById('btn-calculate-hpp').addEventListener('click', async function() {
    const btn = this;
    const originalText = btn.innerHTML;
    
    const materialId = document.querySelector('select[name="material_id"]').value;
    const clothingCategoryId = document.querySelector('select[name="clothing_category_id"]').value;
    
    if (!materialId || !clothingCategoryId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih Kategori Baju dan Bahan / Material terlebih dahulu.',
            customClass: { popup: 'rounded-4' }
        });
        return;
    }

    const sizes = [];
    document.querySelectorAll('.size-row').forEach(row => {
        const sizeId = row.querySelector('input[name$="[size_id]"]')?.value;
        const qty = row.querySelector('input[name$="[quantity]"]')?.value;
        if (sizeId && qty) {
            sizes.push({ size_id: sizeId, quantity: qty });
        }
    });

    if (sizes.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan tambahkan minimal satu rincian ukuran.',
            customClass: { popup: 'rounded-4' }
        });
        return;
    }

    try {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btn.disabled = true;

        const response = await fetch('/hpp/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                material_id: materialId,
                clothing_category_id: clothingCategoryId,
                size_details: sizes
            })
        });

        if (!response.ok) throw new Error('API Error');
        
        const data = await response.json();
        const estimatedHpp = data.estimated_hpp || 0;
        
        document.querySelector('input[name="total_cost"]').value = estimatedHpp;
        
        Swal.fire({
            icon: 'success',
            title: 'HPP Dihitung',
            text: 'Estimasi HPP: Rp ' + estimatedHpp.toLocaleString('id-ID'),
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' }
        });
    } catch (e) {
        console.error(e);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal menghitung estimasi HPP.',
            customClass: { popup: 'rounded-4' }
        });
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});

// Frontend validation for HPP vs Subtotal
const form = document.getElementById('order-form');
if (form) {
    form.addEventListener('submit', function(e) {
        const hppInput = document.querySelector('input[name="total_cost"]');
        const hppValue = parseFloat(hppInput.value) || 0;
        
        let currentSubtotal = 0;
        document.querySelectorAll('.size-row').forEach(row => {
            currentSubtotal += parseInt(row.dataset.price) * parseInt(row.dataset.qty);
        });

        if (hppValue > currentSubtotal) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Valid',
                text: 'Total HPP / Modal Produksi (Rp ' + new Intl.NumberFormat('id-ID').format(hppValue) + ') tidak boleh melebihi Total Harga / Subtotal (Rp ' + new Intl.NumberFormat('id-ID').format(currentSubtotal) + ').',
                confirmButtonText: 'Perbaiki Input',
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-4' }
            }).then(() => {
                hppInput.classList.add('is-invalid');
                hppInput.focus();
            });
            
            // Hapus is-invalid saat diketik ulang
            hppInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            }, { once: true });
        }
    });
}

// ── Region Dependent Dropdown ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async function() {
    const provinceSelect = document.getElementById('customer_province');
    const citySelect = document.getElementById('customer_city');
    const districtSelect = document.getElementById('customer_district');
    
    const oldProvince = document.getElementById('old_province').value;
    const oldCity = document.getElementById('old_city').value;
    const oldDistrict = document.getElementById('old_district').value;

    let provincesData = [];
    let citiesData = [];

    // Load Provinces
    try {
        const res = await fetch('/api/regions/provinces');
        provincesData = await res.json();
        
        provincesData.forEach(prov => {
            const option = document.createElement('option');
            option.value = prov.name;
            option.dataset.id = prov.id;
            option.textContent = prov.name;
            if (prov.name === oldProvince) {
                option.selected = true;
            }
            provinceSelect.appendChild(option);
        });

        if (oldProvince) {
            provinceSelect.dispatchEvent(new Event('change'));
        }
    } catch (e) {
        console.error('Failed to load provinces:', e);
    }

    // Province Change -> Load Cities
    provinceSelect.addEventListener('change', async function() {
        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kab --</option>';
        districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        citySelect.disabled = true;
        districtSelect.disabled = true;

        const selectedOption = this.options[this.selectedIndex];
        const provinceId = selectedOption?.dataset?.id;

        if (!provinceId) return;

        try {
            const res = await fetch(`/api/regions/cities/${provinceId}`);
            citiesData = await res.json();
            
            citiesData.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name;
                option.dataset.id = city.id;
                option.textContent = city.name;
                if (city.name === oldCity) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });
            citySelect.disabled = false;

            if (oldCity && oldProvince === this.value) {
                citySelect.dispatchEvent(new Event('change'));
            }
        } catch (e) {
            console.error('Failed to load cities:', e);
        }
    });

    // City Change -> Load Districts
    citySelect.addEventListener('change', async function() {
        districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        districtSelect.disabled = true;

        const selectedOption = this.options[this.selectedIndex];
        const cityId = selectedOption?.dataset?.id;

        if (!cityId) return;

        try {
            const res = await fetch(`/api/regions/districts/${cityId}`);
            const districtsData = await res.json();
            
            districtsData.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.dataset.id = district.id;
                option.textContent = district.name;
                if (district.name === oldDistrict) {
                    option.selected = true;
                }
                districtSelect.appendChild(option);
            });
            districtSelect.disabled = false;
        } catch (e) {
            console.error('Failed to load districts:', e);
        }
    });
});
</script>
@endpush
