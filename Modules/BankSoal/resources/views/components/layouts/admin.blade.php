<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin Portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-primary selection:text-white">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">
        
        <!-- Sidebar Component -->
        <x-banksoal::ui.sidebar-admin />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex-shrink-0 flex items-center justify-between px-6 z-10">
                <div class="flex items-center text-sm font-medium text-slate-600">
                    <span class="mr-2">SIBASKOM</span> 
                    @hasSection('breadcrumbs')
                        <span class="mx-2 text-slate-300">/</span>
                        @yield('breadcrumbs')
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    
                    <button class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 overflow-hidden border border-slate-300 group-hover:border-primary transition-colors">
                            <span class="font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 leading-tight">{{ auth()->user()->name ?? 'Super Admin' }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">Modules Admin</span>
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

    <!-- Global Loader Overlay (Style 1 for non-interruptible POST/mutation queries) -->
    <div class="pm-loader" id="loaderOverlay" style="position:fixed;inset:0;background:rgba(255,255,255,.7);display:none;align-items:center;justify-content:center;z-index:999999;">
        <div class="pm-spinner" style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:rgb(11,38,110);border-radius:50%;animation:pm-spin .7s linear infinite;"></div>
    </div>
    <style>
        .pm-loader.show { display: flex !important; }
        @keyframes pm-spin { to { transform: rotate(360deg); } }
    </style>
    <script>
        window.showLoader = function() {
            const overlay = document.getElementById('loaderOverlay');
            if (overlay) overlay.classList.add('show');
        };
        window.hideLoader = function() {
            const overlay = document.getElementById('loaderOverlay');
            if (overlay) overlay.classList.remove('show');
        };
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
