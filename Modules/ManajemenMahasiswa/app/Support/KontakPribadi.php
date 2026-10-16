<?php

namespace Modules\ManajemenMahasiswa\Support;

use App\Models\CvProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;

/**
 * Kontak pribadi (Email Pribadi + WhatsApp/Telepon) milik seorang mahasiswa/alumni.
 *
 * Dipakai form edit Direktori Mahasiswa dan Direktori Alumni, baik jalur pengelola
 * maupun jalur pemilik — satu aturan validasi dan satu cara menulis, supaya data yang
 * ditolak di satu bab tidak lolos lewat bab lain.
 *
 * Nama field form: `email_pribadi`, `kontak` (nomor lokal), `phone_code` (+62 dst).
 */
class KontakPribadi
{
    public static function aturan(?User $pemilik): array
    {
        return [
            'kontak'        => 'nullable|string|max:15',
            'phone_code'    => ['nullable', 'regex:/^\+\d{1,4}$/'],
            // Sama dengan aturan Settings global: email pribadi bukan email SSO.
            'email_pribadi' => ['nullable', 'email', 'max:254', Rule::notIn(array_filter([$pemilik?->email]))],
        ];
    }

    public static function pesan(): array
    {
        return [
            'phone_code.regex'     => 'Kode negara nomor WhatsApp tidak dikenal.',
            'email_pribadi.not_in' => 'Email pribadi tidak boleh sama dengan email UNDIP.',
        ];
    }

    public static function atribut(): array
    {
        return ['email_pribadi' => 'email pribadi'];
    }

    /**
     * Kolom kontak yang benar-benar berubah: ['whatsapp' => [lama, baru], 'personal_email' => [lama, baru]].
     * Nilai lama WhatsApp dibaca dengan urutan yang sama seperti form (users.whatsapp,
     * lalu mk_kemahasiswaan.kontak), supaya kiriman tanpa perubahan tidak terhitung berubah.
     */
    public static function perubahan(Request $request, ?User $pemilik, ?string $kontakCadangan = null): array
    {
        $perubahan = [];

        if ($request->has('kontak')) {
            $lama = ($pemilik?->whatsapp ?? $kontakCadangan) ?: null;
            $baru = self::normalisasiWa($request->input('kontak'), $request->input('phone_code'));
            if ($baru !== $lama) {
                $perubahan['whatsapp'] = [$lama, $baru];
            }
        }

        if ($request->has('email_pribadi')) {
            $lama = $pemilik?->personal_email ?: null;
            $baru = $request->input('email_pribadi') ?: null;
            if ($baru !== $lama) {
                $perubahan['personal_email'] = [$lama, $baru];
            }
        }

        return $perubahan;
    }

    /** Nomor lokal + kode negara → "+62812…"; kosong → null. */
    public static function normalisasiWa(?string $nomor, ?string $kode): ?string
    {
        $kode  = $kode ?: '+62';
        $nomor = preg_replace('/[^\d]/', '', (string) $nomor);
        if ($kode === '+62') {
            $nomor = ltrim($nomor, '0');
        }

        return $nomor === '' ? null : $kode . $nomor;
    }

    /**
     * Tulis kontak ke semua tempat yang menyimpannya: users (Settings global),
     * cv_profiles (CV Builder), dan mk_kemahasiswaan.kontak (Direktori). Panggil di
     * dalam transaksi, supaya kegagalan di satu tempat membatalkan semuanya.
     */
    public static function tulis(int $userId, ?User $pemilik, array $kontak): void
    {
        if (!$kontak) {
            return;
        }

        $kolomUser = array_map(fn ($nilai) => $nilai[1], $kontak);
        $pemilik?->updateQuietly($kolomUser);

        $kolomCv = [];
        if (array_key_exists('whatsapp', $kolomUser)) {
            $kolomCv['cv_whatsapp'] = $kolomUser['whatsapp'];
            Kemahasiswaan::where('user_id', $userId)->update(['kontak' => $kolomUser['whatsapp']]);
        }
        if (array_key_exists('personal_email', $kolomUser)) {
            $kolomCv['cv_email'] = $kolomUser['personal_email'];
        }
        CvProfile::where('user_id', $userId)->update($kolomCv);
    }

    /** Ringkasan kolom untuk deskripsi audit — tanpa nilai (deskripsi ikut ke log terpusat). */
    public static function ringkasan(array $kontak): array
    {
        $ringkasan = [];
        if (isset($kontak['personal_email'])) {
            $ringkasan[] = 'email pribadi';
        }
        if (isset($kontak['whatsapp'])) {
            $ringkasan[] = 'nomor WhatsApp';
        }

        return $ringkasan;
    }
}
