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
                        <div class="text-muted mb-1">{!! nl2br(e($order->customer_address)) !!}</div>
                        <div class="text-muted"><i class="bi bi-telephone text-muted me-1"></i> {{ $order->customer_phone }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                        <h6 class="text-muted text-uppercase fw-bold small mb-2">Status Pembayaran:</h6>
                        @if($invoice->isPaid())
                            <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                        @elseif($invoice->isPartial())
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

                <div class="row">
                    <!-- Note & Bank Info -->
                    <div class="col-md-7">
                        @if($order->notes)
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold small mb-2">Catatan (NOTE):</h6>
                            <p class="text-dark bg-light p-3 rounded border">{{ $order->notes }}</p>
                        </div>
                        @endif

                        <div class="card border-primary bg-primary bg-opacity-10 mb-4 shadow-sm">
                            <div class="card-body py-3">
                                <h6 class="text-primary text-uppercase fw-bold small mb-3"><i class="bi bi-wallet2 me-1"></i> Informasi Pembayaran</h6>
                                @forelse($bankAccounts as $bank)
                                    <div class="d-flex align-items-center justify-content-between mb-2 {{ !$loop->last ? 'border-bottom pb-2 border-primary border-opacity-25' : '' }}">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $bank->bank_name }}</div>
                                            <div class="small text-muted">a/n {{ $bank->account_name }}</div>
                                        </div>
                                        <div class="fw-bold fs-5 text-primary">{{ $bank->account_number }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">Belum ada informasi rekening bank.</p>
                                @endforelse
                            </div>
                        </div>
                        
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

                    <!-- Totals & Payment History -->
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded border mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark fs-5">TOTAL TAGIHAN</span>
                                <span class="fw-bold text-dark fs-4">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 text-success">
                                <span>Sudah Dibayar</span>
                                <span class="fw-semibold">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="fw-bold text-danger">SISA TAGIHAN</span>
                                <span class="fw-bold text-danger fs-5">Rp {{ number_format($invoice->remainingAmount(), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Payment History -->
                        <h6 class="text-muted text-uppercase fw-bold small mb-3">Riwayat Pembayaran:</h6>
                        @forelse($invoice->payments as $payment)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <div class="small fw-semibold">{{ $payment->paid_at->format('d M Y, H:i') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $payment->payment_method ?? 'Cash/Transfer' }}</div>
                                @if($payment->notes)
                                <div class="text-muted" style="font-size: 0.75rem;">Note: {{ $payment->notes }}</div>
                                @endif
                            </div>
                            <div class="fw-bold text-success">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </div>
                        @empty
                        <p class="text-muted small">Belum ada riwayat pembayaran (DP/Cicilan).</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            @if(Auth::user()?->isAdmin() && !$invoice->isPaid())
            <!-- Admin Actions -->
            <div class="border-top pt-4 mt-5 bg-light p-4 rounded-3 position-relative" style="z-index: 2;">
                <h6 class="mb-3 fw-bold text-primary"><i class="bi bi-cash-coin me-1"></i> Input Pembayaran Baru</h6>
                <form action="{{ route('orders.invoice.payment', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status Pembayaran</label>
                            <select name="payment_status" class="form-select" id="paymentStatusSelect" required>
                                <option value="partial" {{ $invoice->isPartial() ? 'selected' : '' }}>Cicilan / DP</option>
                                <option value="paid" {{ $invoice->isPaid() ? 'selected' : '' }}>Lunas</option>
                                <option value="unpaid" {{ $invoice->isUnpaid() ? 'selected' : '' }}>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Nominal Pembayaran (Rp)</label>
                            <input type="number" name="payment_amount" class="form-control" id="paymentAmountInput" placeholder="Sisa: {{ $invoice->remainingAmount() }}" value="{{ $invoice->remainingAmount() }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Metode Pembayaran</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="Cth: Transfer BCA">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Catatan (Opsional)</label>
                            <input type="text" name="payment_notes" class="form-control" placeholder="Cth: Pembayaran DP pertama">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pembayaran</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

        </x-card>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('paymentStatusSelect');
        const amountInput = document.getElementById('paymentAmountInput');
        const remaining = {{ $invoice->remainingAmount() }};

        if (statusSelect && amountInput) {
            statusSelect.addEventListener('change', function() {
                if (this.value === 'paid') {
                    amountInput.value = remaining;
                } else if (this.value === 'unpaid') {
                    amountInput.value = '';
                }
            });
        }
    });
</script>
@endsection
