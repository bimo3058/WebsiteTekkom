<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Mahasiswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

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
        }

        .menu-title {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            font-size: 15px;
            margin-bottom: 2px;
            transition: all 0.2s;
        }

        .sidebar a:hover {
            background: #f8fafc;
            color: #1f2937;
        }

        .sidebar a.active {
            background: #f8fafc; /* Subtle light background */
            color: #111827; /* Dark text */
            font-weight: 600;
        }
        
        .sidebar a svg {
            color: #6b7280;
            transition: color 0.2s;
        }
        
        .sidebar a.active svg {
            color: #4f46e5;
        }
        
        .sidebar a:hover svg {
            color: #4b5563;
        }

        .bottom-menu {
            position: absolute;
            bottom: 20px;
            width: 85%;
        }

        .navbar-custom {
            margin-left: 260px;
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 25px;
        }

        .user-profile {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-profile img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .main-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            min-height: calc(100vh - 120px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Sidebar Dropdown */
        .sidebar-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease;
            padding-left: 20px;
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
    </style>

    @stack('styles')
</head>

<body>

    <!-- Sidebar Mahasiswa -->
    <x-manajemenmahasiswa::ui.sidebar-b />

    <!-- Navbar (reuse admin) -->
    <x-manajemenmahasiswa::ui.navbar-admin />

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