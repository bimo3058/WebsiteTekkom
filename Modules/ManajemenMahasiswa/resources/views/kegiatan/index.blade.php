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
