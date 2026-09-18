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

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <link href="{{ asset('modules/banksoal/css/tom-select-custom.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <x-mobile-navigation-assets />
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-primary selection:text-white">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">

        <!-- Sidebar Component -->
        <x-banksoal::ui.sidebar-gpm />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex-shrink-0 flex items-center justify-between px-6 z-10">
                <div class="flex items-center text-sm font-medium text-slate-600">
                    <span class="mr-2">SIBASO</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-500 font-semibold">GPM</span>
                    @hasSection('breadcrumbs')
                        <span class="mx-2 text-slate-300">/</span>
                        @yield('breadcrumbs')
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>

                    <x-banksoal::ui.gpm-notification-bell />

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 overflow-hidden border border-slate-300 group-hover:border-primary transition-colors">
                            <span class="font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 leading-tight">{{ auth()->user()->name ?? 'GPM' }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">Dosen GPM</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="w-full flex-1 overflow-y-auto">
                <div class="p-8 w-full max-w-screen-2xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

    </div>

    <!-- Global Component untuk Toast Message -->
    <x-banksoal::global-toast />

    <!-- Global Loader Overlay (Initialized by Spinner.js) -->
    <script src="{{ asset('modules/banksoal/js/Banksoal/shared/Spinner.js') }}"></script>

    @include('banksoal::partials.gpm.layout-scripts')
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    @livewireScripts
    @stack('scripts')
    <x-mobile-navigation />
</body>
</html>
