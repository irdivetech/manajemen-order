@extends('layouts.app')

@section('title', 'Ubah Pesanan: ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted">{{ $order->order_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
@endsection

@section('content')
<x-card title="Ubah Rincian Pesanan">
    <form id="order-form" action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Informasi Pelanggan</h6>
            <div class="col-md-4">
                <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $order->customer_name) }}" required>
                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', $order->customer_phone) }}" required>
                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Pelanggan <span class="text-danger">*</span></label>
                <select name="customer_category" class="form-select @error('customer_category') is-invalid @enderror" required>
                    <option value="retail" {{ old('customer_category', $order->customer_category) === 'retail' ? 'selected' : '' }}>Retail (Eceran)</option>
                    <option value="b2b" {{ old('customer_category', $order->customer_category) === 'b2b' ? 'selected' : '' }}>B2B (Grosir / Instansi)</option>
                </select>
                @error('customer_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Jabatan / Custom Nama</label>
                <textarea name="customer_title"
                    class="form-control @error('customer_title') is-invalid @enderror"
                    rows="3"
                    placeholder="cth: Ketua OSIS SMA N 1 Semarang&#10;atas nama seluruh anggota&#10;Angkatan 2024"
                    style="resize:vertical;">{{ old('customer_title', $order->customer_title) }}</textarea>
                <div class="form-text small text-muted">Opsional. Tampil di laporan.</div>
                @error('customer_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <input type="text" name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" value="{{ old('customer_address', $order->customer_address) }}" placeholder="cth: Jl. Merdeka No. 10">
                        @error('customer_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kota / Kabupaten</label>
                        <input type="text" name="customer_city" class="form-control @error('customer_city') is-invalid @enderror" value="{{ old('customer_city', $order->customer_city) }}">
                        @error('customer_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="customer_district" class="form-control @error('customer_district') is-invalid @enderror" value="{{ old('customer_district', $order->customer_district) }}">
                        @error('customer_district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Rincian Produk</h6>
            <div class="col-md-4">
                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $order->product_name) }}" required>
                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori Baju <span class="text-danger">*</span></label>
                <select name="clothing_category_id" class="form-select @error('clothing_category_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($clothingCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('clothing_category_id', $order->clothing_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('clothing_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Bahan / Material <span class="text-danger">*</span></label>
                <select name="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ old('material_id', $order->material_id) == $mat->id ? 'selected' : '' }}>{{ $mat->name }}</option>
                    @endforeach
                </select>
                @error('material_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipe Lengan / Spesifikasi <span class="text-danger">*</span></label>
                <input type="text" name="product_type" class="form-control @error('product_type') is-invalid @enderror" value="{{ old('product_type', $order->product_type) }}" required>
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Model Produk</label>
                <input type="text" name="model_product" class="form-control @error('model_product') is-invalid @enderror" value="{{ old('model_product', $order->model_product) }}">
                @error('model_product') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input type="hidden" name="has_embroidery" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="has_embroidery" name="has_embroidery" value="1" {{ old('has_embroidery', $order->has_embroidery) ? 'checked' : '' }}>
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
                <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', $order->order_date?->format('Y-m-d')) }}" required>
                @error('order_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Tenggat Waktu <span class="text-danger">*</span></label>
                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline', $order->deadline?->format('Y-m-d')) }}" required>
                @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total HPP / Modal Produksi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost', round($order->total_cost)) }}" required min="0" step="1000">
                    <button class="btn btn-outline-secondary" type="button" id="btn-calculate-hpp">
                        <i class="bi bi-calculator me-1"></i> Hitung Estimasi
                    </button>
                </div>
                <div class="form-text small text-muted">Estimasi total biaya (bahan+tenaga kerja)</div>
                @error('total_cost') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            
            <div class="col-12">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $order->notes) }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ── Foto Desain ───────────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Foto Desain / Referensi dari Klien</h6>
            <div class="col-12">

                {{-- Existing files --}}
                @if($order->designFiles->isNotEmpty())
                <p class="text-muted small mb-2">Foto yang sudah tersimpan (centang untuk menghapus):</p>
                <div class="row g-2 mb-3" id="existing-design-files">
                    @foreach($order->designFiles as $file)
                    <div class="col-6 col-md-3 col-lg-2" id="existing-file-{{ $file->id }}">
                        <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1;">
                            <img src="{{ $file->url }}" class="w-100 h-100 object-fit-cover" alt="{{ $file->original_name }}">
                            <label class="position-absolute top-0 end-0 m-1" title="Hapus">
                                <input type="checkbox" name="delete_design_files[]" value="{{ $file->id }}"
                                    class="d-none delete-file-cb" data-id="{{ $file->id }}">
                                <span class="btn btn-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width:24px;height:24px;">
                                    <i class="bi bi-trash"></i>
                                </span>
                            </label>
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-1" style="font-size:0.65rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $file->original_name }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Upload new files --}}
                <div id="design-upload-area" class="border border-2 border-dashed rounded-3 p-4 text-center" style="cursor:pointer; border-color: #0d6efd !important; background: #f8f9ff;">
                    <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary mb-2 d-block"></i>
                    <p class="mb-1 fw-semibold text-primary">Klik atau seret & lepas foto baru di sini</p>
                    <p class="text-muted small mb-0">Format: JPG, PNG, WEBP &bull; Maks. 2MB per file &bull; Total maks. 5 foto</p>
                    <input type="file" id="design-files-input" name="design_files[]" multiple accept="image/jpeg,image/png,image/webp" class="d-none">
                </div>
                @error('design_files') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @error('design_files.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                <div id="design-preview" class="row g-2 mt-2"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-4">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-light border">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    
    // Add old data if validation fails, otherwise load from order
    let existingDetails = @json(old('size_details', []));
    if (Object.keys(existingDetails).length === 0) {
        existingDetails = @json($order->sizeDetails ?? []);
    } else {
        existingDetails = Object.values(existingDetails);
    }
    
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
    if (existingDetails.length > 0) {
        existingDetails.forEach(detail => addRow(detail));
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
            Swal.fire('Error', 'Gagal menghitung estimasi HPP. Pastikan data estimasi penggunaan bahan tersedia.', 'error');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });

    // Frontend validation for HPP vs Subtotal
    const form = document.getElementById('editOrderForm');
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
// ── Existing file delete toggle ───────────────────────────────────────────────
document.querySelectorAll('.delete-file-cb').forEach(cb => {
    cb.addEventListener('change', function () {
        const wrapper = this.closest('[id^="existing-file-"]');
        if (this.checked) {
            wrapper.style.opacity = '0.3';
            wrapper.querySelector('img').style.filter = 'grayscale(100%)';
        } else {
            wrapper.style.opacity = '1';
            wrapper.querySelector('img').style.filter = '';
        }
    });
});

// ── New Design File Upload ────────────────────────────────────────────────────
(function () {
    const MAX_FILES    = 5;
    const existingCount = document.querySelectorAll('[id^="existing-file-"]').length;
    const uploadArea   = document.getElementById('design-upload-area');
    const fileInput    = document.getElementById('design-files-input');
    const previewGrid  = document.getElementById('design-preview');
    let selectedFiles  = [];

    uploadArea.addEventListener('click', () => fileInput.click());
    uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.background = '#eef2ff'; });
    uploadArea.addEventListener('dragleave', () => { uploadArea.style.background = '#f8f9ff'; });
    uploadArea.addEventListener('drop', e => { e.preventDefault(); uploadArea.style.background = '#f8f9ff'; handleFiles(Array.from(e.dataTransfer.files)); });
    fileInput.addEventListener('change', () => handleFiles(Array.from(fileInput.files)));

    function handleFiles(newFiles) {
        newFiles.forEach(file => {
            const deletedCount = document.querySelectorAll('.delete-file-cb:checked').length;
            const effectiveExisting = existingCount - deletedCount;
            if (selectedFiles.length + effectiveExisting >= MAX_FILES) return;
            if (!file.type.match(/image\/(jpeg|png|webp)/)) return;
            selectedFiles.push(file);
        });
        syncInput();
        renderPreviews();
    }

    function removeFile(index) { selectedFiles.splice(index, 1); syncInput(); renderPreviews(); }

    function syncInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2';
                col.innerHTML = `
                    <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1;background:#000;">
                        <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" alt="${file.name}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width:24px;height:24px;line-height:1;">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-1" style="font-size:0.65rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            ${file.name}
                        </div>
                    </div>`;
                col.querySelector('button').addEventListener('click', () => removeFile(index));
                previewGrid.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
        if (selectedFiles.length > 0) {
            const info = document.createElement('div');
            info.className = 'col-12';
            info.innerHTML = `<p class="text-muted small mt-1 mb-0">${selectedFiles.length} foto baru dipilih</p>`;
            previewGrid.appendChild(info);
        }
    }
})();
</script>
@endpush
