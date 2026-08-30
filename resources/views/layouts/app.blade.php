<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - POMS</title>

    {{-- Local Offline Assets --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/inter.css') }}" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-bg: #eef2ff;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f8f9fc; }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand {
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .sidebar .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 1rem;
        }
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 1rem;
        }
        /* Custom Scrollbar for Sidebar */
        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .sidebar-menu::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        .sidebar .nav-section { padding: 1rem 0.75rem; }
        .sidebar .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
        }
        .sidebar .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            text-decoration: none;
            margin-bottom: 2px;
        }
        .sidebar .nav-link:hover { background: #f3f4f6; color: #111827; }
        .sidebar .nav-link.active {
            background: var(--primary-bg);
            color: var(--primary);
            font-weight: 600;
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; text-align: center; }

        /* ─── Main ─── */
        .main-wrapper { margin-left: var(--sidebar-width); }
        .top-navbar {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky; top: 0; z-index: 1030;
        }
        .content-area { padding: 1.5rem; }
        .page-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        /* ─── Cards ─── */
        .card { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .card-header { background: #fff; border-bottom: 1px solid #f3f4f6; border-radius: 12px 12px 0 0 !important; }
        .stat-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1.25rem; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: box-shadow 0.2s; }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }

        /* ─── Badge ─── */
        .badge-status { font-size: 0.75rem; font-weight: 500; padding: 0.35em 0.75em; border-radius: 6px; }

        /* ─── Table ─── */
        .table th { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom-width: 1px; }
        .table td { vertical-align: middle; font-size: 0.875rem; color: #374151; }
        .table tbody tr:hover { background-color: #f9fafb; }

        /* ─── Responsive ─── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }

        /* ─── Animations ─── */
        .fade-in { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    @stack('styles')
</head>
<body>
    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main Wrapper --}}
    <div class="main-wrapper d-flex flex-column min-vh-100">
        {{-- Navbar --}}
        @include('layouts.partials.navbar')

        {{-- Content --}}
        <div class="content-area flex-grow-1 fade-in">


            @yield('content')
        </div>

        {{-- Footer --}}
        @include('layouts.partials.footer')
    </div>

    {{-- Overlay for mobile sidebar --}}
    <div class="sidebar-overlay position-fixed top-0 start-0 w-100 h-100 d-none" style="background:rgba(0,0,0,0.3);z-index:1035;" id="sidebarOverlay"></div>

    {{-- Local Offline JS --}}
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    
    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('d-none');
        });
        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.add('d-none');
        });
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
                    customClass: { popup: 'rounded-4' }
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: '{!! addslashes(session('error')) !!}',
                    showConfirmButton: true,
                    confirmButtonColor: '#4f46e5',
                    customClass: { popup: 'rounded-4' }
                });
            @endif
        });
        
        // Global helper for actions
        function confirmAction(event, form, title, text, confirmText = 'Ya, lanjutkan!', confirmColor = '#4f46e5') {
            event.preventDefault();
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
        function confirmDelete(event, form, message = 'Data ini akan dihapus secara permanen!') {
            confirmAction(event, form, 'Apakah Anda yakin?', message, 'Ya, hapus!', '#ef4444');
        }
    </script>
    @stack('scripts')
</body>
</html>
