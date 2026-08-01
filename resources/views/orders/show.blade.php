@extends('layouts.app')

@section('title', 'Rincian Pesanan: ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
@endsection

@push('styles')
<style>
    .info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; font-weight: 600; margin-bottom: 0.25rem; }
    .info-value { font-size: 0.95rem; font-weight: 500; color: #111827; }
    .widget-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; height: 100%; }
    .widget-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; background: #fafafa; font-weight: 600; font-size: 0.9rem; color: #374151; display: flex; align-items: center; gap: 0.5rem; }
    .widget-body { padding: 1.25rem; }
    .size-table th { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; padding: 0.75rem; background: #f9fafb; }
    .size-table td { padding: 0.75rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; }
    .size-table tbody tr:last-child td { border-bottom: none; }
    .financial-row { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px dashed #e5e7eb; }
    .financial-row:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
<!-- Header & Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-3 mb-1">
            <h4 class="mb-0 fw-bold text-dark">{{ $order->order_number }}</h4>
            <x-badge :status="$order->current_status" />
        </div>
        <div class="text-muted small">
            Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} oleh {{ $order->creator?->name ?? 'Sistem' }}
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('orders.tracking', $order) }}" class="btn btn-primary shadow-sm"><i class="bi bi-clock-history me-1"></i> Pelacakan Produksi</a>
        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-secondary shadow-sm"><i class="bi bi-receipt me-1"></i> Faktur & Surat Jalan</a>
        @if(Auth::user()?->isAdmin())
            @if($order->isEditable())
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-light border shadow-sm"><i class="bi bi-pencil"></i> Ubah</a>
            @endif
            @if($order->isDeletable())
            <button type="button" class="btn btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                <i class="bi bi-trash"></i> Hapus
            </button>
            @endif
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column (Main Content) -->
    <div class="col-lg-8">
        <div class="row g-4">
            
            <!-- Widget 1: Spesifikasi Produk & Ukuran -->
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-box-seam text-primary"></i> Spesifikasi Produk & Rincian Ukuran</div>
                    <div class="widget-body p-0">
                        <!-- Basic Specs -->
                        <div class="row g-0 border-bottom border-light p-4">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="info-label">Nama Produk</div>
                                <div class="fs-5 fw-bold text-dark">{{ $order->product_name }}</div>
                                <div class="text-muted small">{{ $order->product_type }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex flex-wrap gap-3">
                                    <div>
                                        <div class="info-label">Warna</div>
                                        <div class="info-value d-flex align-items-center gap-1">
                                            <span class="badge bg-light text-dark border fw-medium">{{ $order->color }}</span>
                                        </div>
                                    </div>
                                    @if($order->material)
                                    <div>
                                        <div class="info-label">Material</div>
                                        <div class="info-value">
                                            <span class="badge bg-light text-dark border fw-medium"><i class="bi bi-tag-fill text-muted me-1"></i>{{ $order->material }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="info-label">Total Item</div>
                                        <div class="info-value">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-bold">{{ $order->totalQuantity() }} pcs</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Size Table -->
                        <div class="p-4 bg-white">
                            @php
                                $genderLabels = [
                                    'male' => 'Laki-laki (Dewasa)',
                                    'female' => 'Perempuan (Dewasa)',
                                    'child' => 'Anak-anak (Unisex)'
                                ];
                                $genderIcons = [
                                    'male' => 'bi-gender-male text-primary',
                                    'female' => 'bi-gender-female text-danger',
                                    'child' => 'bi-emoji-smile text-success'
                                ];
                            @endphp
                            
                            <div class="table-responsive">
                                <table class="table size-table mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Ukuran</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-center">Jumlah (Pcs)</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->sizeDetailsByGender() as $gender => $details)
                                            @foreach($details as $index => $detail)
                                            <tr>
                                                @if($index === 0)
                                                <td rowspan="{{ count($details) }}" class="align-middle border-end border-light" style="width: 25%;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi {{ $genderIcons[$gender] }} fs-5"></i>
                                                        <span class="fw-semibold text-dark">{{ $genderLabels[$gender] ?? ucfirst($gender) }}</span>
                                                    </div>
                                                </td>
                                                @endif
                                                <td class="fw-bold text-dark" style="width: 15%;">{{ $detail->size }}</td>
                                                <td class="text-end text-muted" style="width: 20%;">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                                <td class="text-center fw-semibold text-primary" style="width: 15%;">{{ $detail->quantity }}</td>
                                                <td class="text-end fw-medium" style="width: 25%;">Rp {{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Total Box -->
                            <div class="mt-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                <div class="text-muted fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.05em;">Total Keseluruhan</div>
                                <div class="fs-4 fw-bold text-dark">{{ $order->totalQuantity() }} <span class="fs-6 text-muted fw-normal">pcs</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget 2: Ringkasan Finansial -->
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-wallet2 text-success"></i> Ringkasan Finansial</div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-md-7 offset-md-5">
                                <div class="financial-row">
                                    <span class="text-muted">Total Jumlah Pesanan</span>
                                    <span class="fw-medium text-dark">{{ $order->totalQuantity() }} pcs</span>
                                </div>
                                <div class="financial-row">
                                    <span class="fw-medium text-dark">Pendapatan (Omset)</span>
                                    <span class="fw-bold text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>
                                
                                @if(Auth::user()?->isAdmin() || Auth::user()?->isOwner())
                                <div class="financial-row">
                                    <span class="text-muted">Total HPP (Modal Produksi)</span>
                                    <span class="text-danger">- Rp {{ number_format($order->total_cost, 0, ',', '.') }}</span>
                                </div>
                                <div class="financial-row mt-2 pt-3 border-top border-dark border-opacity-10 border-2" style="border-bottom:none;">
                                    <span class="fw-bold text-dark fs-6">Profit Bersih</span>
                                    <span class="fw-bold text-success fs-4">Rp {{ number_format($order->getProfit(), 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-end mt-1">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                                        Margin Profit: {{ $order->getProfitMargin() }}%
                                    </span>
                                </div>
                                @else
                                <div class="financial-row mt-2 pt-3 border-top border-dark border-opacity-10 border-2" style="border-bottom:none;">
                                    <span class="fw-bold text-dark fs-6">Total Tagihan</span>
                                    <span class="fw-bold text-success fs-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Widget 3: Catatan (Jika ada) -->
            @if($order->notes)
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-card-text text-secondary"></i> Catatan Tambahan</div>
                    <div class="widget-body bg-light bg-opacity-50">
                        <p class="mb-0 text-dark fst-italic">"{{ $order->notes }}"</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Widget 4: Gallery Desain -->
            @if($order->designFiles->isNotEmpty())
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-images text-info"></i> Foto Desain / Referensi</div>
                    <div class="widget-body">
                        <div class="row g-3">
                            @foreach($order->designFiles as $file)
                            <div class="col-6 col-sm-4 col-md-3">
                                <a href="{{ $file->url }}" class="design-lightbox-trigger text-decoration-none" data-src="{{ $file->url }}" data-name="{{ $file->original_name }}">
                                    <div class="position-relative rounded-3 overflow-hidden shadow-sm border border-light" style="aspect-ratio:1; background:#f8f9fa;">
                                        <img src="{{ $file->url }}" class="w-100 h-100 object-fit-cover" alt="{{ $file->original_name }}"
                                            style="transition: transform 0.3s ease;"
                                            onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        <div class="position-absolute inset-0 d-flex align-items-center justify-content-center opacity-0 bg-dark bg-opacity-50"
                                            style="transition: opacity 0.2s; top:0; left:0; right:0; bottom:0;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                            <i class="bi bi-zoom-in text-white fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-2 text-truncate" title="{{ $file->original_name }}">
                                        {{ $file->original_name }}
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Right Column (Sidebar Widgets) -->
    <div class="col-lg-4">
        <div class="row g-4">
            
            <!-- Customer Profile Widget -->
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-person-circle text-primary"></i> Profil Pelanggan</div>
                    <div class="widget-body">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fs-4 fw-bold shadow-sm" style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); flex-shrink: 0;">
                                {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ $order->customer_name }}</h6>
                                @if($order->customer_category === 'b2b')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle fw-medium">B2B (Grosir)</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle fw-medium">Retail (Eceran)</span>
                                @endif
                            </div>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                            <li class="d-flex gap-3">
                                <i class="bi bi-telephone text-muted mt-1"></i>
                                <div>
                                    <div class="info-label">Telepon</div>
                                    <div class="info-value">{{ $order->customer_phone }}</div>
                                </div>
                            </li>
                            @if($order->customer_title)
                            <li class="d-flex gap-3">
                                <i class="bi bi-briefcase text-muted mt-1"></i>
                                <div>
                                    <div class="info-label">Jabatan / Instansi</div>
                                    <div class="info-value text-secondary">{!! nl2br(e($order->customer_title)) !!}</div>
                                </div>
                            </li>
                            @endif
                            @if($order->customer_address)
                            <li class="d-flex gap-3">
                                <i class="bi bi-geo-alt text-muted mt-1"></i>
                                <div>
                                    <div class="info-label">Alamat Lengkap</div>
                                    <div class="info-value" style="line-height: 1.4;">{{ $order->customer_address }}</div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Schedule & Status Widget -->
            <div class="col-12">
                <div class="widget-card">
                    <div class="widget-header"><i class="bi bi-calendar-range text-warning"></i> Jadwal Produksi</div>
                    <div class="widget-body">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                            <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                    <i class="bi bi-calendar-plus text-muted"></i>
                                    <span class="text-muted">Tanggal Pesan</span>
                                </div>
                                <span class="fw-medium text-dark">{{ $order->order_date?->format('d M Y') }}</span>
                            </li>
                            <li class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                    <i class="bi bi-calendar-x {{ $order->deadline?->isPast() ? 'text-danger' : 'text-muted' }}"></i>
                                    <span class="text-muted">Tenggat Waktu</span>
                                </div>
                                <span class="fw-bold {{ $order->deadline?->isPast() ? 'text-danger' : 'text-dark' }}">{{ $order->deadline?->format('d M Y') }}</span>
                            </li>
                            <li class="pt-2 mt-1 border-top border-light">
                                <div class="info-label mb-2">Status Saat Ini</div>
                                <div class="d-grid">
                                    <x-badge :status="$order->current_status" class="py-2" />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @if($order->isArchived())
            <div class="col-12">
                <div class="alert alert-secondary d-flex align-items-center gap-3 border-0 shadow-sm mb-0">
                    <i class="bi bi-archive-fill fs-3 text-secondary"></i>
                    <div>
                        <div class="fw-bold text-dark">Pesanan Diarsipkan</div>
                        <div class="small text-muted">Pada {{ $order->archived_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Lightbox Modal --}}
<div class="modal fade" id="designLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white" id="lightbox-filename"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="lightbox-img" src="" alt="" class="img-fluid rounded" style="max-height:80vh; object-fit:contain;">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a id="lightbox-download" href="" download class="btn btn-outline-light btn-sm">
                    <i class="bi bi-download me-1"></i> Unduh
                </a>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()?->isAdmin() && $order->isDeletable())
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteOrderModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Pesanan?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-muted pt-2 pb-4">
                Apakah Anda yakin ingin menghapus pesanan <strong>{{ $order->order_number }}</strong> secara permanen? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua riwayat pelacakan dan faktur yang terkait.
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('orders.destroy', $order) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">Ya, Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.querySelectorAll('.design-lightbox-trigger').forEach(function(el) {
    el.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('lightbox-img').src = this.dataset.src;
        document.getElementById('lightbox-filename').textContent = this.dataset.name;
        document.getElementById('lightbox-download').href = this.dataset.src;
        new bootstrap.Modal(document.getElementById('designLightboxModal')).show();
    });
});
</script>
@endpush
