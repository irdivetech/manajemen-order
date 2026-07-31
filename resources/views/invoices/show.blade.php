@extends('layouts.app')

@inject('settings', 'App\Services\SettingService')

@section('title', 'Faktur: ' . $invoice->invoice_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Pesanan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted">{{ $order->order_number }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Faktur</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-light border"><i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan</a>
            <a href="{{ route('orders.invoice.print', $order) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i> Cetak / PDF</a>
        </div>

        <x-card>
            <!-- Invoice Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">FAKTUR (INVOICE)</h2>
                    <p class="text-muted mb-0">#{{ $invoice->invoice_number }}</p>
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
                    <div class="fw-semibold text-dark fs-5">{{ $order->customer_name }}</div>
                    <div class="text-muted">{{ $order->customer_phone }}</div>
                </div>
                <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                    <h6 class="text-muted text-uppercase fw-bold small mb-2">Status Pembayaran:</h6>
                    @if($invoice->isPaid())
                        <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                    @elseif($invoice->payment_status === \App\Models\Invoice::PAYMENT_PARTIAL)
                        <span class="badge bg-warning px-3 py-2 fs-6 text-dark"><i class="bi bi-clock-history me-1"></i> CICILAN / SEBAGIAN</span>
                    @else
                        <span class="badge bg-danger px-3 py-2 fs-6"><i class="bi bi-x-circle me-1"></i> BELUM LUNAS</span>
                    @endif
                    <div class="mt-2 text-muted small">Diterbitkan pada: {{ $invoice->created_at->format('d M Y') }}</div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table border">
                    <thead class="table-light">
                        <tr>
                            <th>Deskripsi Produk</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->sizeDetails as $detail)
                        <tr>
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
            <div class="row justify-content-end">
                <div class="col-sm-6 col-md-5 col-lg-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-medium">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Pajak ({{ $settings->get('tax_rate') }}%)</span>
                        <span class="fw-medium">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-5">Total Tagihan</span>
                        <span class="fw-bold text-success fs-4">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if(Auth::user()?->isAdmin())
            <!-- Admin Actions -->
            <div class="border-top pt-4 mt-5 bg-light p-4 rounded-3 text-center">
                <h6 class="mb-3 fw-bold">Perbarui Status Pembayaran</h6>
                <form action="{{ route('orders.invoice.payment', $order) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
                    @csrf
                    @method('PATCH')
                    <select name="payment_status" class="form-select w-auto">
                        <option value="unpaid" {{ $invoice->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="partial" {{ $invoice->payment_status === 'partial' ? 'selected' : '' }}>Cicilan / Sebagian</option>
                        <option value="paid" {{ $invoice->payment_status === 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Simpan Status</button>
                </form>
            </div>
            @endif

        </x-card>
    </div>
</div>
@endsection
