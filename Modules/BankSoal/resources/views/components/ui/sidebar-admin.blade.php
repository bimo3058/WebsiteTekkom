@php
    $iconDashboard = 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10';
    $iconSettings = 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z';
    $iconHelp = 'M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01';
    $iconLogout = 'M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9';
    $iconUmum = 'M12 2L4 6v6c0 5 3.4 9.5 8 10 4.6-.5 8-5 8-10V6l-8-4z'; // shield icon equivalent
    $iconBankSoal = 'M6 4h12v16H6zM9 8h6M9 12h6M9 16h4'; // doc icon equivalent
    $iconSetup = 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'; // gear icon
    $iconMonitor = 'M3 4h18v12H3zM8 20h8M12 16v4'; // monitor icon equivalent
    $iconRiwayat = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'; // clock equivalent
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

        {{-- Brand text --}}
        <div x-show="sidebarOpen" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex-1 min-w-0">
            <div class="font-bold text-[14px] text-[#0D0D12] leading-[1.2] whitespace-nowrap overflow-hidden text-ellipsis" style="font-family:'Geist', 'Inter Tight', sans-serif; letter-spacing:-.01em;">SIBASO</div>
            <div class="font-medium text-[9px] text-[#808897] mt-[2px] whitespace-nowrap overflow-hidden text-ellipsis">Sistem Informasi Bank Soal</div>
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

        @php $isDashboard = request()->routeIs('banksoal.dashboard'); @endphp
        <a href="{{ route('banksoal.dashboard') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isDashboard ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isDashboard)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isDashboard ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconDashboard }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Dashboard</span>
        </a>

        {{-- Kontrol Umum Accordion --}}
        @php 
            $isKontrolUmumActive = request()->routeIs('banksoal.admin.kontrol-umum.*'); 
            $isMataKuliah = request()->routeIs('banksoal.admin.kontrol-umum.mata-kuliah');
            $isPemetaan = request()->routeIs('banksoal.admin.kontrol-umum.pemetaan*');
        @endphp
        <div x-data="{ openAcc: {{ $isKontrolUmumActive ? 'true' : 'false' }} }" class="flex flex-col">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isKontrolUmumActive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isKontrolUmumActive)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isKontrolUmumActive ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconUmum }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Kontrol Umum</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isMataKuliah ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Manajemen Data</a>
                    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isPemetaan ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Pemetaan</a>
                </div>
            </div>
        </div>

        {{-- Bank Soal Accordion --}}
        @php 
            $isKontrolBankSoalActive = request()->routeIs('banksoal.admin.kontrol-banksoal.*');
            $isRps = request()->routeIs('banksoal.admin.kontrol-banksoal.rps');
            $isSoal = request()->routeIs('banksoal.admin.kontrol-banksoal.soal');
        @endphp
        <div x-data="{ openAcc: {{ $isKontrolBankSoalActive ? 'true' : 'false' }} }" class="flex flex-col mt-[1px]">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isKontrolBankSoalActive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isKontrolBankSoalActive)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isKontrolBankSoalActive ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconBankSoal }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Bank Soal</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.admin.kontrol-banksoal.rps') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isRps ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Manajemen RPS</a>
                    <a href="{{ route('banksoal.admin.kontrol-banksoal.soal') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isSoal ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Manajemen Soal</a>
                </div>
                    </div>
                </div>

        {{-- UJIAN KOMPREHENSIF SECTION --}}
        <div x-show="sidebarOpen" class="text-[10px] font-semibold text-[#808897] tracking-[0.06em] uppercase p-[12px_10px_5px] whitespace-nowrap mt-1">Ujian Komprehensif</div>

        {{-- Setup Ujian Accordion --}}
        @php 
            $isPeriodeActive = request()->routeIs('banksoal.periode.*') || request()->routeIs('banksoal.pendaftaran.*');
            $isSetup = request()->routeIs('banksoal.periode.setup');
            $isSesi = request()->routeIs('banksoal.pendaftaran.alokasi-sesi.*');
            $isDaftar = request()->routeIs('banksoal.pendaftaran.index');
        @endphp
        <div x-data="{ openAcc: {{ $isPeriodeActive ? 'true' : 'false' }} }" class="flex flex-col">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isPeriodeActive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isPeriodeActive)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isPeriodeActive ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconSetup }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Setup Ujian</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.periode.setup') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isSetup ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Periode Ujian</a>
                    <a href="{{ route('banksoal.pendaftaran.alokasi-sesi.index') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isSesi ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Jadwal & Sesi</a>
                    <a href="{{ route('banksoal.pendaftaran.index') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isDaftar ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Daftar Peserta</a>
                </div>
            </div>
        </div>

        {{-- Monitoring Ujian Accordion --}}
        @php 
            $isMonitoringActive = request()->routeIs('banksoal.aktivasi.*') || request()->routeIs('banksoal.admin.cbt.live-proctoring');
            $isAktivasi = request()->routeIs('banksoal.aktivasi.index');
            $isLive = request()->routeIs('banksoal.admin.cbt.live-proctoring');
        @endphp
        <div x-data="{ openAcc: {{ $isMonitoringActive ? 'true' : 'false' }} }" class="flex flex-col mt-[1px]">
            <button @click="if(!sidebarOpen){ sidebarOpen=true; openAcc=true; } else { openAcc=!openAcc }" class="relative w-full flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isMonitoringActive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                @if($isMonitoringActive)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
                @endif
                <svg class="w-4 h-4 flex-shrink-0 {{ $isMonitoringActive ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconMonitor }}"/></svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-left">Monitoring Ujian</span>
                <svg x-show="sidebarOpen" class="w-3.5 h-3.5 flex-shrink-0 text-[#666D80] transition-transform duration-200" :class="openAcc ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openAcc && sidebarOpen" x-collapse x-cloak>
                <div class="flex flex-col gap-[2px] pl-[34px] pr-[10px] py-[2px] mt-[1px]">
                    <a href="{{ route('banksoal.aktivasi.index') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isAktivasi ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Aktivasi Sesi</a>
                    <a href="{{ route('banksoal.admin.cbt.live-proctoring') }}" class="block p-[7px_10px] rounded-lg text-[13px] transition-colors {{ $isLive ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] hover:bg-[#F6F8FA]' }}">Pantau Ujian</a>
                </div>
            </div>
        </div>

        {{-- Riwayat Ujian (Flat Link) --}}
        @php $isRiwayat = request()->routeIs('banksoal.admin.cbt.riwayat') || request()->routeIs('banksoal.admin.cbt.detail'); @endphp
        <a href="{{ route('banksoal.admin.cbt.riwayat') }}" @click="if(!sidebarOpen) sidebarOpen=true" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] mt-[1px] transition-colors {{ $isRiwayat ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isRiwayat)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isRiwayat ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconRiwayat }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Riwayat Ujian</span>
        </a>

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