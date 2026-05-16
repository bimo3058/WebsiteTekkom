<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Mahasiswa — E-Office SIPERKOM</title>
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

    $iDashboard = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iPraktikum  = "M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z";
    $iKP         = "M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15";
    $iLogout     = "M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238";

    // Data dari EOfficeController::mahasiswaDashboard
    $praktikumAktif    = $praktikumAktif    ?? null;   // model Praktikum|null
    $statusKp          = $statusKp          ?? null;   // string|null
    $pengumuman        = $pengumuman        ?? collect();
    $tugasMendatang    = $tugasMendatang    ?? collect();
    $absensiPct        = $absensiPct        ?? null;   // int 0-100 | null
@endphp

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('eo_sb') !== '0' }"
     x-init="$watch('sidebarOpen', v => localStorage.setItem('eo_sb', v ? '1' : '0'))">

    @include('eoffice::dashboard._sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <div class="flex items-center justify-between px-6 bg-white border-b border-[#DFE1E7] flex-shrink-0" style="height:56px;">
            <div class="flex items-center gap-3 min-w-0">
                <div>
                    <div class="font-bold text-[15px] text-[#0D0D12] leading-[1.2]">Dashboard</div>
                    <div class="text-[11px] text-[#666D80]">Modul E-Office · SIPERKOM UNDIP</div>
                </div>
                <span class="text-[11px] font-semibold px-[9px] py-[3px] rounded-full whitespace-nowrap"
                      style="background:#F9ECCB; color:#7C5309;">Mahasiswa</span>
                @if($user->student)
                <span class="text-[11px] text-[#A4ABB8]">{{ $user->student->student_number }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <div class="relative flex items-center justify-center w-[34px] h-[34px] rounded-lg border border-[#DFE1E7] bg-white cursor-pointer transition-colors hover:bg-[#F6F8FA]">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-[18px]">

            {{-- Welcome banner --}}
            <div class="flex items-center justify-between rounded-[14px] px-6 py-5 text-white flex-shrink-0"
                 style="background:linear-gradient(120deg,#7C5309 0%,#D39C3D 100%);">
                <div>
                    <div class="text-[18px] font-bold tracking-tight">Halo, {{ $name }}!</div>
                    <div class="text-[12px] opacity-75 mt-1">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel ?? 'Semester Genap 2025/2026' }}
                    </div>
                </div>
                <div class="flex gap-3 flex-shrink-0">
                    <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
                        <div class="text-[20px] font-bold">{{ $tugasMendatang->count() }}</div>
                        <div class="text-[10px] opacity-75 mt-[2px]">Tugas Pending</div>
                    </div>
                    @if($absensiPct !== null)
                    <div class="rounded-[10px] px-4 py-[10px] text-center" style="background:rgba(255,255,255,0.15);">
                        <div class="text-[20px] font-bold">{{ $absensiPct }}%</div>
                        <div class="text-[10px] opacity-75 mt-[2px]">Kehadiran</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Grid atas: Info Praktikum + Status KP --}}
            <div class="grid grid-cols-2 gap-[14px] flex-shrink-0">

                {{-- Praktikum Aktif --}}
                @if($praktikumAktif)
                <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-[11px] font-semibold text-[#A4ABB8] uppercase tracking-wider mb-1">Praktikum Aktif</div>
                            <div class="text-[15px] font-bold text-[#0D0D12]">{{ $praktikumAktif->nama }}</div>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Aktif</span>
                    </div>
                    <div class="text-[12px] text-[#666D80] mb-1">
                        Kode: <span class="font-semibold text-[#D39C3D]">{{ $praktikumAktif->kode ?? '—' }}</span>
                    </div>
                    <div class="text-[12px] text-[#666D80] mb-3">
                        Dosen: <span class="font-semibold text-[#353849]">{{ $praktikumAktif->dosen?->name ?? '—' }}</span>
                    </div>
                    @if($absensiPct !== null)
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-[11px] text-[#666D80]">Kehadiran</span>
                        <span class="text-[11px] font-bold" style="color:{{ $absensiPct >= 75 ? '#40C4AA' : '#DF1C41' }}">{{ $absensiPct }}%</span>
                    </div>
                    <div class="w-full bg-[#F0F1F4] rounded-full h-[6px]">
                        <div class="h-[6px] rounded-full" style="width:{{ $absensiPct }}%; background:{{ $absensiPct >= 75 ? '#40C4AA' : '#DF1C41' }};"></div>
                    </div>
                    @endif
                    <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
                       class="mt-3 inline-flex items-center gap-1 text-[12px] font-semibold no-underline" style="color:#D39C3D;">
                        Lihat Detail
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
                @else
                <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex flex-col items-center justify-center text-center">
                    <svg class="mb-2 text-[#DFE1E7]" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <path d="{{ $iPraktikum }}"/>
                    </svg>
                    <div class="text-[13px] font-semibold text-[#353849]">Belum Terdaftar Praktikum</div>
                    <div class="text-[11px] text-[#A4ABB8] mt-1 mb-3">Masukkan kode untuk bergabung ke kelas</div>
                    <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
                       class="text-[12px] font-semibold px-4 py-[7px] rounded-[8px] no-underline text-white"
                       style="background:#D39C3D;">Masukkan Kode</a>
                </div>
                @endif

                {{-- Status KP --}}
                <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
                    <div class="text-[11px] font-semibold text-[#A4ABB8] uppercase tracking-wider mb-3">Kerja Praktik (KP)</div>
                    @if($statusKp)
                    @php
                        $kpColors = [
                            'Pra-KP'     => ['#D1F0F9','#106A97'],
                            'KP Berjalan'=> ['#DDF2EE','#40C4AA'],
                            'Selesai'    => ['#F0E6FA','#9B59B6'],
                            'default'    => ['#F0F1F4','#666D80'],
                        ];
                        [$kpBg, $kpFg] = $kpColors[$statusKp] ?? $kpColors['default'];
                    @endphp
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[12px] font-bold px-3 py-[4px] rounded-full"
                              style="background:{{ $kpBg }}; color:{{ $kpFg }};">{{ $statusKp }}</span>
                    </div>
                    <a href="{{ route('eoffice.kp.dashboard') }}"
                       class="text-[12px] font-semibold no-underline" style="color:#106A97;">
                        Pantau Progress →
                    </a>
                    @else
                    <div class="text-[13px] text-[#A4ABB8] mb-3">Belum mendaftar KP.</div>
                    <a href="{{ route('eoffice.kp.register') }}"
                       class="text-[12px] font-semibold px-4 py-[7px] rounded-[8px] no-underline text-white inline-block"
                       style="background:#106A97;">Daftar KP</a>
                    @endif
                </div>
            </div>

            {{-- Bottom: Tugas Mendatang + Pengumuman --}}
            <div class="flex gap-[14px] flex-1 min-h-0 mb-1">

                {{-- Tugas Mendatang --}}
                <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                        <div class="font-bold text-[15px] text-[#0D0D12]">Tugas Mendatang</div>
                        @if($praktikumAktif)
                        <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
                           class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
                        @endif
                    </div>
                    <div class="overflow-y-auto flex-1">
                        @forelse($tugasMendatang as $t)
                        @php
                            $dl   = \Carbon\Carbon::parse($t['deadline'] ?? now()->addDay());
                            $sisa = now()->diffInDays($dl, false);
                            $warn = $sisa <= 2;
                        @endphp
                        <div class="px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $t['judul'] ?? '—' }}</div>
                                    <div class="text-[11px] mt-[2px]" style="color:{{ $warn ? '#DF1C41' : '#666D80' }}">
                                        Deadline: {{ $dl->format('d M Y') }}
                                        @if(!$warn) <span class="text-[#A4ABB8]">({{ $sisa }} hari)</span> @endif
                                    </div>
                                </div>
                                @if($t['sudah_kumpul'] ?? false)
                                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43] flex-shrink-0">✓ Dikumpul</span>
                                @elseif($warn)
                                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#FADAE1] text-[#7C1028] flex-shrink-0">Segera!</span>
                                @else
                                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0F1F4] text-[#666D80] flex-shrink-0">Pending</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="flex-1 flex items-center justify-center py-10">
                            <div class="text-center text-[#A4ABB8]">
                                <svg class="mx-auto mb-2" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <div class="text-[13px]">Tidak ada tugas mendatang 🎉</div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pengumuman --}}
                <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
                    <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                        <div class="font-bold text-[15px] text-[#0D0D12]">Pengumuman</div>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        @forelse($pengumuman as $peng)
                        <div class="px-5 py-[12px] border-b border-[#F8F9FB] last:border-0">
                            <div class="text-[13px] font-semibold text-[#0D0D12]">{{ $peng->judul }}</div>
                            <div class="text-[12px] text-[#666D80] mt-[2px] line-clamp-2">{{ $peng->konten }}</div>
                            <div class="text-[11px] text-[#A4ABB8] mt-[5px]">{{ $peng->created_at?->diffForHumans() }}</div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-[13px] text-[#A4ABB8]">Belum ada pengumuman.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>{{-- /content --}}
    </div>{{-- /main --}}
</div>

</body>
</html>
