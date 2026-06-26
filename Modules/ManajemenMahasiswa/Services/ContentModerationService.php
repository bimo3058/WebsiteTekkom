<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ContentModerationService
{
    // =========================================================================
    // Daftar Kata Terlarang (Forbidden Words)
    // =========================================================================

    /**
     * Daftar kata-kata terlarang / sensor.
     * Bisa ditambahkan sesuai kebutuhan kampus.
     * Pengecekan bersifat case-insensitive dan mendukung variasi umum.
     */
    private const FORBIDDEN_WORDS = [
        // ── Kata Kasar / Umpatan ──────────────────────────────────────────
        'anjing',
        'bangsat',
        'bajingan',
        'brengsek',
        'babi',
        'kampret',
        'keparat',
        'sialan',
        'setan',
        'iblis',
        'tolol',
        'bodoh',
        'goblok',
        'idiot',
        'dungu',
        'bego',
        'tai',
        'kontol',
        'memek',
        'ngentot',
        'jancok',
        'asu',
        'cuk',
        'jancuk',
        'dancuk',
        'pantek',
        'pantek',
        'pepek',
        'titit',
        'ngewe',
        'pelacur',
        'sundal',
        'lonte',
        'lacur',

        // ── SARA / Diskriminasi ───────────────────────────────────────────
        'kafir',
        'rasis',
        'nigga',
        'nigger',

        // ── Kekerasan / Ancaman ───────────────────────────────────────────
        'bunuh',
        'pembunuh',
        'membunuh',

        // ── Spam / Penipuan ───────────────────────────────────────────────
        'judi online',
        'slot gacor',
        'togel',
        'situs judi',
        'bandar togel',
        'poker online',
        'slot online',
        'casino online',

        // ── Variasi Leetspeak / Evasion Umum ──────────────────────────────
        'g0bl0k',
        'b4ngsat',
        'k0nt0l',
        'b4bi',
        'anj1ng',
        't0l0l',
    ];

    /**
     * Pola regex tambahan untuk menangkap variasi kata terlarang
     * yang menggunakan karakter pengganti (angka, simbol, spasi).
     */
    private const FORBIDDEN_PATTERNS = [
        '/\ba\s*n\s*j\s*i?\s*n\s*g\b/iu',
        '/\bb\s*a\s*n\s*g\s*s\s*a\s*t\b/iu',
        '/\bg\s*o\s*b\s*l\s*o\s*k\b/iu',
        '/\bk\s*o\s*n\s*t\s*o\s*l\b/iu',
        '/\bn\s*g\s*e\s*n\s*t\s*o\s*t\b/iu',
        '/\bj\s*a\s*n\s*c\s*[ou]\s*k\b/iu',
        '/\bslot\s*gacor\b/iu',
        '/\bjudi\s*online\b/iu',
    ];

    // =========================================================================
    // Rate Limiting (Anti-Spam)
    // =========================================================================

    /**
     * Konfigurasi rate limit per aksi.
     * Format: [maxAttempts, decaySeconds]
     */
    private const RATE_LIMITS = [
        'create_thread'  => ['max' => 3,  'decay' => 300],   // 3 thread per 5 menit
        'update_thread'  => ['max' => 10, 'decay' => 300],   // 10 edit per 5 menit
        'create_comment' => ['max' => 10, 'decay' => 60],    // 10 komentar per 1 menit
        'update_comment' => ['max' => 15, 'decay' => 60],    // 15 edit komentar per 1 menit
        'save_draft'     => ['max' => 10, 'decay' => 60],    // 10 draft save per 1 menit
    ];

    // =========================================================================
    // API Publik
    // =========================================================================

    /**
     * Cek apakah konten mengandung kata terlarang.
     *
     * @param  string  ...$texts  Satu atau lebih string untuk diperiksa
     * @return array{passed: bool, found_words: string[]}
     */
    public function checkForbiddenWords(string ...$texts): array
    {
        $foundWords = [];

        foreach ($texts as $text) {
            if (empty($text)) {
                continue;
            }

            // Normalisasi teks: lowercase, hapus karakter khusus berulang
            $normalizedText = $this->normalizeText($text);

            // 1) Cek kata-kata dari daftar statis
            foreach (self::FORBIDDEN_WORDS as $word) {
                $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
                if (preg_match($pattern, $normalizedText)) {
                    $foundWords[] = $word;
                }
            }

            // 2) Cek pola regex untuk variasi
            foreach (self::FORBIDDEN_PATTERNS as $pattern) {
                if (preg_match($pattern, $normalizedText)) {
                    // Ekstrak kata yang cocok untuk pesan error
                    preg_match($pattern, $normalizedText, $matches);
                    if (!empty($matches[0]) && !in_array(strtolower(trim($matches[0])), $foundWords)) {
                        $foundWords[] = strtolower(trim($matches[0]));
                    }
                }
            }
        }

        $foundWords = array_unique($foundWords);

        return [
            'passed'      => empty($foundWords),
            'found_words' => array_values($foundWords),
        ];
    }

    /**
     * Cek rate limit untuk aksi tertentu.
     *
     * @param  int     $userId  ID user
     * @param  string  $action  Nama aksi (create_thread, create_comment, dll.)
     * @return array{passed: bool, retry_after: int, message: string}
     */
    public function checkRateLimit(int $userId, string $action): array
    {
        $config = self::RATE_LIMITS[$action] ?? ['max' => 5, 'decay' => 60];
        $key    = "forum_rate:{$action}:{$userId}";

        if (RateLimiter::tooManyAttempts($key, $config['max'])) {
            $retryAfter = RateLimiter::availableIn($key);

            return [
                'passed'      => false,
                'retry_after' => $retryAfter,
                'message'     => $this->buildRateLimitMessage($action, $retryAfter),
            ];
        }

        // Hit rate limiter
        RateLimiter::hit($key, $config['decay']);

        return [
            'passed'      => true,
            'retry_after' => 0,
            'message'     => '',
        ];
    }

    /**
     * Validasi konten secara lengkap: rate limit + forbidden words.
     * Convenience method yang menggabungkan kedua pengecekan.
     *
     * @param  int     $userId  ID user
     * @param  string  $action  Nama aksi
     * @param  string  ...$texts  Teks yang akan diperiksa
     * @return array{passed: bool, errors: string[]}
     */
    public function validateContent(int $userId, string $action, string ...$texts): array
    {
        $errors = [];

        // 1) Rate limit check
        $rateResult = $this->checkRateLimit($userId, $action);
        if (!$rateResult['passed']) {
            $errors[] = $rateResult['message'];
        }

        // 2) Forbidden words check
        $wordResult = $this->checkForbiddenWords(...$texts);
        if (!$wordResult['passed']) {
            $censored = array_map(fn($w) => $this->censorWord($w), $wordResult['found_words']);
            $errors[] = 'Konten mengandung kata yang tidak diizinkan: ' . implode(', ', $censored)
                . '. Mohon gunakan bahasa yang sopan dan sesuai etika.';
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors,
        ];
    }

    // =========================================================================
    // Helper Privat
    // =========================================================================

    /**
     * Normalisasi teks untuk pengecekan — konversi variasi karakter umum.
     */
    private function normalizeText(string $text): string
    {
        // Lowercase
        $text = mb_strtolower($text);

        // Ganti angka yang sering dipakai sebagai huruf (leetspeak)
        $leetMap = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
            '!' => 'i',
        ];
        $text = strtr($text, $leetMap);

        // Hapus karakter non-alfanumerik yang dipakai sebagai separator spam
        // tapi pertahankan spasi
        $text = preg_replace('/[_\-\.\*\+\#]+/', '', $text);

        return $text;
    }

    /**
     * Sensor kata untuk ditampilkan di pesan error.
     * Contoh: "anjing" → "a****g"
     */
    private function censorWord(string $word): string
    {
        $len = mb_strlen($word);
        if ($len <= 2) {
            return str_repeat('*', $len);
        }

        return mb_substr($word, 0, 1) . str_repeat('*', $len - 2) . mb_substr($word, -1);
    }

    /**
     * Bangun pesan error rate limit yang informatif.
     */
    private function buildRateLimitMessage(string $action, int $retryAfter): string
    {
        $actionLabels = [
            'create_thread'  => 'membuat thread',
            'update_thread'  => 'mengedit thread',
            'create_comment' => 'membuat komentar',
            'update_comment' => 'mengedit komentar',
            'save_draft'     => 'menyimpan draf',
        ];

        $label = $actionLabels[$action] ?? $action;

        if ($retryAfter >= 60) {
            $minutes = ceil($retryAfter / 60);
            $timeStr = "{$minutes} menit";
        } else {
            $timeStr = "{$retryAfter} detik";
        }

        return "Anda terlalu sering {$label}. Silakan coba lagi dalam {$timeStr}.";
    }
}
