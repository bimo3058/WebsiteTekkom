<aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter transition-all duration-300 relative z-20">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4 overflow-x-hidden">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Icon Background -->
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-primary/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <!-- Title Content -->
                <div x-show="sidebarOpen" class="flex flex-col whitespace-nowrap overflow-hidden" x-transition.opacity.duration.300ms>
                    <span class="font-bold text-slate-900 text-base leading-tight tracking-tight uppercase">SIBASKOM</span>
                    <span class="font-semibold text-slate-500 text-[10px] leading-tight tracking-wider uppercase opacity-80 truncate" style="max-width:170px">Sistem Informasi Bank Soal Teknik Komputer</span>
                </div>
            </div>
            <!-- Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-10 bg-white border border-slate-200 text-slate-400 hover:text-primary rounded-lg p-1 shadow-sm transition-colors z-30">
                <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-6">
            <!-- MAIN MENU GROUP -->
            <div>
                <p x-show="sidebarOpen" class="px-4 mb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-widest whitespace-nowrap" x-transition.opacity.duration.300ms>Main Menu</p>
                <div class="space-y-1">

                    <!-- Dashboard -->
                    @php $isDashboard = request()->routeIs('banksoal.dashboard') && auth()->user()->hasRole('gpm'); @endphp
                    <a href="{{ route('banksoal.dashboard') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isDashboard ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isDashboard)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isDashboard ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                             fill="{{ $isDashboard ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2"/>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Dashboard</span>
                    </a>

                    <!-- Validasi RPS -->
                    @php $isValidasiRps = request()->routeIs('banksoal.rps.gpm.validasi-rps*'); @endphp
                    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isValidasiRps ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isValidasiRps)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isValidasiRps ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                             fill="{{ $isValidasiRps ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Validasi RPS</span>
                    </a>

                    <!-- Validasi Bank Soal -->
                    @php $isValidasiSoal = request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*'); @endphp
                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isValidasiSoal ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isValidasiSoal)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isValidasiSoal ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                             fill="{{ $isValidasiSoal ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-6.586a1 1 0 01-.707-.293l-3.414-3.414A2 2 0 008.586 2H6a2 2 0 00-2 2v3z"/>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Validasi Bank Soal</span>
                    </a>

                    <!-- Riwayat Validasi -->
                    @php $isRiwayat = request()->routeIs('banksoal.rps.gpm.index') || request()->routeIs('banksoal.rps.gpm.riwayat-validasi.*') || request()->routeIs('banksoal.soal.gpm.riwayat-validasi*'); @endphp
                    <a href="{{ route('banksoal.rps.gpm.index') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isRiwayat ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isRiwayat)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isRiwayat ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                             fill="{{ $isRiwayat ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Riwayat Validasi</span>
                    </a>

                </div>
            </div>
        </nav>
    </div>

    <!-- Bottom: Settings & Logout -->
    <div class="p-4 mt-auto">
        <div class="bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center">
            <a href="{{ route('profile.edit') }}" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 rounded-t-xl transition-all border-b border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">Settings</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-b-xl transition-all">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>

</aside>
