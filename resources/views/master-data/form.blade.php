@extends(isMobile() ? 'layouts.mobile' : 'layouts.app')

@section('title', (isset($data) ? 'Edit ' : 'Tambah ') . $config['title'])

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('master-data.index', $type) }}" class="text-decoration-none text-primary d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-dark mb-0">{{ isset($data) ? 'Edit' : 'Tambah' }} {{ $config['title'] }}</h4>
                </div>
                
                <div class="card-body p-4">
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
                                    <label class="form-label fw-semibold text-dark" for="{{ $key }}">{{ $field['label'] }}</label>
                                    
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

                        <div class="d-flex justify-content-end mt-5">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
