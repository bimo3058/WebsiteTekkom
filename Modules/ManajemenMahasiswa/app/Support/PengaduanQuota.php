<?php

namespace Modules\ManajemenMahasiswa\Support;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Kuota pengaduan per akun per 24 jam (kedua jalur berbagi satu jatah).
 *
 * Penghitung disimpan di cache (redis) dengan kedaluwarsa otomatis dan TIDAK
 * ditempelkan pada tiket, sehingga tidak menjadi jalur balik ke identitas
 * pelapor konfidensial. Untuk jalur konfidensial, jatah dipakai saat tautan
 * (draft) dibuat, karena saat pengiriman akhir sistem sengaja tidak tahu siapa pelapornya.
 */
final class PengaduanQuota
{
    public const MAX_PER_DAY = 3;

    private const DECAY_SECONDS = 86400;

    public static function exhausted(int $userId): bool
    {
        return RateLimiter::tooManyAttempts(self::key($userId), self::MAX_PER_DAY);
    }

    public static function consume(int $userId): void
    {
        RateLimiter::hit(self::key($userId), self::DECAY_SECONDS);
    }

    public static function message(int $userId): string
    {
        $jam = max(1, (int) ceil(RateLimiter::availableIn(self::key($userId)) / 3600));

        return 'Batas pengaduan harian tercapai (' . self::MAX_PER_DAY . ' pengaduan per 24 jam). '
            . 'Silakan coba lagi sekitar ' . $jam . ' jam lagi.';
    }

    private static function key(int $userId): string
    {
        return 'mm-pengaduan-harian:' . $userId;
    }
}
