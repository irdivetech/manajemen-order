@props(['status'])

@php
    $classes = [
        \App\Models\Order::STATUS_ORDER_RECEIVED    => 'bg-secondary bg-opacity-10 text-secondary border border-secondary',
        \App\Models\Order::STATUS_FABRIC_CUTTING    => 'bg-info bg-opacity-10 text-info border border-info',
        \App\Models\Order::STATUS_SEWING            => 'bg-primary bg-opacity-10 text-primary border border-primary',
        \App\Models\Order::STATUS_EMBROIDERY        => 'bg-warning bg-opacity-10 text-warning border border-warning',
        \App\Models\Order::STATUS_BUTTON_INSTALLATION => 'bg-danger bg-opacity-10 text-danger border border-danger',
        \App\Models\Order::STATUS_SHIPPING          => 'bg-success bg-opacity-10 text-success border border-success',
    ];

    $labels = [
        \App\Models\Order::STATUS_ORDER_RECEIVED    => 'Pesanan Diterima',
        \App\Models\Order::STATUS_FABRIC_CUTTING    => 'Pemotongan Kain',
        \App\Models\Order::STATUS_SEWING            => 'Penjahitan',
        \App\Models\Order::STATUS_EMBROIDERY        => 'Bordir',
        \App\Models\Order::STATUS_BUTTON_INSTALLATION => 'Pemasangan Kancing',
        \App\Models\Order::STATUS_SHIPPING          => 'Pengiriman (Selesai)',
    ];

    $class = $classes[$status] ?? 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
@endphp

<span class="badge-status {{ $class }}">{{ $label }}</span>
