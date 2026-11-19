<?php

namespace Modules\ManajemenMahasiswa\Support;

/**
 * Batas total satu kiriman form berkas di server.
 *
 * Batas per file diperiksa validasi Laravel, tetapi `post_max_size` PHP membatasi
 * SELURUH isi satu kiriman (banner + foto + dokumen). Kiriman yang melewatinya
 * ditolak sebelum sampai ke controller dan berakhir di halaman error, jadi form
 * kegiatan menahannya di layar lebih dulu (partials/kegiatan-form/_batas-kirim).
 */
class BatasUnggah
{
    /**
     * `post_max_size` dalam byte, atau null bila tidak dibatasi.
     */
    public static function totalKirimByte(): ?int
    {
        $nilai = strtolower(trim((string) ini_get('post_max_size')));

        if ($nilai === '' || $nilai === '0' || $nilai === '-1') {
            return null;
        }

        $angka = (float) $nilai;

        return (int) match (substr($nilai, -1)) {
            'g'     => $angka * 1024 ** 3,
            'm'     => $angka * 1024 ** 2,
            'k'     => $angka * 1024,
            default => $angka,
        };
    }
}
