<?php

namespace Modules\ManajemenMahasiswa\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Perangkap bot untuk form pengaduan: satu field tersembunyi yang tidak boleh
 * terisi, ditambah cap waktu terenkripsi untuk menolak pengiriman yang terlalu
 * cepat untuk dilakukan manusia. Tanpa gesekan bagi pengguna (bukan captcha).
 *
 * Markup form ada di views/pengaduan/partials/honeypot.blade.php.
 */
final class Honeypot
{
    public const FIELD = 'website_url';

    public const STAMP_FIELD = '_hp_ts';

    private const MIN_SECONDS = 3;

    private const MAX_SECONDS = 21600; // 6 jam

    public static function stamp(): string
    {
        return Crypt::encryptString((string) time());
    }

    /**
     * True bila pengiriman lolos: field jebakan kosong dan cap waktu sah,
     * tidak terlalu baru, dan tidak kedaluwarsa.
     */
    public static function passes(?string $trap, ?string $stamp): bool
    {
        if ($trap !== null && $trap !== '') {
            return false;
        }

        if (! is_string($stamp) || $stamp === '') {
            return false;
        }

        try {
            $issuedAt = (int) Crypt::decryptString($stamp);
        } catch (DecryptException) {
            return false;
        }

        $age = time() - $issuedAt;

        return $age >= self::MIN_SECONDS && $age <= self::MAX_SECONDS;
    }
}
