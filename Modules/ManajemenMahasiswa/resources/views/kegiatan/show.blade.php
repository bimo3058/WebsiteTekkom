<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Rencana Proker dan Pelaksanaan.
     Dulu halaman ini membawa salinan sendiri (~1.000 baris) yang isinya pelan-pelan berbeda:
     NIM/NIP, baris Tahun, dan bulan berbahasa Inggris. Keputusan 17 Okt 2026: ketiga detail
     disamakan, tanpa NIM/NIP. Konfirmasi Hapus memakai dialog SITKOM bersama. --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

<!-- Header with back button -->
<x-manajemenmahasiswa::ui.page-header bordered
    title="Detail Kegiatan"
    subtitle="Informasi lengkap tentang kegiatan ini">
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
    </x-slot:leading>

    <x-slot:actions>
    <div class="d-flex gap-2">
        @if($canEdit)
            <a href="{{ route('manajemenmahasiswa.kegiatan.edit', $kegiatan->id) }}"
               class="mk-btn mk-btn--primary mk-btn--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
        @endif
        @if($canDelete)
            @include('manajemenmahasiswa::partials.kegiatan-detail._tombol-hapus', [
                'action'   => route('manajemenmahasiswa.kegiatan.destroy', $kegiatan->id),
                'jenis'    => 'Kegiatan',
                'judul'    => $kegiatan->judul,
                'lampiran' => 'banner, foto, dan dokumennya',
                'catatan'  => $kegiatan->rencana_id ? 'Rencana awalnya di Rencana Proker tetap tersimpan, tetapi tidak bisa diajukan ulang.' : null,
            ])
        @endif
        {{-- Role-nya boleh mengelola, tapi bukan pengelola kegiatan ini (KegiatanPolicy) --}}
        @if($pesanBukanPengelola)
            <p class="mb-0 text-end align-self-center" style="max-width:340px;font-size:12px;color:var(--c-fg-muted);font-weight:500;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ $pesanBukanPengelola }}
            </p>
        @endif
    </div>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<!-- Flash Messages -->
<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />

{{-- Pesan gagal (mis. bukan pengelola kegiatan ini) — sebelumnya halaman ini hanya
     menampilkan pesan sukses, jadi redirect dengan error jatuh diam-diam. --}}
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

{{-- Badan detail dipakai bersama dengan Rencana Proker & Pelaksanaan; $showDokumentasi = true --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', [
    'proker'          => $kegiatan,
    'showDokumentasi' => true,
])

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox', ['proker' => $kegiatan])

</x-manajemenmahasiswa::layouts.mahasiswa>
