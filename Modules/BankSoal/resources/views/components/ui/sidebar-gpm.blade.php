<aside class="w-72 h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center gap-4">
            <!-- Icon Background -->
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-primary/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <!-- Title Content -->
            <div class="flex flex-col">
                <span class="font-bold text-slate-900 text-base leading-tight tracking-tight uppercase">GPM</span>
                <span class="font-semibold text-slate-500 text-[11px] leading-tight tracking-wider uppercase opacity-80">Portal Penjaminan Mutu</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-6">
            
            <!-- MAIN MENU GROUP -->
            <div>
                <p class="px-4 mb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-widest">Main Menu</p>
                <div class="space-y-1">
                    
                    <!-- Dashboard -->
                    @php $isDashboard = request()->routeIs('banksoal.dashboard') && auth()->user()->hasRole('gpm'); @endphp
                    <a href="{{ route('banksoal.dashboard') }}" 
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isDashboard ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isDashboard)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isDashboard ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isDashboard ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2"/>
                            <rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2"/>
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <!-- Validasi RPS -->
                    @php $isValidasiRps = request()->routeIs('banksoal.rps.gpm.validasi-rps*'); @endphp
                    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" 
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isValidasiRps ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isValidasiRps)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isValidasiRps ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isValidasiRps ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium">Validasi RPS</span>
                    </a>

                    <!-- Validasi Bank Soal -->
                    @php $isValidasiSoal = request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*'); @endphp
                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" 
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isValidasiSoal ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isValidasiSoal)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isValidasiSoal ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isValidasiSoal ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-6.586a1 1 0 01-.707-.293l-3.414-3.414A2 2 0 008.586 2H6a2 2 0 00-2 2v3z"></path>
                        </svg>
                        <span class="text-sm font-medium">Validasi Bank Soal</span>
                    </a>

                    <!-- Riwayat Validasi -->
                    @php $isRiwayat = request()->routeIs('banksoal.rps.gpm.index') || request()->routeIs('banksoal.rps.gpm.riwayat-validasi.*') || request()->routeIs('banksoal.soal.gpm.riwayat-validasi*'); @endphp
                    <a href="{{ route('banksoal.rps.gpm.index') }}" 
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isRiwayat ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isRiwayat)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isRiwayat ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isRiwayat ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium">Riwayat Validasi</span>
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Bottom Profile Area -->
    <div class="p-4 mt-auto">
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
            <!-- Profile Info -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <span class="text-primary font-bold text-sm">{{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name ?? 'GPM User' }}</span>
                    <span class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">Penjaminan Mutu</span>
                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" id="logoutForm" class="w-full">
                @csrf
                <button type="submit" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-rose-100 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all shadow-sm shadow-rose-100/50">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

