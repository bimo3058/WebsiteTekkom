<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')
{{-- Kartu, panel filter & empty state dipakai bersama dengan Pelaksanaan dan Laporan & Arsip --}}
@include('manajemenmahasiswa::partials.kegiatan-list._styles')

<x-manajemenmahasiswa::ui.page-header bordered
    title="Rencana Program Kerja"
    subtitle="Daftar rencana proker dari {{ $bidangList->count() }} bidang himpunan & prodi">
    <x-slot:actions>
        @if($canManage)
            <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="mk-btn mk-btn--primary mk-btn--sm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat Proker
            </a>
        @endif
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />

@include('manajemenmahasiswa::partials.kegiatan-list._filter', [
    'action'      => route('manajemenmahasiswa.proker.index'),
    'placeholder' => 'Cari judul atau deskripsi rencana proker...',
    'tahapList'   => $tahapList,
])

@if($prokerList->count() > 0)
    <div class="row g-4">
        @foreach($prokerList as $proker)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                @include('manajemenmahasiswa::partials.kegiatan-list._kartu', [
                    'item' => $proker,
                    'href' => route('manajemenmahasiswa.proker.show', $proker->id),
                    'tampilTahap' => true,
                ])
            </div>
        @endforeach
    </div>
    {{-- Footer: Per page + Showing X to Y of Z results + nomor halaman (partial bersama).
         Daftar ini berupa grid kartu, jadi pilihannya kelipatan 6 (PerPage::KARTU). --}}
    @include('manajemenmahasiswa::partials.table-footer', [
        'paginator'      => $prokerList,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div style="display:flex;justify-content:center;margin-bottom:12px;color:var(--c-border);"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></div>
        <h5>Belum ada rencana proker</h5>
        <p>Rencana program kerja yang dibuat akan muncul di sini</p>
        @if($canManage)
            <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="mk-btn mk-btn--primary mt-2">+ Buat Proker Pertama</a>
        @endif
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
