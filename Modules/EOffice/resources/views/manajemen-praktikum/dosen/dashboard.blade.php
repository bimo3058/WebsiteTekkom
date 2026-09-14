<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard">
@php
    $name      = auth()->user()->name;
    $firstName = explode(' ', $name)[0];
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
@endphp

<x-slot name="header">
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">Dashboard Dosen</h1>
            <span style="font-size:10px; font-weight:600; color:var(--c-primary); background:rgba(94,83,244,0.09); border:1px solid rgba(94,83,244,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Dosen</span>
        </div>
        <p style="font-size:12px; color:var(--c-fg-muted);">
            Selamat datang, <span style="color:var(--c-fg); font-weight:600;">{{ $firstName }}</span>
            <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
            <span style="margin-left:4px; color:var(--c-fg-muted);">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}</span>
        </p>
    </div>
</div>
</x-slot>

{{-- ═══════════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════════ --}}
<div class="mp-stats-grid cols-4" style="flex-shrink:0;">

    {{-- Praktikum Aktif --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Praktikum Aktif</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Saat Ini</div>
    </div>

    {{-- Total Praktikan --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Total Praktikan</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ $totalMahasiswa ?? 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Mahasiswa Terdaftar</div>
    </div>

    {{-- Persetujuan Nilai --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon red">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Persetujuan Nilai</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ $nilaiMenungguApproval ?? 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Belum Disetujui</div>
    </div>

    {{-- Pendaftaran Koordinator --}}
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon yellow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg); margin:0;">Pendaftaran Koor</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;">{{ isset($pendaftaranKoorPending) ? $pendaftaranKoorPending->count() : 0 }}</div>
        <div class="mp-stat-sub" style="font-size:13px;">Perlu Peninjauan</div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MIDDLE ROW: Daftar Praktikum & Pendaftaran
═══════════════════════════════════════════════ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; flex:1; min-height:0;">

    {{-- Panel Daftar Praktikum --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border); flex-shrink:0;">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Praktikum</div>
            <a href="{{ route('eoffice.manprak.dosen.praktikum.index') }}"
               style="font-size:12px; font-weight:600; color:var(--c-primary); text-decoration:none;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:grid; grid-template-columns:1fr 280px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA; flex-shrink:0;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Status</div>
        </div>
        <div style="flex:1; overflow-y:auto; min-height:0;">
            @forelse($praktikums ?? [] as $p)
            <div style="display:grid; grid-template-columns:1fr 280px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
                <div style="font-size:13px; font-weight:600; color:var(--c-fg);">{{ $p->nama }}</div>
                <div style="text-align:right;">
                    @if($p->status === 'aktif')
                    <span class="mp-badge success sm">Aktif</span>
                    @else
                    <span class="mp-badge neutral sm">Tutup</span>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; height:100%;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Belum ada praktikum yang diampu.</span>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Panel Pendaftaran --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; height:100%;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border); flex-shrink:0;">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Pendaftaran Koordinator Praktikum</div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 280px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA; flex-shrink:0;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Mahasiswa</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Nama Praktikum</div>
        </div>
        <div style="flex:1; overflow-y:auto; min-height:0;">
            @forelse($pendaftaranKoorPending as $pend)
            <div style="display:grid; grid-template-columns:1fr 280px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
                <div>
                    <div style="font-size:13px; font-weight:600; color:var(--c-fg);">{{ $pend->user?->name ?? 'Mahasiswa' }}</div>
                    <div style="font-size:12px; color:var(--c-fg-muted); margin-top: 2px;">IPK: {{ number_format($pend->ipk ?? 0, 2) }}</div>
                </div>
                <div style="text-align:right;">
                    <span style="display:inline-block; font-size:11px; font-weight:600; background:rgba(94,83,244,0.1); color:var(--c-primary); border:1px solid rgba(94,83,244,0.2); padding:4px 10px; border-radius:8px; white-space:nowrap;">{{ $pend->praktikum->nama }}</span>
                </div>
            </div>
            @empty
            <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px; height:100%;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                    <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"/>
                </svg>
                <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Tidak ada pendaftaran yang perlu ditinjau.</span>
            </div>
            @endforelse
        </div>
    </div>

</div>
</x-eoffice::manajemen-praktikum.layout>