{{-- Sidebar --}}
<nav class="sidebar" id="appSidebar">
    {{-- Brand --}}
    <div class="brand">
        <div class="brand-icon me-2">P</div>
        <span class="fw-bold text-dark fs-6">POMS</span>
    </div>

    {{-- Navigation --}}
    <div class="nav-section">
        <div class="nav-section-title">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dasbor
        </a>
        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') && !request()->routeIs('orders.tracking') && !request()->routeIs('orders.invoice*') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-data-fill"></i> Data Pesanan
        </a>
        <a href="{{ route('material-purchases.index') }}" class="nav-link {{ request()->routeIs('material-purchases.*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i> Belanja Bahan
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> Laporan Keuangan
        </a>
        <a href="{{ route('hpp.index') }}" class="nav-link {{ request()->routeIs('hpp.*') ? 'active' : '' }}">
            <i class="bi bi-calculator"></i> Laporan Modal
        </a>
        <a href="{{ route('archives.index') }}" class="nav-link {{ request()->routeIs('archives.*') ? 'active' : '' }}">
            <i class="bi bi-archive-fill"></i> Arsip Pesanan
        </a>
    </div>

    @if(Auth::user()?->isAdmin())
    <div class="nav-section">
        <div class="nav-section-title">Master Data</div>
        <a href="{{ route('master-data.index', 'genders') }}" class="nav-link {{ request()->is('master-data/genders*') ? 'active' : '' }}">
            <i class="bi bi-gender-ambiguous"></i> Gender
        </a>
        <a href="{{ route('master-data.index', 'size-categories') }}" class="nav-link {{ request()->is('master-data/size-categories*') ? 'active' : '' }}">
            <i class="bi bi-layers-half"></i> Kategori Ukuran
        </a>
        <a href="{{ route('master-data.index', 'sizes') }}" class="nav-link {{ request()->is('master-data/sizes*') ? 'active' : '' }}">
            <i class="bi bi-rulers"></i> Ukuran
        </a>
        <a href="{{ route('master-data.index', 'materials') }}" class="nav-link {{ request()->is('master-data/materials*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Bahan
        </a>
        <a href="{{ route('master-data.index', 'clothing-categories') }}" class="nav-link {{ request()->is('master-data/clothing-categories*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Kategori Baju
        </a>
        <a href="{{ route('master-data.index', 'usage-estimates') }}" class="nav-link {{ request()->is('master-data/usage-estimates*') ? 'active' : '' }}">
            <i class="bi bi-calculator"></i> Estimasi Bahan
        </a>
        <a href="{{ route('master-data.index', 'tracking-statuses') }}" class="nav-link {{ request()->is('master-data/tracking-statuses*') ? 'active' : '' }}">
            <i class="bi bi-list-task"></i> Status Tracking
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Pengaturan</div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Pengaturan Sistem
        </a>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Kelola Pengguna
        </a>
        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Profil
        </a>
    </div>
    @endif

    {{-- User Info (Bottom) --}}
    <div class="mt-auto border-top p-3" style="position:absolute;bottom:0;left:0;right:0;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold" style="width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--primary-light));font-size:0.85rem;">
                {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-dark small text-truncate">{{ Auth::user()?->name }}</div>
                <div class="text-muted" style="font-size:0.7rem;">{{ ucfirst(Auth::user()?->role) }}</div>
            </div>
        </div>
    </div>
</nav>
