<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Admin — E-Office SIPERKOM</title>
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

    // SVG path strings — dipakai sidebar & topbar
    $iDashboard = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iPraktikum  = "M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z";
    $iKP         = "M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15";
    $iLogout     = "M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238";
@endphp

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('eo_sb') !== '0' }"
     x-init="$watch('sidebarOpen', v => localStorage.setItem('eo_sb', v ? '1' : '0'))">

    {{-- ════════════ SIDEBAR (shared partial) ════════════ --}}
    @include('eoffice::dashboard._sidebar')

    {{-- ════════════ MAIN ════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <div class="flex items-center justify-between px-6 bg-white border-b border-[#DFE1E7] flex-shrink-0" style="height:56px;">
            <div class="flex items-center gap-3 min-w-0">
                <div>
                    <div class="font-bold text-[15px] text-[#0D0D12] leading-[1.2]">Dashboard</div>
                    <div class="text-[11px] text-[#666D80]">Modul E-Office · SIPERKOM UNDIP</div>
                </div>
                <span class="text-[11px] font-semibold px-[9px] py-[3px] rounded-full whitespace-nowrap"
                      style="background:rgba(11,38,110,0.08); color:#0B266E;">Admin</span>
            </div>
            <div class="flex items-center gap-2">
                {{-- Notifikasi --}}
                <div class="relative flex items-center justify-center w-[34px] h-[34px] rounded-lg border border-[#DFE1E7] bg-white cursor-pointer transition-colors hover:bg-[#F6F8FA]">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-[6px] h-[6px] rounded-full bg-[#DF1C41] border-[1.5px] border-white"></span>
                </div>
            </div>
        </div>

        {{-- Scrollable content --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-[18px]">

            {{-- Welcome banner --}}
            <div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
                 style="background:linear-gradient(120deg,#0B266E 0%,#1a3a8f 100%);">
                <div>
                    <div class="text-[18px] font-bold tracking-tight">Selamat Datang, {{ $name }}!</div>
                    <div class="text-[12px] opacity-75 mt-1">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel ?? 'Semester Genap 2025/2026' }}
                    </div>
                </div>
                <div class="flex gap-3 flex-shrink-0">
                    <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.12);">
                        <div class="text-[20px] font-bold">{{ ($totalKpPending??0)+($totalPeminjamanPending??0) }}</div>
                        <div class="text-[10px] opacity-75 mt-[2px]">Perlu Tindakan</div>
                    </div>
                    <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.12);">
                        <div class="text-[20px] font-bold">{{ $totalNotifikasi??0 }}</div>
                        <div class="text-[10px] opacity-75 mt-[2px]">Notifikasi</div>
                    </div>
                </div>
            </div>

            {{-- Stat cards --}}
            <div class="flex gap-[14px] flex-shrink-0">
                @php
                $stats = [
                    ['lbl'=>'Total Surat Diproses','val'=>$totalSuratDiproses??0, 'trend'=>'+'.($statSuratHariIni??0).' hari ini','up'=>true, 'ibg'=>'rgba(11,38,110,0.08)','ic'=>'#0B266E','ip'=>'<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/>'],
                    ['lbl'=>'Peminjaman Ruangan',  'val'=>$totalPeminjamanAktif??0,'sub'=>'aktif minggu ini',               'ibg'=>'#D1F0F9','ic'=>'#106A97','ip'=>'<path d="M3 22V6a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v16"/><path d="M13 22V10a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v12"/><path d="M2 22h20"/>'],
                    ['lbl'=>'Praktikum Aktif',     'val'=>$totalPraktikumAktif??0, 'sub'=>'semester ini',                   'ibg'=>'#DDF2EE','ic'=>'#40C4AA','ip'=>'<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>'],
                    ['lbl'=>'KP Berjalan',         'val'=>$totalKpBerjalan??0,    'trend'=>'+'.($statKpBaru??0).' pengajuan','up'=>true, 'ibg'=>'#F9ECCB','ic'=>'#D39C3D','ip'=>'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>'],
                ];
                @endphp
                @foreach($stats as $s)
                <div class="flex-1 min-w-0 flex flex-col gap-[10px] bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
                    <div class="flex items-start justify-between">
                        <span class="text-[12px] font-medium text-[#666D80] leading-[1.4]">{{ $s['lbl'] }}</span>
                        <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0"
                             style="background:{{ $s['ibg'] }};">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $s['ic'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $s['ip'] !!}</svg>
                        </div>
                    </div>
                    <div class="text-[28px] font-bold text-[#0D0D12] leading-none tracking-tight">{{ $s['val'] }}</div>
                    @if(!empty($s['trend']))
                        <div class="flex items-center gap-[5px]">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ ($s['up']??false)?'#40C4AA':'#DF1C41' }}" stroke-width="2.2" stroke-linecap="round">
                                @if($s['up']??false)<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                                @else<line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>@endif
                            </svg>
                            <span class="text-[11px] font-semibold" style="color:{{ ($s['up']??false)?'#40C4AA':'#DF1C41' }}">{{ $s['trend'] }}</span>
                        </div>
                    @elseif(!empty($s['sub']))
                        <span class="text-[11px] text-[#666D80]">{{ $s['sub'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Subsystem cards --}}
            <div class="grid grid-cols-3 gap-[14px] flex-shrink-0">
                @php
                $subsystems = [
                    ['t'=>'Manajemen Surat',     'd'=>'Kelola pengajuan dan penerbitan surat',  's'=>($totalSuratPending??0).' menunggu persetujuan',  'c'=>'#0B266E','bg'=>'rgba(11,38,110,0.08)','r'=>'#',                                              'i'=>'<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/>'],
                    ['t'=>'Peminjaman Ruangan',  'd'=>'Booking dan persetujuan peminjaman',     's'=>($totalPeminjamanPending??0).' menunggu konfirmasi','c'=>'#106A97','bg'=>'#D1F0F9',            'r'=>route('eoffice.peminjaman.dashboard'),     'i'=>'<path d="M3 22V6a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v16"/><path d="M13 22V10a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v12"/><path d="M2 22h20"/>'],
                    ['t'=>'Manajemen Praktikum', 'd'=>'CRUD data praktikum & koordinator',      's'=>($totalPraktikumAktif??0).' praktikum terdaftar',  'c'=>'#40C4AA','bg'=>'#DDF2EE',            'r'=>route('eoffice.manprak.admin.dashboard'), 'i'=>'<path d="M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z"/>'],
                    ['t'=>'Kerja Praktik (KP)',  'd'=>'Administrasi dan monitoring KP',         's'=>($totalKpBerjalan??0).' KP berjalan',              'c'=>'#D39C3D','bg'=>'#F9ECCB',            'r'=>route('eoffice.kp.koordinator.dashboard'),             'i'=>'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>'],
                    ['t'=>'Daftar Dosen',        'd'=>'Kelola dosen pengampu & koordinator',    's'=>($totalDosen??0).' dosen terdaftar',               'c'=>'#9B59B6','bg'=>'#F0E6FA',            'r'=>route('eoffice.manprak.admin.dosen.index'),'i'=>'<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
                    ['t'=>'Log Aktivitas',       'd'=>'Audit trail seluruh aktivitas sistem',   's'=>($totalLogHariIni??0).' log hari ini',             'c'=>'#666D80','bg'=>'#F0F1F4',            'r'=>'#',                                              'i'=>'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'],
                ];
                @endphp
                @foreach($subsystems as $sys)
                <a href="{{ $sys['r'] }}"
                   class="block bg-white border border-[#DFE1E7] rounded-[12px] p-4 no-underline shadow-[0_1px_2px_rgba(228,229,231,.24)] transition-all duration-150 hover:shadow-[0_4px_14px_rgba(22,22,43,.08)] hover:border-[#C1C7CF]">
                    <div class="flex items-center gap-[10px] mb-[10px]">
                        <div class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0"
                             style="background:{{ $sys['bg'] }};">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="{{ $sys['c'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $sys['i'] !!}</svg>
                        </div>
                        <div class="text-[13px] font-bold text-[#0D0D12]">{{ $sys['t'] }}</div>
                    </div>
                    <div class="text-[12px] text-[#666D80] leading-[1.4] mb-2">{{ $sys['d'] }}</div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full"
                              style="color:{{ $sys['c'] }}; background:{{ $sys['bg'] }};">{{ $sys['s'] }}</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Bottom grid: tabel + aktivitas --}}
            <div class="flex gap-[14px] flex-1 min-h-0 mb-1">

                {{-- Tabel Praktikum Aktif --}}
                <div style="background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); display: flex; flex-direction: column; flex: 2; min-width: 0;">
                    
                    {{-- Table Header / Toolbar --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--c-border, #DFE1E7); gap: 10px; flex-wrap: wrap; flex-shrink: 0;">
                        <div>
                            <h2 style="font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 0;">Daftar Praktikum Aktif</h2>
                            <div style="font-size: 11px; color: var(--c-fg-muted, #666D80); margin-top: 2px;">{{ $semesterLabel ?? 'Semester Genap 2025/2026' }}</div>
                        </div>
                        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
                        class="mp-btn secondary sm" 
                        style="font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none;">
                            Lihat Semua
                        </a>
                    </div>

                    {{-- Table Responsive Wrapper --}}
                    <div style="overflow-x: auto; flex: 1;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--c-border, #DFE1E7); background: #FAFAFA;">
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 90px;">Kode</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap;">Nama Praktikum</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 170px;">Dosen Pengampu</th>
                                    <th style="padding: 11px 16px; text-align: center; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 75px;">Peserta</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 90px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($praktikums ?? [] as $p)
                                <tr style="border-bottom: 1px solid #F3F4F6; transition: background .12s; cursor: pointer;"
                                    onmouseover="this.style.background='#FAFAFA'" 
                                    onmouseout="this.style.background='transparent'"
                                    onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.show', $p->id) }}'">
                                    
                                    {{-- Kode --}}
                                    <td style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #0B266E; font-family: monospace; white-space: nowrap;">
                                        {{ $p->kode ?? '—' }}
                                    </td>
                                    
                                    {{-- Nama Praktikum --}}
                                    <td style="padding: 12px 16px;">
                                        <div style="font-size: 13px; font-weight: 600; color: var(--c-fg, #0D0D12); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $p->nama ?? '' }}">
                                            {{ $p->nama ?? '—' }}
                                        </div>
                                    </td>
                                    
                                    {{-- Dosen Pengampu --}}
                                    <td style="padding: 12px 16px;">
                                        <div style="font-size: 12px; color: var(--c-fg-muted, #666D80); max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $p->dosen?->name ?? '—' }}">
                                            {{ $p->dosen?->name ?? '—' }}
                                        </div>
                                    </td>
                                    
                                    {{-- Peserta (Menggunakan fallback aslinya) --}}
                                    <td style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12);">
                                        {{ ($p->status ?? '') === 'aktif' ? ($p->peserta_count ?? 0) : '—' }}
                                    </td>
                                    
                                    {{-- Status Badge --}}
                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        @if(($p->status ?? '') === 'aktif')
                                            <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                                        @else
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center;">
                                        <svg width="36" height="36" fill="none" stroke="var(--c-fg-placeholder, #A4ABB8)" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" style="margin: 0 auto 10px; display: block;">
                                            <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                                        </svg>
                                        <p style="font-size: 12px; font-weight: 600; color: var(--c-fg-muted, #666D80); margin: 0;">Belum ada praktikum aktif.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Aktivitas Terbaru --}}
                <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0 flex-1">
                    <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                        <div class="font-bold text-[15px] text-[#0D0D12]">Aktivitas Terbaru</div>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        @forelse($recentActivities ?? [] as $act)
                        @php
                            $cm = ['blue'=>['#0B266E','rgba(11,38,110,0.08)'],'success'=>['#40C4AA','#DDF2EE'],'sky'=>['#106A97','#D1F0F9'],'warning'=>['#D39C3D','#F9ECCB'],'error'=>['#DF1C41','#FADAE1']];
                            [$dc,$db] = $cm[$act['type']??'blue'] ?? $cm['blue'];
                        @endphp
                        <div class="flex gap-3 items-start px-5 py-[10px] border-b border-[#F8F9FB] last:border-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg flex-shrink-0 mt-[1px]"
                                 style="background:{{ $db }};">
                                <div class="w-2 h-2 rounded-full" style="background:{{ $dc }};"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-medium text-[#353849] leading-[1.4]">{!! $act['text'] !!}</div>
                                @if(!empty($act['desc']))<div class="text-[12px] text-[#666D80] mt-[1px] leading-[1.3]">{{ $act['desc'] }}</div>@endif
                                <div class="text-[11px] text-[#A4ABB8] mt-[2px]">{{ $act['time'] }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="py-6 text-center text-[13px] text-[#666D80]">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>{{-- /content --}}
    </div>{{-- /main --}}
</div>{{-- /root --}}

</body>
</html>
