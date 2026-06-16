{{-- SIKAPE Topbar --}}
@php $user = auth()->user(); @endphp
<header class="h-16 bg-white border-b border-grey-100 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 z-10">
    <!-- Left: Mobile toggle + Breadcrumb -->
    <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-grey-500 hover:bg-grey-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <!-- Breadcrumb -->
        <nav class="hidden sm:flex items-center gap-1.5 text-[14px]">
            <span class="text-grey-400 font-medium tracking-wide">SIKAPE</span>
            @if(isset($breadcrumbs) && count($breadcrumbs))
                @foreach($breadcrumbs as $i => $crumb)
                <span class="text-grey-300 mx-1">/</span>
                @if($i === count($breadcrumbs) - 1)
                    <span class="font-bold text-grey-800" style="color: var(--grey-800);">{{ $crumb }}</span>
                @else
                    <span class="text-grey-400">{{ $crumb }}</span>
                @endif
                @endforeach
            @elseif(isset($breadcrumb))
                <span class="text-grey-300 mx-1">/</span>
                <span class="font-bold text-grey-800" style="color: var(--grey-800);">{{ $breadcrumb }}</span>
            @endif
        </nav>
        <!-- Mobile title -->
        <span class="sm:hidden text-[15px] font-semibold text-grey-800">{{ $breadcrumb ?? 'SIKAPE' }}</span>
    </div>

    <!-- Right: Search + Notification + User -->
    <div class="flex items-center gap-2">
        <!-- Search Button -->
        <button class="w-9 h-9 rounded-full border border-grey-200 flex items-center justify-center text-grey-500 hover:bg-grey-50 hover:text-grey-800 transition-all focus:outline-none">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>

        <!-- Notification Bell -->
        <button class="relative w-9 h-9 rounded-full border border-grey-200 flex items-center justify-center text-grey-500 hover:bg-grey-50 hover:text-grey-800 transition-all focus:outline-none">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute rounded-full bg-red-500 border-2 border-white" style="width:8px;height:8px;top:1px;right:1px;"></span>
        </button>

        <div class="hidden sm:block w-px h-5 bg-grey-200 mx-2"></div>

        <!-- User Profile Info -->
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 flex-shrink-0" style="background-color:#dbeafe; color:#1e40af;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="hidden sm:block text-left">
                <p class="text-[13px] font-bold text-grey-900 leading-tight" style="color:var(--grey-900);">{{ $user->name ?? 'Mahasiswa' }}</p>
                <p class="text-[11px] text-grey-400 mt-0.5 leading-none">Mahasiswa</p>
            </div>
        </div>
    </div>
</header>
