@props(['user', 'rawSettings', 'currentRoute'])

@php
    $name = $user?->name ?? 'User';
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false)
        $initials .= strtoupper(substr($name, $sp + 1, 1));

    $sb_katalog = filter_var($rawSettings['sb_user_katalog'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_kalender = filter_var($rawSettings['sb_user_kalender'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_peminjaman = filter_var($rawSettings['sb_user_peminjaman'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $sb_riwayat = filter_var($rawSettings['sb_user_riwayat'] ?? true, FILTER_VALIDATE_BOOLEAN);
    
    // icons
    $iHome = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iBook = "M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z";
    $iCal = "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z";
    $iPeminjaman = "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z";
    $iRiwayat = "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z";
    $iLogout = "M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.94L7.87 4.06C6.39 3.66 5 5.21 5 7.27v9.46C5 18.79 6.39 20.34 7.87 19.94l3.2-.87C12.19 18.76 13 17.42 13 15.86v-.59M11 12h8M19 12l-2.5-2.72M19 12l-2.5 2.72";
    $iBack = "M19 12H5M5 12l7-7M5 12l7 7";
@endphp

{{-- ── MOBILE BOTTOM NAV ────────────────────── --}}
<div x-data="{ show: window.innerWidth < 768, openMenu: false, startY: 0 }"
    x-init="window.addEventListener('resize', () => show = window.innerWidth < 768)" x-show="show"
    style="display: none;"
    class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_24px_rgba(0,0,0,0.06)] font-['Inter_Tight']">

    {{-- More Menu Drawer (Lainnya) --}}
    <div x-show="openMenu" class="fixed inset-0 z-[60] font-['Inter_Tight']" style="display: none;">
        {{-- Backdrop --}}
        <div @click="openMenu = false" x-show="openMenu"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/40"></div>

        {{-- Drawer --}}
        <div x-show="openMenu" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full" @touchstart="startY = $event.touches[0].clientY"
            @touchend="if ($event.changedTouches[0].clientY - startY > 80) openMenu = false"
            class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl max-h-[85vh] flex flex-col">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2 shrink-0">
                <div class="w-10 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            {{-- User Info --}}
            <div class="flex items-center gap-3 px-5 pb-4 border-b border-slate-100 shrink-0">
                <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm overflow-hidden text-white shadow-sm" style="background:linear-gradient(135deg,#1F2937,#111827);">
                    <span>{{ $initials }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-slate-800 truncate leading-tight">{{ $name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate mt-0.5">
                        {{ $user?->email ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Menu Items (Scrollable) --}}
            <div class="overflow-y-auto px-4 py-4 space-y-5 custom-scrollbar">

                {{-- Grup: Lainnya --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">
                        Menu Lainnya</p>
                    <div class="space-y-1">
                        @if($sb_riwayat)
                        <a href="{{ route('eoffice.peminjaman.user.riwayat') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'user.riwayat') ? 'bg-[#EEF2FF] text-[#0B266E]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'user.riwayat') ? 'text-[#0B266E]' : 'text-slate-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iRiwayat }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Riwayat Peminjaman</span>
                        </a>
                        @endif

                        <a href="{{ route('eoffice.dashboard') }}" @click="openMenu = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $iBack }}" />
                            </svg>
                            <span class="text-[13px] font-semibold">Kembali ke E-Office Utama</span>
                        </a>
                    </div>
                </div>

                {{-- Grup: Akun --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">
                        Akun</p>
                    <div class="space-y-1">
                        <form method="POST" action="{{ route('logout') }}" data-no-loader>
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $iLogout }}" />
                                </svg>
                                <span class="text-[13px] font-semibold">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Safe area padding bottom --}}
                <div class="h-6"></div>
            </div>
        </div>
    </div>

    {{-- Bottom Nav Bar (Menu Utama Mobile) --}}
    <div class="flex items-center justify-around px-2 py-1.5 safe-area-pb bg-white relative z-10">

        {{-- 1. Dashboard --}}
        <a href="{{ route('eoffice.peminjaman.dashboard') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
               {{ str_contains($currentRoute, 'peminjaman.dashboard') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'peminjaman.dashboard') ? 'text-[#0B266E]' : 'text-slate-400' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iHome }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Home</span>
        </a>

        {{-- 2. Katalog --}}
        @if($sb_katalog)
        <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'user.booking') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'user.booking') ? 'text-[#0B266E]' : 'text-slate-400' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iBook }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Katalog</span>
        </a>
        @endif

        {{-- 3. Kalender --}}
        @if($sb_kalender)
        <a href="{{ route('eoffice.peminjaman.user.kalender') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'user.kalender') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'user.kalender') ? 'text-[#0B266E]' : 'text-slate-400' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iCal }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Kalender</span>
        </a>
        @endif

        {{-- 4. Peminjaman --}}
        @if($sb_peminjaman)
        <a href="{{ route('eoffice.peminjaman.user.saya') }}" class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'user.saya') ? 'text-[#0B266E]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'user.saya') ? 'text-[#0B266E]' : 'text-slate-400' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $iPeminjaman }}" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Status</span>
        </a>
        @endif

        {{-- 5. Lainnya (Membuka Drawer Menggunakan Alpine) --}}
        <button @click="openMenu = true"
            class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all text-[#ADB5BD]">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Lainnya</span>
        </button>

    </div>
</div>
