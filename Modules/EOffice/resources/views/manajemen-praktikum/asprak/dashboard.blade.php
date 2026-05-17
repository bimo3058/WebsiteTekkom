<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Asisten Praktikum">
@php
    $name = auth()->user()->name;
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
@endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Dashboard Asprak</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">
            Halo, {{ $firstName }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if(isset($asprak) && $asprak?->praktikum) · {{ $asprak->praktikum->nama }} @endif
        </p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas
        </a>
        <a href="{{ route('eoffice.manprak.asprak.materi.index') }}" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            Upload Materi
        </a>
    </div>
</div>

{{-- Praktikum switcher --}}
@if(isset($allAsprak) && $allAsprak->count() > 1)
<div style="display:flex;align-items:center;gap:8px;flex-shrink:0;flex-wrap:wrap;padding:12px 0;border-bottom:1px solid #DFE1E7;">
    <span style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;">Praktikum:</span>
    @foreach($allAsprak as $ap)
    <a href="{{ route('eoffice.manprak.asprak.dashboard') }}?praktikum_id={{ $ap->praktikum_id }}"
       class="{{ $ap->id === $asprak->id ? 'mp-btn primary sm' : 'mp-btn secondary sm' }}"
       style="text-decoration:none;">
        {{ $ap->praktikum?->nama ?? 'Praktikum' }}
    </a>
    @endforeach
</div>
@endif

@if(!isset($asprak) || !$asprak)
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Status asisten praktikum Anda belum aktif.</div>
    <div style="font-size:12px;">Hubungi koordinator untuk mengaktifkan akun asprak Anda.</div>
</div>
@else

{{-- Section: Ringkasan --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Ringkasan</span>
    <span class="sec-rule"></span>
    @if(($tugasPendingNilai ?? 0) > 0)
    <span class="mp-badge warning sm"><span class="dot"></span>{{ $tugasPendingNilai }} perlu dinilai</span>
    @endif
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="mp-stat-label">Modul Diampu</div>
        <div class="mp-stat-value">{{ $totalModul ?? 0 }}</div>
        <div class="mp-stat-sub">modul dikelola</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
        </div>
        <div class="mp-stat-label">Total Materi</div>
        <div class="mp-stat-value">{{ $totalMateri ?? 0 }}</div>
        <div class="mp-stat-sub">materi diunggah</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </div>
        <div class="mp-stat-label">Tugas Perlu Dinilai</div>
        <div class="mp-stat-value">{{ $tugasPendingNilai ?? 0 }}</div>
        <div class="mp-stat-sub">pengumpulan masuk</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="mp-stat-label">Absensi Hari Ini</div>
        <div class="mp-stat-value">{{ $absensiHariIni ?? 0 }}</div>
        <div class="mp-stat-sub">sesi tercatat</div>
    </div>
</div>

{{-- Section: Modul Diampu --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Modul yang Diampu</span>
    <span class="sec-rule"></span>
    @if(isset($modulDiampu) && !$modulDiampu->isEmpty())
    <span class="mp-badge navy sm">{{ $modulDiampu->count() }} modul</span>
    @endif
</div>

<div class="mp-card flex-shrink-0">
    @if(!isset($modulDiampu) || $modulDiampu->isEmpty())
    <div style="padding:40px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang diampu.</div>
        <div style="font-size:12px;color:#808897;margin-top:4px;">Hubungi koordinator untuk pendistribusian modul.</div>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:16px;">
        @foreach($modulDiampu as $m)
        <div style="border:1px solid #DFE1E7;border-radius:14px;padding:16px;transition:border-color .15s,box-shadow .15s;"
             onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
             onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
                <div class="mp-stat-icon navy" style="flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:3px;" class="truncate">{{ $m->nama ?? 'Modul' }}</div>
                    <div style="font-size:11px;color:#666D80;" class="line-clamp-2">{{ $m->deskripsi ?? '—' }}</div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
                   class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Absensi</a>
                <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
                   class="mp-btn primary sm flex-1 text-center" style="text-decoration:none;">Tugas</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Section: Aktivitas --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Aktivitas</span>
    <span class="sec-rule"></span>
</div>

{{-- Bottom: Pengumpulan Pending + Tugas Mendatang --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Pengumpulan perlu review --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumpulan Tugas</span>
            @if(($tugasPendingNilai ?? 0) > 0)
            <span class="mp-badge warning sm"><span class="dot"></span>{{ $tugasPendingNilai }} belum dinilai</span>
            @endif
            <div class="right">
                <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua →</a>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumpulanPending ?? [] as $peng)
            <div class="mp-tr flex items-center justify-between" style="padding:12px 20px;">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="mp-av green">{{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 1)) }}{{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', strpos(($peng->daftarPraktikan?->user?->name ?? 'M').' ',' ')+1, 1)) }}</div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#666D80;">{{ $peng->tugas?->judul ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span style="font-size:11px;color:#808897;">{{ $peng->created_at?->diffForHumans() }}</span>
                    <a href="{{ route('eoffice.manprak.asprak.tugas.pengumpulan', $peng->tugas_id) }}"
                       class="mp-btn ghost sm" style="text-decoration:none;">Nilai</a>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Semua pengumpulan sudah dinilai.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Tugas Mendatang --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Tugas Mendatang</span>
            <div class="right">
                <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn primary sm" style="text-decoration:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat
                </a>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($tugasMendatang ?? [] as $t)
            @php
                $sisa = now()->diffInDays(\Carbon\Carbon::parse($t->deadline), false);
                $warn = $sisa <= 2;
            @endphp
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                <div style="width:6px;height:6px;border-radius:999px;margin-top:6px;flex-shrink:0;background:{{ $warn ? '#DF1C41' : '#40C4AA' }};"></div>
                <div class="flex-1 min-w-0">
                    <div style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $t->judul }}</div>
                    <div style="font-size:11px;margin-top:3px;color:{{ $warn ? '#DF1C41' : '#666D80' }};font-weight:{{ $warn ? '600' : '400' }};">
                        Deadline: {{ \Carbon\Carbon::parse($t->deadline)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                        @if($warn && $sisa >= 0) <span style="opacity:.8;">({{ $sisa }} hari lagi)</span> @endif
                        @if($sisa < 0) <span>(sudah lewat)</span> @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada tugas mendatang.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
