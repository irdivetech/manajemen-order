<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    <title>@yield('title', 'POMS') — POMS Mobile</title>

    {{-- Local Offline Assets --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/inter.css') }}" rel="stylesheet">

    <style>
        /* ─── Root Variables ─── */
        :root {
            --primary:        #4f46e5;
            --primary-light:  #6366f1;
            --primary-bg:     #eef2ff;
            --primary-dark:   #3730a3;
            --surface:        #ffffff;
            --bg:             #f4f5f9;
            --border:         #e5e7eb;
            --text:           #111827;
            --text-muted:     #6b7280;
            --success:        #10b981;
            --danger:         #ef4444;
            --warning:        #f59e0b;
            --info:           #0ea5e9;
            --nav-height:     64px;
            --header-height:  58px;
            --safe-bottom:    env(safe-area-inset-bottom, 0px);
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100dvh;
            overscroll-behavior: none;
            padding-top: var(--header-height);
            padding-bottom: calc(var(--nav-height) + var(--safe-bottom));
        }

        /* ─── Mobile Header ─── */
        .mobile-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 1050;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        }
        .mobile-header .brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .mobile-header .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 0.875rem;
        }
        .mobile-header .brand-name {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text);
        }
        .mobile-header .header-actions {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .mobile-header .icon-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: none;
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }
        .mobile-header .icon-btn:hover,
        .mobile-header .icon-btn:active { background: var(--bg); }
        .mobile-header .avatar {
            width: 32px; height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 0.75rem;
            cursor: pointer;
        }

        /* ─── Page Header (breadcrumb-like) ─── */
        .mobile-page-header {
            padding: 0.875rem 1rem 0;
        }
        .mobile-page-header h1 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.125rem;
        }
        .mobile-page-header .page-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* ─── Content Area ─── */
        .mobile-content {
            padding: 0.75rem 1rem 1rem;
        }

        /* ─── Bottom Navigation ─── */
        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: var(--nav-height);
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            z-index: 1050;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.07);
            padding-bottom: var(--safe-bottom);
        }
        .bottom-nav .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.65rem;
            font-weight: 500;
            padding: 0.5rem 0.25rem;
            transition: color 0.15s;
            position: relative;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
        }
        .bottom-nav .nav-item i {
            font-size: 1.35rem;
            line-height: 1;
            transition: transform 0.15s;
        }
        .bottom-nav .nav-item.active {
            color: var(--primary);
        }
        .bottom-nav .nav-item.active i {
            transform: translateY(-1px);
        }
        .bottom-nav .nav-item.active::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 32px; height: 3px;
            background: var(--primary);
            border-radius: 0 0 4px 4px;
        }
        .bottom-nav .nav-item span {
            line-height: 1;
        }

        /* ─── Cards ─── */
        .m-card {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 0.75rem;
        }
        .m-card-header {
            padding: 0.875rem 1rem 0.625rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .m-card-header h2 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }
        .m-card-body {
            padding: 1rem;
        }
        .m-card-body.p-0 { padding: 0; }

        /* ─── KPI / Stat Cards ─── */
        .m-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }
        .m-stat {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            padding: 0.875rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .m-stat .stat-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            margin-bottom: 0.625rem;
        }
        .m-stat .stat-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }
        .m-stat .stat-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .m-stat .stat-badge {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.2em 0.5em;
            border-radius: 20px;
            margin-top: 0.375rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        /* ─── Order Cards ─── */
        .order-card {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s, transform 0.15s;
            display: block;
            text-decoration: none;
            color: inherit;
            margin-bottom: 0.65rem;
        }
        .order-card:active { transform: scale(0.99); }
        .order-card .oc-header {
            padding: 0.75rem 1rem 0.625rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f3f4f6;
        }
        .order-card .oc-num {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        .order-card .oc-body {
            padding: 0.75rem 1rem;
        }
        .order-card .oc-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
            margin-bottom: 0.15rem;
        }
        .order-card .oc-product {
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        .order-card .oc-footer {
            padding: 0.625rem 1rem;
            background: #fafafa;
            border-top: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-card .oc-price {
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--success);
        }
        .order-card .oc-deadline {
            font-size: 0.73rem;
            color: var(--text-muted);
        }
        .order-card .oc-deadline.overdue { color: var(--danger); font-weight: 600; }

        /* ─── Status Badges ─── */
        .badge-status {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 0.3em 0.65em;
            border-radius: 6px;
            white-space: nowrap;
        }

        /* ─── Horizontal scroll chips ─── */
        .chips-scroll {
            display: flex;
            gap: 0.4rem;
            overflow-x: auto;
            padding-bottom: 0.25rem;
            scrollbar-width: none;
        }
        .chips-scroll::-webkit-scrollbar { display: none; }
        .chip {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1.5px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.15s;
        }
        .chip.active, .chip:hover {
            border-color: var(--primary);
            background: var(--primary-bg);
            color: var(--primary);
        }

        /* ─── Section Title ─── */
        .section-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 0.6rem;
            margin-top: 0.25rem;
        }

        /* ─── List Items ─── */
        .m-list-item {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .m-list-item:last-child { border-bottom: none; }

        /* ─── Timeline ─── */
        .timeline { position: relative; padding-left: 1.75rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            bottom: 0.5rem;
            width: 2px;
            background: var(--border);
            border-radius: 2px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-dot {
            position: absolute;
            left: -1.35rem;
            top: 0.25rem;
            width: 12px; height: 12px;
            border-radius: 50%;
            background: var(--primary);
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px var(--primary-bg);
        }
        .timeline-dot.done { background: var(--success); box-shadow: 0 0 0 2px #d1fae5; }
        .timeline-dot.pending { background: var(--border); box-shadow: none; }

        /* ─── Flash Messages ─── */
        .m-alert {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 0.75rem;
        }
        .m-alert-success { background: #d1fae5; color: #065f46; }
        .m-alert-danger  { background: #fee2e2; color: #7f1d1d; }

        /* ─── Search ─── */
        .m-search {
            position: relative;
            margin-bottom: 0.75rem;
        }
        .m-search input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.875rem;
            background: var(--surface);
            outline: none;
            transition: border-color 0.15s;
        }
        .m-search input:focus { border-color: var(--primary); }
        .m-search .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.875rem;
            pointer-events: none;
        }

        /* ─── Floating Action Button ─── */
        .fab {
            position: fixed;
            bottom: calc(var(--nav-height) + var(--safe-bottom) + 1rem);
            right: 1rem;
            width: 52px; height: 52px;
            background: var(--primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            box-shadow: 0 4px 16px rgba(79,70,229,0.4);
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
            z-index: 1040;
        }
        .fab:hover, .fab:active { transform: scale(0.96); color: #fff; box-shadow: 0 2px 8px rgba(79,70,229,0.35); }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.5; }
        .empty-state p { font-size: 0.875rem; margin: 0; }

        /* ─── Pagination ─── */
        .m-pagination {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.5rem 0;
        }

        /* ─── Menu Drawer (Slide-up) ─── */
        .drawer-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1070;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s;
        }
        .drawer-overlay.show { opacity: 1; pointer-events: all; }
        .drawer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: var(--surface);
            border-radius: 20px 20px 0 0;
            z-index: 1080;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
            max-height: 80dvh;
            overflow-y: auto;
            padding-bottom: var(--safe-bottom);
        }
        .drawer.show { transform: translateY(0); }
        .drawer-handle {
            width: 36px; height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin: 0.75rem auto 0;
        }
        .drawer-body { padding: 1rem; }
        .drawer-nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 0.75rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            font-size: 0.9rem;
            transition: background 0.15s;
            margin-bottom: 2px;
        }
        .drawer-nav-item:hover, .drawer-nav-item:active { background: var(--bg); }
        .drawer-nav-item .dni-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .drawer-nav-item.danger { color: var(--danger); }

        /* ─── Utilities ─── */
        .text-primary { color: rgba(79, 70, 229, var(--bs-text-opacity, 1)) !important; }
        .bg-primary   { background-color: rgba(79, 70, 229, var(--bs-bg-opacity, 1)) !important; }
        .btn-primary  { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }
        .fw-6 { font-weight: 600; }
        .fw-7 { font-weight: 700; }
        .text-xs  { font-size: 0.72rem; }
        .text-sm  { font-size: 0.82rem; }
        .text-base { font-size: 0.9rem; }
        .rounded-xl { border-radius: 14px; }
        .gap-xs { gap: 0.375rem; }

        .fade-up {
            animation: fadeIn 0.3s ease both;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══ Mobile Header ═══ --}}
<header class="mobile-header">
    <a href="{{ route('dashboard') }}" class="brand">
        <div class="brand-icon">P</div>
        <span class="brand-name">POMS</span>
    </a>
    <div class="header-actions">
        {{-- Notifications --}}
        <div class="dropdown">
            <button class="icon-btn" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border:none;" data-bs-auto-close="outside">
                <i class="bi bi-bell"></i>
                @if(isset($systemAlerts) && count($systemAlerts) > 0)
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger"
                          style="font-size:0.55rem; padding:3px 5px; min-width:16px;">
                        {{ count($systemAlerts) > 9 ? '9+' : count($systemAlerts) }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
                 style="width:290px; max-height:360px; overflow-y:auto; border-radius:14px;">
                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" style="font-size:0.85rem;">Notifikasi</h6>
                    @if(isset($systemAlerts) && count($systemAlerts) > 0)
                        <span class="badge bg-danger rounded-pill">{{ count($systemAlerts) }}</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    @if(isset($systemAlerts) && count($systemAlerts) > 0)
                        @foreach($systemAlerts as $alert)
                        <a href="{{ $alert['link'] }}"
                           class="list-group-item list-group-item-action d-flex gap-2 py-2"
                           style="border-left:3px solid var(--bs-{{ $alert['color'] }});">
                            <i class="bi {{ $alert['icon'] }} text-{{ $alert['color'] }} mt-1"></i>
                            <div>
                                <div style="font-size:0.8rem; font-weight:600;">{{ $alert['title'] }}</div>
                                <div style="font-size:0.72rem;" class="text-muted">{{ $alert['message'] }}</div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-bell-slash d-block mb-1"></i>
                            <span style="font-size:0.78rem;">Belum ada notifikasi</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- User Avatar --}}
        <button class="avatar" id="menuTrigger" style="border:none;">
            {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
        </button>
    </div>
</header>

{{-- ═══ Main Content ═══ --}}
<main class="fade-up">
    {{-- Page Header --}}
    @hasSection('page-header')
    <div class="mobile-page-header">
        @yield('page-header')
    </div>
    @endif

    <div class="{{ View::hasSection('page-header') ? 'px-3 pt-2' : 'mobile-page-header pt-3' }}">
    </div>

    {{-- Content --}}
    <div class="mobile-content">
        @yield('content')
    </div>
</main>

{{-- ═══ Bottom Navigation ═══ --}}
<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" id="bnav-dashboard"
       class="nav-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2{{ request()->routeIs('dashboard*') ? '-fill' : '' }}"></i>
        <span>Dasbor</span>
    </a>
    <a href="{{ route('orders.index') }}" id="bnav-orders"
       class="nav-item {{ request()->routeIs('orders.*') && !request()->routeIs('orders.tracking') && !request()->routeIs('orders.invoice*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-data{{ request()->routeIs('orders.*') && !request()->routeIs('orders.tracking') && !request()->routeIs('orders.invoice*') ? '-fill' : '' }}"></i>
        <span>Pesanan</span>
    </a>
    <a href="{{ route('reports.index') }}" id="bnav-reports"
       class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart{{ request()->routeIs('reports.*') ? '-fill' : '' }}"></i>
        <span>Laporan</span>
    </a>
    <a href="{{ route('archives.index') }}" id="bnav-archives"
       class="nav-item {{ request()->routeIs('archives.*') ? 'active' : '' }}">
        <i class="bi bi-archive{{ request()->routeIs('archives.*') ? '-fill' : '' }}"></i>
        <span>Arsip</span>
    </a>
    <button class="nav-item" id="bnav-menu">
        <i class="bi bi-grid-3x3-gap"></i>
        <span>Menu</span>
    </button>
</nav>

{{-- ═══ Menu Drawer ═══ --}}
<div class="drawer-overlay" id="drawerOverlay"></div>
<div class="drawer" id="menuDrawer">
    <div class="drawer-handle"></div>
    <div class="drawer-body">
        {{-- User info --}}
        <div class="d-flex align-items-center gap-3 p-3 mb-2"
             style="background:var(--bg); border-radius:12px;">
            <div class="avatar" style="width:44px; height:44px; border-radius:12px; font-size:1rem;">
                {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
            </div>
            <div>
                <div class="fw-7" style="font-size:0.9rem;">{{ Auth::user()?->name }}</div>
                <div class="text-xs text-muted">{{ ucfirst(Auth::user()?->role) }}</div>
            </div>
        </div>



        <div class="section-title mt-3">Menu Utama</div>
        <a href="{{ route('material-purchases.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#e0f2fe; color:#0284c7;">
                <i class="bi bi-cart-check"></i>
            </div>
            Belanja Bahan
        </a>
        <a href="{{ route('best-sellers.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#fef08a; color:#a16207;">
                <i class="bi bi-trophy-fill"></i>
            </div>
            Best Seller
        </a>

        <div class="section-title mt-3">Laporan</div>
        <a href="{{ route('hpp.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#fee2e2; color:var(--danger);">
                <i class="bi bi-calculator"></i>
            </div>
            Laporan Modal (HPP)
        </a>

        @if(Auth::user()?->isOwner())
        <div class="section-title mt-3">Master Data</div>
        <a href="{{ route('master-data.index', 'genders') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-gender-ambiguous"></i>
            </div>
            Gender
        </a>
        <a href="{{ route('master-data.index', 'size-categories') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-layers-half"></i>
            </div>
            Kategori Ukuran
        </a>
        <a href="{{ route('master-data.index', 'sizes') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-rulers"></i>
            </div>
            Ukuran
        </a>
        <a href="{{ route('master-data.index', 'materials') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-box-seam"></i>
            </div>
            Bahan
        </a>
        <a href="{{ route('master-data.index', 'clothing-categories') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-tags"></i>
            </div>
            Kategori Baju
        </a>
        <a href="{{ route('master-data.index', 'usage-estimates') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f3f4f6; color:#4b5563;">
                <i class="bi bi-calculator"></i>
            </div>
            Estimasi Bahan
        </a>

        <div class="section-title mt-3">Sistem</div>
        <a href="{{ route('users.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#f0fdf4; color:var(--success);">
                <i class="bi bi-people-fill"></i>
            </div>
            Kelola Pengguna
        </a>
        <a href="{{ route('settings.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#fefce8; color:var(--warning);">
                <i class="bi bi-receipt"></i>
            </div>
            Pengaturan Invoice
        </a>
        @endif

        <a href="{{ route('profile.index') }}" class="drawer-nav-item">
            <div class="dni-icon" style="background:#faf5ff; color:#9333ea;">
                <i class="bi bi-person-gear"></i>
            </div>
            Profil Saya
        </a>

        <div class="section-title mt-3">Akun</div>

        <form method="POST" action="{{ route('logout') }}" onsubmit="confirmAction(event, this, 'Keluar Sistem', 'Apakah Anda yakin ingin keluar dari aplikasi?', 'Ya, Keluar', '#ef4444');">
            @csrf
            <button type="submit" class="drawer-nav-item danger w-100" style="text-align:left;">
                <div class="dni-icon" style="background:#fff1f2; color:var(--danger);">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                Keluar
            </button>
        </form>
    </div>
</div>

{{-- Local Offline JS --}}
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/chart.min.js') }}"></script>

<script>
    // ─── Menu Drawer ───
    const menuTrigger = document.getElementById('menuTrigger');
    const bnavMenu    = document.getElementById('bnav-menu');
    const drawer      = document.getElementById('menuDrawer');
    const overlay     = document.getElementById('drawerOverlay');

    function openDrawer() {
        drawer.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    menuTrigger?.addEventListener('click', openDrawer);
    bnavMenu?.addEventListener('click', openDrawer);
    overlay?.addEventListener('click', closeDrawer);

    // Swipe down to close drawer
    let startY = 0;
    drawer?.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
    drawer?.addEventListener('touchend', e => {
        const diff = e.changedTouches[0].clientY - startY;
        if (diff > 60) closeDrawer();
    }, { passive: true });
</script>

{{-- SweetAlert2 --}}
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{!! addslashes(session('success')) !!}',
                showConfirmButton: false,
                timer: 2500,
                customClass: { popup: 'rounded-4' },
                width: '90%'
            });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: '{!! addslashes(session('error')) !!}',
                showConfirmButton: true,
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-4' },
                width: '90%'
            });
        @endif
    });
    
    function confirmAction(event, form, title, text, confirmText = 'Ya, lanjutkan!', confirmColor = '#4f46e5') {
        event.preventDefault();
        
        // Tutup drawer jika sedang terbuka (di mobile)
        if (typeof closeDrawer === 'function') {
            closeDrawer();
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' },
            width: '90%'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    function confirmDelete(event, form, message = 'Data ini akan dihapus secara permanen!') {
        confirmAction(event, form, 'Hapus Data?', message, 'Ya, hapus!', '#ef4444');
    }
</script>
@stack('scripts')
</body>
</html>
