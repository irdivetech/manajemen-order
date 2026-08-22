@extends('layouts.app')

@section('title', 'Buat Pesanan Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
@endsection

@section('content')
<x-card title="Rincian Pesanan Baru">
    <form id="order-form" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Informasi Pelanggan</h6>
            <div class="col-md-4">
                <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" required>
                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Pelanggan <span class="text-danger">*</span></label>
                <select name="customer_category" class="form-select @error('customer_category') is-invalid @enderror" required>
                    <option value="retail" {{ old('customer_category') === 'retail' ? 'selected' : '' }}>Retail (Eceran)</option>
                    <option value="b2b" {{ old('customer_category') === 'b2b' ? 'selected' : '' }}>B2B (Grosir / Instansi)</option>
                </select>
                @error('customer_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jabatan / Custom Nama</label>
                <textarea name="customer_title"
                    class="form-control @error('customer_title') is-invalid @enderror"
                    rows="3"
                    placeholder="cth: Ketua OSIS SMA N 1 Semarang&#10;atas nama seluruh anggota&#10;Angkatan 2024"
                    style="resize:vertical;">{{ old('customer_title') }}</textarea>
                <div class="form-text small text-muted">Opsional. Tampil di laporan.</div>
                @error('customer_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" value="{{ old('customer_address') }}" placeholder="cth: Jl. Merdeka No. 10">
                        @error('customer_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4">
                        <label class="form-label">Provinsi</label>
                        <select name="customer_province" id="customer_province" class="form-select @error('customer_province') is-invalid @enderror">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                        <input type="hidden" id="old_province" value="{{ old('customer_province') }}">
                        @error('customer_province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4">
                        <label class="form-label">Kota / Kabupaten</label>
                        <select name="customer_city" id="customer_city" class="form-select @error('customer_city') is-invalid @enderror" disabled>
                            <option value="">-- Pilih Kota/Kab --</option>
                        </select>
                        <input type="hidden" id="old_city" value="{{ old('customer_city') }}">
                        @error('customer_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-4">
                        <label class="form-label">Kecamatan</label>
                        <select name="customer_district" id="customer_district" class="form-select @error('customer_district') is-invalid @enderror" disabled>
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                        <input type="hidden" id="old_district" value="{{ old('customer_district') }}">
                        @error('customer_district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Rincian Produk</h6>
            <div class="col-md-4">
                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" required>
                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Baju <span class="text-danger">*</span></label>
                <select name="clothing_category_id" class="form-select @error('clothing_category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($clothingCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('clothing_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('clothing_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Bahan / Material <span class="text-danger">*</span></label>
                <select name="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ old('material_id') == $mat->id ? 'selected' : '' }}>{{ $mat->name }}</option>
                    @endforeach
                </select>
                @error('material_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipe Lengan / Spesifikasi <span class="text-danger">*</span></label>
                <input type="text" name="product_type" class="form-control @error('product_type') is-invalid @enderror" value="{{ old('product_type') }}" placeholder="Lengan Pendek / Lengan Panjang" required>
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Model Produk</label>
                <input type="text" name="model_product" class="form-control @error('model_product') is-invalid @enderror" value="{{ old('model_product') }}">
                @error('model_product') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input type="hidden" name="has_embroidery" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_embroidery" name="has_embroidery" value="1" {{ old('has_embroidery') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="has_embroidery">Dengan Bordir / Sablon</label>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="text-primary mb-0">Rincian Warna, Ukuran & Jumlah</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-size-row">
                    <i class="bi bi-plus-lg"></i> Tambah Baris
                </button>
            </div>
            
            <div class="col-12">
                @error('size_details') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="size-details-table">
                        <thead class="table-light">
                            <tr>
                                <th>Warna</th>
                                <th>Gender</th>
                                <th>Tipe Ukuran</th>
                                <th>Ukuran</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th class="text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be added here by JS -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Keseluruhan:</td>
                                <td class="fw-bold"><span id="total-qty">0</span> pcs</td>
                                <td class="fw-bold text-success">Rp <span id="total-price">0</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Ketentuan Pesanan</h6>
            <div class="col-md-4">
                <label class="form-label">Tanggal Pesan <span class="text-danger">*</span></label>
                <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tenggat Waktu <span class="text-danger">*</span></label>
                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}" required>
                @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total HPP / Modal Produksi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost', 0) }}" required min="0" step="1000">
                    <button class="btn btn-outline-secondary" type="button" id="btn-calculate-hpp">
                        <i class="bi bi-calculator me-1"></i> Hitung Estimasi
                    </button>
                </div>
                <div class="form-text small text-muted">Estimasi total biaya (bahan+tenaga kerja)</div>
                @error('total_cost') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-12">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Foto Desain / Referensi dari Klien</h6>
            <div class="col-12">
                <div id="design-upload-area" class="border border-2 border-dashed rounded-3 p-4 text-center" style="cursor:pointer; border-color: #0d6efd !important; background: #f8f9ff;">
                    <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary mb-2 d-block"></i>
                    <p class="mb-1 fw-semibold text-primary">Klik atau seret & lepas foto desain di sini</p>
                    <p class="text-muted small mb-0">Format: JPG, PNG, WEBP &bull; Maks. 2MB per file &bull; Maks. 5 foto</p>
                    <input type="file" id="design-files-input" name="design_files[]" multiple accept="image/jpeg,image/png,image/webp" class="d-none">
                </div>
                @error('design_files') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('design_files.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                <div id="design-preview" class="row g-2 mt-2"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-4">
            <a href="{{ route('orders.index') }}" class="btn btn-light border">Batal</a>
            <button type="submit" class="btn btn-primary">Buat Pesanan</button>
        </div>
    </form>
</x-card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#size-details-table tbody');
    const addBtn = document.getElementById('add-size-row');
    const totalQtySpan = document.getElementById('total-qty');
    const totalPriceSpan = document.getElementById('total-price');
    
    let rowIndex = 0;
    
    const masterGenders = @json($genders);
    const masterSizeCategories = @json($sizeCategories);
    const masterSizes = @json($sizes);
    
    // Add old data if validation fails
    const oldDetails = @json(old('size_details', []));
    
    function calculateTotal() {
        let totalQty = 0;
        let totalPrice = 0;
        
        document.querySelectorAll('#size-details-table tbody tr').forEach(tr => {
            const qty = parseInt(tr.querySelector('.qty-input').value || 0);
            const price = parseInt(tr.querySelector('.price-input').value || 0);
            const subtotal = qty * price;
            
            tr.querySelector('.subtotal-text').textContent = new Intl.NumberFormat('id-ID').format(subtotal);
            
            totalQty += qty;
            totalPrice += subtotal;
        });
        
        totalQtySpan.textContent = totalQty;
        totalPriceSpan.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
    }
    
    function updateSizeOptions(selectElement, type, currentValue = null) {
        selectElement.innerHTML = '<option value="">Ukuran...</option>';
        if (!type) return;
        
        masterSizes.filter(s => s.size_type == type).forEach(size => {
            const option = document.createElement('option');
            option.value = size.id;
            option.textContent = size.label;
            if (size.id == currentValue) option.selected = true;
            selectElement.appendChild(option);
        });
    }
    
    function addRow(data = {}) {
        const tr = document.createElement('tr');
        
        let genderOptions = '<option value="">Pilih...</option>';
        masterGenders.forEach(g => {
            genderOptions += `<option value="${g.id}" ${data.gender_id == g.id ? 'selected' : ''}>${g.label}</option>`;
        });

        const currentType = data.size_type || 'standard';

        tr.innerHTML = `
            <td>
                <input type="text" name="size_details[${rowIndex}][color]" class="form-control form-control-sm" value="${data.color || ''}" placeholder="Warna" required>
            </td>
            <td>
                <select name="size_details[${rowIndex}][gender_id]" class="form-select form-select-sm" required>
                    ${genderOptions}
                </select>
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <select name="size_details[${rowIndex}][size_type]" class="form-select form-select-sm type-select" required>
                        <option value="standard" ${currentType == 'standard' ? 'selected' : ''}>Standard</option>
                        <option value="big" ${currentType == 'big' ? 'selected' : ''}>Big Size</option>
                    </select>
                </div>
            </td>
            <td style="vertical-align: top;">
                <select name="size_details[${rowIndex}][size_id]" class="form-select form-select-sm size-select" required>
                    <option value="">Ukuran...</option>
                </select>
            </td>
            <td style="vertical-align: top;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="size_details[${rowIndex}][price]" class="form-control price-input" value="${data.price || ''}" required min="0">
                </div>
            </td>
            <td style="vertical-align: top;">
                <input type="number" name="size_details[${rowIndex}][quantity]" class="form-control form-control-sm qty-input" value="${data.quantity || 1}" required min="1">
            </td>
            <td class="align-top fw-medium text-nowrap" style="font-size: 0.875rem; padding-top: 0.6rem;">
                Rp <span class="subtotal-text">0</span>
            </td>
            <td class="text-center align-top" style="padding-top: 0.45rem;">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row py-0 px-1"><i class="bi bi-x fs-5"></i></button>
            </td>
        `;
        
        tableBody.appendChild(tr);
        
        const typeSelect = tr.querySelector('.type-select');
        const sizeSelect = tr.querySelector('.size-select');
        const priceInput = tr.querySelector('.price-input');
        const qtyInput = tr.querySelector('.qty-input');
        const removeBtn = tr.querySelector('.remove-row');
        
        // Initial sizes
        updateSizeOptions(sizeSelect, currentType, data.size_id);
        
        typeSelect.addEventListener('change', function() {
            updateSizeOptions(sizeSelect, this.value);
        });
        
        priceInput.addEventListener('input', calculateTotal);
        qtyInput.addEventListener('input', calculateTotal);
        
        removeBtn.addEventListener('click', function() {
            tr.remove();
            calculateTotal();
            if (tableBody.children.length === 0) {
                addRow(); // Always keep at least one row
            }
        });
        
        rowIndex++;
        calculateTotal();
    }
    
    addBtn.addEventListener('click', () => addRow());
    
    // Initialize
    const oldDetailsArray = Object.values(oldDetails);
    if (oldDetailsArray.length > 0) {
        oldDetailsArray.forEach(detail => addRow(detail));
    } else {
        addRow(); // Add one empty row by default
    }

    // Hitung Estimasi HPP
    document.getElementById('btn-calculate-hpp').addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        const materialId = document.querySelector('select[name="material_id"]').value;
        const clothingCategoryId = document.querySelector('select[name="clothing_category_id"]').value;
        
        if (!materialId || !clothingCategoryId) {
            Swal.fire('Perhatian', 'Silakan pilih Kategori Baju dan Bahan / Material terlebih dahulu.', 'warning');
            return;
        }

        const sizes = [];
        document.querySelectorAll('#size-details-table tbody tr').forEach(tr => {
            const sizeId = tr.querySelector('.size-select')?.value;
            const qty = tr.querySelector('.qty-input')?.value;
            if (sizeId && qty) {
                sizes.push({ size_id: sizeId, quantity: qty });
            }
        });

        if (sizes.length === 0) {
            Swal.fire('Perhatian', 'Silakan tambahkan minimal satu rincian ukuran.', 'warning');
            return;
        }

        try {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghitung...';
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
                showConfirmButton: false
            });
        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'Gagal menghitung estimasi HPP.', 'error');
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
            document.querySelectorAll('#size-details-table tbody tr').forEach(tr => {
                const qty = parseInt(tr.querySelector('.qty-input').value || 0);
                const price = parseInt(tr.querySelector('.price-input').value || 0);
                currentSubtotal += (qty * price);
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
});
</script>
<script>
// ── Design File Upload ────────────────────────────────────────────────────────
(function () {
    const MAX_FILES = 5;
    const uploadArea  = document.getElementById('design-upload-area');
    const fileInput   = document.getElementById('design-files-input');
    const previewGrid = document.getElementById('design-preview');
    let selectedFiles = [];

    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-primary');
        uploadArea.style.background = '#eef2ff';
    });
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.background = '#f8f9ff';
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.background = '#f8f9ff';
        handleFiles(Array.from(e.dataTransfer.files));
    });

    fileInput.addEventListener('change', () => {
        handleFiles(Array.from(fileInput.files));
    });

    function handleFiles(newFiles) {
        newFiles.forEach(file => {
            if (selectedFiles.length >= MAX_FILES) return;
            if (!file.type.match(/image\/(jpeg|png|webp)/)) return;
            selectedFiles.push(file);
        });
        syncInput();
        renderPreviews();
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        syncInput();
        renderPreviews();
    }

    function syncInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2';
                col.innerHTML = `
                    <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1; background:#000;">
                        <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" alt="${file.name}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width:24px;height:24px;line-height:1;" data-index="${index}">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-1" style="font-size:0.65rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${file.name}
                        </div>
                    </div>`;
                col.querySelector('button').addEventListener('click', () => removeFile(index));
                previewGrid.appendChild(col);
            };
            reader.readAsDataURL(file);
        });

        const remaining = MAX_FILES - selectedFiles.length;
        if (remaining > 0) {
            const info = document.createElement('div');
            info.className = 'col-12';
            info.innerHTML = `<p class="text-muted small mt-1 mb-0">${selectedFiles.length} foto dipilih &bull; Sisa slot: ${remaining}</p>`;
            previewGrid.appendChild(info);
        } else {
            const warn = document.createElement('div');
            warn.className = 'col-12';
            warn.innerHTML = `<p class="text-warning small mt-1 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Batas maksimum 5 foto telah tercapai.</p>`;
            previewGrid.appendChild(warn);
        }
    }
})();

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
