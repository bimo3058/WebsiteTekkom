<aside class="h-full bg-white flex flex-col justify-between overflow-hidden"
    :class="sidebarOpen ? 'w-[260px]' : 'w-[72px]'" style="transition: width 0.3s;">

    <!-- Top Section: Logo & Nav -->
    <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden" style="scrollbar-width: thin;">

        <!-- Logo / Header -->
        <div class="flex items-center justify-between px-4 py-[18px] border-b border-[#DFE1E6]">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Logo Icon -->
                <div class="w-9 h-9 bg-[#081031] rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div x-show="sidebarOpen" class="flex flex-col min-w-0 overflow-hidden"
                    x-transition.opacity.duration.200ms>
                    <span class="font-bold text-[#081031] text-sm leading-tight tracking-tight">SIKP</span>
                    <span class="text-[#A4ABB8] text-[10px] leading-tight font-medium truncate">Sistem Informasi
                        KP</span>
                </div>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                class="flex-shrink-0 w-6 h-6 flex items-center justify-center text-[#A4ABB8] hover:text-[#353849] rounded transition-colors"
                x-show="sidebarOpen" x-transition.opacity.duration.200ms>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 space-y-5">

            <!-- ── MENU UTAMA ── -->
            <div>
                <p x-show="sidebarOpen"
                    class="px-4 mb-2 text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-widest"
                    x-transition.opacity.duration.200ms>Menu Utama</p>

                @php $isDashboard = request()->routeIs('eoffice.kp.koordinator.dashboard'); @endphp
                <div class="relative px-2">
                    @if($isDashboard)
                        {{-- Blue left bar, extends outside the px-2 to sidebar edge --}}
                        <span class="absolute left-0 top-2 bottom-2 w-[3.5px] bg-[#0065FF] rounded-r-full z-10"></span>
                    @endif
                    <a href="{{ route('eoffice.kp.koordinator.dashboard') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] transition-all group
                            {{ $isDashboard ? 'bg-[#F0F2FA]' : 'hover:bg-[#F8F9FB]' }}">
                        {{-- Icon: filled navy badge when active, outline grey when inactive --}}
                        @if($isDashboard)
                            <span class="w-7 h-7 bg-[#353849] rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </span>
                        @else
                            <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @endif
                        <span x-show="sidebarOpen"
                            class="text-[13px] whitespace-nowrap {{ $isDashboard ? 'font-semibold text-[#272835]' : 'text-[#666D80] group-hover:text-[#353849]' }}"
                            x-transition.opacity.duration.200ms>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- ── MANAJEMEN KP ── -->
            <div>
                <p x-show="sidebarOpen"
                    class="px-4 mb-2 text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-widest"
                    x-transition.opacity.duration.200ms>Manajemen KP</p>

                @php
                    $isDataMhs = request()->routeIs('eoffice.kp.koordinator.data_mahasiswa');
                    $isBalancing = request()->routeIs('eoffice.kp.koordinator.balancing');
                    $isValidasi = request()->routeIs('eoffice.kp.koordinator.validasi_berkas');
                    $isNilai = request()->routeIs('eoffice.kp.koordinator.nilai_lapangan');
                    $menuItems = [
                        ['active' => $isDataMhs, 'route' => 'eoffice.kp.koordinator.data_mahasiswa', 'label' => 'Data Mahasiswa', 'icon' => 'users'],
                        ['active' => $isBalancing, 'route' => 'eoffice.kp.koordinator.balancing', 'label' => 'Balancing Dosen', 'icon' => 'balance'],
                        ['active' => $isValidasi, 'route' => 'eoffice.kp.koordinator.validasi_berkas', 'label' => 'Approval Berkas', 'icon' => 'doc'],
                        ['active' => $isNilai, 'route' => 'eoffice.kp.koordinator.nilai_lapangan', 'label' => 'Nilai Lapangan', 'icon' => 'checkdoc'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    <div class="relative px-2">
                        @if($item['active'])
                            <span class="absolute left-0 top-2 bottom-2 w-[3.5px] bg-[#0065FF] rounded-r-full z-10"></span>
                        @endif
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] transition-all group
                                {{ $item['active'] ? 'bg-[#F0F2FA]' : 'hover:bg-[#F8F9FB]' }}">
                            @if($item['active'])
                                <span class="w-7 h-7 bg-[#353849] rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($item['icon'] === 'users')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    @elseif($item['icon'] === 'balance')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    @elseif($item['icon'] === 'doc')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @elseif($item['icon'] === 'checkdoc')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    @endif
                                </span>
                            @else
                                @if($item['icon'] === 'users')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                @elseif($item['icon'] === 'balance')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                @elseif($item['icon'] === 'doc')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @elseif($item['icon'] === 'checkdoc')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                @endif
                            @endif
                            <span x-show="sidebarOpen"
                                class="text-[13px] whitespace-nowrap {{ $item['active'] ? 'font-semibold text-[#272835]' : 'text-[#666D80] group-hover:text-[#353849]' }}"
                                x-transition.opacity.duration.200ms>{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- ── INFORMASI ── -->
            <div>
                <p x-show="sidebarOpen"
                    class="px-4 mb-2 text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-widest"
                    x-transition.opacity.duration.200ms>Informasi</p>

                @php
                    $isPengumuman = request()->routeIs('eoffice.kp.koordinator.pengumuman');
                    $isFaq = request()->routeIs('eoffice.kp.koordinator.faq');
                    $isTemplate = request()->routeIs('eoffice.kp.koordinator.template');
                    $infoItems = [
                        ['active' => $isPengumuman, 'route' => 'eoffice.kp.koordinator.pengumuman', 'label' => 'Pengumuman', 'icon' => 'annc'],
                        ['active' => $isFaq, 'route' => 'eoffice.kp.koordinator.faq', 'label' => 'FAQ & Dokumen', 'icon' => 'faq'],
                        ['active' => $isTemplate, 'route' => 'eoffice.kp.koordinator.template', 'label' => 'Template Dokumen', 'icon' => 'tpl'],
                    ];
                @endphp

                @foreach($infoItems as $item)
                    <div class="relative px-2">
                        @if($item['active'])
                            <span class="absolute left-0 top-2 bottom-2 w-[3.5px] bg-[#0065FF] rounded-r-full z-10"></span>
                        @endif
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] transition-all group
                                {{ $item['active'] ? 'bg-[#F0F2FA]' : 'hover:bg-[#F8F9FB]' }}">
                            @if($item['active'])
                                <span class="w-7 h-7 bg-[#353849] rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($item['icon'] === 'annc')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                    @elseif($item['icon'] === 'faq')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($item['icon'] === 'tpl')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </span>
                            @else
                                @if($item['icon'] === 'annc')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                @elseif($item['icon'] === 'faq')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($item['icon'] === 'tpl')
                                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            @endif
                            <span x-show="sidebarOpen"
                                class="text-[13px] whitespace-nowrap {{ $item['active'] ? 'font-semibold text-[#272835]' : 'text-[#666D80] group-hover:text-[#353849]' }}"
                                x-transition.opacity.duration.200ms>{{ $item['label'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>

        </nav>
    </div>

    <!-- ── BOTTOM SECTION ── -->
    <div class="border-t border-[#DFE1E6] pt-2 pb-3">

        @if(auth()->user() && auth()->user()->email === 'ike.pertiwi@undip.ac.id')
            <div class="px-2">
                <a href="{{ route('eoffice.kp.dosen.dashboard') }}"
                    class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] text-[#666D80] hover:bg-[#F8F9FB] hover:text-[#353849] transition-all group">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-[13px] whitespace-nowrap"
                        x-transition.opacity.duration.200ms>Beralih ke Dosen</span>
                </a>
            </div>
        @endif

        <!-- Pengaturan -->
        @php $isPengaturan = request()->routeIs('eoffice.kp.koordinator.pengaturan'); @endphp
        <div class="relative px-2">
            @if($isPengaturan)
                <span class="absolute left-0 top-2 bottom-2 w-[3.5px] bg-[#0065FF] rounded-r-full z-10"></span>
            @endif
            <a href="{{ route('eoffice.kp.koordinator.pengaturan') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] transition-all group
                    {{ $isPengaturan ? 'bg-[#F0F2FA]' : 'hover:bg-[#F8F9FB]' }}">
                @if($isPengaturan)
                    <span class="w-7 h-7 bg-[#353849] rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                @else
                    <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                @endif
                <span x-show="sidebarOpen"
                    class="text-[13px] whitespace-nowrap {{ $isPengaturan ? 'font-semibold text-[#272835]' : 'text-[#666D80] group-hover:text-[#353849]' }}"
                    x-transition.opacity.duration.200ms>Pengaturan</span>
            </a>
        </div>

        <!-- Help & Center -->
        <div class="px-2">
            <a href="#"
                class="flex items-center gap-3 py-2.5 px-3 rounded-[5px] text-[#666D80] hover:bg-[#F8F9FB] hover:text-[#353849] transition-all group">
                <svg class="w-[18px] h-[18px] flex-shrink-0 text-[#A4ABB8] group-hover:text-[#666D80]" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="sidebarOpen" class="text-[13px] whitespace-nowrap"
                    x-transition.opacity.duration.200ms>Help &amp; Center</span>
            </a>
        </div>

        <!-- Logout -->
        <div class="px-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 py-2.5 px-3 rounded-[5px] text-[#E5503A] hover:bg-red-50 transition-all group">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarOpen" class="text-[13px] font-medium whitespace-nowrap"
                        x-transition.opacity.duration.200ms>Logout</span>
                </button>
            </form>
        </div>

    </div>

</aside>