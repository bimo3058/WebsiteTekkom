<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Mahasiswa') - {{ config('app.name', 'Portal Mahasiswa') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50">
    <div class="flex h-screen overflow-hidden bg-slate-50">
        <!-- Sidebar -->
        @include('banksoal::partials.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col relative overflow-hidden">
            <!-- Header -->
            @include('banksoal::partials.header')

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto w-full">
                <!-- Content has generous padding and light grey background -->
                <div class="p-8 w-full max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
