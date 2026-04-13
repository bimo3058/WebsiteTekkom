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
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-blue-500 selection:text-white">
    <div class="flex h-screen bg-slate-50 font-sans text-slate-900 overflow-hidden">
        
        <!-- Sidebar Component -->
        <x-banksoal::sidebar-admin />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-y-auto w-full relative">
            <main class="w-full flex-1">
                <div class="p-8 w-full max-w-screen-2xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
        
    </div>
</body>
</html>
