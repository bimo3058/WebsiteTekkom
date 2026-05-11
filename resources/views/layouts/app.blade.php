<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SITKOM') }}</title>

        {{-- Cropper.js --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

        {{-- Fonts: Inter Tight (UI) + Geist (brand) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">

        {{-- Material Symbols Outlined --}}
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
        <style>
            .material-symbols-outlined {
                font-family: 'Material Symbols Outlined' !important;
                display: inline-block !important;
                user-select: none;
                vertical-align: middle;
                overflow: hidden;
                font-variant-ligatures: none !important;
                -webkit-font-feature-settings: "liga" 0 !important;
                font-feature-settings: "liga" 0 !important;
            }
            
            /* Pastikan tinggi penuh */
            html, body { 
                height: 100%; 
            }
            
            body { 
                font-family: 'Inter Tight', system-ui, sans-serif; 
            }

            /* =========================================================
               KUSTOMISASI SCROLLBAR & OVERFLOW (PERBAIKAN)
               ========================================================= */
            
            /* Mengatur agar scroll hanya muncul jika butuh (auto) */
            html {
                overflow-y: auto;
                overflow-x: hidden;
                scrollbar-width: thin; /* Firefox: membuat scrollbar lebih tipis */
                scrollbar-color: #CBD5E1 transparent; /* Firefox: thumb color & track color */
            }

            /* Chrome, Edge, dan Safari */
            ::-webkit-scrollbar {
                width: 8px;  /* Lebar scrollbar vertikal */
                height: 8px; /* Tinggi scrollbar horizontal */
            }

            /* Latar belakang jalur scroll (dibuat transparan agar bersih) */
            ::-webkit-scrollbar-track {
                background: transparent;
            }

            /* Batang scroll itu sendiri */
            ::-webkit-scrollbar-thumb {
                background-color: #CBD5E1; /* Warna abu-abu yang kalem */
                border-radius: 10px;       /* Membuat ujungnya membulat */
            }

            /* Efek saat batang scroll di-hover */
            ::-webkit-scrollbar-thumb:hover {
                background-color: #94A3B8; /* Sedikit lebih gelap saat di-hover */
            }
            /* ========================================================= */
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    @php
        $hasSidebar = request()->is('superadmin*')
            || request()->is('bank-soal*')
            || request()->is('capstone*')
            || request()->is('eoffice*')
            || request()->is('manajemen-mahasiswa*')
            || request()->routeIs('profile.*');
    @endphp

    @if($hasSidebar)
        <body class="font-sans antialiased h-full">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset

            <x-ui.loader />
        </body>
    @else
        <body class="font-sans antialiased">
            <div class="min-h-screen" style="background:#F6F8FA;">
                @include('layouts.navigation')

                @isset($header)
                    <header style="background:#fff;border-bottom:1px solid #DFE1E7;">
                        <div class="max-w-7xl mx-auto py-4 px-6">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="w-full">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>
            </div>

            <x-ui.loader />
        </body>
    @endif
</html>
