@props(['user'])

@php
    $userRoles    = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen      = in_array('dosen', $userRoles);
    $isMahasiswa  = in_array('mahasiswa', $userRoles);
    $currentRoute = request()->route()->getName();

    $name     = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $sp       = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));

    $rc = match(true) {
        $isSuperadmin => 'bg-red-50 text-red-600 border-red-100',
        $isDosen      => 'bg-purple-50 text-purple-600 border-purple-100',
        $isMahasiswa  => 'bg-blue-50 text-blue-600 border-blue-100',
        default       => 'bg-slate-50 text-slate-600 border-slate-100'
    };
@endphp

{{-- ── MOBILE BOTTOM NAV ────────────────────── --}}
<div x-data="{ show: window.innerWidth < 768, openMenu: false, startY: 0 }"
     x-init="window.addEventListener('resize', () => show = window.innerWidth < 768)"
     x-show="show"
     class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_24px_rgba(0,0,0,0.06)] font-['Inter_Tight']">

    {{-- More Menu Drawer (Lainnya) --}}
    <div x-show="openMenu"
         class="fixed inset-0 z-[60] font-['Inter_Tight']"
         style="display: none;">
         
        {{-- Backdrop --}}
        <div @click="openMenu = false"
             x-show="openMenu"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/40"></div>

        {{-- Drawer --}}
        <div x-show="openMenu"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             @touchstart="startY = $event.touches[0].clientY"
             @touchend="if ($event.changedTouches[0].clientY - startY > 80) openMenu = false"
             class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl max-h-[85vh] flex flex-col">
             
            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-2 shrink-0">
                <div class="w-10 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            {{-- User Info --}}
            <div class="flex items-center gap-3 px-5 pb-4 border-b border-slate-100 shrink-0">
                <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm overflow-hidden border-2 border-white shadow-sm {{ $rc }}">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-[15px] font-bold text-slate-800 truncate leading-tight">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate mt-0.5">
                        {{ implode(' / ', array_map('ucfirst', $userRoles)) }}
                    </p>
                </div>
            </div>

            {{-- Menu Items (Scrollable) --}}
            <div class="overflow-y-auto px-4 py-4 space-y-5 custom-scrollbar">

                {{-- Grup: Akademik / Modul --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">Akademik & Modul</p>
                    <div class="space-y-1">
                        @if($isSuperadmin || $isDosen || $isMahasiswa)
                        <a href="{{ $isMahasiswa ? route('komprehensif.mahasiswa.dashboard') : route('banksoal.dashboard') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'banksoal') || str_contains($currentRoute, 'komprehensif') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'banksoal') || str_contains($currentRoute, 'komprehensif') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-[13px] font-semibold">{{ $isMahasiswa ? 'Uji Komprehensif' : 'Bank Soal' }}</span>
                        </a>
                        @endif

                        <a href="{{ route('capstone.dashboard') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'capstone') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'capstone') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-[13px] font-semibold">Capstone & TA</span>
                        </a>

                        <a href="{{ route('manajemenmahasiswa.mahasiswa.dashboard') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'manajemen-mahasiswa') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'manajemen-mahasiswa') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-[13px] font-semibold">{{ $isMahasiswa ? 'Forum Mahasiswa' : 'Manajemen Mahasiswa' }}</span>
                        </a>

                        <a href="{{ route('eoffice.dashboard') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'eoffice') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'eoffice') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-[13px] font-semibold">E-Office</span>
                        </a>
                    </div>
                </div>

                {{-- Grup: Sistem (Superadmin Only) --}}
                @if($isSuperadmin)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">Sistem Ekstra</p>
                    <div class="space-y-1">
                        <a href="{{ route('superadmin.modules') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'modules') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'modules') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                            <span class="text-[13px] font-semibold">Modul Sistem</span>
                        </a>

                        <a href="/pulse" target="_blank" @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="text-[13px] font-semibold">System Monitor</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Grup: Akun --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1 opacity-80">Akun</p>
                    <div class="space-y-1">
                        <a href="{{ route('profile.edit') }}"
                           @click="openMenu = false"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ str_contains($currentRoute, 'profile') ? 'bg-[#F1E9FF] text-[#5E53F4]' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0 {{ str_contains($currentRoute, 'profile') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-[13px] font-semibold">Profil Saya</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
        <a href="{{ $isSuperadmin ? route('superadmin.dashboard') : route('dashboard') }}"
           class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
               {{ str_contains($currentRoute, 'dashboard') ? 'text-[#5E53F4]' : 'text-[#ADB5BD]' }}">
            <svg class="w-5 h-5 {{ str_contains($currentRoute, 'dashboard') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Home</span>
        </a>

        {{-- Jika User adalah Superadmin tampilkan Kendali di Bottom Nav --}}
        @if($isSuperadmin)
            {{-- 2. User Management --}}
            <a href="{{ route('superadmin.users.index') }}"
               class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'users') ? 'text-[#5E53F4]' : 'text-[#ADB5BD]' }}">
                <svg class="w-5 h-5 {{ str_contains($currentRoute, 'users') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-[9px] font-extrabold uppercase tracking-tight">Users</span>
            </a>

            {{-- 3. Permissions --}}
            <a href="{{ route('superadmin.permissions') }}"
               class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'permissions') ? 'text-[#5E53F4]' : 'text-[#ADB5BD]' }}">
                <svg class="w-5 h-5 {{ str_contains($currentRoute, 'permissions') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-[9px] font-extrabold uppercase tracking-tight">Akses</span>
            </a>

            {{-- 4. Audit Logs --}}
            <a href="{{ route('superadmin.audit-logs') }}"
               class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all
                   {{ str_contains($currentRoute, 'audit-logs') ? 'text-[#5E53F4]' : 'text-[#ADB5BD]' }}">
                <svg class="w-5 h-5 {{ str_contains($currentRoute, 'audit-logs') ? 'text-[#5E53F4]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-[9px] font-extrabold uppercase tracking-tight">Logs</span>
            </a>
        @endif

        {{-- 5. Lainnya (Membuka Drawer Menggunakan Alpine) --}}
        <button @click="openMenu = true"
                class="flex flex-col items-center gap-1 px-3 py-1.5 rounded-xl transition-all text-[#ADB5BD]">
            {{-- Icon grid custom untuk Lainnya --}}
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="text-[9px] font-extrabold uppercase tracking-tight">Lainnya</span>
        </button>

    </div>
</div>

{{-- Spacer agar konten utama tidak tertutup bottom nav --}}
<div x-data="{ show: window.innerWidth < 768 }"
     x-init="window.addEventListener('resize', () => show = window.innerWidth < 768)"
     x-show="show"
     class="h-16"></div>