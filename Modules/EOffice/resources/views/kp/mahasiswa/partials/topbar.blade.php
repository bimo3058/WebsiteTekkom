{{-- SIKAPE Topbar --}}
@php $user = auth()->user(); @endphp
<header class="h-16 bg-white border-b border-grey-100 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-10">
    <!-- Left: Mobile toggle + Breadcrumb -->
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-grey-500 hover:bg-grey-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Breadcrumb -->
        <nav class="hidden sm:flex items-center gap-1.5 text-[13px]">
            <span class="text-grey-400 font-medium">SIKAPE</span>
            @if(isset($breadcrumbs) && count($breadcrumbs))
                @foreach($breadcrumbs as $i => $crumb)
                <svg class="w-3 h-3 text-grey-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if($i === count($breadcrumbs) - 1)
                    <span class="font-semibold text-grey-800">{{ $crumb }}</span>
                @else
                    <span class="text-grey-400">{{ $crumb }}</span>
                @endif
                @endforeach
            @elseif(isset($breadcrumb))
                <svg class="w-3 h-3 text-grey-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-grey-800">{{ $breadcrumb }}</span>
            @endif
        </nav>
        <!-- Mobile title -->
        <span class="sm:hidden text-[15px] font-semibold text-grey-800">{{ $breadcrumb ?? 'SIKAPE' }}</span>
    </div>

    <!-- Right: Notification + User -->
    <div class="flex items-center gap-2">
        <!-- Notification Bell -->
        <button class="relative p-2 rounded-lg text-grey-500 hover:bg-grey-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error-200 rounded-full border-2 border-white"></span>
        </button>

        <div class="hidden sm:block w-px h-5 bg-grey-200 mx-1"></div>

        <!-- User -->
        <div class="flex items-center gap-2.5 cursor-pointer px-2 py-1.5 rounded-xl hover:bg-grey-50 transition-colors">
            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-500 font-semibold text-sm">
                {{ strtoupper(substr($user->name ?? 'M', 0, 2)) }}
            </div>
            <div class="hidden sm:block">
                <p class="text-[13px] font-semibold text-grey-800 leading-tight">{{ $user->name ?? 'Mahasiswa' }}</p>
                <p class="text-[11px] text-grey-400 leading-tight">Mahasiswa KP</p>
            </div>
        </div>
    </div>
</header>
