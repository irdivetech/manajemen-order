@props(['title' => null, 'actions' => null])

<div class="card mb-4">
    @if($title || $actions)
    <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
        @if($title)
        <h6 class="mb-0 fw-semibold">{{ $title }}</h6>
        @endif
        @if($actions)
        <div>{{ $actions }}</div>
        @endif
    </div>
    @endif
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
