<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="mp-page-title">{{ $modul->nama }}</h1>
            @if($isAssigned)
            <span class="mp-badge success sm">Diampu Anda</span>
            @endif
            @if($modul->kode_modul)
            <span class="mp-badge primary sm" style="font-family:monospace;letter-spacing:0.1em;">{{ $modul->kode_modul }}</span>
            @endif
        </div>
        <p class="mp-page-sub">
            {{ $modul->praktikum?->nama }} &middot; Urutan {{ $modul->urutan }} &middot; {{ $modul->jadwal_minggu ?? 'Jadwal belum diisi' }}
        </p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.modul.index') }}" class="mp-btn secondary md" style="text-decoration:none;">← Kembali</a>
    </div>
</div>

{{-- Stat Cards --}}
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

{{-- Deskripsi --}}
@if($modul->deskripsi)
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="font-weight:700;font-size:13px;color:var(--c-fg);margin-bottom:8px;">Deskripsi Modul</div>
    <div style="font-size:13px;color:var(--c-fg-sub);line-height:1.6;">{{ $modul->deskripsi }}</div>
</div>
@endif

<div class="grid grid-cols-[340px_1fr] gap-[14px] flex-1 min-h-0">

    {{-- Kolom Kiri: Edit + Asprak Pengampu --}}
    <div class="flex flex-col gap-[14px]">

        {{-- Edit Modul --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="mp-card-title" style="margin-bottom:16px;">Edit Modul</div>
            <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.update', $modul->id) }}" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Nama Modul</label>
                    <input name="nama" value="{{ $modul->nama }}" required class="mp-input w-full">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Urutan</label>
                        <input type="number" name="urutan" value="{{ $modul->urutan }}" min="1" required class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Jadwal</label>
                        <input name="jadwal_minggu" value="{{ $modul->jadwal_minggu }}" class="mp-input w-full">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="mp-input w-full">{{ $modul->deskripsi }}</textarea>
                </div>
                <button class="mp-btn primary md">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Asprak Pengampu Modul ini --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div class="mp-card-title" style="margin-bottom:12px;">Asprak Pengampu</div>
            @forelse($modul->modulAsprak as $ma)
            <div class="flex items-center gap-3" style="padding:10px 0;border-bottom:1px solid var(--c-border-light);">
                <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#1a6691,#40C4AA);">
                    {{ strtoupper(substr($ma->asprak?->user?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $ma->asprak?->user?->name ?? '—' }}</div>
                    <div style="font-size:11px;color:var(--c-fg-muted);">{{ $ma->asprak?->user?->email ?? '' }}</div>
                </div>
            </div>
            @empty
            <div style="font-size:13px;color:var(--c-fg-placeholder);">Belum ada asprak pengampu.</div>
            @endforelse
        </div>

    </div>

    {{-- Kolom Kanan: Materi, Tugas, Absensi Ringkasan --}}
    <div class="flex flex-col gap-[14px] overflow-y-auto">

        {{-- Materi --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Materi Modul</span>
                <a href="{{ route('eoffice.manprak.asprak.materi.index') }}"
                   class="mp-btn ghost sm" style="text-decoration:none;">Kelola Materi →</a>
            </div>
            @forelse($modul->materi as $materi)
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-[32px] h-[32px] rounded-[8px] flex items-center justify-center flex-shrink-0"
                         style="background:var(--c-bg-sub);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div style="font-weight:600;font-size:13px;color:var(--c-fg);" class="truncate">{{ $materi->judul }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);">{{ $materi->created_at?->format('d M Y') }}</div>
                    </div>
                </div>
                @if($materi->file_path)
                <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="mp-btn primary sm flex-shrink-0" style="text-decoration:none;">Unduh</a>
                @endif
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada materi.</div>
            @endforelse
        </div>

        {{-- Tugas --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Tugas Modul</span>
                <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
                   class="mp-btn ghost sm" style="text-decoration:none;">Kelola Tugas →</a>
            </div>
            @forelse($modul->tugas as $tugas)
            @php
                $totalKumpul   = $tugas->pengumpulan->count();
                $totalDinilai  = $tugas->pengumpulan->where('status_pengumpulan', 'dinilai')->count();
                $totalPending  = $tugas->pengumpulan->where('status_pengumpulan', 'belum_dicek')->count();
            @endphp
            <div class="mp-tr" style="padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div style="font-weight:600;font-size:13px;color:var(--c-fg);">{{ $tugas->judul }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">
                            Deadline: {{ $tugas->deadline?->format('d M Y H:i') ?? 'Tanpa deadline' }}
                        </div>
                    </div>
                    @if($tugas->is_published)
                    <span class="mp-badge success sm flex-shrink-0">Dipublikasikan</span>
                    @else
                    <span class="mp-badge neutral sm flex-shrink-0">Draft</span>
                    @endif
                </div>
                <div class="flex gap-3 mt-2" style="font-size:11px;color:var(--c-fg-muted);">
                    <span>{{ $totalKumpul }} pengumpulan</span>
                    <span style="{{ $totalPending > 0 ? 'color:#D39C3D;font-weight:600;' : '' }}">{{ $totalPending }} belum dinilai</span>
                    <span>{{ $totalDinilai }} sudah dinilai</span>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada tugas.</div>
            @endforelse
        </div>

        {{-- Ringkasan Absensi --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Rekap Absensi</span>
                <a href="{{ route('eoffice.manprak.asprak.absensi.show', $modul->id) }}"
                   class="mp-btn ghost sm" style="text-decoration:none;">Kelola Absensi →</a>
            </div>
            @php
                $absensiGroup = $modul->absensi->groupBy('tanggal');
            @endphp
            @forelse($absensiGroup as $tgl => $absList)
            @php
                $hadir = $absList->where('status','hadir')->count();
                $total = $absList->count();
            @endphp
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-size:13px;font-weight:500;color:var(--c-fg);">
                    {{ \Carbon\Carbon::parse($tgl)->isoFormat('dddd, D MMMM YYYY') }}
                </div>
                <div class="flex items-center gap-2">
                    <span style="font-size:12px;color:var(--c-fg-muted);">{{ $hadir }}/{{ $total }} hadir</span>
                    <div style="width:60px;background:#F0F1F4;border-radius:999px;height:6px;">
                        <div style="height:6px;border-radius:999px;background:#40C4AA;width:{{ $total > 0 ? round($hadir/$total*100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada data absensi.</div>
            @endforelse
        </div>

    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
