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
    <style>
        #banksoal-main-content .rounded-xl {
            border-radius: 8px;
        }

        #banksoal-main-content .rounded-2xl,
        #banksoal-main-content .rounded-3xl {
            border-radius: 12px;
        }

        #banksoal-main-content select,
        #banksoal-main-content input:not([type='checkbox']):not([type='radio']),
        #banksoal-main-content textarea,
        #banksoal-main-content .ts-control {
            border-radius: 8px;
        }

        #banksoal-main-content button.rounded-xl,
        #banksoal-main-content a.rounded-xl {
            border-radius: 8px;
        }

        #banksoal-main-content button,
        #banksoal-main-content a[class*='inline-flex'] {
            font-weight: 600;
        }

        #banksoal-main-content button.rounded-full,
        #banksoal-main-content a.rounded-full,
        #banksoal-main-content button[class*='filter'],
        #banksoal-main-content [data-filter-button] {
            border-radius: 8px;
        }

        #banksoal-main-content button:not(:disabled),
        #banksoal-main-content a[href] {
            cursor: pointer;
        }

        #banksoal-main-content button.bg-primary,
        #banksoal-main-content a.bg-primary,
        #banksoal-main-content button[class*='bg-[#0B266E]'],
        #banksoal-main-content a[class*='bg-[#0B266E]'] {
            background-color: #0b266e;
            border-color: #0b266e;
            color: #fff;
        }

        #banksoal-main-content button.bg-primary:hover,
        #banksoal-main-content a.bg-primary:hover,
        #banksoal-main-content button[class*='bg-[#0B266E]']:hover,
        #banksoal-main-content a[class*='bg-[#0B266E]']:hover {
            background-color: #081c52;
        }

        #banksoal-main-content select,
        #banksoal-main-content .ts-control {
            min-height: 38px;
            background-color: #fff;
            border-color: #cbd5e1;
            color: #334155;
        }

        #banksoal-main-content p,
        #banksoal-main-content tbody td,
        #banksoal-main-content .prose,
        #banksoal-main-content .prose p,
        #banksoal-main-content .prose li {
            font-weight: 400;
        }

        #banksoal-main-content p.font-bold,
        #banksoal-main-content p.font-semibold,
        #banksoal-main-content tbody td .font-bold,
        #banksoal-main-content tbody td .font-semibold,
        #banksoal-main-content .prose .font-bold,
        #banksoal-main-content .prose .font-semibold {
            font-weight: 400;
        }

        #banksoal-main-content table th:last-child,
        #banksoal-main-content table td:last-child {
            text-align: center;
        }
    </style>
    <x-mobile-navigation-assets />
</head>

<body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-primary selection:text-white">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">

        <!-- Sidebar Component -->
        <x-banksoal::ui.sidebar-gpm />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

            <!-- Topbar -->
            <header
                class="bg-white border-b border-slate-200 h-16 shrink-0 flex items-center justify-between px-6 z-10">
                <div class="flex items-center text-sm font-medium text-slate-600">
                    <span class="mr-2">SIBASO</span>
                    @hasSection('breadcrumbs')
                        <span class="mx-2 text-slate-300">/</span>
                        @yield('breadcrumbs')
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button type="button"
                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors"
                        aria-label="Cari">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0" />
                        </svg>
                    </button>

                    <x-banksoal::ui.gpm-notification-bell />

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div
                            class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 overflow-hidden border border-slate-300 group-hover:border-primary transition-colors">
                            <span
                                class="font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-bold text-slate-800 leading-tight">{{ auth()->user()->name ?? 'GPM' }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">Modules GPM</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="w-full flex-1 overflow-y-auto">
                <div id="banksoal-main-content" class="p-4 md:p-6 w-full max-w-screen-2xl mx-auto">
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