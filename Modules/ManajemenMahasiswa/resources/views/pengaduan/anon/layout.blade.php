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
    {{-- Alpine.js — dibutuhkan dropdown <x-manajemenmahasiswa::ui.select> di form konfidensial --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Token warna & badge yang sama dengan halaman Pengaduan di dalam portal. --}}
    @include('manajemenmahasiswa::pengaduan.partials.palette')

    <style>
        body {
            font-family: 'Inter Tight', sans-serif;
            background-color: var(--c-bg);
            color: var(--c-fg);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .anon-header {
            background: #ffffff;
            border-bottom: 1px solid var(--c-border);
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
            background: var(--c-primary);
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
            color: var(--c-fg);
        }
        /* Lebar isi disamakan dengan halaman konfirmasi (1040px), di tengah layar. */
        .anon-container {
            margin: 24px auto 0;
            padding: 0 24px;
            flex: 1;
            width: 100%;
            max-width: 1088px;
        }
        .anon-footer {
            text-align: center;
            padding: 24px;
            color: var(--c-fg-placeholder);
            font-size: 12px;
            margin-top: auto;
        }
        @media (max-width: 767px) {
            .anon-container { margin-top: 12px; padding: 0 16px; }
        }
    </style>
    @stack('styles')
    {{-- Sistem tombol modul (.mk-btn), sama seperti layout portal. --}}
    @include('manajemenmahasiswa::partials.button-theme')
    <x-mobile-navigation-assets />
</head>
<body>

    <div class="anon-header">
        <div class="anon-header-logo">
            <x-manajemenmahasiswa::ui.icon name="shield-02" size="20" />
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
    <x-mobile-navigation />
</body>
</html>
