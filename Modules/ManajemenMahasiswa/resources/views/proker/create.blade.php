<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

@include('manajemenmahasiswa::partials.kegiatan-form._styles')

{{--
    Rencana Proker — Buat (form ringkas, S-82 11 Nov 2026).
    KABID dkk. hanya mengisi Judul, Kategori (S-85: centang Kegiatan Prodi bila bekerja
    sama dengan prodi), Bidang/Unit, dan Ketua Pelaksana; selebihnya diisi
    ketua pelaksana lewat form Edit, lalu ia mengirimnya untuk ditinjau. Partial form
    tetap sama dengan Edit & Pelaksanaan ($ringkas menyembunyikan kartu lain).
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._fields', [
    'showDokumentasi' => false,
    'showDokumen'     => false,
    'tahapRencana'    => true,
    'ringkas'         => true,
    'formAction'      => route('manajemenmahasiswa.proker.store'),
    'formMethod'      => 'POST',
    'backUrl'         => route('manajemenmahasiswa.proker.index'),
    'headerTitle'     => 'Buat Rencana Proker',
    'headerSubtitle'  => 'Isi judul, kategori, bidang/unit, dan tunjuk ketua pelaksana. Ketua pelaksana yang melengkapi data proker lalu mengirimnya untuk Anda tinjau.',
    'submitLabel'     => 'Simpan',
])

@include('manajemenmahasiswa::partials.kegiatan-form._scripts', [
    'showDokumentasi' => false,
    'showDokumen'     => false,
])

</x-manajemenmahasiswa::layouts.mahasiswa>
