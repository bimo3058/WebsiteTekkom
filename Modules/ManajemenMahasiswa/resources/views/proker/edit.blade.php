<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Rencana Proker — Edit.
    Bisa diakses ketua himpunan/bidang/unit, admin, dan staff_himpunan
    (staff hanya boleh mengedit, tidak membuat/mengajukan/menghapus).
    $showDokumentasi = false → Foto diisi di subbab Pelaksanaan; $showDokumen = true →
    Dokumen Rencana (proposal, RAB, surat) yang ikut disalin saat diajukan (S-49).
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => false,
    'showDokumen'     => true,
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
    'showDokumen'     => true,
])

</x-manajemenmahasiswa::layouts.mahasiswa>
