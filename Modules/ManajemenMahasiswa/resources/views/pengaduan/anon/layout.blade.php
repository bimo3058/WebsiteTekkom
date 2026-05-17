<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Layanan Pengaduan Konfidensial')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter Tight', sans-serif;
            background-color: #f5f6fa;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .anon-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .anon-header-logo {
            width: 32px;
            height: 32px;
            background: #0B266E;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .anon-header-title {
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.2px;
            color: #111827;
        }
        .anon-container {
            max-width: 720px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }
        .anon-footer {
            text-align: center;
            padding: 24px;
            color: #9ca3af;
            font-size: 12px;
            margin-top: auto;
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="anon-header">
        <div class="anon-header-logo">
            <span class="material-symbols-outlined" style="font-size: 20px;">shield_lock</span>
        </div>
        <div class="anon-header-title">Portal Pengaduan Konfidensial</div>
    </div>

    <div class="anon-container">
        @yield('content')
    </div>

    <div class="anon-footer">
        &copy; {{ date('Y') }} Sistem Layanan Pengaduan. Tautan ini bersifat rahasia dan aman.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
