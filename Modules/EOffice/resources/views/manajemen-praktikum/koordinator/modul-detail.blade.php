<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">{{ $modul->nama }}</h1>
        <p class="mp-page-sub">
            {{ $modul->praktikum?->nama }} · {{ $modul->jadwal_minggu ?? 'Jadwal belum diisi' }}
            · Kode: <span style="font-family:monospace;font-weight:700;">{{ $modul->kode_modul ?? '-' }}</span>
        </p>
    </div>
    <div class="mp-page-actions">
        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.generate-kode', $modul->id) }}">
            @csrf
            <button type="submit" class="mp-btn success md">
                {{ $modul->kode_modul ? 'Refresh Kode' : 'Generate Kode' }}
            </button>
        </form>
        <a href="{{ route('eoffice.manprak.koor.modul.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-label">Materi</div>
        <div class="mp-stat-value">{{ $modul->materi->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Tugas</div>
        <div class="mp-stat-value">{{ $modul->tugas->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Asprak</div>
        <div class="mp-stat-value">{{ $modul->modulAsprak->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Praktikan</div>
        <div class="mp-stat-value">{{ $daftarPraktikan->count() }}</div>
    </div>
</div>

<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">
    <div class="flex flex-col gap-[14px]">
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div style="font-weight:700;font-size:14px;color:var(--c-fg);margin-bottom:16px;">Edit Modul</div>
            <form method="POST" action="{{ route('eoffice.manprak.koor.modul.update', $modul->id) }}" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <input name="nama" value="{{ $modul->nama }}" required class="mp-input w-full">
                <input type="number" name="urutan" value="{{ $modul->urutan }}" min="1" required class="mp-input w-full">
                <input name="jadwal_minggu" value="{{ $modul->jadwal_minggu }}" class="mp-input w-full" placeholder="Jadwal / minggu">
                <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none">{{ $modul->deskripsi }}</textarea>
                <button class="mp-btn primary md w-full">Simpan Perubahan</button>
            </form>
        </div>
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Asprak Modul</span>
            </div>
            @forelse($modul->modulAsprak as $ma)
            <div style="padding:8px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-size:13px;font-weight:600;color:var(--c-fg);">{{ $ma->asprak?->user?->name ?? '-' }}</div>
                <div style="font-size:11px;color:var(--c-fg-muted);">{{ $ma->asprak?->user?->email }}</div>
            </div>
            @empty
            <div style="padding:24px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada asprak.</div>
            @endforelse
        </div>
    </div>

    <div class="flex flex-col gap-[14px] overflow-y-auto">
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Materi Modul</span>
            </div>
            @forelse($modul->materi as $materi)
            <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-weight:600;font-size:13px;color:var(--c-fg);">{{ $materi->judul }}</div>
                <div style="font-size:12px;color:var(--c-fg-muted);">{{ $materi->deskripsi }}</div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada materi.</div>
            @endforelse
        </div>
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Tugas Modul</span>
            </div>
            @forelse($modul->tugas as $tugas)
            <div class="flex justify-between gap-3" style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div>
                    <div style="font-weight:600;font-size:13px;color:var(--c-fg);">{{ $tugas->judul }}</div>
                    <div style="font-size:12px;color:var(--c-fg-muted);">{{ $tugas->deadline?->format('d M Y H:i') ?? 'Tanpa deadline' }}</div>
                </div>
                <span style="font-size:11px;color:var(--c-fg-muted);">{{ $tugas->pengumpulan->count() }} pengumpulan</span>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada tugas.</div>
            @endforelse
        </div>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
