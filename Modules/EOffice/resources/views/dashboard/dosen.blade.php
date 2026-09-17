<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Dosen — E-Office SIPERKOM</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@include('eoffice::dashboard._styles')
    <x-mobile-navigation-assets />
</head>
<body class="eo-dashboard h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased" style="font-family:'Inter Tight',system-ui,sans-serif;">

@php
    $user         = auth()->user();
    $name         = $user->name;
    $initials     = strtoupper(substr($name, 0, 1));
    $sp           = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
    $currentRoute = request()->route()?->getName() ?? '';

    $iDashboard = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iPraktikum  = "M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z";
    $iKP         = "M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15";
    $iLogout     = "M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238";

    // Data praktikum yang diampu (dikirim dari EOfficeController::dosenDashboard)
    $praktikumList  = $praktikumList  ?? collect();
    $totalDiampu    = $praktikumList->count();
    $totalAktif     = $praktikumList->where('status', 'aktif')->count();
    $totalMahasiswa = $praktikumList->sum('daftar_praktikan_count') ?? 0;
    $kpList         = $kpList ?? collect();
@endphp

<div class="eo-dashboard-shell flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, isMobile: window.innerWidth < 768 }"
     :class="{ 'eo-sidebar-open': sidebarOpen }"
     x-init="(() => { try { sidebarOpen = window.innerWidth >= 768 &amp;&amp; localStorage.getItem('eo_sb') !== '0'; } catch { sidebarOpen = window.innerWidth >= 768; } $watch('sidebarOpen', v => { if (window.innerWidth >= 768) { try { localStorage.setItem('eo_sb', v ? '1' : '0'); } catch {} } }); })()"
     @resize.window.debounce.150ms="if (isMobile !== (window.innerWidth < 768)) { isMobile = window.innerWidth < 768; try { sidebarOpen = !isMobile &amp;&amp; localStorage.getItem('eo_sb') !== '0'; } catch { sidebarOpen = !isMobile; } }"
     @keydown.escape.window="if (isMobile &amp;&amp; sidebarOpen) { sidebarOpen = false; $refs.eoMenuButton.focus(); }">
    <button type="button" class="eo-sidebar-backdrop" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" aria-label="Tutup menu navigasi"></button>
    @include('eoffice::dashboard._sidebar')

    <main class="eo-dashboard-main" :inert="isMobile &amp;&amp; sidebarOpen">

        @include('eoffice::dashboard._topbar')
        <div class="eo-dashboard-wrap">
            <section class="eo-dashboard-box" aria-labelledby="eo-dashboard-title">
                @include('eoffice::dashboard._header', ['dashboardRole' => 'dosen'])
                <div class="eo-dashboard-content">
                    @include('eoffice::dashboard._summary', ['dashboardRole' => 'dosen'])
                    @include('eoffice::dashboard._services', ['dashboardRole' => 'dosen'])

            {{-- Daftar Praktikum --}}
            <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
                <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                    <div>
                        <div class="font-bold text-[15px] text-[#0D0D12]">Praktikum yang Diampu</div>
                        <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $semesterLabel ?? 'Semester Genap 2025/2026' }}</div>
                    </div>
                    <a href="{{ route('eoffice.manprak.dosen.dashboard') }}"
                       class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">
                        Kelola →
                    </a>
                </div>

                @if($praktikumList->isEmpty())
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center py-10">
                        <svg class="mx-auto mb-3 w-10 h-10 text-[#DFE1E7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                            <path d="{{ $iPraktikum }}"/>
                        </svg>
                        <div class="text-[13px] text-[#A4ABB8]">Belum ada praktikum yang diampu.</div>
                        <div class="text-[12px] text-[#C8CAD4] mt-1">Hubungi Admin untuk ditambahkan sebagai dosen pengampu.</div>
                    </div>
                </div>
                @else
                <div class="eo-table-scroll"><div class="eo-table-wide">
                <div class="flex px-5 py-2 bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
                    <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:90px;">Kode</div>
                    <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama Praktikum</div>
                    <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:120px;">Koordinator</div>
                    <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em] text-center" style="width:80px;">Mahasiswa</div>
                    <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Status</div>
                </div>
                <div class="overflow-y-auto flex-1">
                    @foreach($praktikumList as $p)
                    <div class="flex items-center px-5 py-[11px] border-b border-[#F8F9FB] last:border-0 hover:bg-[#FAFAFC] cursor-pointer"
                         onclick="window.location='{{ route('eoffice.manprak.dosen.dashboard') }}'">
                        <div class="text-[12px] font-bold" style="width:90px; color:#9B59B6;">{{ $p->kode ?? '—' }}</div>
                        <div class="flex-1 text-[13px] font-medium text-[#0D0D12] truncate pr-3">{{ $p->nama }}</div>
                        <div class="text-[12px] text-[#666D80] truncate" style="width:120px;">{{ $p->koordinator?->name ?? '—' }}</div>
                        <div class="text-[13px] font-semibold text-[#0D0D12] text-center" style="width:80px;">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                        <div style="width:80px;">
                            @if($p->status === 'aktif')
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#DDF2EE] text-[#174E43]">
                                <span class="w-[5px] h-[5px] rounded-full bg-[#40C4AA]"></span>Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#F0F1F4] text-[#666D80]">
                                <span class="w-[5px] h-[5px] rounded-full bg-[#A4ABB8]"></span>Nonaktif
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                </div></div>
                @endif
            </div>

                </div>{{-- /content --}}
            </section>
        </div>
    </main>
</div>

    <x-mobile-navigation />
</body>
</html>
