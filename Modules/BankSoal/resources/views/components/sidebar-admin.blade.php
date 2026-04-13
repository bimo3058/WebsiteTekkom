<aside class="w-72 h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter">
    
    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4">
        
        <!-- Header -->
        <div class="px-6 py-8 flex items-center gap-4 border-b border-transparent">
            <!-- Icon Background -->
            <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <!-- Title Content -->
            <div class="flex flex-col">
                <span class="font-bold text-slate-900 text-[15px] leading-tight tracking-tight uppercase">Admin</span>
                <span class="font-bold text-slate-900 text-[15px] leading-tight tracking-tight uppercase">Portal</span>
            </div>
        </div>

        <div class="px-6 mb-4 mt-2">
            <div class="h-px w-full bg-slate-100"></div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 space-y-1">
            
            <!-- Dashboard -->
            <a href="{{ route('banksoal.dashboard') }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.dashboard') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2"/><rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2"/><rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2"/><rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2"/>
                </svg>
                <span class="text-[13px]">Dashboard</span>
            </a>

            <!-- Manajemen Periode Ujian (with Submenu) -->
            <div x-data="{ open: {{ request()->routeIs('banksoal.periode.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl hover:bg-slate-50 font-medium transition-all group {{ request()->routeIs('banksoal.periode.*') ? 'bg-slate-50' : 'text-slate-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('banksoal.periode.*') ? 'text-blue-600' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" stroke-width="2"/><line x1="16" x2="16" y1="2" y2="6" stroke-width="2"/><line x1="8" x2="8" y1="2" y2="6" stroke-width="2"/><line x1="3" x2="21" y1="10" y2="10" stroke-width="2"/>
                        </svg>
                        <span class="text-[13px] text-left leading-tight {{ request()->routeIs('banksoal.periode.*') ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Manajemen Periode<br>Ujian</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('banksoal.periode.*') ? 'text-blue-600' : 'text-slate-400' }}" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="{{ route('banksoal.periode.setup') }}" class="block text-[13px] {{ request()->routeIs('banksoal.periode.setup') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Setup Periode</a>
                    <a href="{{ route('banksoal.periode.jadwal') }}" class="block text-[13px] {{ request()->routeIs('banksoal.periode.jadwal') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Pengaturan Jadwal</a>
                </div>
            </div>

            <!-- Manajemen Peserta -->
            <div x-data="{ open: {{ request()->routeIs('banksoal.pendaftaran.*') ? 'true' : 'false' }} }" class="space-y-1 mt-1">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.pendaftaran.*') ? 'bg-slate-50' : 'text-slate-600' }} hover:bg-slate-50 font-medium transition-all group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('banksoal.pendaftaran.*') ? 'text-blue-600' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="text-[13px] text-left {{ request()->routeIs('banksoal.pendaftaran.*') ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Manajemen Peserta</span>
                    </div>
                    <svg class="w-4 h-4 {{ request()->routeIs('banksoal.pendaftaran.*') ? 'text-blue-600' : 'text-slate-400' }} transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="{{ route('banksoal.pendaftaran.index') }}" class="block text-[13px] {{ request()->routeIs('banksoal.pendaftaran.*') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Daftar Pendaftar</a>
                    <a href="#" class="block text-[13px] text-slate-500 hover:text-slate-800 py-1.5 transition-colors">Alokasi Sesi</a>
                </div>
            </div>

            <!-- Manajemen CBT & Soal -->
            <a href="#" class="flex items-center gap-3 py-2.5 px-4 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition-all mt-1">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-[13px]">Manajemen CBT & Soal</span>
            </a>

            <!-- Monitoring Ujian (with Submenu) -->
            <div x-data="{ open: false }" class="space-y-1 mt-1">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition-all group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-[13px] text-left">Monitoring Ujian</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="#" class="block text-[13px] text-slate-500 hover:text-slate-800 py-1 transition-colors">Aktivasi Sesi</a>
                    <a href="#" class="block text-[13px] text-slate-500 hover:text-slate-800 py-1 transition-colors">Pantau Status</a>
                </div>
            </div>

            <!-- Hasil & Analitik (with Submenu) -->
            <div x-data="{ open: false }" class="space-y-1 mt-1">
                <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition-all group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-[13px] text-left">Hasil & Analitik</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="#" class="block text-[13px] text-slate-500 hover:text-slate-800 py-1 transition-colors">Data Kelulusan</a>
                    <a href="#" class="block text-[13px] text-slate-500 hover:text-slate-800 py-1 transition-colors">Laporan CPL</a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Footer Section -->
    <div class="px-6 py-4 bg-white">
        <div class="border-t border-slate-100 pt-6"></div>
        
        <!-- Profile Row -->
        <div class="flex items-center gap-4 mb-4">
            <!-- Icon Background -->
            <div class="w-10 h-10 bg-[#CBD5E1] rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <!-- Details -->
            <span class="font-medium text-[14px] text-slate-700 truncate">{{ auth()->user()->name ?? 'Admin Name' }}</span>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-4 text-red-500 hover:text-red-600 font-medium transition-colors text-left text-[14px]">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
