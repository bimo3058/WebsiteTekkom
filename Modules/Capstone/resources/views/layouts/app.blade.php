<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Capstone & TA') | SICATA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ url('/capstone/assets/images/UNDIPOfficial.png') }}">
    <link rel="stylesheet" href="{{ url('/capstone/assets/build/app.css') }}?v={{ filemtime(module_path('Capstone', 'public/build/app.css')) }}">
    <script type="application/json" id="capstone-context">{!! json_encode(['actor'=>$actor ?? null,'role'=>$activeRole ?? null,'path'=>$pagePath ?? '/','params'=>$pageParams ?? [],'base'=>url('/capstone'),'api'=>url('/capstone/session/capstone')], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE) !!}</script>
    <script defer src="{{ url('/capstone/assets/build/app.js') }}?v={{ filemtime(module_path('Capstone', 'public/build/app.js')) }}"></script>
    <x-mobile-navigation-assets />
    @include('capstone::layouts.shell-styles')
</head>
<body class="font-sans bg-background text-foreground" x-data="capstoneShell">
<div x-show="pendingRequests > 0" x-cloak class="capstone-progress" role="status" aria-label="Sedang memproses permintaan">
    <span class="capstone-progress-bar" aria-hidden="true"></span>
</div>
<div class="sitkom-shell-capstone flex min-h-svh w-full bg-[#F6F8FA] dark:bg-background" style="--sidebar-width:240px">
    @include('capstone::layouts.sidebar')
    <main class="flex h-screen w-full min-w-0 flex-col overflow-hidden">
        @include('capstone::layouts.topbar')
        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-7 sm:pb-12">
            <div class="capstone-mobile-content w-full pb-20 md:pb-0">@yield('content')</div>
        </div>
    </main>
</div>
<div aria-live="polite" aria-relevant="additions" class="capstone-notices fixed bottom-5 right-5 flex w-80 flex-col gap-2">
    <template x-for="notice in notices" :key="notice.id">
        <div role="status" class="capstone-notice bg-popover text-popover-foreground flex items-start gap-3 rounded-lg border p-4 shadow-lg text-sm" :class="notice.error ? 'border-red-300' : 'border-green-200'">
            <template x-if="!notice.error">
                <span class="capstone-success-icon flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-700">
                    <x-capstone::icon name="Check" class="h-4 w-4" />
                </span>
            </template>
            <template x-if="notice.error">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                    <x-capstone::icon name="X" class="h-4 w-4" />
                </span>
            </template>
            <span class="min-w-0 flex-1 break-words pt-0.5" x-text="notice.message"></span>
            <button type="button" class="text-muted-foreground shrink-0 rounded p-1 hover:bg-muted focus-visible:outline-2 focus-visible:outline-ring" aria-label="Tutup notifikasi" @click="notices=notices.filter(item=>item.id!==notice.id)">
                <x-capstone::icon name="X" class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>
@stack('scripts')
    <x-mobile-navigation />
</body>
</html>
