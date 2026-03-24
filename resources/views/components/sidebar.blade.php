@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
    
    // Get current route for active state
    $currentRoute = request()->route()->getName();
@endphp

<div x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }" 
     x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
     class="flex h-screen bg-slate-900 overflow-hidden">
    
    {{-- Sidebar --}}
    <aside 
        :class="sidebarOpen ? 'w-64' : 'w-20'" 
        class="hidden md:flex flex-col fixed inset-y-0 left-0 z-50 bg-slate-800 border-r border-slate-700 transition-all duration-300 ease-in-out"
    >
        {{-- Logo --}}
        <div class="h-20 flex items-center px-6 border-b border-slate-700">
            <div class="flex items-center w-full" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
                {{-- Logo with text --}}
                <template x-if="sidebarOpen">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold tracking-wider uppercase text-xs whitespace-nowrap">
                            @if($isSuperadmin)
                                Superadmin
                            @elseif($isDosen)
                                Dosen
                            @elseif($isMahasiswa)
                                Mahasiswa
                            @else
                                User
                            @endif
                        </span>
                    </div>
                </template>
                
                {{-- Logo only (when collapsed) --}}
                <template x-if="!sidebarOpen">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </template>
                
                {{-- Toggle button --}}
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white transition-colors p-1">
                    <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-slate-800">
            <div class="space-y-6">
                
                {{-- Core System Section (only for superadmin) --}}
                @if($isSuperadmin)
                <div>
                    <p x-show="sidebarOpen" class="text-slate-500 text-[10px] font-bold uppercase tracking-widest px-2 mb-3">Core System</p>
                    
                    {{-- Dashboard --}}
                    <x-sidebar-link 
                        href="{{ route('superadmin.dashboard') }}"
                        icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        label="Dashboard"
                        active="{{ str_contains($currentRoute, 'dashboard') }}"
                    />

                    {{-- User Management --}}
                    <x-sidebar-link 
                        href="{{ route('superadmin.users.index') }}"
                        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        label="User Management"
                        active="{{ str_contains($currentRoute, 'users') }}"
                    />

                    {{-- Audit Logs --}}
                    <x-sidebar-link 
                        href="{{ route('superadmin.audit-logs') }}"
                        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        label="Audit Logs"
                        color="text-emerald-400"
                        active="{{ str_contains($currentRoute, 'audit-logs') }}"
                    />
                </div>
                @endif

                {{-- Modules Section --}}
                <div>
                    <p x-show="sidebarOpen" class="text-slate-500 text-[10px] font-bold uppercase tracking-widest px-2 mb-3">Modules</p>
                    
                    {{-- Bank Soal Module --}}
                    @if($isSuperadmin || $isDosen || $isMahasiswa)
                    <x-sidebar-link 
                        href="#"
                        icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        label="{{ $isMahasiswa ? 'Ujian Komprehensif' : 'Bank Soal' }}"
                        color="text-yellow-400"
                        active="{{ str_contains($currentRoute, 'bank-soal') || str_contains($currentRoute, 'bs.') || str_contains($currentRoute, 'komprehensif.mahasiswa') }}"
                    />
                    @endif

                    {{-- Capstone Module --}}
                    @if($isSuperadmin || $isDosen || $isMahasiswa)
                    <x-sidebar-link 
                        href="#"
                        icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                        label="Capstone TA"
                        color="text-cyan-400"
                        active="{{ str_contains($currentRoute, 'capstone') }}"
                    />
                    @endif

                    {{-- E-Office Module --}}
                    @if($isSuperadmin || $isDosen)
                    <x-sidebar-link 
                        href="#"
                        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                        label="E-Office"
                        color="text-purple-400"
                        active="{{ str_contains($currentRoute, 'eoffice') || str_contains($currentRoute, 'eo.') }}"
                    />
                    @endif

                    {{-- Manajemen Mahasiswa Module --}}
                    @if($isSuperadmin || $isDosen || $isMahasiswa)
                    <x-sidebar-link 
                        href="#"
                        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        label="Manajemen Mahasiswa"
                        color="text-orange-400"
                        active="{{ str_contains($currentRoute, 'manajemen-mahasiswa') || str_contains($currentRoute, 'mk.') }}"
                    />
                    @endif
                </div>

                {{-- System Section --}}
                @if($isSuperadmin)
                <div>
                    <p x-show="sidebarOpen" class="text-slate-500 text-[10px] font-bold uppercase tracking-widest px-2 mb-3">System</p>
                    
                    {{-- Modules Settings --}}
                    <x-sidebar-link 
                        href="{{ route('superadmin.modules') }}"
                        icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                        label="Modules"
                        color="text-indigo-400"
                        active="{{ str_contains($currentRoute, 'modules') }}"
                    />
                </div>
                @endif
            </div>
        </nav>

        {{-- User Profile Footer --}}
        <div class="border-t border-slate-700 p-4">
            <div class="flex items-center gap-3" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div x-show="sidebarOpen" class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ $user->name }}</p>
                    <p class="text-slate-500 text-xs truncate">
                        @foreach($userRoles as $role)
                            {{ $role }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </div>
            
            {{-- Profile Link --}}
            <a href="{{ route('profile.edit') }}" 
               class="w-full flex items-center gap-3 p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/30 transition group mt-2"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span x-show="sidebarOpen" class="text-sm">Profile</span>
            </a>
            
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-3 p-2 rounded-lg text-slate-300 hover:text-white hover:bg-red-500/20 transition group"
                        :class="!sidebarOpen ? 'justify-center' : ''">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <main 
        :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'" 
        class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 transition-all duration-300 ease-in-out"
    >
        {{ $slot }}
        {{-- Footer --}}
        <footer class="border-t border-slate-700/50 px-8 py-5 mt-4">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-slate-500 text-xs">
                    &copy; {{ date('Y') }} <span class="text-slate-400 font-medium">{{ config('app.name') }}</span>. All rights reserved.
                </p>
                <p class="text-slate-600 text-xs">
                    Built with <span class="text-red-400">♥</span> using Laravel & Tailwind CSS
                </p>
            </div>
        </footer>
    </main>
</div>