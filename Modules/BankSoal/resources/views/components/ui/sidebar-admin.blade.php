<aside class="w-72 h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center gap-4 border-b border-transparent">
            <!-- Icon Background -->
            <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
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
            <a href="{{ route('banksoal.dashboard') }}"
                class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.dashboard') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2" />
                    <rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2" />
                    <rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2" />
                    <rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2" />
                </svg>
                <span class="text-[13px]">Dashboard</span>
            </a>

            @php
                $isKontrolUmumActive = request()->routeIs('banksoal.admin.kontrol-umum.*');
            @endphp
            <!-- Kontrol Umum (Akordion) -->
            <div x-data="{ open: {{ $isKontrolUmumActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl hover:bg-slate-50 font-medium transition-all group {{ $isKontrolUmumActive ? 'bg-slate-50' : 'text-slate-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolUmumActive ? 'text-blue-600' : 'text-slate-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <span
                            class="text-[13px] text-left {{ $isKontrolUmumActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Kontrol
                            Umum</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200 {{ $isKontrolUmumActive ? 'text-blue-600' : 'text-slate-400' }}"
                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}"
                        class="block text-[13px] {{ request()->routeIs('banksoal.admin.kontrol-umum.mata-kuliah') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen
                        Mata Kuliah</a>
                    <a href="{{ route('banksoal.admin.kontrol-umum.cpl-cpmk') }}"
                        class="block text-[13px] {{ request()->routeIs('banksoal.admin.kontrol-umum.cpl-cpmk') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen
                        CPL & CPMK</a>
                    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}"
                        class="block text-[13px] {{ request()->routeIs('banksoal.admin.kontrol-umum.pemetaan') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Pemetaan</a>
                </div>
            </div>

            @php
                $isKontrolBankSoalActive = request()->routeIs('banksoal.admin.kontrol-banksoal.*');
            @endphp
            <!-- Kontrol BankSoal (Akordion) -->
            <div x-data="{ open: {{ $isKontrolBankSoalActive ? 'true' : 'false' }} }" class="space-y-1 mt-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl hover:bg-slate-50 font-medium transition-all group {{ $isKontrolBankSoalActive ? 'bg-slate-50' : 'text-slate-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolBankSoalActive ? 'text-blue-600' : 'text-slate-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span
                            class="text-[13px] text-left {{ $isKontrolBankSoalActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Kontrol
                            BankSoal</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200 {{ $isKontrolBankSoalActive ? 'text-blue-600' : 'text-slate-400' }}"
                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-11 pr-4 py-1 space-y-2">
                    <a href="{{ route('banksoal.admin.kontrol-banksoal.rps') }}"
                        class="block text-[13px] {{ request()->routeIs('banksoal.admin.kontrol-banksoal.rps') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen
                        RPS</a>
                    <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}"
                        class="block text-[13px] {{ request()->routeIs('banksoal.admin.kontrol-banksoal.soal') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1.5 transition-colors">Manajemen
                        Soal</a>
                </div>
            </div>

            @php
                $isKontrolUjianActive = request()->routeIs('banksoal.periode.*') || request()->routeIs('banksoal.pendaftaran.*') || request()->routeIs('banksoal.aktivasi.*');
            @endphp
            <!-- Kontrol Ujian Komprehensif (Akordion Utama) -->
            <div x-data="{ open: {{ $isKontrolUjianActive ? 'true' : 'false' }} }" class="space-y-1 mt-1">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl hover:bg-slate-50 font-medium transition-all group {{ $isKontrolUjianActive ? 'bg-slate-50' : 'text-slate-600' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isKontrolUjianActive ? 'text-blue-600' : 'text-slate-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span
                            class="text-[13px] text-left {{ $isKontrolUjianActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Kontrol
                            Ujian<br>Komprehensif</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200 {{ $isKontrolUjianActive ? 'text-blue-600' : 'text-slate-400' }}"
                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="pl-10 pr-4 py-2 space-y-2 bg-slate-50/50 rounded-lg ml-2 mb-1">
                    @php
                        $isPeriodeActive = request()->routeIs('banksoal.periode.*') || request()->routeIs('banksoal.pendaftaran.*') || request()->routeIs('banksoal.pendaftaran.alokasi-sesi.*');
                    @endphp
                    <!-- Manajemen Ujian & Peserta (Sub-Akordion) -->
                    <div x-data="{ open: {{ $isPeriodeActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between py-1.5 px-3 rounded-lg text-slate-600 hover:bg-slate-100/50 font-medium transition-all group text-[12px]">
                            <span
                                class="text-left {{ $isPeriodeActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Manajemen
                                Ujian & Peserta</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isPeriodeActive ? 'text-blue-600' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-7 pr-2 py-1 space-y-1">
                            <a href="{{ route('banksoal.periode.setup') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.periode.setup') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Setup
                                Periode</a>
                            <a href="{{ route('banksoal.pendaftaran.alokasi-sesi.index') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.pendaftaran.alokasi-sesi.*') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Jadwal
                                & Sesi</a>
                            <a href="{{ route('banksoal.pendaftaran.index') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.pendaftaran.index') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Daftar
                                Peserta</a>
                        </div>
                    </div>



                    @php
                        $isMonitoringActive = request()->routeIs('banksoal.aktivasi.*') || request()->routeIs('banksoal.admin.cbt.live-proctoring');
                    @endphp
                    <!-- Monitoring Ujian (Sub-Akordion) -->
                    <div x-data="{ open: {{ $isMonitoringActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between py-1.5 px-3 rounded-lg text-slate-600 hover:bg-slate-100/50 font-medium transition-all group text-[12px]">
                            <span
                                class="text-left {{ $isMonitoringActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Monitoring
                                Ujian</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isMonitoringActive ? 'text-blue-600' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-7 pr-2 py-1 space-y-1">
                            <a href="{{ route('banksoal.aktivasi.index') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.aktivasi.index') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Aktivasi
                                Sesi</a>
                            <a href="{{ route('banksoal.admin.cbt.live-proctoring') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.admin.cbt.live-proctoring') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Live Proctoring</a>
                        </div>
                    </div>

                    @php
                        $isHasilActive = request()->routeIs('banksoal.admin.cbt.riwayat') || request()->routeIs('banksoal.admin.cbt.detail');
                    @endphp
                    <!-- Hasil & Analitik (Sub-Akordion) -->
                    <div x-data="{ open: {{ $isHasilActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between py-1.5 px-3 rounded-lg text-slate-600 hover:bg-slate-100/50 font-medium transition-all group text-[12px]">
                            <span class="text-left {{ $isHasilActive ? 'text-blue-600 font-semibold' : 'text-slate-600' }}">Hasil & Analitik</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 {{ $isHasilActive ? 'text-blue-600' : 'text-slate-400' }}"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-7 pr-2 py-1 space-y-1">
                            <a href="{{ route('banksoal.admin.cbt.riwayat') }}"
                                class="block text-[11px] {{ request()->routeIs('banksoal.admin.cbt.riwayat') || request()->routeIs('banksoal.admin.cbt.detail') ? 'text-blue-600 font-semibold' : 'text-slate-500 hover:text-slate-800' }} py-1 transition-colors">Riwayat Ujian</a>
                            <a href="#"
                                class="block text-[11px] text-slate-500 hover:text-slate-800 py-1 transition-colors">Laporan
                                CPL</a>
                        </div>
                    </div>
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
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <!-- Details -->
            <span
                class="font-medium text-[14px] text-slate-700 truncate">{{ auth()->user()->name ?? 'Admin Name' }}</span>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-4 text-red-500 hover:text-red-600 font-medium transition-colors text-left text-[14px]">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>