<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>ManajemenMahasiswa Module - {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    {{-- Vite CSS --}}
    {{-- {{ module_vite('build-manajemenmahasiswa', 'resources/assets/sass/app.scss') }} --}}

    @stack('styles')
</head>

<body>
    {{ $slot }}

    {{-- Vite JS --}}
    {{-- {{ module_vite('build-manajemenmahasiswa', 'resources/assets/js/app.js') }} --}}

    @stack('scripts')
</body>

</html>