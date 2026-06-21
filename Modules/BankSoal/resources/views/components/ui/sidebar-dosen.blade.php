@php
    $iconDashboard = 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10';
    $iconSettings = 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z';
    $iconHelp = 'M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01';
    $iconLogout = 'M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9';
    $iconRps = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    $iconSoal = 'M4 7v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-6.586a1 1 0 01-.707-.293l-3.414-3.414A2 2 0 008.586 2H6a2 2 0 00-2 2v3z';
    $iconArsip = 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4';
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

        @php $isDashboard = request()->routeIs('banksoal.dashboard') && auth()->user()->hasRole('dosen'); @endphp
        <a href="{{ route('banksoal.dashboard') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] transition-colors {{ $isDashboard ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isDashboard)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isDashboard ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconDashboard }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Dashboard</span>
        </a>

        @php $isRps = request()->routeIs('banksoal.rps.dosen.*'); @endphp
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] mt-[1px] transition-colors {{ $isRps ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isRps)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isRps ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconRps }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Manajemen RPS</span>
        </a>

        @php $isSoal = request()->routeIs('banksoal.soal.dosen.*'); @endphp
        <a href="{{ route('banksoal.soal.dosen.index') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] mt-[1px] transition-colors {{ $isSoal ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isSoal)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isSoal ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconSoal }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Bank Soal</span>
        </a>

        @php $isArsip = request()->routeIs('banksoal.arsip.dosen.*'); @endphp
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] mt-[1px] transition-colors {{ $isArsip ? 'bg-[#F6F8FA] text-[#0D0D12] font-semibold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
            @if($isArsip)
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]" x-show="sidebarOpen"></span>
            @endif
            <svg class="w-4 h-4 flex-shrink-0 {{ $isArsip ? 'text-[#0B266E]' : 'text-[#666D80]' }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $iconArsip }}"/></svg>
            <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Arsip Soal</span>
        </a>

        @if(auth()->user()->hasRole('gpm'))
            <div x-show="sidebarOpen" class="text-[10px] font-semibold text-[#808897] tracking-[0.06em] uppercase p-[12px_10px_5px] whitespace-nowrap mt-1">Gugus Penjaminan Mutu</div>
            <a href="{{ route('banksoal.switch-role', 'gpm') }}" class="relative flex items-center gap-[9px] p-[7px_10px_7px_14px] rounded-lg text-[13px] mt-[1px] transition-colors text-[#353849] font-medium hover:bg-[#F6F8FA]" :class="!sidebarOpen ? 'justify-center p-[7px_0]' : ''">
                <svg class="w-4 h-4 flex-shrink-0 text-[#666D80]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                <span x-show="sidebarOpen" class="flex-1 whitespace-nowrap overflow-hidden text-ellipsis leading-tight">Akses GPM</span>
            </a>
        @endif
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
