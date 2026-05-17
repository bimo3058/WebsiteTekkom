<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Asisten Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Dashboard Asprak</h1>
            <span class="mp-badge success sm">Asprak</span>
        </div>
        <p class="mp-page-sub">
            Halo, {{ $name }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($asprak?->praktikum) · {{ $asprak->praktikum->nama }} @endif
        </p>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Tugas Perlu Dinilai</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $tugasPendingNilai }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Modul Diampu</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $totalModul }}</div>
        </div>
    </div>
</div>

{{-- Praktikum switcher --}}
@if(isset($allAsprak) && $allAsprak->count() > 1)
<div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
    <span style="font-size:11px;color:var(--c-fg-muted);">Praktikum:</span>
    @foreach($allAsprak as $ap)
    <a href="{{ route('eoffice.manprak.asprak.dashboard') }}?praktikum_id={{ $ap->praktikum_id }}"
       class="{{ $ap->id === $asprak->id ? 'mp-btn primary sm' : 'mp-btn secondary sm' }}"
       style="text-decoration:none;">
        {{ $ap->praktikum?->nama ?? 'Praktikum' }}
    </a>
    @endforeach
</div>
@endif

@if(!$asprak)
<div class="mp-alert warning flex-shrink-0">
    <div style="font-size:13px;font-weight:600;">Status asisten praktikum Anda belum aktif.</div>
    <div style="font-size:12px;margin-top:4px;">Hubungi koordinator untuk mengaktifkan akun asprak Anda.</div>
</div>
@else

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-label">Modul Diampu</div>
        <div class="mp-stat-value">{{ $totalModul }}</div>
        <div class="mp-stat-sub">modul dikelola</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Materi</div>
        <div class="mp-stat-value">{{ $totalMateri }}</div>
        <div class="mp-stat-sub">materi diunggah</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Tugas Perlu Dinilai</div>
        <div class="mp-stat-value">{{ $tugasPendingNilai }}</div>
        <div class="mp-stat-sub">pengumpulan masuk</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Absensi Hari Ini</div>
        <div class="mp-stat-value">{{ $absensiHariIni }}</div>
        <div class="mp-stat-sub">sesi tercatat</div>
    </div>
</div>

{{-- Modul Cards --}}
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Modul yang Diampu</span>
        <a href="{{ route('eoffice.manprak.asprak.materi.index') }}" class="mp-btn success sm" style="text-decoration:none;">+ Upload Materi</a>
    </div>
    @if($modulDiampu->isEmpty())
    <div style="padding:24px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada modul yang diampu.</div>
    @else
    <div class="grid grid-cols-3 gap-3 p-5">
        @foreach($modulDiampu as $m)
        <div style="border:1px solid var(--c-border);border-radius:10px;padding:16px;" class="hover:border-[#40C4AA] transition-colors">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);margin-bottom:4px;">{{ $m->nama ?? 'Modul' }}</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-bottom:12px;">{{ $m->deskripsi ?? '—' }}</div>
            <div class="flex gap-2">
                <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
                   class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Absensi</a>
                <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
                   class="mp-btn success sm flex-1 text-center" style="text-decoration:none;">Tugas</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Bottom: Pengumpulan Pending + Tugas Mendatang --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Pengumpulan perlu review --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <div>
                <span class="mp-card-title">Pengumpulan Tugas — Perlu Review</span>
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">Belum diberi nilai</div>
            </div>
            <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumpulanPending ?? [] as $peng)
            <div class="mp-tr flex items-center justify-between" style="padding:11px 20px;">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#0D6B55,#40C4AA);">
                        {{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:500;color:var(--c-fg);" class="truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);">{{ $peng->tugas?->judul ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span style="font-size:11px;color:var(--c-fg-placeholder);">{{ $peng->created_at?->diffForHumans() }}</span>
                    <a href="{{ route('eoffice.manprak.asprak.tugas.pengumpulan', $peng->tugas_id) }}"
                       class="mp-btn ghost sm" style="text-decoration:none;">Nilai</a>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Semua pengumpulan sudah dinilai 🎉</div>
            @endforelse
        </div>
    </div>

    {{-- Tugas Mendatang --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Tugas Mendatang</span>
            <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn primary sm" style="text-decoration:none;">+ Buat Tugas</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($tugasMendatang ?? [] as $t)
            @php
                $sisa = now()->diffInDays(\Carbon\Carbon::parse($t->deadline), false);
                $warn = $sisa <= 2;
            @endphp
            <div class="flex items-start gap-3" style="padding:11px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="w-[6px] h-[6px] rounded-full mt-[6px] flex-shrink-0"
                     style="background:{{ $warn ? '#DF1C41' : '#40C4AA' }};"></div>
                <div class="flex-1 min-w-0">
                    <div style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $t->judul }}</div>
                    <div style="font-size:11px;margin-top:2px;color:{{ $warn ? '#DF1C41' : 'var(--c-fg-muted)' }};">
                        Deadline: {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y, H:i') }}
                        @if($warn) ({{ $sisa }} hari lagi) @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Tidak ada tugas mendatang.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
