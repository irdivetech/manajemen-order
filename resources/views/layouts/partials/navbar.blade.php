{{-- Top Navbar --}}
<div class="top-navbar">
    <div class="d-flex align-items-center gap-3">
        {{-- Mobile Toggle --}}
        <button class="btn btn-sm btn-light d-lg-none border-0" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Beranda</a></li>
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @endif
            </ol>
        </nav>
    </div>

    <div class="d-flex align-items-center gap-3">
        {{-- Notifications Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-light border-0 position-relative dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right:0.5rem;" data-bs-auto-close="outside">
                <i class="bi bi-bell"></i>
                @if(isset($systemAlerts) && count($systemAlerts) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                    {{ count($systemAlerts) > 9 ? '9+' : count($systemAlerts) }}
                </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Notifikasi</h6>
                    @if(isset($systemAlerts) && count($systemAlerts) > 0)
                    <span class="badge bg-danger rounded-pill">{{ count($systemAlerts) }} Baru</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    @if(isset($systemAlerts) && count($systemAlerts) > 0)
                        @foreach($systemAlerts as $alert)
                        <a href="{{ $alert['link'] }}" class="list-group-item list-group-item-action d-flex gap-3 py-3" style="border-left: 3px solid transparent; border-left-color: var(--bs-{{ $alert['color'] }});">
                            <div class="text-{{ $alert['color'] }} fs-5 mt-1">
                                <i class="bi {{ $alert['icon'] }}"></i>
                            </div>
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-dark" style="font-size: 0.85rem;">{{ $alert['title'] }}</h6>
                                    <p class="mb-1 text-muted" style="font-size: 0.75rem;">{{ $alert['message'] }}</p>
                                </div>
                                <small class="text-muted" style="font-size: 0.7rem; white-space: nowrap;">{{ $alert['time'] }}</small>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                            <span class="small">Belum ada notifikasi baru</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-light border-0 dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold" style="width:28px;height:28px;background:var(--primary);font-size:0.7rem;">
                    {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
                </div>
                <span class="d-none d-sm-inline small fw-medium">{{ Auth::user()?->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                <li><a class="dropdown-item small" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="confirmAction(event, this, 'Keluar Sistem', 'Apakah Anda yakin ingin keluar dari aplikasi?', 'Ya, Keluar', '#ef4444');">
                        @csrf
                        <button type="submit" class="dropdown-item small text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
