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
        body {
            margin: 0;
            background-color: #f5f6fa;
            font-family: 'Inter Tight', sans-serif;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 20px;
            transition: width 0.3s ease;
            z-index: 1000;
        }

        /* Collapsed Sidebar */
        .sidebar-collapsed .sidebar {
            width: 80px;
            padding: 20px 10px;
        }

        .menu-title {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            margin-top: 24px;
            margin-bottom: 12px;
            padding-left: 12px;
            opacity: 0.7;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            text-decoration: none;
            color: #6C757D;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .sidebar-collapsed .sidebar a {
            justify-content: center;
            padding: 10px;
            gap: 0;
        }

        .sidebar a:hover {
            background: #F8F9FA;
            color: #1A1C1E;
        }

        .sidebar a.active {
            background: #F1E9FF;
            color: #5E53F4;
            font-weight: 600;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .sidebar a svg {
            color: #ADB5BD;
            transition: color 0.2s;
            flex-shrink: 0;
        }
        
        .sidebar a.active svg {
            color: #5E53F4;
        }
        
        .sidebar a:hover svg {
            color: #1A1C1E;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
            transition: all 0.2s;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            white-space: nowrap;
        }

        .sidebar-collapsed .btn-logout {
            justify-content: center;
            padding: 10px;
            gap: 0;
        }

        .btn-logout:hover {
            background: #fef2f2;
            color: #dc2626;
        }
        .btn-logout svg {
            color: #94a3b8;
            transition: color 0.2s;
            flex-shrink: 0;
        }
        .btn-logout:hover svg {
            color: #dc2626;
        }

        .bottom-menu {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
            left: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .bottom-menu {
            width: calc(100% - 20px);
            left: 10px;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
            transition: margin-left 0.3s ease;
        }

        .sidebar-collapsed .content {
            margin-left: 80px;
        }

        .main-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            min-height: calc(100vh - 50px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        .sidebar-dropdown .sub-item {
            font-size: 14px !important;
            padding: 9px 16px !important;
        }

        /* Utils */
        .nav-label {
            transition: opacity 0.2s ease;
        }
        .sidebar-collapsed .nav-label, 
        .sidebar-collapsed .menu-title,
        .sidebar-collapsed .portal-info,
        .sidebar-collapsed .user-info {
            display: none;
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: absolute;
            right: -12px;
            top: 32px;
            width: 24px;
            height: 24px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: #6b7280;
            transition: all 0.2s;
        }
        .sidebar-toggle:hover {
            background: #f8fafc;
            color: #4f46e5;
        }
    </style>

    @stack('styles')
</head>

<body x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" 
      x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
      :class="{ 'sidebar-collapsed': !sidebarOpen }">

    <!-- Sidebar -->
    <x-manajemenmahasiswa::ui.sidebar />

    <!-- Content -->
    <div class="content">
        <div class="main-wrapper">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>