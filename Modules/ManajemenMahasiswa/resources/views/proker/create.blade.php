<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Rencana Proker — Buat.
    Form memakai partial bersama dengan Pelaksanaan Kegiatan.
    $showDokumentasi = false → kartu Foto tidak dirender (dokumentasi acara, diisi di
    Pelaksanaan). $showDokumen = true → Dokumen Rencana (proposal, RAB, surat) yang
    ikut disalin ke Pelaksanaan saat diajukan (S-49).
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => false,
    'showDokumen'     => true,
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
    'showDokumen'     => true,
])

</x-manajemenmahasiswa::layouts.mahasiswa>
