@extends('layouts.app')

@section('title', 'Rincian Pesanan: ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <!-- Quick Actions -->
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a href="{{ route('orders.tracking', $order) }}" class="btn btn-primary"><i class="bi bi-clock-history me-1"></i> Pelacakan Produksi</a>
        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-secondary"><i class="bi bi-receipt me-1"></i> Lihat Faktur</a>
        @if(Auth::user()?->isAdmin())
            @if($order->isEditable())
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-light border"><i class="bi bi-pencil me-1"></i> Ubah</a>
            @endif
            @if($order->isDeletable())
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                <i class="bi bi-trash"></i>
            </button>
            @endif
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-card title="Informasi Pesanan">
            <div class="row g-4">
                <!-- Customer Info -->
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Detail Pelanggan</h6>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted small d-block">Nama</span>
                            <span class="fw-semibold">{{ $order->customer_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small d-block">Telepon</span>
                                <span class="fw-medium">{{ $order->customer_phone }}</span>
                            </div>
                            <div>
                                @if($order->customer_category === 'b2b')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">B2B (Grosir)</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">Retail (Eceran)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Spesifikasi Produk</h6>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted small d-block">Produk</span>
                            <span class="fw-semibold">{{ $order->product_name }} <span class="text-muted fw-normal">({{ $order->product_type }})</span></span>
                        </div>
                        <div class="d-flex gap-4 mb-3">
                            <div>
                                <span class="text-muted small d-block">Warna</span>
                                <span class="fw-medium">{{ $order->color }}</span>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Total Jumlah</span>
                                <span class="fw-medium">{{ $order->totalQuantity() }} pcs</span>
                            </div>
                        </div>

                        <span class="text-muted small d-block mb-2">Rincian Ukuran & Harga</span>
                        <div class="row g-3">
                            @php
                                $genderLabels = [
                                    'male' => 'LAKI-LAKI (DEWASA)',
                                    'female' => 'PEREMPUAN (DEWASA)',
                                    'child' => 'ANAK-ANAK (UNISEX)'
                                ];
                                $genderColors = [
                                    'male' => 'primary',
                                    'female' => 'danger',
                                    'child' => 'success'
                                ];
                            @endphp
                            @foreach($order->sizeDetailsByGender() as $gender => $details)
                            <div class="col-md-6">
                                <div class="border rounded overflow-hidden h-100 d-flex flex-column">
                                    <div class="bg-{{ $genderColors[$gender] }} text-white text-center py-2 fw-bold">
                                        {{ $genderLabels[$gender] ?? strtoupper($gender) }}
                                    </div>
                                    <table class="table table-sm mb-0 text-center flex-grow-1">
                                        <thead class="table-light">
                                            <tr>
                                                <th>UKURAN</th>
                                                <th>HARGA</th>
                                                <th>JUMLAH</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($details as $detail)
                                            <tr>
                                                <td class="fw-bold">{{ $detail->size }}</td>
                                                <td class="text-muted small align-middle">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                                <td class="fw-medium fs-6">{{ $detail->quantity }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-{{ $genderColors[$gender] }} table-striped" style="--bs-table-bg-type: var(--bs-{{ $genderColors[$gender] }}-bg-subtle);">
                                            <tr>
                                                <td colspan="2" class="text-end fw-bold text-{{ $genderColors[$gender] }} align-middle">TOTAL {{ explode(' ', $genderLabels[$gender])[0] }}</td>
                                                <td class="fw-bold fs-5 text-{{ $genderColors[$gender] }}">{{ $details->sum('quantity') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="col-12 mt-3">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-success bg-opacity-10 rounded border border-success border-2">
                                    <h5 class="mb-0 fw-bold text-success">TOTAL KESELURUHAN</h5>
                                    <h3 class="mb-0 fw-bold text-success">{{ $order->totalQuantity() }} PCS</h3>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="col-12"><hr class="text-muted opacity-25"></div>

                <!-- Pricing & Dates -->
                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Harga & Profit</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Jumlah Keseluruhan</span>
                            <span class="fw-medium">x {{ $order->totalQuantity() }} pcs</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="fw-medium">Pendapatan (Omset)</span>
                            <span class="fw-semibold text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if(Auth::user()?->isAdmin() || Auth::user()?->isOwner())
                        <div class="d-flex justify-content-between text-danger mt-1">
                            <span>Total HPP (Modal)</span>
                            <span>- Rp {{ number_format($order->total_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top border-2">
                            <span class="fw-bold">Profit Bersih</span>
                            <span class="fw-bold text-success fs-5">Rp {{ number_format($order->getProfit(), 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted small">Margin Profit</span>
                            <span class="badge bg-success">{{ $order->getProfitMargin() }}%</span>
                        </div>
                        @else
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top border-2">
                            <span class="fw-bold">Total Harga</span>
                            <span class="fw-bold text-success fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Jadwal</h6>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="text-muted small d-block">Tanggal Pesan</span>
                            <span class="fw-medium">{{ $order->order_date?->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Tenggat Waktu</span>
                            <span class="fw-semibold {{ $order->deadline?->isPast() ? 'text-danger' : 'text-warning' }}">{{ $order->deadline?->format('d M Y') }}</span>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted small d-block mb-1">Status Saat Ini</span>
                            <x-badge :status="$order->current_status" />
                        </div>
                    </div>
                </div>

                @if($order->notes)
                <div class="col-12"><hr class="text-muted opacity-25"></div>
                <div class="col-12">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Catatan Tambahan</h6>
                    <p class="mb-0 text-dark">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </x-card>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <x-card title="Informasi Sistem">
            <div class="d-flex flex-column gap-3">
                <div>
                    <span class="text-muted small d-block">Dibuat Oleh</span>
                    <span class="fw-medium">{{ $order->creator?->name ?? 'Sistem' }}</span>
                </div>
                <div>
                    <span class="text-muted small d-block">Dibuat Pada</span>
                    <span class="fw-medium">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div>
                    <span class="text-muted small d-block">Terakhir Diperbarui</span>
                    <span class="fw-medium">{{ $order->updated_at->diffForHumans() }}</span>
                </div>
                @if($order->isArchived())
                <div class="p-3 bg-secondary bg-opacity-10 rounded mt-2">
                    <div class="d-flex align-items-center gap-2 text-secondary fw-semibold">
                        <i class="bi bi-archive-fill"></i> Pesanan Diarsipkan
                    </div>
                    <div class="small mt-1">{{ $order->archived_at->format('d M Y, H:i') }}</div>
                </div>
                @endif
            </div>
        </x-card>
    </div>
</div>

{{-- Design Photos Gallery --}}
@if($order->designFiles->isNotEmpty())
<div class="row g-4 mt-0">
    <div class="col-12">
        <x-card>
            <x-slot name="title">Foto Desain / Referensi Klien</x-slot>
            <div class="row g-3">
                @foreach($order->designFiles as $file)
                <div class="col-6 col-md-3 col-lg-2">
                    <a href="{{ $file->url }}" class="design-lightbox-trigger" data-src="{{ $file->url }}" data-name="{{ $file->original_name }}">
                        <div class="position-relative rounded-3 overflow-hidden shadow-sm" style="aspect-ratio:1; background:#1a1a2e;">
                            <img src="{{ $file->url }}" class="w-100 h-100 object-fit-cover" alt="{{ $file->original_name }}"
                                style="transition: transform 0.3s ease;"
                                onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            <div class="position-absolute inset-0 d-flex align-items-center justify-content-center opacity-0 bg-dark bg-opacity-50"
                                style="transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                <i class="bi bi-zoom-in text-white fs-4"></i>
                            </div>
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-60 text-white p-1" style="font-size:0.65rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $file->original_name }}
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </x-card>
    </div>
</div>
@endif

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
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-5" id="deleteOrderModalLabel">Hapus Pesanan?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-muted">
                Apakah Anda yakin ingin menghapus pesanan <strong>{{ $order->order_number }}</strong>? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua riwayat pelacakan dan faktur yang terkait.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('orders.destroy', $order) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Pesanan</button>
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
