<aside class="w-64 flex-shrink-0 bg-white border-r border-slate-200 flex flex-col h-full font-inter">
    <!-- Logo Area -->
    <div class="h-20 flex items-center px-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-slate-800 leading-none">Portal Mahasiswa</span>
                <span class="text-[10px] text-slate-500 font-medium tracking-wide mt-1 uppercase">S1 Teknik Komputer</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        @php
            $currentRoute = request()->route() ? request()->route()->getName() : '';
        @endphp

        <!-- Beranda -->
        <a href="{{ route('komprehensif.mahasiswa.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ $currentRoute === 'komprehensif.mahasiswa.dashboard' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-sm">Beranda</span>
        </a>

        <!-- Pengajuan Pendaftaran -->
        <a href="{{ route('komprehensif.mahasiswa.pendaftaran') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ $currentRoute === 'komprehensif.mahasiswa.pendaftaran' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm">Pengajuan Pendaftaran</span>
        </a>

        <!-- Riwayat Ujian -->
        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-colors {{ $currentRoute === 'komprehensif.mahasiswa.riwayat' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm">Riwayat Ujian</span>
        </a>
    </nav>

    <!-- Bottom Profile Area -->
    <div class="p-4 mt-auto">
        <div class="border-t border-slate-200 pt-4 flex flex-col gap-4">
            <!-- Profile Info -->
            <div class="flex items-center gap-3 px-2">
                <!-- Avatar placeholder similar to image -->
                <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=047857&color=fff" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <!-- Profile details -->
                <div class="flex flex-col min-w-0">
                    <span class="text-sm font-semibold text-slate-800 truncate">Budi Santoso</span>
                    <span class="text-[10px] text-slate-500 font-medium">21060119120001</span>
                </div>
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-2 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors font-semibold text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
