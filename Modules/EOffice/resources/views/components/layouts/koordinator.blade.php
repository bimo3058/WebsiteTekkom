<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIKP - {{ $title ?? 'Koordinator Dashboard' }}</title>

    <!-- Inter Tight Font (Figma Design System) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html,
        body,
        * {
            font-family: 'Inter Tight', system-ui, -apple-system, sans-serif !important;
        }
    </style>
</head>

<body class="antialiased bg-[#ECEFF3]">
    <div x-data="{ sidebarOpen: true, ...(typeof pageData !== 'undefined' ? pageData : {}) }"
        class="flex h-screen bg-[#ECEFF3] overflow-hidden">

        <!-- ===== SIDEBAR (flush kiri, tanpa rounded) ===== -->
        <!-- Mobile overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 bg-black/20 z-10 md:hidden" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display:none;">
        </div>

        <!-- Sidebar wrapper — no rounded, flush left, bg white (matches sidebar) -->
        <div class="flex-shrink-0 relative z-20 bg-white border-r border-[#DFE1E6]"
            :class="sidebarOpen ? 'w-[260px]' : 'w-[72px]'" style="transition: width 0.3s;">
            <x-eoffice::ui.sidebar-koordinator />
        </div>

        <!-- ===== MAIN AREA: with padding, white rounded rectangle ===== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden p-3">

            <!-- White rounded rectangle card (topbar + content) -->
            <div class="flex-1 flex flex-col min-w-0 bg-white rounded-xl overflow-hidden shadow-sm">

                <!-- Topbar — inside the white card -->
                <header class="flex-shrink-0 h-14 flex items-center justify-between px-6 border-b border-[#DFE1E6]">

                    <!-- Left: Mobile hamburger + Breadcrumb -->
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-1.5 text-[#A4ABB8] hover:text-[#353849] rounded-lg hover:bg-[#F8F9FB] transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Breadcrumb -->
                        <div class="flex items-center gap-1.5 text-[13px]">
                            <span class="text-[#A4ABB8] font-medium">SIKP</span>
                            @hasSection('breadcrumbs')
                                <span class="text-[#C1C7CF] mx-0.5">/</span>
                                @yield('breadcrumbs')
                            @endif
                        </div>
                    </div>

                    <!-- Right: Search + Notif + User -->
                    <div class="flex items-center gap-1.5">
                        <!-- Search -->
                        <button
                            class="w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Notification -->
                        <button
                            class="w-8 h-8 flex items-center justify-center text-[#808897] hover:text-[#353849] rounded-full hover:bg-[#F8F9FB] transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        </button>

                        <div class="w-px h-5 bg-[#DFE1E6] mx-1"></div>

                        <!-- User -->
                        <div class="flex items-center gap-2.5 cursor-pointer group">
                            <div
                                class="w-8 h-8 rounded-full bg-[#E8EEFF] flex items-center justify-center text-[#0065FF] border border-[#C1D0FF] overflow-hidden group-hover:border-[#0065FF] transition-colors flex-shrink-0">
                                <span
                                    class="font-bold text-xs leading-none">{{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}</span>
                            </div>
                            <div class="hidden sm:flex flex-col leading-none">
                                <span
                                    class="text-[13px] font-semibold text-[#272835]">{{ auth()->user()->name ?? 'Koordinator KP' }}</span>
                                <span class="text-[11px] text-[#808897] font-normal mt-0.5">Koordinator KP</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto" style="scrollbar-width: thin;">
                    <div class="p-6 lg:p-8 max-w-screen-2xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

            </div>{{-- end white card --}}

        </div>{{-- end main area --}}

    </div>

    @stack('scripts')
</body>

</html>