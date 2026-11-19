<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            /* Kotak .kf-box sudah menggambar bingkainya sendiri (pola Edit User SITKOM),
               jadi bingkai & padding bawaan .main-wrapper dimatikan. */
            .main-wrapper { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        </style>
    @endpush

    {{-- Satu form untuk semua pelapor; anonim cukup lewat centang di bagian Pelapor. --}}
    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.confirm') }}" enctype="multipart/form-data" style="position: relative; margin: 0;">
        @csrf
        @include('manajemenmahasiswa::pengaduan.partials.honeypot')

        @include('manajemenmahasiswa::pengaduan.partials.form-fields', [
            'kategoriList'      => $kategoriList,
            'dosenList'         => $dosenList ?? [],
            'buktiPendingItems' => $buktiPendingItems ?? [],
            'backUrl'           => route('manajemenmahasiswa.pengaduan.index'),
        ])
    </form>

</x-dynamic-component>
