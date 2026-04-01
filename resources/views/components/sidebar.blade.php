@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
    $currentRoute = request()->route()->getName();
@endphp

<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }"
     x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
     class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'w-60' : 'w-16'"
        class="hidden md:flex flex-col fixed inset-y-0 left-0 z-50 bg-blue-700 transition-all duration-300 ease-in-out">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-4 border-b border-blue-600">
            <div class="flex items-center w-full" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                <template x-if="sidebarOpen">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-white font-semibold text-sm whitespace-nowrap">
                            @if($isSuperadmin) Superadmin
                            @elseif($isDosen) Dosen
                            @elseif($isMahasiswa) Mahasiswa
                            @else User
                            @endif
                        </span>
                    </div>
                </template>
                <template x-if="!sidebarOpen">
                    <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </template>
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-blue-200 hover:text-white transition-colors p-1 rounded"
                        :class="!sidebarOpen ? 'hidden' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>
            <button x-show="!sidebarOpen" @click="sidebarOpen = !sidebarOpen"
                    class="text-blue-200 hover:text-white transition-colors p-1 rounded absolute right-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 overflow-y-auto space-y-5">

            {{-- Core System --}}
            @if($isSuperadmin)
            <div>
                <p x-show="sidebarOpen" class="text-blue-300 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Core System</p>

                <x-sidebar-link href="{{ route('superadmin.dashboard') }}"
                    icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    label="Dashboard" active="{{ str_contains($currentRoute, 'dashboard') }}"/>

                <x-sidebar-link href="{{ route('superadmin.users.index') }}"
                    icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    label="User Management" active="{{ str_contains($currentRoute, 'users') }}"/>

                <x-sidebar-link href="{{ route('superadmin.permissions') }}"
                    icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                    label="Permissions" active="{{ str_contains($currentRoute, 'permissions') }}"/>

                <x-sidebar-link href="{{ route('superadmin.audit-logs') }}"
                    icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    label="Audit Logs" active="{{ str_contains($currentRoute, 'audit-logs') }}"/>

                <x-sidebar-link href="{{ url('/pulse') }}"
                    icon="M13 10V3L4 14h7v7l9-11h-7z"
                    label="System Monitor" active="{{ request()->is('pulse*') }}"/>
            </div>
            @endif

            {{-- Modules --}}
            <div>
                <p x-show="sidebarOpen" class="text-blue-300 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Modul</p>
                @if($isSuperadmin || $isDosen || $isMahasiswa)
                <x-sidebar-link 
                    :href="
                        $isMahasiswa 
                            ? route('komprehensif.mahasiswa.dashboard') 
                            : route('banksoal.dashboard')
                    "
                    icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                    :label="$isMahasiswa ? 'Ujian Komprehensif' : 'Bank Soal'"
                    :active="str_contains($currentRoute, 'banksoal') 
                        || str_contains($currentRoute, 'komprehensif')"
                />
                @endif

                @if($isSuperadmin || $isDosen || $isMahasiswa)
                <x-sidebar-link 
                    :href="route('capstone.dashboard')"
                    icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                    label="Capstone TA"
                    :active="str_contains($currentRoute, 'capstone')"
                />
                @endif

                @if($isSuperadmin || $isDosen)
                <x-sidebar-link
                    icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    href="{{ route('eoffice.dashboard') }}"
                    label="E-Office"
                    active="{{ str_contains($currentRoute, 'eoffice') || str_contains($currentRoute, 'eo.') }}"/>
                @endif

                @if($isSuperadmin || $isDosen || $isMahasiswa)
                <x-sidebar-link href="#"
                    icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    href="{{ $isMahasiswa ? route('manajemenmahasiswa.mahasiswa.dashboard') : route('manajemenmahasiswa.mahasiswa.dashboard') }}"
                    label="Manajemen Mahasiswa"
                    active="{{ str_contains($currentRoute, 'manajemen-mahasiswa') || str_contains($currentRoute, 'mk.') }}"/>
                @endif
            </div>

            {{-- System --}}
            @if($isSuperadmin)
            <div>
                <p x-show="sidebarOpen" class="text-blue-300 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">System</p>
                <x-sidebar-link href="{{ route('superadmin.modules') }}"
                    icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    label="Modul Sistem"
                    active="{{ str_contains($currentRoute, 'modules') }}"/>
            </div>
            @endif
        </nav>

        {{-- User Footer --}}
        <div class="border-t border-blue-600 p-3 space-y-1">
            <div class="flex items-center gap-2.5 px-2 py-2" :class="!sidebarOpen ? 'justify-center' : ''">
                <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ $user->name }}</p>
                    <p class="text-blue-300 text-xs truncate">
                        @foreach($userRoles as $role){{ $role }}@if(!$loop->last), @endif@endforeach
                    </p>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-blue-100 hover:text-white hover:bg-blue-600 transition-colors"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="sidebarOpen" class="text-sm">Profil</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2.5 px-2 py-2 rounded-lg text-blue-100 hover:text-white hover:bg-red-500/30 transition-colors"
                        :class="!sidebarOpen ? 'justify-center' : ''">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main
        :class="sidebarOpen ? 'md:ml-60' : 'md:ml-16'"
        class="flex-1 overflow-y-auto bg-slate-50 transition-all duration-300 ease-in-out">
        {{ $slot }}
        <footer class="border-t border-slate-200 px-8 py-4 mt-4">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-slate-400 text-xs">&copy; {{ date('Y') }} <span class="text-slate-500 font-medium">{{ config('app.name') }}</span>. All rights reserved.</p>
                <p class="text-slate-400 text-xs">Laravel & Tailwind CSS</p>
            </div>
        </footer>
    </main>
</div>