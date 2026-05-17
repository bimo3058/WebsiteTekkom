<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Modul — Manajemen Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Modul</h1>
        <p class="mp-page-sub">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($terdaftarDi) · {{ $terdaftarDi->nama }} @endif
        </p>
    </div>
    <div style="text-align:right;">
        <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Total Modul</div>
        <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $modulList->count() }}</div>
    </div>
</div>

{{-- Switcher praktikum jika ikut lebih dari 1 --}}
@if($daftarPraktikan->count() > 1)
<div class="flex flex-wrap gap-2 flex-shrink-0">
    @foreach($daftarPraktikan as $dp)
    <a href="{{ route('eoffice.manprak.mahasiswa.modul.index') }}?praktikum_id={{ $dp->praktikum_id }}"
       class="{{ $dp->praktikum_id === $terdaftarDi?->id ? 'mp-btn primary sm' : 'mp-btn secondary sm' }}"
       style="text-decoration:none;">
        {{ $dp->praktikum?->nama ?? 'Praktikum' }}
    </a>
    @endforeach
</div>
@endif

@if(!$terdaftarDi)
<div class="mp-card flex-shrink-0" style="padding:32px;text-align:center;">
    <div style="font-size:13px;color:var(--c-fg-placeholder);">Anda belum terdaftar di kelas praktikum manapun.</div>
    <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
       class="mp-btn ghost sm" style="text-decoration:none;display:inline-block;margin-top:12px;">← Kembali ke Dashboard</a>
</div>
@elseif($modulList->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:32px;text-align:center;">
    <div style="font-size:13px;color:var(--c-fg-placeholder);">Belum ada modul yang tersedia untuk praktikum ini.</div>
</div>
@else

{{-- Daftar Modul --}}
<div class="flex flex-col gap-3 flex-1">
    @foreach($modulList as $modul)
    @php
        $asprakList = $modul->modulAsprak->map(fn($ma) => $ma->asprak?->user?->name)->filter()->values();
        $jumlahMateri = $modul->materi->count();
    @endphp
    <div class="mp-card flex-shrink-0">
        {{-- Modul header (toggle) --}}
        <button type="button"
                onclick="toggleModul({{ $modul->id }})"
                class="w-full flex items-center justify-between px-5 py-4 text-left cursor-pointer border-none bg-transparent">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex items-center justify-center flex-shrink-0"
                     style="width:32px;height:32px;border-radius:8px;font-size:13px;font-weight:700;background:var(--c-bg-sub);color:var(--c-primary);">
                    {{ $modul->urutan ?? $loop->iteration }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:15px;font-weight:700;color:var(--c-fg);" class="truncate">{{ $modul->nama }}</div>
                    <div class="flex items-center gap-3 mt-1">
                        <span style="font-size:11px;color:var(--c-fg-muted);">{{ $jumlahMateri }} materi</span>
                        @if($asprakList->isNotEmpty())
                        <span style="font-size:11px;color:var(--c-fg-muted);">Asprak: {{ $asprakList->join(', ') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($jumlahMateri > 0)
                <span class="mp-badge success sm">{{ $jumlahMateri }} file</span>
                @endif
                <svg id="chevron-{{ $modul->id }}" class="w-4 h-4 transition-transform duration-200"
                     style="color:var(--c-fg-muted);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        {{-- Konten modul --}}
        <div id="modul-content-{{ $modul->id }}" class="hidden" style="border-top:1px solid var(--c-border-light);">
            @if($modul->deskripsi)
            <div style="padding:12px 20px;font-size:12px;color:var(--c-fg-muted);border-bottom:1px solid var(--c-border-light);">{{ $modul->deskripsi }}</div>
            @endif

            @if($jumlahMateri === 0)
            <div style="padding:24px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada materi untuk modul ini.</div>
            @else
            @foreach($modul->materi as $materi)
            @php
                $iconColor = match(true) {
                    str_contains($materi->tipe_file ?? '', 'pdf') => '#DF1C41',
                    str_contains($materi->tipe_file ?? '', 'word') || str_contains($materi->tipe_file ?? '', 'document') => '#1565C0',
                    str_contains($materi->tipe_file ?? '', 'presentation') || str_contains($materi->tipe_file ?? '', 'powerpoint') => '#E64A19',
                    str_contains($materi->tipe_file ?? '', 'image') => '#0D6B55',
                    default => 'var(--c-fg-muted)',
                };
            @endphp
            <div class="mp-tr flex items-center justify-between gap-4" style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-[32px] h-[32px] rounded-[7px] flex items-center justify-center flex-shrink-0"
                         style="background:{{ $iconColor }}18;">
                        <svg class="w-4 h-4" fill="none" stroke="{{ $iconColor }}" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $materi->judul }}</div>
                        @if($materi->deskripsi)
                        <div style="font-size:11px;color:var(--c-fg-muted);margin-top:1px;" class="line-clamp-1">{{ $materi->deskripsi }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span style="font-size:11px;color:var(--c-fg-muted);">{{ $materi->created_at?->diffForHumans() }}</span>
                    @if($materi->file_path)
                    <a href="{{ Storage::disk('public')->url($materi->file_path) }}"
                       target="_blank"
                       class="mp-btn primary sm" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh
                    </a>
                    @else
                    <span style="font-size:12px;color:var(--c-fg-muted);padding:3px 12px;">Tidak ada file</span>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
    @endforeach
</div>

@endif

<script>
function toggleModul(id) {
    const content  = document.getElementById('modul-content-' + id);
    const chevron  = document.getElementById('chevron-' + id);
    const isHidden = content.classList.contains('hidden');
    content.classList.toggle('hidden', !isHidden);
    chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}
</script>

</x-eoffice::manajemen-praktikum.layout>
