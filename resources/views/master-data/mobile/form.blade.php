@extends('layouts.mobile')

@section('title', (isset($data) ? 'Edit ' : 'Tambah ') . $config['title'])

@section('page-header')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('master-data.index', $type) }}" class="icon-btn" style="border:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size:1.05rem;">{{ isset($data) ? 'Edit' : 'Tambah' }}</h1>
            <p class="page-sub">{{ $config['title'] }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="m-card mb-4">
    <div class="m-card-body">
        <form action="{{ isset($data) ? route('master-data.update', ['type' => $type, 'id' => $data->id]) : route('master-data.store', $type) }}" method="POST">
            @csrf
            @if(isset($data))
                @method('PUT')
            @endif

            @foreach($config['fields'] as $key => $field)
                <div class="mb-4">
                    @if($field['type'] === 'boolean')
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="{{ $key }}" name="{{ $key }}" value="1" {{ old($key, $data->$key ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark" for="{{ $key }}">{{ $field['label'] }}</label>
                        </div>
                    @else
                        <label class="form-label text-sm fw-6" for="{{ $key }}">{{ $field['label'] }}</label>
                        
                        @if($field['type'] === 'select')
                            <select name="{{ $key }}" id="{{ $key }}" class="form-select @error($key) is-invalid @enderror">
                                <option value="">-- Pilih {{ $field['label'] }} --</option>
                                @foreach($field['options'] as $val => $optLabel)
                                    <option value="{{ $val }}" {{ old($key, $data->$key ?? '') == $val ? 'selected' : '' }}>{{ $optLabel }}</option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'select_model')
                            <select name="{{ $key }}" id="{{ $key }}" class="form-select @error($key) is-invalid @enderror">
                                <option value="">-- Pilih {{ $field['label'] }} --</option>
                                @foreach($selectData[$key] as $modelItem)
                                    <option value="{{ $modelItem->id }}" {{ old($key, $data->$key ?? '') == $modelItem->id ? 'selected' : '' }}>{{ $modelItem->{$field['display']} }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field['type'] }}" name="{{ $key }}" id="{{ $key }}" 
                                class="form-control @error($key) is-invalid @enderror"
                                value="{{ old($key, $data->$key ?? '') }}"
                                {{ isset($field['step']) ? 'step=' . $field['step'] : '' }}>
                        @endif
                    @endif
                    
                    @error($key)
                        <div class="invalid-feedback d-block fw-medium">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endforeach

            <div class="position-fixed start-0 end-0 p-3"
                 style="bottom:calc(var(--nav-height) + env(safe-area-inset-bottom,0px)); background:var(--surface); border-top:1px solid var(--border); z-index:1040;">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
            <div style="height: 80px;"></div>
        </form>
    </div>
</div>

@endsection
