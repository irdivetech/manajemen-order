@extends(isMobile() ? 'layouts.mobile' : 'layouts.app')

@section('title', $config['title'])

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-0">{{ $config['title'] }}</h1>
        <a href="{{ route('master-data.create', $type) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data
        </a>
    </div>

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

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($config['fields'] as $key => $field)
                            <th class="py-3 px-4 fw-semibold text-secondary">{{ $field['label'] }}</th>
                        @endforeach
                        <th class="py-3 px-4 fw-semibold text-secondary text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($data as $item)
                        <tr>
                            @foreach($config['fields'] as $key => $field)
                                <td class="py-3 px-4">
                                    @if($field['type'] === 'boolean')
                                        <span class="badge {{ $item->$key ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-2 fw-semibold">
                                            {{ $item->$key ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    @elseif($field['type'] === 'select_model')
                                        @php
                                            $relationName = \Illuminate\Support\Str::camel(str_replace('_id', '', $key));
                                        @endphp
                                        <span class="fw-medium text-dark">{{ $item->$relationName->{$field['display']} ?? '-' }}</span>
                                    @elseif($key === 'estimated_usage')
                                        <span class="fw-medium text-dark">{{ rtrim(rtrim(number_format($item->$key, 4, ',', '.'), '0'), ',') }} {{ $item->material->unit ?? 'M' }}</span>
                                    @else
                                        <span class="fw-medium text-dark">{{ $item->$key }}</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="py-3 px-4 text-center">
                                @if(isset($config['fields']['is_active']))
                                <form action="{{ route('master-data.toggle', ['type' => $type, 'id' => $item->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} shadow-sm rounded-pill me-1" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('master-data.edit', ['type' => $type, 'id' => $item->id]) }}" class="btn btn-sm btn-light text-primary shadow-sm rounded-pill me-1" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('master-data.destroy', ['type' => $type, 'id' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger shadow-sm rounded-pill" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($config['fields']) + 1 }}" class="py-5 px-4 text-center text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox fs-1 mb-2"></i>
                                    <span>Belum ada data.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
