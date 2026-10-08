<?php

namespace Modules\ManajemenMahasiswa\Support;

use Illuminate\Http\Request;

/**
 * Pilihan "Per page" untuk footer tabel/daftar modul Manajemen Mahasiswa
 * (resources/views/partials/table-footer.blade.php).
 *
 * Whitelist ini dipakai bersama oleh controller (memvalidasi ?per_page=) dan view
 * (membangun dropdown), supaya keduanya tidak bisa berbeda pendapat.
 */
class PerPage
{
    /** Tabel & daftar baris — pilihan yang sama dengan tabel Audit Log global. */
    public const TABEL = [5, 10, 25, 50];

    /** Grid kartu 2–3 kolom: kelipatan 6 supaya baris terakhir tidak pincang. */
    public const KARTU = [6, 12, 24, 48];

    /**
     * Baca ?per_page= dari request. Nilai di luar daftar (diketik manual,
     * mis. ?per_page=100000) jatuh ke $default, bukan dipercaya begitu saja.
     *
     * @param  int[]  $options
     */
    public static function resolve(
        Request $request,
        array $options = self::TABEL,
        int $default = 10,
        string $param = 'per_page',
    ): int {
        $value = (int) $request->input($param);

        return in_array($value, $options, true) ? $value : $default;
    }
}
