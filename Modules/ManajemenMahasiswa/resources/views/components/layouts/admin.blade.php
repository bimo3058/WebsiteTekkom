<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Mahasiswa - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Palette SIMENMA admin diselaraskan dengan shell global SITKOM. */
        :root {
            --c-primary: #0B266E;
            --c-primary-hover: #091958;
            --c-primary-subtle: rgba(11, 38, 110, 0.08);
            --c-primary-border: #5C78B8;
            --c-bg: #F6F8FA;
            --c-card: #FFFFFF;
            --c-fg: #0D0D12;
            --c-fg-sec: #353849;
            --c-fg-muted: #666D80;
            --c-fg-placeholder: #808897;
            --c-border: #DFE1E7;
            --c-border-strong: #C1C7CF;
            --c-success: #287F6E;
            --c-success-subtle: #DDF2EE;
            --c-warning: #956321;
            --c-warning-subtle: #F9ECCB;
            --c-error: #DF1C41;
            --c-error-subtle: #FADAE1;
            --c-sky: #0C4D6E;
            --c-sky-subtle: #D1F0F9;
        }

        body {
            margin: 0;
            background-color: var(--c-bg);
            font-family: 'Inter Tight', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .simenma-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
            overflow: hidden;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            position: sticky;
            top: 0;
            background: var(--c-card);
            border-right: 1px solid var(--c-border);
            padding: 0;
            transition: width 0.25s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        /* Collapsed Sidebar */
        .sidebar-collapsed .sidebar {
            width: 64px;
            padding: 0;
        }

        .menu-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--c-fg-placeholder);
            margin-top: 24px;
            margin-bottom: 12px;
            padding-left: 12px;
            opacity: 0.7;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--c-fg-sec);
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            white-space: nowrap;
        }

        .sidebar-collapsed .sidebar a {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .sidebar a:hover {
            background: var(--c-bg);
            color: var(--c-fg);
        }

        .sidebar a.active {
            background: var(--c-primary-subtle);
            color: var(--c-primary);
            font-weight: 600;
            box-shadow: none;
        }
        
        .sidebar a.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--c-primary);
            border-radius: 0 3px 3px 0;
        }
        .sidebar-collapsed .sidebar a.active::before {
            display: none;
        }
        
        .sidebar a svg {
            color: var(--c-fg-muted);
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }
        
        .sidebar a.active svg {
            color: var(--c-primary);
        }
        
        .sidebar a:hover svg {
            color: var(--c-fg);
        }

        .btn-logout {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--c-fg-sec);
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 1px;
            transition: background .12s, color .12s;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            white-space: nowrap;
        }

        .sidebar-collapsed .btn-logout {
            justify-content: center;
            padding: 7px 0;
            gap: 0;
        }

        .btn-logout:hover {
            background: var(--c-error-subtle);
            color: var(--c-error);
        }
        .btn-logout svg {
            color: var(--c-fg-muted);
            width: 16px;
            height: 16px;
            transition: color 0.12s;
            flex-shrink: 0;
        }
        .btn-logout:hover svg {
            color: var(--c-error);
        }

        .bottom-menu {
            margin-top: auto;
            padding-top: 10px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .bottom-menu {
            width: 100%;
        }

        /* Area konten — setara .sitkom-content pada shell global SITKOM
           (resources/views/components/sidebar.blade.php). Padding 0 supaya
           halaman bisa menggambar kotak setinggi viewport sendiri, persis
           pola .*-wrap/.*-box di halaman Super Admin. */
        .content {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Kotak konten bawaan — setara .*-box pada shell global SITKOM
           (lihat resources/views/superadmin/users/index.blade.php).
           Halaman yang sudah menggambar kotaknya sendiri mematikan blok ini. */
        .main-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            box-sizing: border-box;
            margin: 10px;
            background: var(--c-card);
            border: 1px solid var(--c-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 20px 24px;
            overflow-y: auto;
        }

        /* ── Mobile: kembalikan scroll natif halaman ── */
        @media (max-width: 767px) {
            body {
                height: auto;
                min-height: 100vh;
                overflow: visible;
            }
            .simenma-main {
                overflow: visible;
            }
            .content {
                display: block;
                flex: none;
                overflow: visible;
                padding: 8px 8px 80px;
            }
            .main-wrapper {
                display: block;
                flex: none;
                min-height: 0;
                margin: 0;
                overflow: visible;
                border-radius: 10px;
                padding: 12px 14px;
            }
        }

        /* Sidebar Dropdown */
        .sidebar-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease;
            padding-left: 20px;
        }

        .sidebar-collapsed .sidebar-dropdown-menu {
            padding-left: 0;
            display: none; /* Hide submenus when collapsed for simplicity */
        }

        .sidebar-dropdown.open .sidebar-dropdown-menu {
            max-height: 200px;
        }
        .sidebar-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }
        /* Ukuran huruf sengaja tidak ditimpa: sub-item ikut `.sidebar a` (13px)
           supaya sama dengan menu induk. */
        .sidebar-dropdown .sub-item {
            padding: 7px 10px 7px 38px !important;
        }

        /* Utils */
        .nav-label {
            transition: opacity 0.2s ease;
        }
        .sidebar-collapsed .nav-label, 
        .sidebar-collapsed .menu-title,
        .sidebar-collapsed .portal-info,
        .sidebar-collapsed .user-info,
        .sidebar-collapsed .sb-section-label,
        .sidebar-collapsed .sb-brand-text,
        .sidebar-collapsed .dropdown-arrow {
            display: none;
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: absolute;
            right: -12px;
            top: 32px;
            width: 24px;
            height: 24px;
            background: var(--c-card);
            border: 1px solid var(--c-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: var(--c-fg-muted);
            transition: all 0.2s;
        }
        .sidebar-toggle:hover {
            background: var(--c-bg);
            color: var(--c-primary);
        }

        /* Global Scrollbar Customization */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--c-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
            border: 2px solid var(--c-bg);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--c-fg-placeholder);
        }
        /*
        Button pattern untuk modul ini:
        Primary: class="btn" style="background: var(--c-primary, #0B266E); color: white; border-radius: 10px;"
        Danger:  class="btn" style="background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; border-radius: 8px;"
        */
    </style>

    @stack('styles')

    {{-- Sistem tombol modul; sengaja SETELAH @stack('styles') supaya menang atas
         sisa gaya tombol lama yang masih menempel di masing-masing halaman. --}}
    @include('manajemenmahasiswa::partials.button-theme')
    <x-mobile-navigation-assets />
</head>

<body x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" 
      x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
      :class="{ 'sidebar-collapsed': !sidebarOpen }">

    <!-- Sidebar -->
    <x-manajemenmahasiswa::ui.sidebar />

    <!-- Main Area (topbar + content) -->
    <div class="simenma-main">
        <!-- Topbar -->
        @include('manajemenmahasiswa::components.ui.topbar')

        <!-- Content -->
        <div class="content" style="margin-left: 0;">
            <div class="main-wrapper">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dialog konfirmasi/pemberitahuan global; pengganti confirm() & alert() bawaan browser.
         Dipasang sebelum @stack('scripts') supaya mkConfirm/mkNotify sudah ada saat skrip
         halaman dijalankan. --}}
    <x-manajemenmahasiswa::ui.dialog />

    @stack('scripts')
    <x-mobile-navigation />
</body>

</html>
