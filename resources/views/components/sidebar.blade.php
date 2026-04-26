@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
    $currentRoute = request()->route()->getName();

    // Logika Inisial untuk Fallback Avatar
    $name = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false) {
        $initials .= strtoupper(substr($name, $sp + 1, 1));
    }

    // Warna tema berdasarkan role
    $themeClass = match(true) {
        $isSuperadmin => 'bg-red-50 text-red-600 border-red-100',
        $isDosen      => 'bg-purple-50 text-purple-600 border-purple-100',
        $isMahasiswa  => 'bg-blue-50 text-blue-600 border-blue-100',
        default       => 'bg-slate-50 text-slate-600 border-slate-100'
    };
    
    $rc = $themeClass;
@endphp

<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }"
     x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
     class="flex h-screen overflow-hidden font-['Inter_Tight']">

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="hidden md:flex flex-col fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200 transition-all duration-300 ease-in-out">

        {{-- Logo Area --}}
        <div class="h-16 flex items-center px-4 border-b border-slate-100 bg-white">
            <div class="flex items-center w-full" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-primary-100">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="text-slate-800 font-semibold text-sm tracking-tight whitespace-nowrap">
                            {{ config('app.name') }}
                        </span>
                        <p class="text-slate-400 text-[10px] font-semibold uppercase tracking-widest leading-none mt-0.5">
                            @if($isSuperadmin) Superadmin
                            @elseif($isDosen) Dosen Portal
                            @elseif($isMahasiswa) Mahasiswa
                            @else Portal
                            @endif
                        </p>
                    </div>
                </div>
                
                <button @click="sidebarOpen = !sidebarOpen"
                        x-show="sidebarOpen"
                        class="text-slate-400 hover:text-primary-600 transition-colors p-1 rounded-lg hover:bg-slate-50">
                    <span class="material-symbols-outlined !text-[20px]">first_page</span>
                </button>
            </div>

            {{-- Toggle Button for Collapsed State --}}
            <button x-show="!sidebarOpen" @click="sidebarOpen = !sidebarOpen"
                    class="text-slate-400 hover:text-primary-600 transition-colors p-1 rounded-full absolute -right-3 top-5 bg-white border border-slate-200 shadow-sm z-50">
                <span class="material-symbols-outlined !text-[14px]">last_page</span>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-6 overflow-y-auto custom-scrollbar">
            <div class="space-y-6">

                {{-- Dashboard Group --}}
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">
                        Utama
                    </p>
                    <div class="space-y-1">
                        @if($isSuperadmin)
                            <x-sidebar-link href="{{ route('superadmin.dashboard') }}"
                                icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                label="Dashboard"
                                active="{{ str_contains($currentRoute, 'dashboard') }}"/>
                        @else
                            {{-- Dashboard global untuk user biasa (dosen, mahasiswa, dll) --}}
                            <x-sidebar-link href="{{ route('dashboard') }}"
                                icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                label="Dashboard"
                                active="{{ $currentRoute === 'dashboard' }}"/>
                        @endif
                    </div>
                </div>

                {{-- Management Group (Superadmin Only) --}}
                @if($isSuperadmin)
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">
                        Kendali
                    </p>
                    <div class="space-y-1">
                        <x-sidebar-link href="{{ route('superadmin.users.index') }}"
                            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            label="User Management"
                            active="{{ str_contains($currentRoute, 'users') }}"/>

                        <x-sidebar-link href="{{ route('superadmin.permissions') }}"
                            icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            label="Permissions"
                            active="{{ str_contains($currentRoute, 'permissions') }}"/>

                        <x-sidebar-link href="{{ route('superadmin.audit-logs') }}"
                            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            label="Audit Logs"
                            active="{{ str_contains($currentRoute, 'audit-logs') }}"/>
                    </div>
                </div>
                @endif

                {{-- Academic Group --}}
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">
                        Akademik
                    </p>
                    <div class="space-y-1">

                        {{-- Bank Soal / Uji Komprehensif --}}
                        @if($isSuperadmin || $isDosen || $isMahasiswa)
                            <x-sidebar-link
                                :href="$isMahasiswa ? route('komprehensif.mahasiswa.dashboard') : route('banksoal.dashboard')"
                                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                :label="$isMahasiswa ? 'Uji Komprehensif' : ($isDosen ? 'Manajemen RPS / Bank Soal' : 'Bank Soal')"
                                :active="str_contains($currentRoute, 'banksoal') || str_contains($currentRoute, 'komprehensif')"/>
                        @endif

                        {{-- Capstone & TA --}}
                        <x-sidebar-link
                            :href="route('capstone.dashboard')"
                            icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            label="Capstone & TA"
                            :active="str_contains($currentRoute, 'capstone')"/>

                        {{-- Manajemen Mahasiswa / Forum Mahasiswa --}}
                        <x-sidebar-link
                            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            href="{{ route('manajemenmahasiswa.dashboard') }}"
                            :label="$isMahasiswa ? 'Kemahasiswaan' : 'Manajemen Mahasiswa'"
                            active="{{ str_contains($currentRoute, 'manajemen-mahasiswa') || str_contains($currentRoute, 'mk.') }}"/>

                        {{-- E-Office --}}
                        <x-sidebar-link
                            icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            href="{{ route('eoffice.dashboard') }}"
                            label="E-Office"
                            active="{{ str_contains($currentRoute, 'eoffice') || str_contains($currentRoute, 'eo.') }}"/>

                    </div>
                </div>
                
                {{-- System Group --}}
                @if($isSuperadmin)
                <div>
                    <p x-show="sidebarOpen" class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] px-3 mb-3 opacity-70">
                        Sistem
                    </p>
                    <div class="space-y-1">
                        <x-sidebar-link href="{{ route('superadmin.modules') }}"
                            icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                            label="Modul Sistem"
                            active="{{ str_contains($currentRoute, 'modules') }}"/>

                        {{-- Tambahkan Button System Monitor Di Sini --}}
                        <x-sidebar-link href="/pulse"
                            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            label="System Monitor"
                            target="_blank"
                            active="{{ request()->is('pulse*') }}"/>
                    </div>
                </div>
                @endif
            </div>
        </nav>

        {{-- User Footer Area --}}
        <div class="border-t border-slate-100 p-3 space-y-1 bg-white">
            {{-- User Info Card --}}
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 border border-slate-100" :class="!sidebarOpen ? 'justify-center' : ''">
                {{-- Avatar Logic (System Integrated) --}}
                <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center font-semibold text-xs shadow-sm overflow-hidden border-2 border-white {{ $rc }} bg-white">
                    @if($user->avatar_url)
                        {{-- Menampilkan Foto dari Supabase --}}
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{-- Fallback ke Inisial --}}
                        <span>{{ $initials }}</span>
                    @endif
                </div>

                {{-- Name & Role (Hidden when collapsed) --}}
                <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                    <p class="text-slate-800 text-[13px] font-semibold truncate tracking-tight">
                        {{ $user->name }}
                    </p>
                    <p class="text-slate-400 text-[10px] font-semibold uppercase tracking-wider truncate">
                        {{ implode(' / ', array_map('ucfirst', $userRoles)) }}
                    </p>
                </div>
            </div>

            {{-- Quick Actions di Footer Sidebar --}}
            <div class="pt-1 space-y-1">
                {{-- Link Profil - GUNAKAN SIDEBAR-LINK COMPONENT --}}
                <x-sidebar-link 
                    href="{{ route('profile.edit') }}"
                    icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    label="Profil"
                    active="{{ str_contains($currentRoute, 'profile') }}"
                    class="!text-slate-500 hover:!text-primary-600"/>

                {{-- Form Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group mb-1 text-slate-500 hover:text-red-600 hover:bg-red-50"
                            :class="!sidebarOpen ? 'justify-center px-2' : 'gap-3'">
                        
                        <svg class="w-5 h-5 flex-shrink-0 transition-all group-hover:scale-110 text-slate-400 group-hover:text-red-600" 
                             fill="none" 
                             stroke="currentColor" 
                             viewBox="0 0 24 24"
                             stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        
                        <span x-show="sidebarOpen" class="text-sm tracking-tight whitespace-nowrap font-sans">
                            Keluar
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main Content Viewport --}}
    <main
        :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'"
        class="flex-1 overflow-y-auto bg-[#F8F9FA] transition-all duration-300 ease-in-out">

        <div class="p-8">
            {{ $slot }}
        </div>

        <footer class="border-t border-slate-200 px-8 py-5 bg-white">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-slate-400 text-[11px] font-medium tracking-tight">
                    &copy; {{ date('Y') }} <span class="text-slate-800 font-semibold">{{ config('app.name') }}</span>. All rights reserved.
                </p>
                <div class="flex items-center gap-4">
                    <p class="text-slate-300 text-[10px] font-semibold uppercase tracking-[0.2em]">
                        LuminHR System v2.0
                    </p>
                </div>
            </div>
        </footer>
    </main>
    {{-- ── MOBILE BOTTOM NAV — hanya muncul di mobile ── --}}
    <x-sidebar-mobile :user="$user" />
</div>