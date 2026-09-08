<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Rencana Proker — Edit.
    Bisa diakses ketua himpunan/bidang/unit, admin, dan staff_himpunan
    (staff hanya boleh mengedit, tidak membuat/mengajukan/menghapus).
    $showDokumentasi = false → Foto & Dokumen Kegiatan diisi di subbab Pelaksanaan.
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => false,
    'tahapRencana'    => true,
    'formAction'      => route('manajemenmahasiswa.proker.update', $proker->id),
    'formMethod'      => 'PUT',
    'backUrl'         => route('manajemenmahasiswa.proker.show', $proker->id),
    'headerTitle'     => 'Edit Rencana Proker',
    'headerSubtitle'  => 'Perbarui rencana <strong>' . e($proker->judul) . '</strong>',
    'submitLabel'     => 'Simpan Perubahan',
])

@include('manajemenmahasiswa::partials.kegiatan-form._scripts', [
    'showDokumentasi' => false,
])

</x-manajemenmahasiswa::layouts.mahasiswa>
