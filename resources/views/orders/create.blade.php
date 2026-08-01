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
            <div class="col-md-6">
                <label class="form-label">Jabatan / Custom Nama</label>
                <textarea name="customer_title"
                    class="form-control @error('customer_title') is-invalid @enderror"
                    rows="3"
                    placeholder="cth: Ketua OSIS SMA N 1 Semarang&#10;atas nama seluruh anggota&#10;Angkatan 2024"
                    style="resize:vertical;">{{ old('customer_title') }}</textarea>
                <div class="form-text small text-muted">Opsional — bisa lebih dari satu baris. Tampil di detail pesanan &amp; laporan.</div>
                @error('customer_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Alamat Pemesan</label>
                <input type="text" name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" value="{{ old('customer_address') }}" placeholder="cth: Jl. Merdeka No. 10, Semarang">
                <div class="form-text small text-muted">Opsional — untuk keperluan pengiriman / surat jalan.</div>
                @error('customer_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <h6 class="border-bottom pb-2 mb-3 mt-4 text-primary">Rincian Produk</h6>
            <div class="col-md-6">
                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" required>
                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipe / Model Produk <span class="text-danger">*</span></label>
                <input type="text" name="product_type" class="form-control @error('product_type') is-invalid @enderror" value="{{ old('product_type') }}" required>
                @error('product_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Warna <span class="text-danger">*</span></label>
                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}" required>
                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Bahan / Material</label>
                <input type="text" name="material" class="form-control @error('material') is-invalid @enderror" value="{{ old('material') }}" placeholder="cth: Lacoste CVC, Katun 30s, Polyester">
                <div class="form-text small text-muted">Opsional — jenis kain / bahan yang digunakan.</div>
                @error('material') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4">
                <h6 class="text-primary mb-0">Rincian Ukuran & Jumlah</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-size-row">
                    <i class="bi bi-plus-lg"></i> Tambah Ukuran
                </button>
            </div>
            
            <div class="col-12">
                @error('size_details') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="size-details-table">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori</th>
                                <th>Ukuran</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah (Qty)</th>
                                <th>Subtotal</th>
                                <th class="text-center" style="width: 50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be added here by JS -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Keseluruhan:</td>
                                <td class="fw-bold"><span id="total-qty">0</span> pcs</td>
                                <td colspan="2" class="fw-bold text-success">Rp <span id="total-price">0</span></td>
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
                <label class="form-label">Total HPP / Modal Produksi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost', 0) }}" required min="0">
                </div>
                <div class="form-text small text-muted">Estimasi total biaya produksi (bahan+tenaga kerja) untuk menghitung profit.</div>
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
    
    const standardSizes = @json(\App\Models\OrderSizeDetail::STANDARD_SIZES);
    const childSizes = @json(\App\Models\OrderSizeDetail::CHILD_SIZES);
    
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
    
    function updateSizeOptions(selectElement, gender) {
        const sizes = gender === 'child' ? childSizes : standardSizes;
        const currentValue = selectElement.value;
        
        selectElement.innerHTML = '<option value="">Pilih Ukuran...</option>';
        sizes.forEach(size => {
            const option = document.createElement('option');
            option.value = size;
            option.textContent = size;
            if (size === currentValue) option.selected = true;
            selectElement.appendChild(option);
        });
    }
    
    function addRow(data = {}) {
        const tr = document.createElement('tr');
        
        tr.innerHTML = `
            <td>
                <select name="size_details[${rowIndex}][gender]" class="form-select gender-select" required>
                    <option value="">Pilih...</option>
                    <option value="male" ${data.gender === 'male' ? 'selected' : ''}>Laki-laki (Dewasa)</option>
                    <option value="female" ${data.gender === 'female' ? 'selected' : ''}>Perempuan (Dewasa)</option>
                    <option value="child" ${data.gender === 'child' ? 'selected' : ''}>Anak-anak (Unisex)</option>
                </select>
            </td>
            <td>
                <select name="size_details[${rowIndex}][size]" class="form-select size-select" required>
                    <option value="">Pilih Kategori Dulu...</option>
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="size_details[${rowIndex}][price]" class="form-control price-input" value="${data.price || ''}" required min="0">
                </div>
            </td>
            <td>
                <input type="number" name="size_details[${rowIndex}][quantity]" class="form-control qty-input" value="${data.quantity || 1}" required min="1">
            </td>
            <td class="align-middle fw-medium">
                Rp <span class="subtotal-text">0</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        
        tableBody.appendChild(tr);
        
        const genderSelect = tr.querySelector('.gender-select');
        const sizeSelect = tr.querySelector('.size-select');
        const priceInput = tr.querySelector('.price-input');
        const qtyInput = tr.querySelector('.qty-input');
        const removeBtn = tr.querySelector('.remove-row');
        
        // Initial size options if data exists
        if (data.gender) {
            sizeSelect.value = data.size || '';
            updateSizeOptions(sizeSelect, data.gender);
            sizeSelect.value = data.size || '';
        }
        
        genderSelect.addEventListener('change', function() {
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
    if (oldDetails.length > 0) {
        oldDetails.forEach(detail => addRow(detail));
    } else {
        addRow(); // Add one empty row by default
    }

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
</script>
@endpush

