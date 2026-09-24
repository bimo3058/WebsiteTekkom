<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }
        </style>
    @endpush

    {{-- Tampilan konfirmasi dipakai bersama jalur Reguler & Konfidensial. --}}
    @include('manajemenmahasiswa::pengaduan.partials.konfirmasi', [
        'payload' => $payload,
        'isAnonim' => (bool) $payload['is_anonim'],
        'backUrl' => route('manajemenmahasiswa.pengaduan.create', ['jalur' => $payload['is_anonim'] ? 'konfidensial' : 'reguler']),
        'formAction' => route('manajemenmahasiswa.pengaduan.store'),
        'reporterName' => auth()->user()->name ?? 'Mahasiswa',
    ])

</x-manajemenmahasiswa::layouts.mahasiswa>
