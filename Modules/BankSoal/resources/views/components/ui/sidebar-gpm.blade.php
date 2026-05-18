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

                    <!-- Manajemen Modul (Accordion) -->
                    @php $isManajemenModulActive = request()->routeIs('banksoal.rps.gpm.validasi-rps*') || request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*') || request()->routeIs('banksoal.soal.gpm.parameter*'); @endphp
                    <div x-data="{ open: {{ $isManajemenModulActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true } else { open = !open }"
                            class="group w-full relative flex items-center justify-between py-2.5 px-4 rounded-xl transition-all {{ $isManajemenModulActive ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            @if($isManajemenModulActive)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                            @endif
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ $isManajemenModulActive ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                                    fill="{{ $isManajemenModulActive ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Manajemen Modul</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $isManajemenModulActive ? 'text-slate-900' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open && sidebarOpen" x-cloak class="pl-12 pr-4 py-1 space-y-1">
                            <a href="{{ route('banksoal.soal.gpm.parameter.index') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.soal.gpm.parameter*') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Kontrol Umum</a>
                            <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.rps.gpm.validasi-rps*') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Validasi RPS</a>
                            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Validasi Soal</a>
                        </div>
                    </div>

                    <!-- Riwayat Validasi -->
                    @php 
                        $isRiwayatRps = request()->routeIs('banksoal.rps.gpm.riwayat-validasi.*'); 
                        $isRiwayatSoal = request()->routeIs('banksoal.soal.gpm.riwayat-validasi*');
                        $isRiwayat = request()->routeIs('banksoal.rps.gpm.index') || $isRiwayatRps || $isRiwayatSoal; 
                    @endphp
                    <div x-data="{ isRiwayatOpen: {{ $isRiwayat ? 'true' : 'false' }} }">
                        <button @click="if(sidebarOpen) { isRiwayatOpen = !isRiwayatOpen } else { sidebarOpen = true; isRiwayatOpen = true }"
                            class="w-full group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isRiwayat ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            @if($isRiwayat)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                            @endif
                            <svg class="w-5 h-5 flex-shrink-0 {{ $isRiwayat ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                                fill="{{ $isRiwayat ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-show="sidebarOpen" class="flex-1 text-left text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Riwayat Validasi</span>
                            <svg x-show="sidebarOpen" :class="isRiwayatOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200 {{ $isRiwayat ? 'text-slate-900' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Submenu -->
                        <div x-show="isRiwayatOpen && sidebarOpen" x-collapse x-transition.duration.200ms class="mt-1 space-y-1 pl-11">
                            <a href="{{ route('banksoal.rps.gpm.riwayat-validasi.rps') }}"
                                class="block py-2 px-3 text-sm font-medium rounded-lg transition-all {{ $isRiwayatRps ? 'text-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                                Riwayat Validasi RPS
                            </a>
                            <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}"
                                class="block py-2 px-3 text-sm font-medium rounded-lg transition-all {{ $isRiwayatSoal ? 'text-primary bg-primary/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                                Riwayat Validasi Bank Soal
                            </a>
                        </div>
                    </div>

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
