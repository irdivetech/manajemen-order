@props(['status'])

@php
    // Color mapping by status group (from master_tracking_statuses.group)
    // This ensures all statuses have a visual style, even new ones added later.
    $groupColors = [
        'penerimaan' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary',
        'persiapan'  => 'bg-info bg-opacity-10 text-info border border-info',
        'produksi'   => 'bg-primary bg-opacity-10 text-primary border border-primary',
        'finishing'  => 'bg-warning bg-opacity-10 text-warning border border-warning',
        'pengiriman' => 'bg-success bg-opacity-10 text-success border border-success',
    ];

    // Per-status color overrides for visual distinction within the same group
    $statusColors = [
        'order_received'        => 'bg-secondary bg-opacity-10 text-secondary border border-secondary',
        'material_order_pending' => 'bg-info bg-opacity-10 text-info border border-info',
        'material_order_ready'  => 'bg-info bg-opacity-10 text-info border border-info',
        'fabric_cutting'        => 'bg-info bg-opacity-10 text-info border border-info',
        'production'            => 'bg-primary bg-opacity-10 text-primary border border-primary',
        'embroidery'            => 'bg-warning bg-opacity-10 text-warning border border-warning',
        'sewing'                => 'bg-primary bg-opacity-10 text-primary border border-primary',
        'button_installation'   => 'bg-danger bg-opacity-10 text-danger border border-danger',
        'qc'                    => 'bg-warning bg-opacity-10 text-warning border border-warning',
        'ironing'               => 'bg-warning bg-opacity-10 text-warning border border-warning',
        'packing'               => 'bg-dark bg-opacity-10 text-dark border border-dark',
        'shipping'              => 'bg-success bg-opacity-10 text-success border border-success',
    ];

    $class = $statusColors[$status] ?? 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
    $label = \App\Models\Order::statusLabel($status);
@endphp

<span class="badge-status {{ $class }}">{{ $label }}</span>
