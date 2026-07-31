@extends('layouts.app')

@inject('settings', 'App\Services\SettingService')

@section('title', 'Faktur: ' . $invoice->invoice_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted">{{ $order->order_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Faktur</li>
@endsection

@section('content')
<style>
    .lunas-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-15deg);
        font-size: 8rem;
        font-weight: 900;
        color: rgba(220, 38, 38, 0.1);
        border: 10px solid rgba(220, 38, 38, 0.1);
        padding: 10px 40px;
        border-radius: 1rem;
        pointer-events: none;
        z-index: 0;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan</a>
            <a href="{{ route('orders.invoice.print', $order) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> Cetak / PDF</a>
        </div>

        <x-card class="position-relative overflow-hidden">
            @if($invoice->isPaid())
                <div class="lunas-watermark">LUNAS</div>
            @endif

            <div class="position-relative" style="z-index: 1;">
                <!-- Invoice Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        @if(!empty($settings->get('company_logo')))
                            <img src="{{ Storage::url($settings->get('company_logo')) }}" alt="Logo" style="max-height: 80px;" class="rounded">
                        @endif
                        <div>
                            <h2 class="fw-bold text-dark mb-1">FAKTUR (INVOICE)</h2>
                            <p class="text-muted mb-0">#{{ $invoice->invoice_number }}</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fs-4 fw-bold text-primary mb-1">{{ $settings->get('company_name') }}</div>
                        <p class="text-muted small mb-0">{!! nl2br(e($settings->get('company_address'))) !!}<br>{{ $settings->get('company_email') }} | {{ $settings->get('company_phone') }}</p>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fw-bold small mb-2">Ditagihkan Kepada:</h6>
                        <div class="fw-semibold text-dark fs-5">
                            {{ $order->customer_name }}
                            @if($order->customer_title) - <span class="fs-6 fw-normal text-muted">{{ $order->customer_title }}</span> @endif
                        </div>
                        @if($order->customer_address)
                        <div class="text-muted mb-1">{!! nl2br(e($order->customer_address)) !!}</div>
                        @endif
                        <div class="text-muted"><i class="bi bi-telephone text-muted me-1"></i> {{ $order->customer_phone }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                        <h6 class="text-muted text-uppercase fw-bold small mb-2">Status Pembayaran:</h6>
                        @if($invoice->isPaid())
                            <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                        @elseif($invoice->payment_status === 'partial')
                            <span class="badge bg-warning px-3 py-2 fs-6 text-dark"><i class="bi bi-clock-history me-1"></i> CICILAN / DP</span>
                        @else
                            <span class="badge bg-danger px-3 py-2 fs-6"><i class="bi bi-x-circle me-1"></i> BELUM LUNAS</span>
                        @endif
                        <div class="mt-2 text-muted small">Diterbitkan pada: {{ $invoice->created_at->format('d M Y') }}</div>
                        
                        @if($order->deadline)
                        <div class="mt-1 text-danger small fw-semibold">Jatuh Tempo: {{ \Carbon\Carbon::parse($order->deadline)->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table border">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Deskripsi Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->sizeDetails as $index => $detail)
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->product_name }}</div>
                                    <div class="text-muted small">
                                        {{ $order->product_type }} - {{ $order->color }}
                                        @if($order->material) - {{ $order->material }} @endif
                                        - Ukuran: {{ $detail->size }} 
                                        ({{ ['male' => 'Laki-laki', 'female' => 'Perempuan', 'child' => 'Anak-anak'][$detail->gender] ?? $detail->gender }})
                                    </div>
                                </td>
                                <td class="text-center align-middle">{{ $detail->quantity }}</td>
                                <td class="text-end align-middle">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-end align-middle fw-medium">Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless mb-4">
                            <tr>
                                <td class="text-end">Subtotal:</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($invoice->tax > 0)
                            <tr>
                                <td class="text-end">Pajak ({{ $settings->get('tax_rate', '11') }}%):</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td class="text-end"><strong class="fs-5">Total Tagihan:</strong></td>
                                <td class="text-end"><strong class="fs-5 text-primary">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                @if($order->notes)
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase fw-bold small mb-2">Catatan:</h6>
                    <p class="text-dark bg-light p-3 rounded border">{{ $order->notes }}</p>
                </div>
                @endif
                
                <!-- Owner Signature -->
                <div class="mt-4 text-center" style="width: fit-content;">
                    <h6 class="fw-bold mb-0">{{ $settings->get('owner_name') ?: 'Pimpinan' }}</h6>
                    <p class="text-muted small">{{ $settings->get('owner_title') ?: 'Owner' }}</p>
                    @if(!empty($settings->get('signature_image')))
                        <img src="{{ Storage::url($settings->get('signature_image')) }}" alt="Signature" style="max-height: 100px;">
                    @else
                        <div style="height: 100px; width: 200px;" class="border border-dashed text-muted d-flex align-items-center justify-content-center">TTD Owner</div>
                    @endif
                </div>
            </div>
            
            @if(Auth::user()?->isAdmin() && !$invoice->isPaid())
            <!-- Admin Actions -->
            <div class="border-top pt-4 mt-5 bg-light p-4 rounded-3 position-relative" style="z-index: 2;">
                <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-cash-coin me-1"></i> Ubah Status Pembayaran</h6>
                <form action="{{ route('orders.invoice.payment', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status Pembayaran</label>
                            <select name="payment_status" class="form-select" required>
                                <option value="unpaid" {{ $invoice->isUnpaid() ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="partial" {{ $invoice->payment_status === 'partial' ? 'selected' : '' }}>Cicilan / DP</option>
                                <option value="paid" {{ $invoice->isPaid() ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

        </x-card>
    </div>
</div>
@endsection
