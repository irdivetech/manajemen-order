@extends('layouts.mobile')

@section('title', 'Pelacakan Produksi: ' . $order->order_number)

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('orders.show', $order) }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">Pelacakan Produksi</h1>
            <p class="page-sub">{{ $order->order_number }}</p>
        </div>
    </div>
@endsection

@section('content')

{{-- ── Status Saat Ini ── --}}
<div class="m-card mb-3" style="background:linear-gradient(135deg,#4f46e5,#6366f1); border:none;">
    <div class="m-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div style="font-size:0.7rem; color:rgba(255,255,255,0.7); font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Status Pesanan</div>
                <div style="color:#fff; font-weight:700; font-size:1rem; margin-top:0.25rem;">
                    {{ Str::title(str_replace('_', ' ', $order->current_status)) }}
                </div>
            </div>
            @if($order->isShipped())
                <i class="bi bi-check-circle-fill text-white fs-1 opacity-50"></i>
            @else
                <i class="bi bi-arrow-repeat text-white fs-1 opacity-50"></i>
            @endif
        </div>
    </div>
</div>

{{-- ── Form Lanjutkan Tahap ── --}}
@if(Auth::user()?->isAdmin() && !$order->isShipped())
@php $nextStatus = $order->getNextStatus(); @endphp
@if($nextStatus)
<div class="m-card mb-3 border-primary">
    <div class="m-card-header bg-primary bg-opacity-10">
        <h2 class="text-primary"><i class="bi bi-fast-forward-circle me-1"></i> Perbarui Status</h2>
    </div>
    <div class="m-card-body">
        <div class="d-flex flex-column gap-2 mb-3">
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
                @endphp
                <div class="d-flex align-items-center gap-2">
                    @if($isCompleted)
                        <i class="bi bi-check-circle-fill text-success"></i>
                    @elseif($isCurrent)
                        <i class="bi bi-arrow-right-circle-fill text-primary fs-5"></i>
                    @elseif($isNext)
                        <i class="bi bi-circle-fill text-warning" style="font-size:0.6rem; margin-left:2px;"></i>
                    @else
                        <i class="bi bi-circle text-muted opacity-50" style="font-size:0.6rem; margin-left:2px;"></i>
                    @endif
                    <span class="text-sm {{ $isCurrent ? 'fw-7 text-primary' : ($isCompleted ? 'fw-6 text-success' : ($isNext ? 'fw-6 text-warning' : 'text-muted opacity-50')) }}">
                        {{ $pipelineStatus->label }}
                    </span>
                </div>
            @endforeach
        </div>

        <form action="{{ route('orders.tracking.store', $order) }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="{{ $nextStatus }}">
            
            @if(isset($nextStatusModel) && $nextStatusModel->is_produksi)
                <div class="mb-3">
                    <label class="form-label text-sm fw-6">Tipe Produksi <span class="text-danger">*</span></label>
                    <select name="sub_type" class="form-select text-sm @error('sub_type') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe Produksi --</option>
                        <option value="penjahitan" {{ old('sub_type') == 'penjahitan' ? 'selected' : '' }}>Penjahitan</option>
                        <option value="bordir" {{ old('sub_type') == 'bordir' ? 'selected' : '' }}>Bordir</option>
                        <option value="penjahitan_dan_bordir" {{ old('sub_type') == 'penjahitan_dan_bordir' ? 'selected' : '' }}>Penjahitan & Bordir</option>
                    </select>
                    @error('sub_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label text-sm fw-6">Catatan Pembaruan <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control text-sm @error('description') is-invalid @enderror" 
                          rows="2" required placeholder="Keterangan proses ini...">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-6" 
                    onclick="confirmAction(event, this.closest('form'), 'Lanjutkan Pesanan?', 'Lanjutkan ke tahap: {{ \App\Models\Order::statusLabel($nextStatus) }}?')">
                Lanjutkan ke: {{ \App\Models\Order::statusLabel($nextStatus) }}
            </button>
        </form>
    </div>
</div>
@endif
@endif

{{-- ── Riwayat Pelacakan ── --}}
<div class="section-title">Riwayat Pelacakan</div>
<div class="m-card mb-4">
    <div class="m-card-body">
        @if($history->isEmpty())
            <div class="empty-state py-4">
                <i class="bi bi-clock-history"></i>
                <p>Belum ada riwayat pelacakan.</p>
            </div>
        @else
            <div class="timeline">
                @foreach($history->sortByDesc('created_at') as $item)
                <div class="timeline-item">
                    <div class="timeline-dot {{ $loop->first ? 'active' : 'done' }}"></div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-7 text-sm {{ $loop->first ? 'text-primary' : '' }}">
                                {{ \App\Models\Order::statusLabel($item->status) }}
                                @if($item->sub_type)
                                    <span class="badge bg-secondary ms-1" style="font-size:0.6rem;">{{ ucfirst(str_replace('_', ' ', $item->sub_type)) }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-muted fw-6">{{ $item->created_at->format('d M y, H:i') }}</span>
                        </div>
                        <div class="text-sm text-dark mb-1" style="line-height:1.4;">{{ $item->description }}</div>
                        <div class="text-xs text-muted">
                            Oleh: <span class="fw-6">{{ $item->updatedBy?->name ?? 'Sistem' }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
