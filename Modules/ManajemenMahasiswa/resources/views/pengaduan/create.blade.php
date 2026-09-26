<x-dynamic-component :component="$isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'">

    @push('styles')
        <style>
            /* Kotak .kf-box sudah menggambar bingkainya sendiri (pola Edit User SITKOM),
               jadi bingkai & padding bawaan .main-wrapper dimatikan. */
            .main-wrapper { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        </style>
    @endpush

    {{-- Form ini khusus jalur Reguler; jalur Konfidensial memakai alur magic link terpisah. --}}
    <form method="POST" action="{{ route('manajemenmahasiswa.pengaduan.confirm') }}" enctype="multipart/form-data" style="position: relative; margin: 0;">
        @csrf
        @include('manajemenmahasiswa::pengaduan.partials.honeypot')
        <input type="hidden" name="is_anonim" value="0">
        <input type="hidden" name="jalur_query" value="reguler">

        @include('manajemenmahasiswa::pengaduan.partials.form-fields', [
            'jalur'             => 'reguler',
            'kategoriList'      => $kategoriList,
            'dosenList'         => $dosenList ?? [],
            'frekuensiList'     => $frekuensiList ?? [],
            'buktiPendingItems' => $buktiPendingItems ?? [],
            'backUrl'           => route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]),
        ])
    </form>

</x-dynamic-component>
