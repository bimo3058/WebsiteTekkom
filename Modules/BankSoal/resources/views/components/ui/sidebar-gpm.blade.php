@php
    $iconDashboard = 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10';
    $iconSettings = 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z';
    $iconHelp = 'M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01';
    $iconLogout = 'M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9';
    $iconManajemen = 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z';
    $iconRiwayat = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'; 
@endphp

<aside :class="sidebarOpen ? 'w-[240px]' : 'w-[64px]'"
       class="relative h-screen bg-white border-r border-[#DFE1E7] flex flex-col flex-shrink-0 transition-all duration-200 ease-in-out z-20 font-sans"
       style="font-family: 'Inter Tight', system-ui, sans-serif;">
    
    {{-- Brand + Collapse Button --}}
    <div class="flex items-center gap-2 px-[14px] py-[14px] border-b border-[#DFE1E7] min-h-[60px] flex-shrink-0 relative" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
        {{-- Logo --}}
        <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center overflow-hidden">
            <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP" style="width:32px;height:32px;object-fit:contain;">
        </div>

<<<<<<< HEAD
        {{-- Brand text --}}
        <div x-show="sidebarOpen" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex-1 min-w-0">
            <div class="font-bold text-[14px] text-[#0D0D12] leading-[1.2] whitespace-nowrap overflow-hidden text-ellipsis" style="font-family:'Geist', 'Inter Tight', sans-serif; letter-spacing:-.01em;">SIBASO</div>
            <div class="font-medium text-[9px] text-[#808897] mt-[2px] whitespace-nowrap overflow-hidden text-ellipsis">Sistem Informasi Bank Soal</div>
=======
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
                    <span class="font-semibold text-slate-500 text-[10px] leading-tight tracking-wider uppercase opacity-80 truncate" style="max-width:170px">Sistem Informasi Bank Soal Teknik Komputer</span>
                </div>
            </div>
            <!-- Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-10 bg-white border border-slate-200 text-slate-400 hover:text-primary rounded-lg p-1 shadow-sm transition-colors z-30">
                <svg :class="sidebarOpen ? '' : 'rotate-180'" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
>>>>>>> 7434167313ec190790457a15aace01fc6a498f07
        </div>

        {{-- Collapse button --}}
        <button @click="sidebarOpen = !sidebarOpen" 
                class="absolute right-3 w-[28px] h-[28px] rounded-[7px] border border-[#DFE1E7] bg-white flex items-center justify-center text-[#666D80] hover:bg-[#F6F8FA] hover:border-[#C1C7CF] hover:text-[#0D0D12] transition-all flex-shrink-0"
                :class="sidebarOpen ? '' : 'relative right-0'">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200 ease-in-out" :class="sidebarOpen ? '' : 'rotate-180'">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
    </div>

    {{-- Nav Container --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden p-[6px_10px_10px] flex flex-col gap-[1px] scrollbar-thin scrollbar-thumb-[#DFE1E7] scrollbar-track-transparent">

        {{-- MAIN MENU --}}
        <div x-show="sidebarOpen" class="text-[10px] font-semibold text-[#808897] tracking-[0.06em] uppercase p-[12px_10px_5px] whitespace-nowrap">Main Menu</div>

        @php $isDashboard = request()->routeIs('banksoal.dashboard') && auth()->user()->hasRole('gpm'); @endphp
        <a href="{{ route('banksoal.dashboard') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isDashboard ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isDashboard)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isDashboard ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconDashboard }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Dashboard</span>
        </a>

        {{-- Manajemen Modul Accordion --}}
        @php 
            $isManajemenModulActive = request()->routeIs('banksoal.rps.gpm.validasi-rps*') || request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*') || request()->routeIs('banksoal.soal.gpm.parameter*');
            $isKontrolUmum = request()->routeIs('banksoal.soal.gpm.parameter*');
            $isValidasiRps = request()->routeIs('banksoal.rps.gpm.validasi-rps*');
            $isValidasiSoal = request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*');
        @endphp
        <div x-data="{ openAcc: {{ $isManajemenModulActive ? 'true' : 'false' }} }" class="flex flex-col mt-[1px]">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isManajemenModulActive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isManajemenModulActive)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isManajemenModulActive ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconManajemen }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Manajemen Modul</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.soal.gpm.parameter.index') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isKontrolUmum ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Kontrol Umum</a>
                    <a href="{{ route('banksoal.rps.gpm.validasi-rps') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isValidasiRps ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Validasi RPS</a>
                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isValidasiSoal ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Validasi Soal</a>
                </div>
            </div>
<<<<<<< HEAD
        </div>

        {{-- Riwayat Validasi Accordion --}}
        @php 
            $isRiwayatRps = request()->routeIs('banksoal.rps.gpm.riwayat-validasi.*'); 
            $isRiwayatSoal = request()->routeIs('banksoal.soal.gpm.riwayat-validasi*');
            $isRiwayat = request()->routeIs('banksoal.rps.gpm.index') || $isRiwayatRps || $isRiwayatSoal; 
        @endphp
        <div x-data="{ openAcc: {{ $isRiwayat ? 'true' : 'false' }} }" class="flex flex-col mt-[1px]">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isRiwayat ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isRiwayat)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isRiwayat ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconRiwayat }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Riwayat Validasi</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.rps.gpm.riwayat-validasi.rps') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isRiwayatRps ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Riwayat Validasi RPS</a>
                    <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isRiwayatSoal ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Riwayat Validasi Bank Soal</a>
                </div>
            </div>
=======

            <!-- Dosen Pengampu (Role Switcher) -->
            @if(auth()->user()->hasRole('dosen'))
                <div>
                    <p x-show="sidebarOpen" class="px-4 mb-3 text-[11px] font-semibold text-slate-400 uppercase tracking-widest whitespace-nowrap" x-transition.opacity.duration.300ms>Dosen Pengampu</p>
                    <div class="space-y-1">
                        <a href="{{ route('banksoal.switch-role', 'dosen') }}"
                            class="group relative flex items-center gap-3 py-2.5 px-4 rounded-xl transition-all text-slate-500 hover:bg-slate-50 hover:text-slate-700">
                            <svg class="w-5 h-5 flex-shrink-0 text-slate-400 group-hover:text-slate-500"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap" x-transition.opacity.duration.300ms>Akses Dosen</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </div>

    <!-- Bottom: Home, Settings & Logout -->
    <div class="p-4 mt-auto">
        <div class="bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center">
            <a href="{{ route('dashboard') }}" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 rounded-t-xl transition-all border-b border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" class="text-xs font-bold uppercase tracking-wider whitespace-nowrap">HOME</span>
            </a>

            <a href="{{ route('profile.edit') }}" class="group w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all border-b border-slate-200">
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
>>>>>>> 7434167313ec190790457a15aace01fc6a498f07
        </div>

    </div>

    {{-- Bottom Nav --}}
    <div class="p-[8px_10px_12px] border-t border-[#DFE1E7] flex flex-col gap-[1px] flex-shrink-0">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-[10px] p-[8px_10px] rounded-lg text-[13px] font-medium text-[#353849] hover:bg-[#F6F8FA] transition-colors {{ request()->routeIs('profile.edit') ? 'bg-[#F6F8FA]' : '' }}" :class="!sidebarOpen ? 'justify-center p-[8px_0]' : ''">
            <svg class="w-4 h-4 flex-shrink-0 text-[#666D80]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconSettings }}"/></svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap overflow-hidden text-ellipsis">Settings</span>
        </a>
        <a href="#" class="flex items-center gap-[10px] p-[8px_10px] rounded-lg text-[13px] font-medium text-[#353849] hover:bg-[#F6F8FA] transition-colors" :class="!sidebarOpen ? 'justify-center p-[8px_0]' : ''">
            <svg class="w-4 h-4 flex-shrink-0 text-[#666D80]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconHelp }}"/></svg>
            <span x-show="sidebarOpen" class="whitespace-nowrap overflow-hidden text-ellipsis">Help &amp; Center</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0" data-no-loader>
            @csrf
            <button type="submit" class="w-full flex items-center gap-[10px] p-[8px_10px] rounded-lg text-[13px] font-medium text-[#DF1C41] hover:bg-[#FEF1F4] transition-colors" :class="!sidebarOpen ? 'justify-center p-[8px_0]' : ''">
                <svg class="w-4 h-4 flex-shrink-0 text-[#DF1C41]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconLogout }}"/></svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap overflow-hidden text-ellipsis text-left">Logout</span>
            </button>
        </form>
    </div>

</aside>
