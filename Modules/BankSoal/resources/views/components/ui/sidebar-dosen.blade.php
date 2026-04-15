<aside class="w-72 h-full bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 font-inter">

    <!-- Top Section: Header & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 pb-4">

        <!-- Header -->
        <div class="px-6 py-8 flex items-center gap-4 border-b border-transparent">
            <!-- Icon Background -->
            <div class="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                </svg>
            </div>
            <!-- Title Content -->
            <div class="flex flex-col">
                <span class="font-bold text-slate-900 text-[15px] leading-tight tracking-tight uppercase">Bank</span>
                <span class="font-bold text-slate-900 text-[15px] leading-tight tracking-tight uppercase">Soal</span>
            </div>
        </div>

        <div class="px-6 mb-4 mt-2">
            <div class="h-px w-full bg-slate-100"></div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('banksoal.dashboard') }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.dashboard') && auth()->user()->hasRole('dosen') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2"/><rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2"/><rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2"/><rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2"/>
                </svg>
                <span class="text-[13px]">Dashboard</span>
            </a>

            <!-- Manajemen RPS -->
            <a href="{{ route('banksoal.rps.dosen.index') }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.rps.dosen.*') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="text-[13px]">Manajemen RPS</span>
            </a>

            <!-- Bank Soal -->
            <a href="{{ route('banksoal.soal.dosen.index') }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.soal.dosen.*') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all mt-1">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-6.586a1 1 0 01-.707-.293l-3.414-3.414A2 2 0 008.586 2H6a2 2 0 00-2 2v3z"></path>
                </svg>
                <span class="text-[13px]">Bank Soal</span>
            </a>

            <!-- Arsip Soal -->
            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl {{ request()->routeIs('banksoal.arsip.dosen.*') ? 'bg-[#EBF4FF] text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }} transition-all mt-1">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <span class="text-[13px]">Arsip Soal</span>
            </a>

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
            <span class="font-medium text-[14px] text-slate-700 truncate">{{ auth()->user()->name ?? 'Dosen' }}</span>
        </div>

        <!-- Logout Button -->
        <button 
            onclick="document.getElementById('logoutForm').submit()" 
            class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:text-red-700 hover:bg-red-50 font-medium transition-colors rounded-lg text-[14px] cursor-pointer"
        >
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Logout</span>
        </button>

        <!-- Hidden Logout Form -->
        <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
    </div>
</aside>
