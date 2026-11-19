<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')
{{-- Kartu, panel filter & empty state dipakai bersama dengan Rencana Proker dan Pelaksanaan --}}
@include('manajemenmahasiswa::partials.kegiatan-list._styles')

{{-- Judul sama dengan label menu & breadcrumb ("Laporan & Arsip"); "Manajemen Kegiatan"
     adalah nama bab, bukan nama halaman ini. --}}
<x-manajemenmahasiswa::ui.page-header bordered
    title="Laporan & Arsip"
    subtitle="Kegiatan yang sudah selesai dan diarsipkan dari berbagai bidang kepengurusan">
    <x-slot:actions>
        {{-- Ekspor rekap realisasi (S-78): mengikuti filter yang sedang aktif, semua halaman.
             Hanya role yang boleh melihat anggaran (KegiatanPolicy::LIHAT_INTERNAL). --}}
        @if($canEkspor)
            @php
                $filterAktif = array_filter(
                    request()->only(['search', 'bidang', 'tahun', 'periode', 'dari', 'sampai']),
                    fn ($v) => is_string($v) && filled($v) && $v !== 'semua'
                );
            @endphp
            {{-- kiri: di layar HP tombol turun ke kiri, jadi menu (rata kanan, 230px) dibuka ke
                 kanan supaya tidak terpotong tepi layar. --}}
            <div style="position: relative; display: inline-block;" x-data="{ open: false, kiri: false }" @keydown.escape.window="open = false">
                <button type="button" @click="kiri = $el.getBoundingClientRect().right < 240; open = !open" @click.outside="open = false"
                        class="mk-btn mk-btn--secondary mk-btn--sm" :aria-expanded="open" aria-haspopup="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Ekspor Rekap
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div x-show="open" x-cloak class="mk-menu" style="display: none; min-width: 230px;" role="menu"
                     :style="kiri ? { left: '0px', right: 'auto' } : {}">
                    <a href="{{ route('manajemenmahasiswa.kegiatan.ekspor', $filterAktif + ['format' => 'xlsx']) }}" class="mk-menu-item" role="menuitem" @click="open = false">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#067647" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="17"></line><line x1="16" y1="13" x2="8" y2="17"></line></svg>
                        Excel (.xlsx)
                    </a>
                    <a href="{{ route('manajemenmahasiswa.kegiatan.ekspor', $filterAktif + ['format' => 'pdf']) }}" class="mk-menu-item" role="menuitem" @click="open = false">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#B42318" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        PDF
                    </a>
                    <div style="padding: 6px 10px 4px; font-size: 11px; color: var(--c-fg-muted); border-top: 1px solid var(--c-border); margin-top: 4px; line-height: 1.4;">
                        {{ $filterAktif ? 'Sesuai filter yang sedang aktif, semua halaman.' : 'Semua kegiatan di Laporan & Arsip.' }}
                    </div>
                </div>
            </div>
        @endif
        @if($canTambahKegiatan)
            <a href="{{ route('manajemenmahasiswa.kegiatan.create') }}"
               class="mk-btn mk-btn--primary mk-btn--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Kegiatan
            </a>
        @endif
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

@include('manajemenmahasiswa::partials.kegiatan-list._filter', [
    'action'      => route('manajemenmahasiswa.kegiatan.index'),
    'placeholder' => 'Cari judul atau deskripsi kegiatan...',
])

@if($kegiatan->count() > 0)
    <div class="row g-4">
        @foreach($kegiatan as $item)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                @include('manajemenmahasiswa::partials.kegiatan-list._kartu', [
                    'item' => $item,
                    'href' => route('manajemenmahasiswa.kegiatan.show', $item->id),
                ])
            </div>
        @endforeach
    </div>

    {{-- Footer: Per page + Showing X to Y of Z results + nomor halaman (partial bersama).
         Daftar ini berupa grid kartu, jadi pilihannya kelipatan 6 (PerPage::KARTU). --}}
    @include('manajemenmahasiswa::partials.table-footer', [
        'paginator'      => $kegiatan,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div style="display:flex;justify-content:center;margin-bottom:12px;color:var(--c-border);"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
        <h5>Belum ada kegiatan</h5>
        <p>Kegiatan yang sudah diarsipkan akan muncul di sini</p>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
