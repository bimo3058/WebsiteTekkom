<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Mahasiswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background-color: #f5f6fa;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 20px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .sidebar-brand .brand-text strong {
            font-size: 14px;
            color: #1f2937;
        }

        .sidebar-brand .brand-text span {
            font-size: 11px;
            color: #9ca3af;
            display: block;
        }

        .menu-title {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 24px;
            margin-bottom: 8px;
        }

        .sidebar-nav {
            flex: 1;
        }

        .sidebar a.nav-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .sidebar a.nav-link-item:hover {
            background: #f3f4f6;
            color: #4f46e5;
        }

        .sidebar a.nav-link-item.active {
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .sidebar a.nav-link-item .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .bottom-menu {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: auto;
        }

        .bottom-menu a.nav-link-item {
            font-size: 13px;
        }

        .bottom-menu .logout-btn {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #ef4444;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s ease;
        }

        .bottom-menu .logout-btn:hover {
            background: #fef2f2;
        }

        .navbar-custom {
            margin-left: 260px;
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 25px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .user-profile {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-profile span {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
            min-height: calc(100vh - 64px);
        }

        .main-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            min-height: calc(100vh - 120px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar Mahasiswa -->
    <x-manajemenmahasiswa::ui.sidebar-mahasiswa />

    <!-- Navbar -->
    <x-manajemenmahasiswa::ui.navbar-admin />

    <!-- Content -->
    <div class="content">
        <div class="main-wrapper">
            {{ $slot ?? '' }}
            @yield('page-content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>