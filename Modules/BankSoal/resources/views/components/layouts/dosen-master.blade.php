<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>BankSoal Module - {{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="{{ $description ?? '' }}">
        <meta name="keywords" content="{{ $keywords ?? '' }}">
        <meta name="author" content="{{ $author ?? '' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        {{-- TomSelect CSS must load BEFORE bsDosen.css so custom overrides win --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('modules/banksoal/css/bsDosen.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        {{-- Vite CSS --}}
        {{-- {{ module_vite('build-banksoal', 'resources/assets/sass/app.scss') }} --}}

        @stack('styles')
    </head>

    <body>
        {{ $slot }}

        {{-- Vite JS --}}
        {{-- {{ module_vite('build-banksoal', 'resources/assets/js/app.js') }} --}}
        <script src="{{ asset('modules/banksoal/js/app.js') }}"></script>
    </body>
</html>
