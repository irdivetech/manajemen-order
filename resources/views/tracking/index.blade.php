@extends('layouts.app')

@section('title', 'Pelacakan Produksi: ' . $order->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted">{{ $order->order_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pelacakan</li>
@endsection

@push('styles')
<style>
    /* Custom Stepper Styles */
    .tracking-timeline {
        position: relative;
        padding-left: 2rem;
    }
    .tracking-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0.5rem;
        width: 2px;
        background-color: #e5e7eb;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #cbd5e1;
        z-index: 1;
    }
    .timeline-item.active .timeline-marker {
        border-color: var(--primary);
        background-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
    }
    .timeline-item.completed .timeline-marker {
        border-color: #10b981;
        background-color: #10b981;
    }
    .timeline-item.completed ~ .timeline-item:not(.active):not(.completed) .timeline-marker {
        border-color: #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <!-- Tracking Timeline (Left) -->
    <div class="col-lg-8">
        <x-card title="Riwayat Pelacakan Produksi">
            @if($history->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                    Belum ada riwayat pelacakan. Silakan tambahkan status produksi.
                </div>
            @else
                <div class="tracking-timeline mt-3">
                    @foreach($history as $item)
                        @php
                            $isCurrent = $order->current_status === $item->status;
                            $markerClass = 'completed';
                            if ($loop->first) {
                                $markerClass = $isCurrent ? 'active' : 'completed';
                            }
                            
                            $statusLabels = [
                                'order_received' => 'Pesanan Diterima',
                                'fabric_cutting' => 'Pemotongan Kain',
                                'sewing' => 'Penjahitan',
                                'embroidery' => 'Bordir',
                                'button_installation' => 'Pemasangan Kancing',
                                'shipping' => 'Pengiriman',
                            ];
                        @endphp
                        <div class="timeline-item {{ $markerClass }}">
                            <div class="timeline-marker"></div>
                            <div class="card bg-light border-0 shadow-sm mb-0">
                                <div class="card-body py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            {{ \App\Models\Order::statusLabel($item->status) }}
                                            @if($item->sub_type)
                                                <span class="badge bg-secondary ms-2">{{ ucfirst(str_replace('_', ' ', $item->sub_type)) }}</span>
                                            @endif
                                        </h6>
                                        <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $item->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="mb-2 text-muted small">{{ $item->description }}</p>
                                    <div class="text-secondary" style="font-size:0.75rem;">
                                        Diperbarui oleh <span class="fw-medium text-dark">{{ $item->updatedBy?->name ?? 'Sistem' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <!-- Update Form (Right) -->
    <div class="col-lg-4">
        <x-card title="Info Pesanan">
            <div class="mb-3">
                <span class="text-muted d-block small">Nomor Pesanan</span>
                <span class="fw-bold">{{ $order->order_number }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted d-block small">Status Saat Ini</span>
                <x-badge :status="$order->current_status" />
            </div>
            @if($order->isShipped())
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>
                        <strong>Produksi Selesai!</strong><br>
                        <span class="small">Pesanan ini telah dikirim.</span>
                    </div>
                </div>
                <a href="{{ route('orders.shipping-label', $order) }}" class="btn btn-dark w-100" target="_blank">
                    <i class="bi bi-printer me-1"></i> Cetak Resi Pengiriman
                </a>
            @endif
        </x-card>

        @if(Auth::user()?->isAdmin() && !$order->isShipped())
        @php $nextStatus = $order->getNextStatus(); @endphp
        @if($nextStatus)
        <x-card title="Lanjutkan ke Tahap Berikutnya" class="mt-4">
            {{-- Visual Pipeline Indicator --}}
            <div class="mb-4">
                <div class="d-flex flex-column gap-2">
                    @php
                        $currentIdx = $pipeline->search(fn($item) => $item->code === $order->current_status);
                        $nextIdx = $pipeline->search(fn($item) => $item->code === $nextStatus);
                        $nextStatusModel = $pipeline->firstWhere('code', $nextStatus);
                    @endphp
                    @foreach($pipeline as $idx => $pipelineStatus)
                        @php
                            $isCompleted = $idx < $currentIdx;
                            $isCurrent = $idx === $currentIdx;
                            $isNext = $idx === $nextIdx;
                            $isFuture = $idx > $nextIdx;
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            @if($isCompleted)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @elseif($isCurrent)
                                <i class="bi bi-arrow-right-circle-fill text-primary"></i>
                            @elseif($isNext)
                                <i class="bi bi-circle text-warning"></i>
                            @else
                                <i class="bi bi-circle text-muted opacity-50"></i>
                            @endif
                            <span class="small {{ $isCurrent ? 'fw-bold text-primary' : ($isCompleted ? 'text-success' : ($isNext ? 'fw-semibold text-warning' : 'text-muted opacity-50')) }}">
                                {{ $pipelineStatus->label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="alert alert-info d-flex align-items-start gap-2 py-2 mb-3">
                <i class="bi bi-info-circle mt-1"></i>
                <div class="small">
                    <strong>Tahap saat ini:</strong> {{ \App\Models\Order::statusLabel($order->current_status) }}<br>
                    <strong>Tahap selanjutnya:</strong> {{ \App\Models\Order::statusLabel($nextStatus) }}
                </div>
            </div>

            @if($nextStatusModel && $nextStatusModel->requires_payment && !$isPaymentFulfilled)
                <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div class="small">
                        <strong>Pembayaran Belum Lunas!</strong><br>
                        Tahap "{{ \App\Models\Order::statusLabel($nextStatus) }}" mensyaratkan pesanan sudah lunas. Silakan cek invoice dan selesaikan pembayaran terlebih dahulu.
                    </div>
                </div>
            @endif

            <form action="{{ route('orders.tracking.store', $order) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="{{ $nextStatus }}">

                @if($nextStatusModel && $nextStatusModel->has_sub_type)
                    <div class="mb-3">
                        @if($nextStatus === 'production')
                            @if($order->has_embroidery)
                                <label class="form-label">Urutan Pengerjaan <span class="text-danger">*</span></label>
                                <select name="sub_type" class="form-select @error('sub_type') is-invalid @enderror" required {{ ($nextStatusModel && $nextStatusModel->requires_payment && !$isPaymentFulfilled) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Urutan Pengerjaan --</option>
                                    <option value="bordir" {{ old('sub_type') == 'bordir' ? 'selected' : '' }}>Bordir Dahulu, Lalu Jahit</option>
                                    <option value="penjahitan" {{ old('sub_type') == 'penjahitan' ? 'selected' : '' }}>Jahit Dahulu, Lalu Bordir</option>
                                    <option value="barengan" {{ old('sub_type') == 'barengan' ? 'selected' : '' }}>Jahit & Bordir Barengan</option>
                                </select>
                            @else
                                <div class="alert alert-info py-2 mb-2 small">
                                    <i class="bi bi-info-circle me-1"></i> Pesanan ini tidak menggunakan bordir. Alur akan otomatis lanjut ke penjahitan.
                                </div>
                            @endif
                        @elseif($nextStatus === 'ironing')
                            <label class="form-label">Lokasi Setrika <span class="text-danger">*</span></label>
                            <select name="sub_type" class="form-select @error('sub_type') is-invalid @enderror" required {{ ($nextStatusModel && $nextStatusModel->requires_payment && !$isPaymentFulfilled) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Lokasi Setrika --</option>
                                <option value="dalam" {{ old('sub_type') == 'dalam' ? 'selected' : '' }}>Di Dalam</option>
                                <option value="vendor" {{ old('sub_type') == 'vendor' ? 'selected' : '' }}>Vendor / Di Luar</option>
                            </select>
                        @endif
                        @error('sub_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Keterangan / Catatan <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required placeholder="Tambahkan rincian tentang pembaruan ini..." {{ ($nextStatusModel && $nextStatusModel->requires_payment && !$isPaymentFulfilled) ? 'disabled' : '' }}>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" {{ ($nextStatusModel && $nextStatusModel->requires_payment && !$isPaymentFulfilled) ? 'disabled' : '' }} onclick="confirmAction(event, this.closest('form'), 'Lanjutkan Pesanan?', 'Anda yakin ingin melanjutkan pesanan ini ke tahap: {{ \App\Models\Order::statusLabel($nextStatus) }}? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lanjutkan ke: {{ \App\Models\Order::statusLabel($nextStatus) }}
                    </button>
                </div>
            </form>
        </x-card>
        @endif
        @endif
    </div>
</div>
@endsection
