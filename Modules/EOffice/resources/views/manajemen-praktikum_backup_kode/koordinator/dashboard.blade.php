<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Koordinator — Manajemen Praktikum">
@php
    /** @var \Modules\EOffice\Models\Praktikum|null $praktikum */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $daftarAsprak */
    $name = auth()->user()->name;
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
    $pct = ($totalAsprak ?? 0) > 0 ? round(($asprakTerdistribusi ?? 0) / ($totalAsprak ?? 1) * 100) : 0;
@endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Dashboard Koordinator</h1>
            <span class="mp-badge sm" style="background:#E0E7FF;color:#6366F1;"><span class="dot"></span>Koor. Prak.</span>
            @if(isset($praktikum) && $praktikum)
            <span class="mp-badge sm" style="background:{{ $praktikum->status === 'aktif' ? '#DDF2EE' : '#ECEFF3' }};color:{{ $praktikum->status === 'aktif' ? '#174E43' : '#666D80' }};">
                <span class="dot" style="background:{{ $praktikum->status === 'aktif' ? '#40C4AA' : '#A4ABB8' }};"></span>
                {{ ucfirst($praktikum->status) }}
            </span>
            @endif
        </div>
        <p class="mp-page-sub">
            Halo, {{ $firstName }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}
            @if(isset($praktikum) && $praktikum) · <strong>{{ $praktikum->nama }}</strong> @endif
        </p>
        {{-- Hint jika koordinator punya banyak praktikum --}}
        @if(isset($allPraktikum) && $allPraktikum->count() > 1)
        <p class="mp-page-sub" style="margin-top:3px;color:#808897;">
            Anda mengampu {{ $allPraktikum->count() }} praktikum · Ganti tampilan via tombol di kanan atas
        </p>
        @endif
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.koor.pengumuman.index') }}" class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
            Pengumuman
        </a>
        <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            Bagi Modul
        </a>
    </div>
</div>

@if(!isset($praktikum) || !$praktikum)
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Anda belum ditunjuk sebagai koordinator praktikum manapun.</div>
    <div style="font-size:12px;">Hubungi dosen pengampu untuk mendapatkan penugasan.</div>
</div>
@else

{{-- Section: Ringkasan --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Ringkasan Praktikum</span>
    <span class="sec-rule"></span>
    <span style="font-size:12px;color:#666D80;">{{ $praktikum->nama }}</span>
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="mp-stat-label">Total Praktikan</div>
        <div class="mp-stat-value">{{ $totalPraktikan ?? 0 }}</div>
        <div class="mp-stat-sub">terdaftar</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5"/></svg>
        </div>
        <div class="mp-stat-label">Total Asisten Praktikum</div>
        <div class="mp-stat-value">{{ $totalAsprak ?? 0 }}</div>
        <div class="mp-stat-sub">asisten aktif</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="mp-stat-label">Asisten Praktikum Terdistribusi</div>
        <div class="mp-stat-value">{{ $asprakTerdistribusi ?? 0 }}</div>
        <div class="mp-stat-sub">sudah dapat modul</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        </div>
        <div class="mp-stat-label">Total Modul</div>
        <div class="mp-stat-value">{{ $totalModul ?? 0 }}</div>
        <div class="mp-stat-sub">modul tersedia</div>
    </div>
</div>

{{-- Section: Operasional --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Operasional</span>
    <span class="sec-rule"></span>
    @if(($asprakBelumModul ?? 0) > 0)
    <span class="mp-badge warning sm"><span class="dot"></span>{{ $asprakBelumModul }} asisten praktikum belum dapat modul</span>
    @endif
</div>

{{-- Progress Distribusi --}}
<div style="display:grid;grid-template-columns:1fr;gap:14px;flex-shrink:0;">

    {{-- Progress Distribusi Asisten Praktikum --}}
    <div class="mp-card" style="padding:20px;">
        <div style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Distribusi Asisten Praktikum ke Modul</div>
        <div class="flex items-end justify-between mb-3">
            <div style="font-size:13px;color:#666D80;">{{ $asprakTerdistribusi ?? 0 }} dari {{ $totalAsprak ?? 0 }} asisten praktikum sudah mendapat modul</div>
            <div style="font-size:28px;font-weight:700;color:#0B266E;line-height:1;letter-spacing:-.02em;">{{ $pct }}%</div>
        </div>
        <div style="width:100%;background:#ECEFF3;border-radius:999px;height:8px;overflow:hidden;">
            <div style="height:8px;border-radius:999px;width:{{ $pct }}%;background:linear-gradient(90deg,#0B266E,#4C619A);transition:width .4s ease;"></div>
        </div>
        @if(($asprakBelumModul ?? 0) > 0)
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#956321" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <span style="font-size:11px;color:#956321;font-weight:500;">{{ $asprakBelumModul }} asisten praktikum belum mendapat modul</span>
            <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}"
               style="font-size:11px;font-weight:700;color:#0B266E;text-decoration:none;margin-left:4px;" class="hover:underline">Bagi Modul →</a>
        </div>
        @else
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#287F6E" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:11px;color:#287F6E;font-weight:500;">Semua asisten praktikum sudah mendapat modul</span>
        </div>
        @endif
    </div>



</div>

{{-- Section: Tim & Pengumuman --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Tim &amp; Pengumuman</span>
    <span class="sec-rule"></span>
</div>

{{-- Bottom Grid: Daftar Asisten Praktikum + Pengumuman --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Daftar Asisten Praktikum --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Asisten Praktikum</span>
            <span class="mp-badge green sm">{{ $totalAsprak ?? 0 }} aktif</span>
            <div class="right">
                <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Bagi Modul</a>
            </div>
        </div>
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
            <div class="mp-th flex-1">Nama Asisten Praktikum</div>
            <div class="mp-th" style="width:90px;">NIM</div>
            <div class="mp-th" style="width:140px;">Modul Diampu</div>
            <div class="mp-th" style="width:80px;text-align:center;">Status</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
                <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                    <div class="mp-av green">{{ strtoupper(substr($a->user?->name ?? 'A', 0, 1)) }}{{ strtoupper(substr($a->user?->name ?? 'A', strpos(($a->user?->name ?? 'A').' ',' ')+1, 1)) }}</div>
                    <span style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate">{{ $a->user?->name ?? '—' }}</span>
                </div>
                <div style="width:90px;font-size:12px;color:#666D80;font-family:ui-monospace,monospace;">{{ $a->user?->student_number ?? '—' }}</div>
                <div style="width:140px;font-size:12px;color:#353849;" class="truncate">
                    {{ $a->modulAsprak->pluck('modul.nama')->join(', ') ?: '—' }}
                </div>
                <div style="width:80px;text-align:center;">
                    <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asisten praktikum.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumuman</span>
            <div class="right">
                <a href="{{ route('eoffice.manprak.koor.pengumuman.index') }}" class="mp-btn primary sm" style="text-decoration:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Buat
                </a>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumuman ?? [] as $peng)
            <div style="padding:14px 20px;border-bottom:1px solid #ECEFF3;">
                <div style="font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:4px;">{{ $peng->judul }}</div>
                <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2">{{ $peng->konten }}</div>
                <div style="font-size:11px;color:#808897;margin-top:6px;">{{ $peng->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pengumuman.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>