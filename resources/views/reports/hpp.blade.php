@extends('layouts.app')

@section('title', 'Laporan & Analitik')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan Modal Belanja (HPP)</li>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <x-card>
            <form action="{{ route('hpp.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Filter Berdasarkan</label>
                    <select name="date_column" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="order_date" {{ request('date_column', 'order_date') === 'order_date' ? 'selected' : '' }}>Tanggal Pesan</option>
                        <option value="deadline" {{ request('date_column') === 'deadline' ? 'selected' : '' }}>Tenggat Waktu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Periode Waktu</label>
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="daily" {{ request('period') === 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ request('period') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ request('period', 'monthly') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ request('period') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                        <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Pilih Tanggal Manual</option>
                    </select>
                </div>
                
                @if(request('period') === 'custom')
                <div class="col-md-2">
                    <label class="form-label small text-muted">Dari Tanggal</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Hingga</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Terapkan</button>
                </div>
                @endif
            </form>
        </x-card>
    </div>
</div>

@php
    $periodLabels = [
        'daily' => 'Harian',
        'weekly' => 'Mingguan',
        'monthly' => 'Bulanan',
        'yearly' => 'Tahunan',
        'custom' => 'Kustom'
    ];
    $periodStr = (string) $period;
    $periodName = $periodLabels[$periodStr] ?? ucfirst($periodStr);
@endphp

<div class="row g-4 mb-4">
    <!-- HPP Card -->
    <div class="col-md-6">
        <div class="stat-card border-danger">
            <h6 class="text-muted mb-3 fw-semibold">Total HPP / Modal Belanja ({{ $periodName }})</h6>
            <h3 class="fw-bold text-danger mb-0">Rp {{ number_format((float) $totalHpp, 0, ',', '.') }}</h3>
        </div>
    </div>
    <!-- Total Orders Card -->
    <div class="col-md-6">
        <div class="stat-card border-primary">
            <h6 class="text-muted mb-3 fw-semibold">Pesanan Terkait</h6>
            <h3 class="fw-bold text-primary mb-0">{{ $orders->count() }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <x-card>
            <x-slot name="title">Daftar Modal Belanja (Periode {{ $periodName }})</x-slot>
            <x-slot name="actions">
                <a href="{{ route('hpp.export', request()->all()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel (.xlsx)
                </a>
            </x-slot>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Bahan / Material</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total HPP (Modal)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td><span class="badge bg-light text-dark border fw-normal">{{ $order->material }}</span></td>
                            <td>{{ $order->order_date?->format('d M Y') }}</td>
                            <td class="text-end fw-semibold text-danger">Rp {{ number_format($order->total_cost, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada pesanan ditemukan untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection
