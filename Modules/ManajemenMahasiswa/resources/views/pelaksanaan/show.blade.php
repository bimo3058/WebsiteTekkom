<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Rencana Proker dan Laporan & Arsip.
     Konfirmasi Hapus & Unggah ke Arsip memakai dialog SITKOM bersama (mkConfirmSubmit). --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

@php
    // Syarat tombol "Unggah ke Arsip" — dihitung di sini agar tidak memenuhi slot header.
    $punyaFoto = $proker->repoMulmed->where('tipe_file', 'image')->isNotEmpty();
    // Sama dengan PelaksanaanController::publishToArsip(): lokasi, ketua pelaksana, dan
    // anggaran wajib berisi (anggaran 0 sah — kegiatan tanpa biaya).
    $kurangRealisasi = collect([
        'lokasi'          => blank($proker->lokasi),
        'ketua pelaksana' => empty($proker->ketua_pelaksana_id),
        'anggaran'        => $proker->anggaran === null,
    ])->filter()->keys();
@endphp

<!-- Header with back button -->
<x-manajemenmahasiswa::ui.page-header bordered
    title="Detail Pelaksanaan Kegiatan"
    subtitle="Informasi kegiatan pada subbab pelaksanaan">
    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
        </a>
    </x-slot:leading>

    <x-slot:actions>
    <div class="d-flex gap-2">
        @if($canManage && $proker->status !== 'selesai')
            <a href="{{ route('manajemenmahasiswa.pelaksanaan.edit', $proker->id) }}"
               class="mk-btn mk-btn--primary mk-btn--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
            </a>
        @endif
        @if($canDelete && $proker->status !== 'selesai')
            @include('manajemenmahasiswa::partials.kegiatan-detail._tombol-hapus', [
                'action'   => route('manajemenmahasiswa.pelaksanaan.destroy', $proker->id),
                'jenis'    => 'Kegiatan',
                'judul'    => $proker->judul,
                'lampiran' => 'banner, foto, dan dokumennya',
            ])
        @endif
        @if($proker->status !== 'selesai')
            @if($canArsip)
                @if($proker->is_pelaksanaan_updated && $proker->banner && $punyaFoto && $kurangRealisasi->isEmpty())
                    <form action="{{ route('manajemenmahasiswa.pelaksanaan.publish', $proker->id) }}" method="POST" class="m-0"
                          onsubmit="return mkConfirmSubmit(this, @js('Semua data kegiatan "' . $proker->judul . '" akan tersinkron ke subbab Laporan & Arsip dan ditandai sebagai Selesai.'), { title: 'Unggah ke Laporan & Arsip', variant: 'primary', confirmText: 'Ya, Unggah' })">
                        @csrf
                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Unggah ke Arsip
                        </button>
                    </form>
                @else
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                            title="{{ !$proker->is_pelaksanaan_updated ? 'Silakan edit/update data pelaksanaan kegiatan terlebih dahulu sebelum mengunggah ke arsip' : (!$proker->banner ? 'Banner kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip' : ($kurangRealisasi->isNotEmpty() ? 'Lengkapi dulu ' . $kurangRealisasi->implode(', ') . ' sebelum mengunggah ke arsip' : 'Minimal satu foto kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip')) }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Unggah ke Arsip
                    </button>
                @endif
            @endif
            {{-- Role tanpa hak arsip (staff_himpunan, gpm) tidak menampilkan tombol sama sekali (view-only) --}}
        @endif
        @if($proker->status === 'selesai')
            <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
               class="mk-btn mk-btn--primary mk-btn--sm">
                Lihat di Arsip &rarr;
            </a>
        @endif
        {{-- Role-nya boleh mengelola, tapi bukan pengelola kegiatan ini (KegiatanPolicy) --}}
        @if($pesanBukanPengelola && $proker->status !== 'selesai')
            <p class="mb-0 text-end align-self-center" style="max-width:340px;font-size:12px;color:var(--c-fg-muted);font-weight:500;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ $pesanBukanPengelola }}
            </p>
        @endif
    </div>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<!-- Flash Messages -->
<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />

{{-- Pesan gagal (mis. bukan pengelola, atau belum boleh diunggah ke arsip) — sebelumnya
     halaman ini hanya menampilkan pesan sukses, jadi redirect dengan error jatuh diam-diam. --}}
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

{{-- Badan detail dipakai bersama dengan Rencana Proker dan Laporan & Arsip; $showDokumentasi = true --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => true])

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

</x-manajemenmahasiswa::layouts.mahasiswa>
