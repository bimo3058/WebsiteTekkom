<x-eoffice::manajemen-praktikum.layout pageTitle="Detail Modul">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">{{ $modul->nama }}</h1>
            <span class="mp-badge" style="background:#D0D6E9;color:#5D6DA2;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#5D6DA2;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">
            {{ $modul->praktikum?->nama }} · {{ $modul->jadwal_minggu ?? 'Jadwal belum diisi' }}
            · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>
    <div class="mp-page-actions">

        <a href="{{ route('eoffice.manprak.koor.modul.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Stats --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div class="mp-stat-label">Materi</div>
        <div class="mp-stat-value">{{ $modul->materi->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        </div>
        <div class="mp-stat-label">Tugas</div>
        <div class="mp-stat-value">{{ $modul->tugas->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon green">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="mp-stat-label">Asisten Praktikum</div>
        <div class="mp-stat-value">{{ $modul->modulAsprak->count() }}</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="mp-stat-label">Praktikan</div>
        <div class="mp-stat-value">{{ $daftarPraktikan->count() }}</div>
    </div>
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Detail Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">
    <div class="flex flex-col gap-[14px]">
        {{-- Edit Form --}}
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Edit Modul</div>
            <form method="POST" action="{{ route('eoffice.manprak.koor.modul.update', $modul->id) }}" class="flex flex-col gap-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul</label>
                    <input name="nama" value="{{ $modul->nama }}" required class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan</label>
                    <input type="number" name="urutan" value="{{ $modul->urutan }}" min="1" required class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jadwal / Minggu</label>
                    <input name="jadwal_minggu" value="{{ $modul->jadwal_minggu }}" class="mp-input w-full" placeholder="Jadwal / minggu">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none">{{ $modul->deskripsi }}</textarea>
                </div>
                <button class="mp-btn primary md w-full">Simpan Perubahan</button>
            </form>
        </div>

        {{-- Asisten Praktikum Modul --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Asisten Praktikum Modul</span>
                <div class="right">
                    <span class="mp-badge neutral sm">{{ $modul->modulAsprak->count() }} orang</span>
                </div>
            </div>
            @forelse($modul->modulAsprak as $ma)
            <div style="padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                <div class="flex items-center gap-[10px]">
                    <div class="mp-av green">{{ strtoupper(substr($ma->asprak?->user?->name ?? 'A', 0, 2)) }}</div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $ma->asprak?->user?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:#666D80;">{{ $ma->asprak?->user?->email }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asprak.</div>
            </div>
            @endforelse
        </div>
    </div>

    <div class="flex flex-col gap-[14px] overflow-y-auto">
        {{-- Materi Modul --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Materi Modul</span>
                <div class="right">
                    <span class="mp-badge neutral sm">{{ $modul->materi->count() }} materi</span>
                </div>
            </div>
            @forelse($modul->materi as $materi)
            <div style="padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div style="font-weight:600;font-size:13px;color:#0D0D12;">{{ $materi->judul }}</div>
                <div style="font-size:12px;color:#666D80;margin-top:3px;">{{ $materi->deskripsi }}</div>
            </div>
            @empty
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada materi.</div>
            </div>
            @endforelse
        </div>

        {{-- Tugas Modul --}}
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Tugas Modul</span>
                <div class="right">
                    <span class="mp-badge neutral sm">{{ $modul->tugas->count() }} tugas</span>
                </div>
            </div>
            @forelse($modul->tugas as $tugas)
            <div class="flex justify-between gap-3" style="padding:12px 20px;border-bottom:1px solid #DFE1E7;">
                <div>
                    <div style="font-weight:600;font-size:13px;color:#0D0D12;">{{ $tugas->judul }}</div>
                    <div style="font-size:12px;color:#666D80;margin-top:3px;">{{ $tugas->deadline?->format('d M Y H:i') ?? 'Tanpa deadline' }}</div>
                </div>
                <span class="mp-badge neutral sm" style="white-space:nowrap;align-self:center;">{{ $tugas->pengumpulan->count() }} pengumpulan</span>
            </div>
            @empty
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada tugas.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
