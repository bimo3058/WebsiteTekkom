<aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter transition-all duration-300 relative z-20">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4 overflow-x-hidden">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Icon Background -->
                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/logo-undip.png') }}" alt="UNDIP Logo" class="w-10 h-10 object-contain">
                </div>
                <!-- Title Content -->
                <div x-show="sidebarOpen" class="flex flex-col whitespace-nowrap overflow-hidden" x-transition.opacity.duration.300ms>
                    <span class="font-bold text-slate-900 text-base leading-tight tracking-tight uppercase">SIBASKOM</span>
                    <span class="font-semibold text-slate-500 text-[10px] leading-tight tracking-wider uppercase opacity-80 truncate" style="max-width: 170px;">Sistem Informasi Bank Soal Teknik Komputer</span>
                </div>
            </div>
            <!-- Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-10 bg-white border border-slate-200 text-slate-400 hover:text-primary rounded-lg p-1 shadow-sm transition-colors z-30">
                <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
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
                    @php $isDashboard = request()->routeIs('banksoal.dashboard'); @endphp
                    <a href="{{ route('banksoal.dashboard') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isDashboard ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isDashboard)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isDashboard ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isDashboard ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2" />
                            <rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2" />
                            <rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2" />
                            <rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2" />
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Dashboard</span>
                    </a>

                    <!-- Kontrol Umum (Accordion) -->
                    @php $isKontrolUmumActive = request()->routeIs('banksoal.admin.kontrol-umum.*'); @endphp
                    <div x-data="{ open: {{ $isKontrolUmumActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true } else { open = !open }"
                            class="group w-full relative flex items-center justify-between py-2.5 px-4 rounded-xl transition-all {{ $isKontrolUmumActive ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            @if($isKontrolUmumActive)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                            @endif
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolUmumActive ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                                    fill="{{ $isKontrolUmumActive ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Kontrol Umum</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $isKontrolUmumActive ? 'text-slate-900' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open && sidebarOpen" x-cloak class="pl-12 pr-4 py-1 space-y-1">
                            <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.admin.kontrol-umum.mata-kuliah') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen Data</a>

                            <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.admin.kontrol-umum.pemetaan') || request()->routeIs('banksoal.admin.kontrol-umum.pemetaan.*') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Pemetaan</a>
                        </div>
                    </div>

                    <!-- Kontrol BankSoal (Accordion) -->
                    @php $isKontrolBankSoalActive = request()->routeIs('banksoal.admin.kontrol-banksoal.*'); @endphp
                    <div x-data="{ open: {{ $isKontrolBankSoalActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true } else { open = !open }"
                            class="group w-full relative flex items-center justify-between py-2.5 px-4 rounded-xl transition-all {{ $isKontrolBankSoalActive ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            @if($isKontrolBankSoalActive)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                            @endif
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolBankSoalActive ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                                    fill="{{ $isKontrolBankSoalActive ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Bank Soal</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $isKontrolBankSoalActive ? 'text-slate-900' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open && sidebarOpen" x-cloak class="pl-12 pr-4 py-1 space-y-1">
                            <a href="{{ route('banksoal.admin.kontrol-banksoal.rps') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.admin.kontrol-banksoal.rps') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen RPS</a>
                            <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}"
                                class="block text-sm {{ request()->routeIs('banksoal.admin.kontrol-banksoal.soal') ? 'text-primary font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen Soal</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EXAM SYSTEM GROUP -->
            <div>
                <p x-show="sidebarOpen" class="px-4 mb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-widest whitespace-nowrap" x-transition.opacity.duration.300ms>Ujian Komprehensif</p>
                <div class="space-y-1">
                    @php $isKontrolUjianActive = request()->routeIs('banksoal.periode.*') || request()->routeIs('banksoal.pendaftaran.*') || request()->routeIs('banksoal.aktivasi.*'); @endphp
                    <div x-data="{ open: {{ $isKontrolUjianActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="if (!sidebarOpen) { sidebarOpen = true; open = true } else { open = !open }"
                            class="group w-full relative flex items-center justify-between py-2.5 px-4 rounded-xl transition-all {{ $isKontrolUjianActive ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            @if($isKontrolUjianActive)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                            @endif
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolUjianActive ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}"
                                    fill="{{ $isKontrolUjianActive ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Sistem Ujian</span>
                            </div>
                            <svg x-show="sidebarOpen" class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $isKontrolUjianActive ? 'text-slate-900' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open && sidebarOpen" x-cloak class="pl-12 pr-4 py-2 space-y-3 bg-slate-50/50 rounded-xl mx-2 mb-2">
                            <!-- Manajemen Ujian & Peserta (Sub-Accordion) -->
                            @php $isPeriodeActive = request()->routeIs('banksoal.periode.*') || request()->routeIs('banksoal.pendaftaran.*'); @endphp
                            <div x-data="{ subOpen: {{ $isPeriodeActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between text-xs font-semibold {{ $isPeriodeActive ? 'text-primary' : 'text-slate-500 hover:text-slate-700' }}">
                                    <span>Manajemen Peserta</span>
                                    <svg class="w-3 h-3 transition-transform" :class="subOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div x-show="subOpen" class="pl-2 space-y-1.5 mt-1 border-l border-slate-200">
                                    <a href="{{ route('banksoal.periode.setup') }}" class="block text-[12px] {{ request()->routeIs('banksoal.periode.setup') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Setup Periode</a>
                                    <a href="{{ route('banksoal.pendaftaran.alokasi-sesi.index') }}" class="block text-[12px] {{ request()->routeIs('banksoal.pendaftaran.alokasi-sesi.*') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Jadwal & Sesi</a>
                                    <a href="{{ route('banksoal.pendaftaran.index') }}" class="block text-[12px] {{ request()->routeIs('banksoal.pendaftaran.index') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Daftar Peserta</a>
                                </div>
                            </div>

                            <!-- Monitoring (Sub-Accordion) -->
                            @php $isMonitoringActive = request()->routeIs('banksoal.aktivasi.*') || request()->routeIs('banksoal.admin.cbt.live-proctoring'); @endphp
                            <div x-data="{ subOpen: {{ $isMonitoringActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between text-xs font-semibold {{ $isMonitoringActive ? 'text-primary' : 'text-slate-500 hover:text-slate-700' }}">
                                    <span>Monitoring Ujian</span>
                                    <svg class="w-3 h-3 transition-transform" :class="subOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div x-show="subOpen" class="pl-2 space-y-1.5 mt-1 border-l border-slate-200">
                                    <a href="{{ route('banksoal.aktivasi.index') }}" class="block text-[12px] {{ request()->routeIs('banksoal.aktivasi.index') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Aktivasi Sesi</a>
                                    <a href="{{ route('banksoal.admin.cbt.live-proctoring') }}" class="block text-[12px] {{ request()->routeIs('banksoal.admin.cbt.live-proctoring') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Live Pengawasan</a>
                                </div>
                            </div>

                            <!-- Hasil (Sub-Accordion) -->
                            @php $isHasilActive = request()->routeIs('banksoal.admin.cbt.riwayat') || request()->routeIs('banksoal.admin.cbt.analitik'); @endphp
                            <div x-data="{ subOpen: {{ $isHasilActive ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between text-xs font-semibold {{ $isHasilActive ? 'text-primary' : 'text-slate-500 hover:text-slate-700' }}">
                                    <span>Hasil & Analitik</span>
                                    <svg class="w-3 h-3 transition-transform" :class="subOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div x-show="subOpen" class="pl-2 space-y-1.5 mt-1 border-l border-slate-200">
                                    <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="block text-[12px] {{ request()->routeIs('banksoal.admin.cbt.riwayat') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Riwayat Ujian</a>
                                    <a href="{{ route('banksoal.admin.cbt.analitik') }}" class="block text-[12px] {{ request()->routeIs('banksoal.admin.cbt.analitik') ? 'text-primary font-bold' : 'text-slate-500 hover:text-slate-800' }}">Dasbor Analitik</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Bottom Profile Area -->
    <div class="p-4 mt-auto">
        <div class="bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center">
            
            @if(auth()->user()->hasRole('superadmin'))
                <a href="{{ route('superadmin.dashboard') }}" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 rounded-t-xl transition-all border-b border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">HOME</span>
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 {{ auth()->user()->hasRole('superadmin') ? '' : 'rounded-t-xl' }} transition-all border-b border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">Settings</span>
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-b-xl transition-all">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>