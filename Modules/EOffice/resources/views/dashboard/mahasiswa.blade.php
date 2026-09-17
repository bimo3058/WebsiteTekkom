<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Mahasiswa — E-Office SIPERKOM</title>
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

    // Data dari EOfficeController::mahasiswaDashboard
    $praktikumAktif    = $praktikumAktif    ?? null;   // model Praktikum|null
    $statusKp          = $statusKp          ?? null;   // string|null
    $pengumuman        = $pengumuman        ?? collect();
    $tugasMendatang    = $tugasMendatang    ?? collect();
    $absensiPct        = $absensiPct        ?? null;   // int 0-100 | null
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
                @include('eoffice::dashboard._header', ['dashboardRole' => 'mahasiswa'])
                <div class="eo-dashboard-content">
                    @include('eoffice::dashboard._summary', ['dashboardRole' => 'mahasiswa'])
                    @include('eoffice::dashboard._services', ['dashboardRole' => 'mahasiswa'])

            {{-- Bottom: Tugas Mendatang + Pengumuman --}}
            <div class="eo-two-grid">

                {{-- Tugas Mendatang --}}
                <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-w-0">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                        <div class="font-bold text-[15px] text-[#0D0D12]">Tugas yang Perlu Ditindaklanjuti</div>
                        @if($praktikumAktif)
                        <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
                           class="text-[12px] font-medium text-[#353849] px-3 py-[6px] rounded-[7px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">Lihat Semua</a>
                        @endif
                    </div>
                    <div class="overflow-y-auto flex-1">
                        @forelse($tugasMendatang as $t)
                        @php
                            $dl   = !empty($t['deadline']) ? \Carbon\Carbon::parse($t['deadline']) : null;
                            $sisa = $dl ? (int) now()->diffInDays($dl, false) : null;
                            $warn = $dl && $sisa <= 2;
                        @endphp
                        <div class="px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-[13px] font-semibold text-[#0D0D12] truncate"><a href="{{ $t['url'] ?? route('eoffice.manprak.mahasiswa.tugas.index') }}">{{ $t['judul'] ?? '—' }}</a></div>
                                    <div class="text-[11px] mt-[2px]" style="color:{{ $warn ? '#DF1C41' : '#666D80' }}">
                                        Deadline: {{ $dl ? $dl->format('d M Y H:i') : 'Belum ditentukan' }}
                                        @if($dl && !$warn) <span class="text-[#A4ABB8]">({{ $sisa }} hari)</span> @endif
                                    </div>
                                </div>
                                @if($t['revisi'] ?? false)
                                <span class="eo-status-label">Perlu revisi</span>
                                @elseif($dl && $dl->isPast())
                                <span class="eo-status-label">Tenggat lewat</span>
                                @elseif($t['sudah_kumpul'] ?? false)
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
                                <div class="text-[13px]">Tidak ada tugas yang perlu ditindaklanjuti.</div>
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
                        <div class="px-5 py-[12px] border-b border-[#F8F9FB] last:border-0 group {{ isset($peng->url) && $peng->url ? 'cursor-pointer hover:bg-[#F6F8FA] transition-colors' : '' }}"
                             @if(isset($peng->url) && $peng->url) onclick="window.location.href='{{ $peng->url }}'" @endif>
                            <div class="text-[13px] font-semibold text-[#0D0D12] group-hover:text-[#0B266E] transition-colors">{{ $peng->judul }}</div>
                            <div class="text-[12px] text-[#666D80] mt-[2px] line-clamp-2">{{ $peng->konten }}</div>
                            <div class="text-[11px] text-[#A4ABB8] mt-[5px]">{{ $peng->date }}</div>
                        </div>
                        @empty
                        <div class="py-8 text-center text-[13px] text-[#A4ABB8]">Belum ada pengumuman.</div>
                        @endforelse
                    </div>
                </div>

            </div>

                </div>{{-- /content --}}
            </section>
        </div>
    </main>
</div>

    <x-mobile-navigation />
</body>
</html>
