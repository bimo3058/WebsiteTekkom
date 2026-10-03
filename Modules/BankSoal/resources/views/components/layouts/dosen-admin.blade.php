@props(['bankSoal' => false, 'rpsPreview' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIBASO: Sistem Informasi Bank Soal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tom Select for Dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <link href="{{ asset('modules/banksoal/css/tom-select-custom.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    @if($bankSoal || $rpsPreview)
        <link href="{{ asset('modules/banksoal/css/dosen-bank-soal.css') }}" rel="stylesheet">
    @endif
    @if($rpsPreview)<link href="{{ asset('modules/banksoal/css/rps-preview.css') }}?v={{ filemtime(public_path('modules/banksoal/css/rps-preview.css')) }}" rel="stylesheet">@endif
    <x-mobile-navigation-assets />
    <link href="{{ asset('modules/banksoal/css/dosen-topbar.css') }}" rel="stylesheet">
</head>
<body class="{{ ($bankSoal || $rpsPreview) ? 'banksoal-management' : '' }} {{ $rpsPreview ? 'bs-rps-preview' : '' }} font-sans antialiased text-slate-900 bg-slate-50 selection:bg-primary selection:text-white">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">

        <!-- Sidebar Component -->
        <x-banksoal::ui.sidebar-dosen />

        <!-- Main Content Wrapper -->
        <div class="bs-workspace flex-1 flex flex-col h-screen overflow-hidden relative">

            <!-- Topbar -->
            <x-banksoal::ui.topbar-dosen />

            <!-- Main Content Area -->
            <main class="bs-main w-full flex-1 overflow-y-auto">
                <div class="bs-content p-8 w-full max-w-screen-2xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>

    <!-- Global Component untuk Toast Message -->
    <x-banksoal::global-toast />

    <!-- Global Loader Overlay (Initialized by Spinner.js) -->
    <script src="{{ asset('modules/banksoal/js/Banksoal/shared/Spinner.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    @livewireScripts
    @stack('scripts')
    <x-mobile-navigation />
</body>

</html>