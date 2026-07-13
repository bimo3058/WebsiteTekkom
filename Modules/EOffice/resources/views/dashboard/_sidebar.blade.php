{{--
    _sidebar.blade.php — Multi-role sidebar EOffice
    Mendukung user dengan beberapa role sekaligus.
    Contoh: Mahasiswa + Koor + Asprak → semua menu spesifik muncul bersamaan.
--}}

@php
    $isAdmin  = $user->hasRole('superadmin') || $user->hasRole('admin_eoffice');
    $isDosen  = $user->hasRole('dosen');
    $isKoor   = $user->hasRole('koor_prak');
    $isAsprak = $user->hasRole('asprak');
    $isMhs    = $user->hasRole('mahasiswa');

    // Multi-role: kumpulkan semua section yang berlaku
    // Tiap section = ['label', 'icon', 'color', 'match', 'subs']
    $manprakSections = [];

    if ($isAdmin) {
        $manprakSections[] = [
            'label' => 'Admin',
            'color' => '#DF1C41',
            'match' => 'manprak.admin',
            'link'  => route('eoffice.manprak.admin.dashboard'),
            'icon'  => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            'subs'  => [
                ['href' => route('eoffice.manprak.admin.dashboard'),                    'label' => 'Ringkasan',          'match' => 'admin.dashboard'],
                ['href' => route('eoffice.manprak.admin.praktikum.index'),              'label' => 'Kelola Praktikum',   'match' => 'admin.praktikum'],
                ['href' => route('eoffice.manprak.admin.dosen.index'),                  'label' => 'Daftar Dosen',       'match' => 'admin.dosen'],
                ['href' => route('eoffice.manprak.admin.asprak.index'),                 'label' => 'Asisten Praktikum',  'match' => 'admin.asprak'],
                ['href' => route('eoffice.manprak.admin.daftar-praktikan.index'),       'label' => 'Daftar Praktikan',   'match' => 'daftar-praktikan'],
                ['href' => route('eoffice.manprak.admin.pendaftaran-koor.index'),       'label' => 'Pendaftaran',   'match' => 'pendaftaran-koor'],
            ],
        ];
    }

    if ($isDosen) {
        $manprakSections[] = [
            'label' => 'Dosen',
            'color' => '#0B266E',
            'match' => 'manprak.dosen',
            'link'  => route('eoffice.manprak.dosen.dashboard'),
            'icon'  => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
            'subs'  => [
                ['href' => route('eoffice.manprak.dosen.dashboard'),                          'label' => 'Ringkasan',          'match' => 'dosen.dashboard'],
                ['href' => route('eoffice.manprak.dosen.pendaftaran-koor.index'),             'label' => 'Seleksi Koor',       'match' => 'dosen.pendaftaran-koor'],
                ['href' => route('eoffice.manprak.dosen.asprak.index'),                       'label' => 'Asisten Praktikum',  'match' => 'dosen.asprak'],
                ['href' => route('eoffice.manprak.dosen.daftar-praktikan.index'),             'label' => 'Daftar Praktikan',   'match' => 'dosen.daftar-praktikan'],
                ['href' => route('eoffice.manprak.dosen.pengumuman.index'),                   'label' => 'Pengumuman',         'match' => 'dosen.pengumuman'],
                ['href' => route('eoffice.manprak.dosen.tugas.index'),                        'label' => 'Daftar Tugas',       'match' => 'dosen.tugas'],
                ['href' => route('eoffice.manprak.dosen.modul.index', ['praktikumId' => 0]), 'label' => 'Daftar Modul',       'match' => 'dosen.modul'],
                ['href' => route('eoffice.manprak.dosen.nilai.index', ['praktikumId' => 0]), 'label' => 'Absensi & Nilai',    'match' => 'dosen.nilai'],
            ],
        ];
    }

    if ($isKoor) {
        $manprakSections[] = [
            'label' => 'Koordinator',
            'color' => '#6366F1',
            'match' => 'manprak.koor',
            'link'  => route('eoffice.manprak.koor.dashboard'),
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'subs'  => [
                ['href' => route('eoffice.manprak.koor.dashboard'),                  'label' => 'Ringkasan',        'match' => 'koor.dashboard'],
                ['href' => route('eoffice.manprak.koor.pendaftaran-asprak.index'),   'label' => 'Seleksi Asprak',   'match' => 'koor.pendaftaran-asprak'],
                ['href' => route('eoffice.manprak.koor.modul.index'),                'label' => 'Kelola Modul',     'match' => 'koor.modul'],
                ['href' => route('eoffice.manprak.koor.bagi-modul.index'),           'label' => 'Bagi Modul',       'match' => 'bagi-modul'],
                ['href' => route('eoffice.manprak.koor.pengumuman.index'),           'label' => 'Pengumuman',       'match' => 'koor.pengumuman'],
                ['href' => route('eoffice.manprak.koor.praktikan.index'),            'label' => 'Data Praktikan',   'match' => 'koor.praktikan'],
                ['href' => route('eoffice.manprak.koor.nilai.index'),                'label' => 'Absensi & Nilai',  'match' => 'koor.nilai'],
            ],
        ];
    }

    if ($isAsprak) {
        $manprakSections[] = [
            'label' => 'Asprak',
            'color' => '#40C4AA',
            'match' => 'manprak.asprak',
            'link'  => route('eoffice.manprak.asprak.dashboard'),
            'icon'  => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'subs'  => [
                ['href' => route('eoffice.manprak.asprak.dashboard'),          'label' => 'Ringkasan',   'match' => 'asprak.dashboard'],
                ['href' => route('eoffice.manprak.asprak.absensi.index'),      'label' => 'Absensi',     'match' => 'asprak.absensi'],
                ['href' => route('eoffice.manprak.asprak.tugas.index'),        'label' => 'Tugas',       'match' => 'asprak.tugas'],
                ['href' => route('eoffice.manprak.asprak.materi.index'),       'label' => 'Materi',      'match' => 'asprak.materi'],
                ['href' => route('eoffice.manprak.asprak.pengumuman.index'),   'label' => 'Pengumuman',  'match' => 'asprak.pengumuman'],
            ],
        ];
    }

    if ($isMhs) {
        $manprakSections[] = [
            'label' => 'Mahasiswa',
            'color' => '#D39C3D',
            'match' => 'manprak.mahasiswa',
            'link'  => route('eoffice.manprak.mahasiswa.dashboard'),
            'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'subs'  => [
                ['href' => route('eoffice.manprak.mahasiswa.dashboard'),            'label' => 'Ringkasan',    'match' => 'mahasiswa.dashboard'],
                ['href' => route('eoffice.manprak.mahasiswa.pengumuman.index'),     'label' => 'Pengumuman',   'match' => 'mahasiswa.pengumuman'],
                ['href' => route('eoffice.manprak.mahasiswa.tugas.index'),          'label' => 'Tugas',        'match' => 'mahasiswa.tugas'],
                ['href' => route('eoffice.manprak.mahasiswa.nilai.index'),          'label' => 'Nilai',        'match' => 'mahasiswa.nilai'],
                ['href' => route('eoffice.manprak.mahasiswa.daftar-asprak.index'),  'label' => 'Daftar Asprak/Koor', 'match' => 'daftar-asprak'],
            ],
        ];
    }

    $manprakActive = str_contains($currentRoute, 'manprak');
    $multiRole     = count($manprakSections) > 1;
@endphp

<aside class="flex flex-col flex-shrink-0 bg-white border-r border-[#DFE1E7] relative overflow-visible z-20 transition-all duration-[240ms] ease-[cubic-bezier(.4,0,.2,1)]"
       :class="sidebarOpen ? 'w-[240px]' : 'w-[68px]'">

    {{-- Brand --}}
    <div class="relative px-[10px] pt-[18px] pb-[10px]">
        <div class="flex items-center gap-[10px] px-[10px] py-2 rounded-[10px] overflow-hidden">
            <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0 bg-white shadow-sm overflow-hidden">
                <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP Logo" class="w-[24px] h-[24px] object-contain">
            </div>
            <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200"
                 :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                <div class="font-bold text-[14px] text-[#0D0D12] leading-[1.2] whitespace-nowrap">SIPERKOM</div>
                <div class="text-[9px] font-semibold text-[#A4ABB8] uppercase tracking-[.04em] whitespace-nowrap">Portal E-Office</div>
            </div>
        </div>
        <button @click="sidebarOpen = !sidebarOpen"
                class="absolute right-[-12px] top-[34px] flex items-center justify-center w-6 h-6 rounded-full bg-white border border-[#DFE1E7] shadow-[0_1px_4px_rgba(0,0,0,.08)] cursor-pointer z-30 transition-colors hover:bg-[#F6F8FA] hover:border-[#0B266E]">
            <svg class="transition-transform duration-[240ms]" :class="sidebarOpen ? '' : 'rotate-180'"
                 width="8" height="8" viewBox="0 0 10 10" fill="none" stroke="#666D80" stroke-width="2.2" stroke-linecap="round">
                <path d="M7 1L3 5L7 9"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-[10px] py-1 flex flex-col [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

        {{-- Utama --}}
        <div class="mb-1">
            <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[.06em] px-[10px] py-1 mb-[2px] whitespace-nowrap overflow-hidden transition-opacity duration-200"
                 :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Utama</div>
            @php $activeDash = str_contains($currentRoute, 'eoffice.dashboard'); @endphp
            <a href="{{ route('eoffice.dashboard') }}"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $activeDash ? 'bg-[#0B266E]' : 'text-[#353849] hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $activeDash ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $iDashboard }}"/>
                </svg>
                <span class="text-[13px] font-medium flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $activeDash ? 'font-semibold text-white' : 'text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Dashboard</span>
            </a>
        </div>

        <div class="h-px bg-[#F0F1F4] mx-[14px] my-1"></div>

        {{-- E-Office Subsistem --}}
        <div class="mb-1">
            <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[.06em] px-[10px] py-1 mb-[2px] whitespace-nowrap overflow-hidden transition-opacity duration-200"
                 :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">E-Office</div>

            {{-- Manajemen Surat --}}
            @php $activeSurat = str_contains($currentRoute, 'surat'); @endphp
            <a href="#"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $activeSurat ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $activeSurat ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/>
                </svg>
                <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $activeSurat ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Manajemen Surat</span>
            </a>

            {{-- Peminjaman --}}
            @php $activePeminjaman = str_contains($currentRoute, 'peminjaman'); @endphp
            <a href="{{ route('eoffice.peminjaman.dashboard') }}"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $activePeminjaman ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $activePeminjaman ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 22V6a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v16"/><path d="M13 22V10a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v12"/><path d="M2 22h20"/>
                </svg>
                <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $activePeminjaman ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Peminjaman Ruangan</span>
            </a>

            {{-- ══ Manajemen Praktikum ══ --}}
            {{-- Single role: langsung link, tanpa dropdown --}}
            {{-- Multi-role: dropdown expandable per role --}}
            @if(!$multiRole && count($manprakSections) === 1)
            <a href="{{ $manprakSections[0]['link'] }}"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $manprakActive ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $manprakActive ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $iPraktikum }}"/>
                </svg>
                <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $manprakActive ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Manajemen Praktikum</span>
            </a>
            @elseif(count($manprakSections) > 1)
            <a href="{{ route('eoffice.manprak.dashboard') }}"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $manprakActive ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $manprakActive ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $iPraktikum }}"/>
                </svg>
                <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $manprakActive ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Manajemen Praktikum</span>
            </a>
            @endif

            {{-- Kerja Praktik --}}
            @php 
                $activeKp = str_contains($currentRoute, 'eoffice.kp'); 
                $kpLink = match(true) {
                    $isAdmin  => route('eoffice.kp.koordinator.dashboard'),
                    $isDosen  => route('eoffice.kp.dosen.dashboard'),
                    $isKoor   => route('eoffice.kp.koordinator.dashboard'),
                    default   => route('eoffice.kp.mahasiswa.dashboard'),
                };
            @endphp
            <a href="{{ $kpLink }}"
               class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                      {{ $activeKp ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $activeKp ? 'text-white' : 'text-[#666D80]' }}"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $iKP }}"/>
                </svg>
                <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                             {{ $activeKp ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                      :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Kerja Praktik (KP)</span>
            </a>
        </div>

        {{-- Kembali ke Dashboard Utama SITKOM --}}
        <div class="h-px bg-[#F0F1F4] mx-[14px] my-[6px]"></div>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg no-underline transition-colors hover:bg-[#F6F8FA] text-[#666D80]"
           :class="sidebarOpen ? '' : 'justify-center'">
            <svg class="w-[14px] h-[14px] flex-shrink-0 text-[#A4ABB8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M19 12H5M5 12l7-7M5 12l7 7"/>
            </svg>
            <span class="text-[12px] font-medium flex-1 transition-[opacity,width] duration-200"
                  :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Kembali ke Dasbor Utama</span>
        </a>
    </nav>

    {{-- User footer --}}
    <div class="px-3 py-[10px] border-t border-[#DFE1E7] flex-shrink-0">


        <div class="flex items-center gap-[10px] px-[10px] py-2 rounded-lg cursor-pointer overflow-hidden transition-colors hover:bg-[#F6F8FA]">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $name }}"
                     class="w-[30px] h-[30px] rounded-full object-cover flex-shrink-0">
            @else
                <div class="flex items-center justify-center w-[30px] h-[30px] rounded-full flex-shrink-0 text-white text-[11px] font-bold"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                    {{ $initials }}
                </div>
            @endif
            <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200"
                 :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                <div class="text-[12px] font-semibold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis leading-[1.2]">{{ $name }}</div>
                <div class="text-[10px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0"
                  :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'">
                @csrf
                <button type="submit" class="flex items-center p-1 rounded-md text-[#A4ABB8] transition-colors hover:text-red-500 border-none bg-transparent cursor-pointer"
                        title="Logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $iLogout }}"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>