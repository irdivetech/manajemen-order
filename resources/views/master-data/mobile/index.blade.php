@extends('layouts.mobile')

@section('title', $config['title'])

@section('page-header')
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 style="font-size:1.05rem;">{{ $config['title'] }}</h1>
            <p class="page-sub">Kelola Data Master</p>
        </div>
        <a href="{{ route('master-data.create', $type) }}" class="icon-btn bg-primary text-white" style="border:none;">
            <i class="bi bi-plus-lg"></i>
        </a>
    </div>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@forelse($data as $item)
    <div class="m-card mb-3 shadow-sm">
        <div class="m-card-body">
            @foreach($config['fields'] as $key => $field)
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span class="text-xs text-muted fw-bold" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 40%;">{{ $field['label'] }}</span>
                    <div style="max-width: 60%; text-align: right;">
                    @if($field['type'] === 'boolean')
                        <span class="badge {{ $item->$key ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} border {{ $item->$key ? 'border-success-subtle' : 'border-danger-subtle' }} rounded-pill px-2 py-1">
                            {{ $item->$key ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    @elseif($field['type'] === 'select_model')
                        @php
                            $relationName = \Illuminate\Support\Str::camel(str_replace('_id', '', $key));
                        @endphp
                        <span class="fw-bold text-sm text-dark">{{ $item->$relationName->{$field['display']} ?? '-' }}</span>
                    @elseif($key === 'estimated_usage')
                        <span class="fw-bold text-sm text-dark">{{ rtrim(rtrim(number_format($item->$key, 4, ',', '.'), '0'), ',') }} {{ $item->material->unit ?? 'M' }}</span>
                    @else
                        <span class="fw-bold text-sm text-dark" style="word-break: break-word;">{{ $item->$key }}</span>
                    @endif
                    </div>
                </div>
            @endforeach
            
            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-3">
                @if(isset($config['fields']['is_active']))
                <form action="{{ route('master-data.toggle', ['type' => $type, 'id' => $item->id]) }}" method="POST" class="d-inline flex-grow-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} w-100" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi bi-power"></i> {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                @endif
                <a href="{{ route('master-data.edit', ['type' => $type, 'id' => $item->id]) }}" class="btn btn-sm btn-light text-primary border flex-grow-1" title="Edit">
                    <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <form action="{{ route('master-data.destroy', ['type' => $type, 'id' => $item->id]) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light text-danger border w-100" title="Hapus">
                        <i class="bi bi-trash-fill"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 mb-2"></i>
        <p>Belum ada data.</p>
    </div>
@endforelse

<div style="height:40px;"></div>
@endsection
