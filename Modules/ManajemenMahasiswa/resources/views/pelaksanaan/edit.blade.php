<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Pelaksanaan Kegiatan — Edit.
    Subbab ini tempat pengurus mengecek apakah data yang direncanakan di
    Rencana Proker sudah sesuai realisasi, sekaligus mengunggah dokumentasi.
    $showDokumentasi = true → kartu Foto & Dokumen Kegiatan ikut dirender.
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => true,
    'tahapRencana'    => false,
    'formAction'      => route('manajemenmahasiswa.pelaksanaan.update', $proker->id),
    'formMethod'      => 'PUT',
    'backUrl'         => route('manajemenmahasiswa.pelaksanaan.show', $proker->id),
    'headerTitle'     => 'Cek & Perbarui Data Pelaksanaan',
    'headerSubtitle'  => 'Sesuaikan data <strong>' . e($proker->judul) . '</strong> dengan realisasi di lapangan, lalu unggah foto & dokumen kegiatan.',
    'submitLabel'     => 'Simpan Perubahan',
])

@include('manajemenmahasiswa::partials.kegiatan-form._scripts', [
    'showDokumentasi' => true,
])

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>
