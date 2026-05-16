<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $pageTitle ?? 'Manajemen Praktikum' }} — SIPERKOM</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased" style="font-family:'Inter Tight',system-ui,sans-serif;">

@php
    $user         = auth()->user();
    $name         = $user->name;
    $initials     = strtoupper(substr($name, 0, 1));
    $sp           = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
    $currentRoute = request()->route()?->getName() ?? '';
    $isAdmin = $user->hasRole('superadmin') || $user->hasRole('admin_eoffice', 'eoffice');
    $isDosen = $user->hasRole('dosen', 'eoffice');
    $isKoor  = $user->hasRole('koor_prak', 'eoffice');
    $isAsprak= $user->hasRole('asprak', 'eoffice');
    $isMhs   = !$isAdmin && !$isDosen && !$isKoor && !$isAsprak;
    $roleLabel = $isAdmin ? 'Admin' : ($isDosen ? 'Dosen' : ($isKoor ? 'Koordinator' : ($isAsprak ? 'Asisten Praktikum' : 'Mahasiswa')));
    $roleColor = $isAdmin ? '#0B266E' : ($isDosen ? '#9B59B6' : ($isKoor ? '#106A97' : ($isAsprak ? '#40C4AA' : '#D39C3D')));
    $roleBg    = $isAdmin ? 'rgba(11,38,110,0.08)' : ($isDosen ? '#F0E6FA' : ($isKoor ? '#D1F0F9' : ($isAsprak ? '#DDF2EE' : '#F9ECCB')));

    // Icons as path strings
    $iHome = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iList = "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2";
    $iUser = "M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z";
    $iCheck= "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z";
    $iBook = "M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z";
    $iBell = "M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0";
    $iBack = "M19 12H5M5 12l7-7M5 12l7 7";
    $iLogout= "M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.94L7.87 4.06C6.39 3.66 5 5.21 5 7.27v9.46C5 18.79 6.39 20.34 7.87 19.94l3.2-.87C12.19 18.76 13 17.42 13 15.86v-.59M11 12h8M19 12l-2.5-2.72M19 12l-2.5 2.72";

    // Nav items per role
    if ($isAdmin) {
        $navGroups = [
            'Utama' => [
                ['href'=>route('eoffice.manprak.admin.dashboard'),                'label'=>'Dashboard',          'match'=>'admin.dashboard',    'icon'=>$iHome],
            ],
            'Kelola Data' => [
                ['href'=>route('eoffice.manprak.admin.praktikum.index'),          'label'=>'Daftar Praktikum',   'match'=>'admin.praktikum',    'icon'=>$iBook],
                ['href'=>route('eoffice.manprak.admin.dosen.index'),              'label'=>'Daftar Dosen',       'match'=>'admin.dosen',        'icon'=>$iUser],
            ],
            'Pendaftaran' => [
                ['href'=>route('eoffice.manprak.admin.pendaftaran-asprak.index'), 'label'=>'Pendaftaran Asprak', 'match'=>'pendaftaran-asprak', 'icon'=>$iList],
                ['href'=>route('eoffice.manprak.admin.pendaftaran-koor.index'),   'label'=>'Pendaftaran Koor',   'match'=>'pendaftaran-koor',   'icon'=>$iList],
                ['href'=>route('eoffice.manprak.admin.bagi-asprak.index'),        'label'=>'Distribusi Asprak',  'match'=>'bagi-asprak',        'icon'=>$iCheck],
            ],
        ];
    } elseif ($isDosen) {
        $navGroups = [
            'Utama' => [
                ['href'=>route('eoffice.manprak.dosen.dashboard'),                          'label'=>'Dashboard', 'match'=>'dosen.dashboard', 'icon'=>$iHome],
            ],
            'Praktikum' => [
                ['href'=>route('eoffice.manprak.dosen.modul.index', ['praktikumId'=>0]),    'label'=>'Modul',     'match'=>'dosen.modul',     'icon'=>$iBook],
                ['href'=>route('eoffice.manprak.dosen.nilai.index', ['praktikumId'=>0]),    'label'=>'Nilai',     'match'=>'dosen.nilai',     'icon'=>$iCheck],
            ],
        ];
    } elseif ($isKoor) {
        $navGroups = [
            'Utama' => [
                ['href'=>route('eoffice.manprak.koor.dashboard'),          'label'=>'Dashboard',      'match'=>'koor.dashboard',  'icon'=>$iHome],
            ],
            'Kelola' => [
                ['href'=>route('eoffice.manprak.koor.bagi-modul.index'),   'label'=>'Bagi Modul',     'match'=>'bagi-modul',      'icon'=>$iList],
                ['href'=>route('eoffice.manprak.koor.praktikan.index'),    'label'=>'Data Praktikan', 'match'=>'koor.praktikan',  'icon'=>$iUser],
                ['href'=>route('eoffice.manprak.koor.pengumuman.index'),   'label'=>'Pengumuman',     'match'=>'koor.pengumuman', 'icon'=>$iBell],
            ],
        ];
    } elseif ($isAsprak) {
        $navGroups = [
            'Utama' => [
                ['href'=>route('eoffice.manprak.asprak.dashboard'),     'label'=>'Dashboard', 'match'=>'asprak.dashboard', 'icon'=>$iHome],
            ],
            'Aktivitas' => [
                ['href'=>route('eoffice.manprak.asprak.absensi.index'), 'label'=>'Absensi',   'match'=>'asprak.absensi',   'icon'=>$iCheck],
                ['href'=>route('eoffice.manprak.asprak.tugas.index'),   'label'=>'Tugas',     'match'=>'asprak.tugas',     'icon'=>$iList],
                ['href'=>route('eoffice.manprak.asprak.materi.index'),  'label'=>'Materi',    'match'=>'asprak.materi',    'icon'=>$iBook],
            ],
        ];
    } else {
        $navGroups = [
            'Utama' => [
                ['href'=>route('eoffice.manprak.mahasiswa.dashboard'),           'label'=>'Dashboard',    'match'=>'mahasiswa.dashboard',  'icon'=>$iHome],
            ],
            'Aktivitas' => [
                ['href'=>route('eoffice.manprak.mahasiswa.tugas.index'),         'label'=>'Tugas',        'match'=>'mahasiswa.tugas',      'icon'=>$iList],
                ['href'=>route('eoffice.manprak.mahasiswa.daftar-asprak.index'), 'label'=>'Daftar Asprak','match'=>'daftar-asprak',        'icon'=>$iUser],
            ],
        ];
    }

    $notifCount = \Modules\EOffice\Models\Notifikasi::where('user_id', $user->id)->where('is_read', false)->count();
@endphp

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('mp_sb') !== '0' }"
     x-init="$watch('sidebarOpen', v => localStorage.setItem('mp_sb', v ? '1' : '0'))">

    {{-- SIDEBAR --}}
    <aside class="flex flex-col flex-shrink-0 bg-white border-r border-[#DFE1E7] relative overflow-visible z-20 transition-all duration-[240ms] ease-[cubic-bezier(.4,0,.2,1)]"
           :class="sidebarOpen ? 'w-[240px]' : 'w-[64px]'">

        {{-- Brand --}}
        <div class="relative px-[10px] pt-[18px] pb-[10px]">
            <div class="flex items-center gap-[10px] px-[10px] py-2 rounded-[10px]">
                <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0 bg-white shadow-sm overflow-hidden">
                    <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP" class="w-[24px] h-[24px] object-contain">
                </div>
                <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                    <div class="font-bold text-[13px] text-[#0D0D12] leading-[1.2] whitespace-nowrap">SIPERKOM</div>
                    <div class="text-[9px] font-semibold text-[#A4ABB8] uppercase tracking-[.04em] whitespace-nowrap">Man. Praktikum</div>
                </div>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-[-12px] top-[34px] flex items-center justify-center w-6 h-6 rounded-full bg-white border border-[#DFE1E7] shadow-[0_1px_4px_rgba(0,0,0,.08)] cursor-pointer z-30 transition-colors hover:bg-[#F6F8FA]">
                <svg class="transition-transform duration-[240ms]" :class="sidebarOpen ? '' : 'rotate-180'"
                     width="8" height="8" viewBox="0 0 10 10" fill="none" stroke="#666D80" stroke-width="2.2" stroke-linecap="round">
                    <path d="M7 1L3 5L7 9"/>
                </svg>
            </button>
        </div>

        {{-- Role pill --}}
        <div class="px-5 pb-2 transition-opacity duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'">
            <span class="inline-block text-[10px] font-bold px-2 py-[2px] rounded-full"
                  style="background:{{ $roleBg }}; color:{{ $roleColor }};">{{ $roleLabel }}</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-[10px] py-1 flex flex-col [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach($navGroups as $groupLabel => $items)
            <div class="mb-1">
                <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[.06em] px-[10px] py-1 mb-[2px] whitespace-nowrap overflow-hidden transition-opacity duration-200"
                     :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $groupLabel }}</div>
                @foreach($items as $item)
                @php $active = str_contains($currentRoute, $item['match']); @endphp
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap
                          {{ $active ? 'bg-[#0B266E]' : 'hover:bg-[#F6F8FA]' }}"
                   :class="sidebarOpen ? '' : 'justify-center'">
                    <svg class="w-[15px] h-[15px] flex-shrink-0 {{ $active ? 'text-white' : 'text-[#666D80]' }}"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                                 {{ $active ? 'font-semibold text-white' : 'font-medium text-[#353849]' }}"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">{{ $item['label'] }}</span>
                </a>
                @endforeach
            </div>
            @endforeach

            <div class="h-px bg-[#F0F1F4] mx-[14px] my-1"></div>
            <div class="mb-1">
                <a href="{{ route('eoffice.dashboard') }}"
                   class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg no-underline transition-colors hover:bg-[#F6F8FA]"
                   :class="sidebarOpen ? '' : 'justify-center'">
                    <svg class="w-[15px] h-[15px] flex-shrink-0 text-[#666D80]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="{{ $iBack }}"/>
                    </svg>
                    <span class="text-[13px] font-medium text-[#666D80] flex-1 transition-[opacity,width] duration-200"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Kembali ke EOffice</span>
                </a>
            </div>
        </nav>

        {{-- User footer --}}
        <div class="px-3 py-[10px] border-t border-[#DFE1E7] flex-shrink-0">
            <div class="flex items-center gap-[10px] px-[10px] py-2 rounded-lg overflow-hidden transition-colors hover:bg-[#F6F8FA]">
                <div class="flex items-center justify-center w-[30px] h-[30px] rounded-full flex-shrink-0 text-white text-[11px] font-bold"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">{{ $initials }}</div>
                <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                    <div class="text-[12px] font-semibold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis leading-[1.2]">{{ $name }}</div>
                    <div class="text-[10px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0" :class="sidebarOpen ? '' : 'hidden'">
                    @csrf
                    <button type="submit" class="p-1 rounded text-[#A4ABB8] hover:text-red-500 bg-transparent border-none cursor-pointer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="{{ $iLogout }}"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Topbar --}}
        <div class="flex items-center justify-between px-6 bg-white border-b border-[#DFE1E7] flex-shrink-0" style="height:56px;">
            <div class="flex items-center gap-3 min-w-0">
                <div>
                    <div class="font-bold text-[15px] text-[#0D0D12] leading-[1.2]">{{ $pageTitle ?? 'Dashboard' }}</div>
                    <div class="text-[11px] text-[#666D80]">Manajemen Praktikum · SIPERKOM</div>
                </div>
                <span class="text-[11px] font-semibold px-[9px] py-[3px] rounded-full whitespace-nowrap flex-shrink-0"
                      style="background:{{ $roleBg }}; color:{{ $roleColor }};">{{ $roleLabel }}</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative flex items-center justify-center w-[34px] h-[34px] rounded-lg border border-[#DFE1E7] bg-white cursor-pointer hover:bg-[#F6F8FA]">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.8" stroke-linecap="round">
                        <path d="{{ $iBell }}"/>
                    </svg>
                    @if($notifCount > 0)
                    <span class="absolute top-[6px] right-[6px] w-[6px] h-[6px] rounded-full bg-[#DF1C41] border-[1.5px] border-white"></span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
        <div class="mx-6 mt-3 flex items-center gap-2 bg-[#DDF2EE] border border-[#40C4AA] rounded-[10px] px-4 py-[10px] text-[13px] font-medium text-[#174E43] flex-shrink-0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-3 flex items-center gap-2 bg-[#FADAE1] border border-[#DF1C41] rounded-[10px] px-4 py-[10px] text-[13px] font-medium text-[#7C1028] flex-shrink-0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Page Content --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-[18px]">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
