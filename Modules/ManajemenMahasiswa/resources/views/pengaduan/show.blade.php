<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            /* Kotak .kf-box sudah menggambar bingkainya sendiri (pola Detail User SITKOM),
               jadi bingkai & padding bawaan .main-wrapper dimatikan. */
            .main-wrapper {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        </style>
    @endpush

    @include('manajemenmahasiswa::pengaduan.partials.detail-box', [
        'pengaduan'     => $pengaduan,
        'title'         => 'Detail Pengaduan',
        'isStaff'       => $isStaff,
        'canDelete'     => $canDelete,
        'backUrl'       => route('manajemenmahasiswa.pengaduan.index'),
        'kategoriLabel' => $kategoriLabel,
    ])

</x-dynamic-component>
