<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Dosen — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Dashboard Dosen</h1>
            <span class="mp-badge primary sm">Dosen</span>
        </div>
        <p class="mp-page-sub">Selamat datang, {{ $name }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel ?? 'Semester Genap 2025/2026' }}</p>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Praktikum Diampu</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $totalPraktikumDiampu ?? 0 }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Nilai Pending</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $nilaiPending ?? 0 }}</div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-label">Praktikum Aktif</div>
        <div class="mp-stat-value">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub">diampu semester ini</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Mahasiswa</div>
        <div class="mp-stat-value">{{ $totalMahasiswa ?? 0 }}</div>
        <div class="mp-stat-sub">semua praktikum</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Modul</div>
        <div class="mp-stat-value">{{ $totalModul ?? 0 }}</div>
        <div class="mp-stat-sub">modul tersedia</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Nilai Pending</div>
        <div class="mp-stat-value">{{ $nilaiPending ?? 0 }}</div>
        <div class="mp-stat-sub">perlu diinput</div>
    </div>
</div>

{{-- Daftar Praktikum yang Diampu --}}
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <div>
            <span class="mp-card-title">Praktikum yang Diampu</span>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $semesterLabel ?? 'Semester Genap 2025/2026' }}</div>
        </div>
    </div>

    @if($praktikums->isEmpty())
    <div style="padding:48px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada praktikum yang diampu.</div>
    @else
    <div class="grid grid-cols-2 gap-4 p-5">
        @foreach($praktikums as $p)
        <div style="border:1px solid var(--c-border);border-radius:12px;padding:16px;" class="hover:border-[#C1C7CF] transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div style="font-size:13px;font-weight:700;color:var(--c-fg);">{{ $p->nama }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Kode: <span style="font-weight:600;color:var(--c-primary);">{{ $p->kode ?? '—' }}</span></div>
                </div>
                @if($p->status === 'aktif')
                    <span class="mp-badge success sm">Aktif</span>
                @else
                    <span class="mp-badge neutral sm">Nonaktif</span>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-2 mb-3">
                <div style="text-align:center;padding:8px;background:var(--c-bg-sub);border-radius:8px;">
                    <div style="font-size:16px;font-weight:700;color:var(--c-fg);">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                    <div style="font-size:10px;color:var(--c-fg-muted);">Mahasiswa</div>
                </div>
                <div style="text-align:center;padding:8px;background:var(--c-bg-sub);border-radius:8px;">
                    <div style="font-size:16px;font-weight:700;color:var(--c-fg);">{{ $p->modul_count ?? 0 }}</div>
                    <div style="font-size:10px;color:var(--c-fg-muted);">Modul</div>
                </div>
                <div style="text-align:center;padding:8px;background:var(--c-bg-sub);border-radius:8px;">
                    <div style="font-size:16px;font-weight:700;color:var(--c-fg);">{{ $p->asisten_praktikum_count ?? 0 }}</div>
                    <div style="font-size:10px;color:var(--c-fg-muted);">Asprak</div>
                </div>
            </div>

            <div class="flex items-center gap-2 mb-3">
                <span style="font-size:11px;color:var(--c-fg-muted);">Koordinator:</span>
                @if($p->koordinator)
                <span style="font-size:11px;font-weight:600;color:var(--c-fg);">{{ $p->koordinator->name }}</span>
                @else
                <span style="font-size:11px;font-weight:600;color:#DF1C41;">Belum ditunjuk</span>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
                   class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Lihat Modul</a>
                <a href="{{ route('eoffice.manprak.dosen.nilai.index', $p->id) }}"
                   class="mp-btn primary sm flex-1 text-center" style="text-decoration:none;">Input Nilai</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Perlu Tindakan: Tunjuk Koordinator --}}
@if($praktikumTanpaKoor->isNotEmpty())
<div class="mp-alert error flex-shrink-0">
    <div class="flex items-center gap-2 mb-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-size:13px;font-weight:700;">Praktikum Belum Punya Koordinator</span>
    </div>
    <div class="flex flex-col gap-2">
        @foreach($praktikumTanpaKoor as $p)
        <div class="flex items-center justify-between bg-white rounded-[8px] px-3 py-2">
            <span style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $p->nama }}</span>
            <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                <input type="text" name="nim" placeholder="NIM Koordinator" class="mp-input" style="width:150px;font-size:12px;">
                <button type="submit" class="mp-btn primary sm">Tunjuk</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
