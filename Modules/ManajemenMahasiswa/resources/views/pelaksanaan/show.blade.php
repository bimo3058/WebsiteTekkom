<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

@include('manajemenmahasiswa::partials.kegiatan-detail._styles')


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
            <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm"
                    onclick="document.getElementById('deleteModal').style.display='flex'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Hapus
            </button>
        @endif
        @if($proker->status !== 'selesai')
            @if($canArsip)
                @if($proker->is_pelaksanaan_updated && $proker->banner)
                    <button type="button" class="mk-btn mk-btn--primary mk-btn--sm"
                            onclick="document.getElementById('arsipModal').style.display='flex'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Unggah ke Arsip
                    </button>
                @else
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                            title="{{ !$proker->is_pelaksanaan_updated ? 'Silakan edit/update data pelaksanaan kegiatan terlebih dahulu sebelum mengunggah ke arsip' : 'Banner kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip' }}">
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

{{-- Badan detail dipakai bersama dengan Rencana Proker; $showDokumentasi = true --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', ['showDokumentasi' => true])


<!-- Unggah ke Arsip Confirmation Modal -->
@if($canArsip && $proker->status !== 'selesai' && $proker->is_pelaksanaan_updated && $proker->banner)
<div id="arsipModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background: var(--c-surface); border-radius: 16px; padding: 32px; max-width: 440px; width: 90%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--c-primary-subtle); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </div>
        <h5 style="font-weight: 700; color: var(--c-fg); margin-bottom: 8px;">Unggah ke Laporan & Arsip?</h5>
        <p style="color: var(--c-fg-muted); font-size: 14px; margin-bottom: 24px;">
            Semua data kegiatan <strong>{{ $proker->judul }}</strong> akan tersinkron ke subbab Laporan &amp; Arsip dan ditandai sebagai <strong>Selesai</strong>.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button" class="mk-btn mk-btn--secondary"
                    onclick="document.getElementById('arsipModal').style.display='none'">
                Batal
            </button>
            <form action="{{ route('manajemenmahasiswa.pelaksanaan.publish', $proker->id) }}" method="POST">
                @csrf
                <button type="submit" class="mk-btn mk-btn--primary">
                    Unggah
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
@if($canDelete)
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--c-surface); border-radius: 16px; padding: 32px; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--c-error-subtle); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--c-error)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h5 style="font-weight: 700; color: var(--c-fg); margin-bottom: 8px;">Hapus Kegiatan?</h5>
        <p style="color: var(--c-fg-muted); font-size: 14px; margin-bottom: 24px;">
            Kegiatan <strong>{{ $proker->judul }}</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button type="button" class="mk-btn mk-btn--secondary"
                    onclick="document.getElementById('deleteModal').style.display='none'">
                Batal
            </button>
            <form action="{{ route('manajemenmahasiswa.pelaksanaan.destroy', $proker->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="mk-btn mk-btn--primary">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

</x-manajemenmahasiswa::layouts.mahasiswa>
