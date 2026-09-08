<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Rencana Proker — Buat.
    Form memakai partial bersama dengan Pelaksanaan Kegiatan.
    $showDokumentasi = false → kartu Foto & Dokumen Kegiatan tidak dirender:
    keduanya dokumentasi acara yang sudah berlangsung, diisi di subbab Pelaksanaan.
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => false,
    'tahapRencana'    => true,
    'formAction'      => route('manajemenmahasiswa.proker.store'),
    'formMethod'      => 'POST',
    'backUrl'         => route('manajemenmahasiswa.proker.index'),
    'headerTitle'     => 'Buat Rencana Proker',
    'headerSubtitle'  => 'Lengkapi rencana program kerja. Detail yang belum pasti boleh dikosongkan dulu dan dilengkapi kemudian sebelum diajukan.',
    'submitLabel'     => 'Simpan',
])

@include('manajemenmahasiswa::partials.kegiatan-form._scripts', [
    'showDokumentasi' => false,
])

</x-manajemenmahasiswa::layouts.mahasiswa>
