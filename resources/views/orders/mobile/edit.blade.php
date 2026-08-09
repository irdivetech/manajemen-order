@extends('layouts.mobile')

@section('title', 'Ubah Pesanan: ' . $order->order_number)

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('orders.show', $order) }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Ubah {{ $order->order_number }}</h1>
            <p class="page-sub">Perbarui detail pesanan</p>
        </div>
    </div>
@endsection

@section('content')

<form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data" id="editOrderForm">
    @csrf
    @method('PUT')

    {{-- ── Informasi Pelanggan ── --}}
    <div class="section-title">Informasi Pelanggan</div>
    <div class="m-card">
        <div class="m-card-body">
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="customer_name"
                       class="form-control @error('customer_name') is-invalid @enderror"
                       value="{{ old('customer_name', $order->customer_name) }}" required>
                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="customer_phone"
                       class="form-control @error('customer_phone') is-invalid @enderror"
                       value="{{ old('customer_phone', $order->customer_phone) }}" required>
                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Kategori Pelanggan <span class="text-danger">*</span></label>
                <select name="customer_category" class="form-select @error('customer_category') is-invalid @enderror" required>
                    <option value="retail" {{ old('customer_category', $order->customer_category) === 'retail' ? 'selected' : '' }}>Retail (Eceran)</option>
                    <option value="b2b"    {{ old('customer_category', $order->customer_category) === 'b2b'    ? 'selected' : '' }}>B2B (Grosir / Instansi)</option>
                </select>
                @error('customer_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Jabatan / Custom Nama</label>
                <textarea name="customer_title"
                       class="form-control @error('customer_title') is-invalid @enderror"
                       rows="3"
                       placeholder="cth: Ketua OSIS&#10;atas nama semua anggota" style="resize:vertical;">{{ old('customer_title', $order->customer_title) }}</textarea>
                <div class="text-xs text-muted mt-1">Opsional — bisa lebih dari satu baris</div>
                @error('customer_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Alamat Lengkap</label>
                <input type="text" name="customer_address"
                       class="form-control @error('customer_address') is-invalid @enderror"
                       value="{{ old('customer_address', $order->customer_address) }}" placeholder="cth: Jl. Merdeka No. 10">
                @error('customer_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label text-sm fw-6">Kota / Kab.</label>
                    <input type="text" name="customer_city"
                           class="form-control @error('customer_city') is-invalid @enderror"
                           value="{{ old('customer_city', $order->customer_city) }}">
                    @error('customer_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6">
                    <label class="form-label text-sm fw-6">Kecamatan</label>
                    <input type="text" name="customer_district"
                           class="form-control @error('customer_district') is-invalid @enderror"
                           value="{{ old('customer_district', $order->customer_district) }}">
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
                       value="{{ old('product_name', $order->product_name) }}" required placeholder="Cth: Kaos Oblong, Jaket, dll.">
                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Kategori Baju <span class="text-danger">*</span></label>
                <select name="clothing_category_id" class="form-select @error('clothing_category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($clothingCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('clothing_category_id', $order->clothing_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('clothing_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Bahan / Material <span class="text-danger">*</span></label>
                <select name="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ old('material_id', $order->material_id) == $mat->id ? 'selected' : '' }}>{{ $mat->name }}</option>
                    @endforeach
                </select>
                @error('material_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Tipe Lengan / Spesifikasi <span class="text-danger">*</span></label>
                <input type="text" name="product_type"
                       class="form-control @error('product_type') is-invalid @enderror"
                       value="{{ old('product_type', $order->product_type) }}" required placeholder="Lengan Pendek / Lengan Panjang">
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label text-sm fw-6">Model Produk</label>
                <input type="text" name="model_product"
                       class="form-control @error('model_product') is-invalid @enderror"
                       value="{{ old('model_product', $order->model_product) }}">
                @error('model_product') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <div class="form-check form-switch mt-3">
                    <input type="hidden" name="has_embroidery" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_embroidery" name="has_embroidery" value="1" {{ old('has_embroidery', $order->has_embroidery) ? 'checked' : '' }}>
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
            @php 
                $sizeIndex = 0; 
                $oldDetails = old('size_details');
                if (empty($oldDetails)) {
                    $oldDetails = $order->sizeDetails->toArray();
                } else {
                    $oldDetails = array_values($oldDetails);
                }
            @endphp
            @foreach($oldDetails as $detail)
            @php
                $gender = \App\Models\MasterGender::find($detail['gender_id']);
                $size = \App\Models\MasterSize::find($detail['size_id']);
                $cat = \App\Models\MasterSizeCategory::find($detail['size_category_id']);
            @endphp
            <div class="m-list-item size-row p-3 border-bottom" data-price="{{ $detail['price'] }}" data-qty="{{ $detail['quantity'] }}" data-row="{{ $sizeIndex }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary text-xs">{{ $detail['color'] }}</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-xs">{{ $gender ? $gender->name : '' }}</span>
                            <span class="fw-7">{{ $size ? $size->name : '' }}</span>
                            <span class="text-xs text-muted">({{ ucfirst($detail['size_type']) }})</span>
                        </div>
                        <div class="text-xs text-muted">Rp {{ number_format($detail['price'], 0, ',', '.') }} × {{ $detail['quantity'] }} pcs = <strong class="text-dark">Rp {{ number_format($detail['price'] * $detail['quantity'], 0, ',', '.') }}</strong></div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border text-danger remove-row" style="flex-shrink:0;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <input type="hidden" name="size_details[{{ $sizeIndex }}][color]"   value="{{ $detail['color'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][gender_id]" value="{{ $detail['gender_id'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][size_category_id]" value="{{ $detail['size_category_id'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][size_type]" value="{{ $detail['size_type'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][size_id]" value="{{ $detail['size_id'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][price]"    value="{{ $detail['price'] }}">
                <input type="hidden" name="size_details[{{ $sizeIndex }}][quantity]" value="{{ $detail['quantity'] }}">
            </div>
            @php $sizeIndex++; @endphp
            @endforeach
            
            <div class="empty-state py-3" id="size-empty-msg" style="display:{{ count($oldDetails) > 0 ? 'none' : 'block' }}">
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
                       value="{{ old('order_date', $order->order_date?->format('Y-m-d')) }}" required>
                @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Tenggat Waktu <span class="text-danger">*</span></label>
                <input type="date" name="deadline"
                       class="form-control @error('deadline') is-invalid @enderror"
                       value="{{ old('deadline', $order->deadline?->format('Y-m-d')) }}" required>
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
                <input type="number" name="total_cost"
                       class="form-control @error('total_cost') is-invalid @enderror"
                       value="{{ old('total_cost', round($order->total_cost)) }}" min="0" step="1000" required>
                <div class="text-xs text-muted mt-1">Estimasi biaya bahan & tenaga kerja</div>
                @error('total_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label text-sm fw-6">Catatan Tambahan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                          rows="3" placeholder="Catatan khusus untuk order ini...">{{ old('notes', $order->notes) }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ── Upload Foto Desain ── --}}
    <div class="section-title">Foto Desain (Opsional)</div>
    
    @if($order->designFiles->isNotEmpty())
    <div class="m-card mb-2">
        <div class="m-card-header"><h2>Desain Saat Ini</h2></div>
        <div class="m-card-body p-0">
            @foreach($order->designFiles as $file)
            <div class="m-list-item d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $file->url }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                    <div class="text-xs text-truncate" style="max-width:150px;">{{ $file->original_name }}</div>
                </div>
                <div class="form-check m-0">
                    <input class="form-check-input" type="checkbox" name="delete_design_files[]" value="{{ $file->id }}" id="del_{{ $file->id }}">
                    <label class="form-check-label text-xs text-danger" for="del_{{ $file->id }}">Hapus</label>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="m-card">
        <div class="m-card-body">
            <label class="form-label text-sm fw-6">Upload Tambahan (Opsional)</label>
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
            <a href="{{ route('orders.show', $order) }}" class="btn btn-light border flex-shrink-0">Batal</a>
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label text-sm fw-6">Kategori</label>
                <select id="modal-size-cat" class="form-select">
                    <option value="">Pilih...</option>
                    @foreach($sizeCategories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label text-sm fw-6">Tipe Ukuran</label>
                <select id="modal-size-type" class="form-select">
                    <option value="standard">Standard</option>
                    <option value="big">Big Size</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label text-sm fw-6">Ukuran</label>
            <select id="modal-size-id" class="form-select">
                <option value="">Pilih Kategori...</option>
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
let rowIndex = {{ count($oldDetails ?? []) }};

const masterSizes = @json($sizes);
const modalSizeCat = document.getElementById('modal-size-cat');
const modalSizeId = document.getElementById('modal-size-id');

modalSizeCat.addEventListener('change', function() {
    modalSizeId.innerHTML = '<option value="">Pilih Ukuran...</option>';
    const catId = this.value;
    if (catId) {
        masterSizes.filter(s => s.size_category_id == catId).forEach(size => {
            const option = document.createElement('option');
            option.value = size.id;
            option.textContent = size.name;
            modalSizeId.appendChild(option);
        });
    }
});

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
    const sizeCatId = document.getElementById('modal-size-cat').value;
    const sizeTypeId = document.getElementById('modal-size-type').value;
    const sizeId = document.getElementById('modal-size-id').value;
    const sizeText = modalSizeId.options[modalSizeId.selectedIndex]?.text || '';
    const price  = parseInt(document.getElementById('modal-price').value) || 0;
    const qty    = parseInt(document.getElementById('modal-qty').value) || 0;

    if (!color || !sizeCatId || !sizeId || qty <= 0 || price < 0) {
        alert('Harap isi warna, kategori, ukuran, harga, dan jumlah dengan benar.');
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
            <input type="hidden" name="size_details[${rowIndex}][size_category_id]" value="${sizeCatId}">
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

// Initial update
updateSummary();

// Frontend validation for HPP vs Subtotal
const form = document.getElementById('editOrderForm');
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
</script>
@endpush
