<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Inter Tight — semua weight yang dipakai di design system --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        {{-- Material Symbols Outlined --}}
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet"/>
        <style>
            /* Paksa Material Symbols agar tidak merender teks ligature di bawahnya */
            .material-symbols-outlined {
                font-family: 'Material Symbols Outlined' !important;
                display: inline-block !important;
                user-select: none;
                vertical-align: middle;
                overflow: hidden;
                /* Matikan ligature teks agar kata 'person_settings' tidak muncul di belakang ikon */
                font-variant-ligatures: none !important;
                -webkit-font-feature-settings: "liga" 0 !important;
                font-feature-settings: "liga" 0 !important;
            }
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
            {{ $slot }}
        </body>
    @else
        <body class="font-sans antialiased">
            <div class="min-h-screen bg-slate-50">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white border-b border-slate-200">
                        <div class="max-w-7xl mx-auto py-4 px-6">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="w-full">
                    {{ $slot }}
                </main>
            </div>
        </body>
    @endif

</html>