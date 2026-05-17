<aside :class="sidebarOpen ? 'w-72' : 'w-20'" class="h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter transition-all duration-300 relative z-20">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4 overflow-x-hidden">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Icon Background -->
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-primary/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                    </svg>
                </div>
                <!-- Title Content -->
                <div x-show="sidebarOpen" class="flex flex-col whitespace-nowrap overflow-hidden" x-transition.opacity.duration.300ms>
                    <span class="font-bold text-slate-900 text-base leading-tight tracking-tight uppercase">Portal Mahasiswa</span>
                    <span class="font-semibold text-slate-500 text-[10px] leading-tight tracking-wider uppercase opacity-80 truncate" style="max-width: 170px;">S1 Teknik Komputer</span>
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
                <p x-show="sidebarOpen" class="px-4 mb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-widest whitespace-nowrap" x-transition.opacity.duration.300ms>Ujian Komprehensif</p>
                <div class="space-y-1">
                    
                    <!-- Portal Ujian -->
                    @php $isPortalUjian = request()->routeIs('komprehensif.mahasiswa.dashboard'); @endphp
                    <a href="{{ route('komprehensif.mahasiswa.dashboard') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isPortalUjian ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isPortalUjian)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isPortalUjian ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isPortalUjian ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Portal Ujian</span>
                    </a>

                    <!-- Riwayat Ujian -->
                    @php $isRiwayat = request()->routeIs('komprehensif.mahasiswa.riwayat'); @endphp
                    <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                        class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all {{ $isRiwayat ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        @if($isRiwayat)
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1.5 bg-primary rounded-r-full"></div>
                        @endif
                        <svg class="w-5 h-5 flex-shrink-0 {{ $isRiwayat ? 'text-primary' : 'text-slate-400 group-hover:text-slate-500' }}" 
                             fill="{{ $isRiwayat ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Riwayat Ujian</span>
                    </a>

                </div>
            </div>

        </nav>
    </div>

    <!-- Bottom Profile Area -->
    <div class="p-4 mt-auto">
        
        <div class="bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center">
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-xl transition-all">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
