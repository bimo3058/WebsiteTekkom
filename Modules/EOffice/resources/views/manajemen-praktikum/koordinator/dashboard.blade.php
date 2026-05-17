<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Koordinator — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Dashboard Koordinator</h1>
            <span class="mp-badge sky sm">Koordinator</span>
        </div>
        <p class="mp-page-sub">
            Selamat datang, {{ $name }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($praktikum) · Koordinator {{ $praktikum->nama }} @endif
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Asisten Praktikum</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $totalAsprak }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Belum dapat Modul</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $asprakBelumModul }}</div>
        </div>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;">Anda belum ditunjuk sebagai koordinator praktikum manapun.</div>
    <div style="font-size:12px;margin-top:4px;">Hubungi dosen pengampu untuk mendapatkan penugasan.</div>
</div>
@else

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-label">Total Praktikan</div>
        <div class="mp-stat-value">{{ $totalPraktikan }}</div>
        <div class="mp-stat-sub">terdaftar</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Asprak</div>
        <div class="mp-stat-value">{{ $totalAsprak }}</div>
        <div class="mp-stat-sub">asisten aktif</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Asprak Terdistribusi</div>
        <div class="mp-stat-value">{{ $asprakTerdistribusi }}</div>
        <div class="mp-stat-sub">sudah dapat modul</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Modul</div>
        <div class="mp-stat-value">{{ $totalModul }}</div>
        <div class="mp-stat-sub">modul tersedia</div>
    </div>
</div>

{{-- Progress Distribusi Asprak --}}
@php
$pct = $totalAsprak > 0 ? round($asprakTerdistribusi / $totalAsprak * 100) : 0;
@endphp
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div style="font-weight:700;font-size:14px;color:var(--c-fg);">Progress Distribusi Asprak ke Modul</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $asprakTerdistribusi }} dari {{ $totalAsprak }} asprak sudah mendapat modul</div>
        </div>
        <div style="font-size:24px;font-weight:700;color:var(--c-primary);">{{ $pct }}%</div>
    </div>
    <div style="width:100%;background:var(--c-bg-sub);border-radius:9999px;height:8px;">
        <div style="height:8px;border-radius:9999px;width:{{ $pct }}%;background:linear-gradient(90deg,#106A97,#40C4AA);transition:all .3s;"></div>
    </div>
    @if($asprakBelumModul > 0)
    <div class="mt-2 flex items-center gap-2">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        <span style="font-size:11px;color:#7C5309;">{{ $asprakBelumModul }} asprak belum mendapat modul — segera distribusikan</span>
        <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}"
           style="font-size:11px;font-weight:600;color:var(--c-primary);text-decoration:none;" class="hover:underline">Bagi Modul →</a>
    </div>
    @endif
</div>

{{-- Kode Praktikum --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div class="flex items-center justify-between gap-4">
        <div>
            <div style="font-weight:700;font-size:14px;color:var(--c-fg);">Kode Join Praktikum</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">Bagikan kode ini ke praktikan yang sudah diverifikasi IRS agar mereka bisa bergabung ke kelas.</div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            @if($praktikum->kode)
            <div class="flex items-center gap-2 px-4 py-2 rounded-[10px]" style="background:#EEF2FF;border:1px solid #C7D2FE;">
                <span id="kode-praktikum-text" style="font-size:22px;font-weight:700;letter-spacing:.15em;color:var(--c-primary);font-family:monospace;" class="select-all">{{ $praktikum->kode }}</span>
                <button onclick="navigator.clipboard.writeText('{{ $praktikum->kode }}').then(()=>this.textContent='✓').catch(()=>{}); setTimeout(()=>this.textContent='Salin',1500)"
                    class="mp-btn secondary sm" style="font-size:11px;">Salin</button>
            </div>
            @else
            <div style="padding:8px 16px;background:var(--c-bg-sub);border-radius:10px;font-size:13px;color:var(--c-fg-placeholder);font-style:italic;">Belum ada kode</div>
            @endif
            <form method="POST" action="{{ route('eoffice.manprak.koor.praktikum.generate-kode') }}">
                @csrf
                <button class="mp-btn primary md">
                    {{ $praktikum->kode ? '↺ Buat Kode Baru' : '+ Generate Kode' }}
                </button>
            </form>
        </div>
    </div>
    @if($praktikum->kode)
    <div class="mt-3 p-3 rounded-[8px]" style="background:#F9ECCB;border:1px solid #D39C3D;font-size:11px;color:#7C5309;">
        Kode aktif akan hangus jika kamu generate kode baru. Pastikan semua praktikan yang berhak sudah bergabung sebelum mereset.
    </div>
    @endif
</div>

{{-- Bottom Grid: Daftar Asprak + Pengumuman --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Daftar Asprak --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Daftar Asisten Praktikum</span>
            <a href="{{ route('eoffice.manprak.koor.bagi-modul.index') }}" class="mp-btn secondary sm">Bagi Modul</a>
        </div>
        <div class="mp-card-body" style="flex-shrink:0;">
            <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <div class="mp-th flex-1">Nama</div>
                <div class="mp-th" style="width:80px;">NIM</div>
                <div class="mp-th" style="width:120px;">Modul</div>
                <div class="mp-th" style="width:70px;">Status</div>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div class="mp-tr" style="display:flex;align-items:center;padding:10px 20px;">
                <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                    <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1a6691,#40C4AA);">
                        {{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <span style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $a->user?->name ?? '—' }}</span>
                </div>
                <div style="width:80px;font-size:12px;color:var(--c-fg-muted);">{{ $a->user?->student_number ?? '—' }}</div>
                <div style="width:120px;font-size:12px;color:var(--c-fg-sub);">
                    {{ $a->modulAsprak->pluck('modul.nama')->join(', ') ?: '—' }}
                </div>
                <div style="width:70px;">
                    <span class="mp-badge success sm">Aktif</span>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada asisten praktikum.</div>
            @endforelse
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumuman</span>
            <a href="{{ route('eoffice.manprak.koor.pengumuman.index') }}" class="mp-btn primary sm">+ Buat</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumuman ?? [] as $peng)
            <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-size:13px;font-weight:600;color:var(--c-fg);">{{ $peng->judul }}</div>
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:4px;" class="line-clamp-2">{{ $peng->konten }}</div>
                <div style="font-size:11px;color:var(--c-fg-placeholder);margin-top:6px;">{{ $peng->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
