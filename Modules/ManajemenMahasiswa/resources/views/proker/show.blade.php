<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Pelaksanaan dan Laporan & Arsip.
     Konfirmasi Hapus & Ajukan memakai dialog SITKOM bersama (mkConfirmSubmit). --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

@php
    // Field yang masih kosong — dipakai untuk tooltip tombol Ajukan
    $kelengkapanKurang = collect($kelengkapan ?? [])->reject(fn($i) => $i['terisi'])->pluck('label');
    $rencanaLengkap    = $kelengkapanKurang->isEmpty();
@endphp

{{-- Header --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Detail Rencana Proker">
    Dibuat oleh <span style="color:var(--c-fg-sec);font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span>
    &bull; {{ $proker->created_at->locale('id')->translatedFormat('d M Y') }}

    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg></a>
    </x-slot:leading>

    <x-slot:actions>
    {{--
        Badge status sengaja TIDAK dirender di sini: tingginya (28px + jarak 8px)
        menjadikan blok kanan dua tingkat dan mendorong baris tombol turun sampai
        menempel banner. Badge tahap di kartu daftar juga sudah dicabut (27 Okt 2026).
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
            {{-- $canDelete sudah memuat aturan tahap: draft, atau rencana diajukan tanpa salinan --}}
            @if($canDelete)
                @include('manajemenmahasiswa::partials.kegiatan-detail._tombol-hapus', [
                    'action'   => route('manajemenmahasiswa.proker.destroy', $proker->id),
                    'jenis'    => 'Rencana Proker',
                    'judul'    => $proker->judul,
                    'lampiran' => $documents->isNotEmpty() ? 'banner dan dokumen rencananya' : 'banner-nya',
                ])
            @endif
            {{-- staff_himpunan & role pengawas tidak melihat tombol ini sama sekali --}}
            @if($proker->status === 'draft' && $canSeeAjukan)
                @if($canAjukan)
                    @if($rencanaLengkap)
                        <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" class="m-0"
                              onsubmit="return mkAjukanProker(this, @js('Rencana proker "' . $proker->judul . '" akan diajukan. Rencana ini dikunci dan tetap tersimpan di Rencana Proker, sedangkan salinannya beserta dokumen rencana dibuat di Pelaksanaan Kegiatan. Rencana yang sudah diajukan tidak bisa diubah lagi.'))">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                                Ajukan Proker
                            </button>
                        </form>
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

{{-- Badan detail dipakai bersama dengan Pelaksanaan; tanpa galeri foto, dengan Dokumen Rencana --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => false, 'showDokumen' => true])

{{-- Baris lama (sebelum S-49) yang berpindah status: link ke barisnya sendiri --}}
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

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

<script>
// Ajukan menyalin banner & dokumen rencana ke Pelaksanaan (S-49) dan bisa makan waktu
// belasan detik. Tanpa keterangan, halaman tampak macet dan tombol rawan diklik ulang.
function mkAjukanProker(form, pesan) {
    window.mkConfirm({ message: pesan, title: 'Ajukan Proker', variant: 'primary', confirmText: 'Ya, Ajukan' })
        .then(function (ok) {
            if (!ok) return;
            const tombol = form.querySelector('button[type="submit"]');
            if (tombol) {
                tombol.disabled = true;
                tombol.textContent = 'Menyalin data & dokumen…';
            }
            form.submit();
        });

    return false;
}
</script>

</x-manajemenmahasiswa::layouts.mahasiswa>
