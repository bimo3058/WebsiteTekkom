<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            /* Kotak .kf-box sudah punya bingkai sendiri; bingkai bawaan layout dimatikan. */
            .main-wrapper { background: transparent !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        </style>
    @endpush

    {{-- Satu tampilan konfirmasi untuk pengaduan biasa & anonim. --}}
    @include('manajemenmahasiswa::pengaduan.partials.konfirmasi', [
        'payload' => $payload,
        'isAnonim' => (bool) $payload['is_anonim'],
        'backUrl' => route('manajemenmahasiswa.pengaduan.create'),
        'formAction' => route('manajemenmahasiswa.pengaduan.store'),
        'reporterName' => auth()->user()->name ?? 'Mahasiswa',
    ])

</x-manajemenmahasiswa::layouts.mahasiswa>
