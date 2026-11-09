<x-manajemenmahasiswa::layouts.mahasiswa>
@include('manajemenmahasiswa::partials.card-frame')

@include('manajemenmahasiswa::partials.kegiatan-theme')

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Pelaksanaan --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

<style>
/* Khusus Rencana Proker: modal. Tombol memakai komponen bersama kegiatan. */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center}
.modal-box{background:var(--c-surface);border-radius:16px;padding:32px;max-width:440px;width:90%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.15)}
</style>

@php
    // Field yang masih kosong — dipakai untuk tooltip tombol Ajukan
    $kelengkapanKurang = collect($kelengkapan ?? [])->reject(fn($i) => $i['terisi'])->pluck('label');
    $rencanaLengkap    = $kelengkapanKurang->isEmpty();
@endphp

{{-- Header --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Detail Rencana Proker">
    Dibuat oleh <span style="color:var(--c-fg-sec);font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span>
    &bull; {{ $proker->created_at->translatedFormat('d M Y') }}

    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg></a>
    </x-slot:leading>

    <x-slot:actions>
    {{--
        Badge status sengaja TIDAK dirender di sini. Daftar Rencana Proker hanya
        memuat proker berstatus draft, jadi badge-nya selalu bertuliskan "Draft"
        — nol informasi, tapi tingginya (28px + jarak 8px) menjadikan blok kanan
        dua tingkat dan mendorong baris tombol turun sampai menempel banner
        (jaraknya terukur 0px). Tanpa badge, tombol naik sejajar judul dan banner
        dapat jarak 34px. Untuk proker yang sudah diajukan/diarsipkan, statusnya
        tetap terbaca dari hilangnya semua tombol di bawah ini plus kartu
        "Proker Diajukan!" / "Kegiatan Selesai & Diarsipkan" di bagian bawah.
    --}}
    <div class="d-flex flex-column align-items-end">
        <div class="d-flex gap-2 flex-wrap align-items-start justify-content-end">
            @if($canEdit && $proker->status === 'draft')
                <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
                   class="mk-btn mk-btn--primary mk-btn--sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit
                </a>
            @endif
            @if($canDelete && $proker->status === 'draft')
                <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm"
                        onclick="document.getElementById('deleteModal').style.display='flex'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Hapus
                </button>
            @endif
            {{-- staff_himpunan & role pengawas tidak melihat tombol ini sama sekali --}}
            @if($proker->status === 'draft' && $canSeeAjukan)
                @if($canAjukan)
                    @if($rencanaLengkap)
                        <button type="button" class="mk-btn mk-btn--primary mk-btn--sm"
                                onclick="document.getElementById('ajukanModal').style.display='flex'">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                            Ajukan Proker
                        </button>
                    @else
                        <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                                title="Lengkapi dulu: {{ $kelengkapanKurang->implode(', ') }}">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                            Ajukan Proker
                        </button>
                    @endif
                @else
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                            title="Hanya Ketua / Ketua Bidang / Ketua Unit yang dapat mengajukan proker">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                        Ajukan Proker
                    </button>
                @endif
            @endif
        </div>
        {{-- Role-nya boleh mengelola, tapi bukan pengelola proker ini (KegiatanPolicy) --}}
        @if($pesanBukanPengelola && $proker->status === 'draft')
            <p class="mb-0 mt-1 text-end" style="max-width:340px;font-size:12px;color:var(--c-fg-muted);font-weight:500;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ $pesanBukanPengelola }}
            </p>
        @endif
    </div>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

{{-- Badan detail dipakai bersama dengan Pelaksanaan; $showDokumentasi = false --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => false])

{{-- Link ke Pelaksanaan / Arsip --}}
@if($proker->status === 'disetujui')
<div class="detail-card" style="background:var(--c-primary-subtle);border:1px solid var(--c-primary-border);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:var(--c-primary-hover);margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>Proker Diajukan!</div>
            <div style="font-size:14px;color:var(--c-primary);font-weight:500;">Cek kesesuaian data dengan realisasi di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="mk-btn mk-btn--primary">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

@if($proker->status === 'selesai')
<div class="detail-card" style="background:var(--c-success-subtle);border:1px solid var(--c-success-border);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:var(--c-success);margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Kegiatan Selesai &amp; Diarsipkan</div>
            <div style="font-size:14px;color:var(--c-success);font-weight:500;">Laporan akhir kegiatan ini ada di halaman Laporan &amp; Arsip.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
           class="mk-btn mk-btn--primary">
            Lihat di Arsip &rarr;
        </a>
    </div>
</div>
@endif

{{-- Modals --}}
@if($canAjukan && $canSeeAjukan && $proker->status === 'draft' && $rencanaLengkap)
<div id="ajukanModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--c-primary-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
        </div>
        <h5 class="fw-bold mb-2">Ajukan Proker?</h5>
        <p style="color:var(--c-fg-muted);font-size:14px;">Rencana proker "<strong>{{ $proker->judul }}</strong>" akan diajukan dan masuk ke tahap <strong>Pelaksanaan Kegiatan</strong>.</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <button type="button" class="mk-btn mk-btn--secondary" onclick="document.getElementById('ajukanModal').style.display='none'">Batal</button>
            <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="mk-btn mk-btn--primary">Ajukan</button>
            </form>
        </div>
    </div>
</div>
@endif

@if($canDelete && $proker->status === 'draft')
<div id="deleteModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div style="width:56px;height:56px;border-radius:50%;background:var(--c-error-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--c-error);"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></div>
        <h5 class="fw-bold mb-2">Hapus Proker?</h5>
        <p style="color:var(--c-fg-muted);font-size:14px;">Data proker "<strong>{{ $proker->judul }}</strong>" akan dihapus permanen.</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
            <button type="button" class="mk-btn mk-btn--secondary" onclick="document.getElementById('deleteModal').style.display='none'">Batal</button>
            <form action="{{ route('manajemenmahasiswa.proker.destroy', $proker->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="mk-btn mk-btn--primary">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endif

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

<script>
// Tutup modal konfirmasi saat klik area gelap
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.style.display = 'none'; });
});
</script>
</x-manajemenmahasiswa::layouts.mahasiswa>
