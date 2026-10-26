<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')
{{-- Kartu, panel filter & empty state dipakai bersama dengan Rencana Proker dan Laporan & Arsip --}}
@include('manajemenmahasiswa::partials.kegiatan-list._styles')

{{-- Tidak ada tahap persetujuan admin: proker masuk ke sini saat ketua menekan
     "Ajukan Proker" di Rencana Proker (status internalnya tetap `disetujui`). --}}
<x-manajemenmahasiswa::ui.page-header bordered
    title="Pelaksanaan Kegiatan"
    subtitle="Proker yang sudah diajukan — sesuaikan datanya dengan realisasi, lalu unggah ke Laporan & Arsip" />

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />

@include('manajemenmahasiswa::partials.kegiatan-list._filter', [
    'action'      => route('manajemenmahasiswa.pelaksanaan.index'),
    'placeholder' => 'Cari judul atau deskripsi kegiatan...',
])

@if($pelaksanaanList->count() > 0)
    <div class="row g-4">
        @foreach($pelaksanaanList as $item)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                @include('manajemenmahasiswa::partials.kegiatan-list._kartu', [
                    'item' => $item,
                    'href' => route('manajemenmahasiswa.pelaksanaan.show', $item->id),
                ])
            </div>
        @endforeach
    </div>
    {{-- Footer: Per page + Showing X to Y of Z results + nomor halaman (partial bersama).
         Daftar ini berupa grid kartu, jadi pilihannya kelipatan 6 (PerPage::KARTU). --}}
    @include('manajemenmahasiswa::partials.table-footer', [
        'paginator'      => $pelaksanaanList,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div style="display:flex;justify-content:center;margin-bottom:12px;color:var(--c-border);"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line><path d="m9 16 2 2 4-4"></path></svg></div>
        <h5>Belum ada proker yang siap dilaksanakan</h5>
        <p>Proker yang sudah diajukan dari Rencana Proker akan muncul di sini</p>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="mk-btn mk-btn--primary mt-2">
            Lihat Rencana Proker
        </a>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
