<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;

/**
 * Data di balik bagian "Akses Kelola" pada form kegiatan — dipakai bersama
 * Rencana Proker, Pelaksanaan, dan Laporan & Arsip. Aturan siapa-boleh-apa ada
 * di KegiatanPolicy; service ini menyiapkan pilihannya dan menyimpan hasilnya.
 */
class PengelolaKegiatanService
{
    /**
     * Pengurus yang bisa dipilih di bagian Akses Kelola: staff himpunan saja.
     *
     * Ketua bidang/unit lain tidak ditawarkan — satu proker dipegang satu bidang,
     * jadi mereka tidak berhak mengedit apalagi menghapus proker buatan orang lain
     * (KegiatanPolicy::bisaJadiPengelola). Ketua Himpunan dan admin juga tidak
     * ditawarkan karena hak editnya sudah datang dari KegiatanPolicy.
     *
     * @return Collection<int, array{id: int, nama: string, role: string}>
     */
    public function calonPengelola(): Collection
    {
        return User::query()
            ->whereHas('roles', fn($q) => $q->whereIn('name', KegiatanPolicy::ROLE_PENGELOLA))
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', [
                ...KegiatanPolicy::JABATAN_KETUA,
                ...KegiatanPolicy::PENGELOLA_SEMUA,
            ]))
            ->orderBy('name')
            ->get()
            ->map(fn(User $user) => [
                'id'   => (int) $user->id,
                'nama' => $user->name,
                'role' => 'Staff Himpunan',
            ])
            ->values();
    }

    /**
     * Variabel untuk partial kegiatan-form/_akses_kelola.blade.php.
     *
     * @return array{bolehAturAkses: bool, calonPengelola: Collection, pengelolaTerpilih: list<int>, namaPembuat: string}
     */
    public function dataForm(Kegiatan $kegiatan): array
    {
        // Kegiatan baru: route create hanya terbuka untuk role pembuat, jadi yang
        // sedang mengisi form pasti calon pemiliknya.
        $bolehAturAkses = !$kegiatan->exists || Gate::allows('aturAkses', $kegiatan);

        if (!$bolehAturAkses) {
            return [
                'bolehAturAkses'    => false,
                'calonPengelola'    => collect(),
                'pengelolaTerpilih' => [],
                'namaPembuat'       => '',
            ];
        }

        $pemilikId = $this->pemilikId($kegiatan);

        return [
            'bolehAturAkses'    => true,
            // Pemilik sudah pasti berhak, jadi tidak ditawarkan di daftarnya sendiri.
            'calonPengelola'    => $this->calonPengelola()
                ->reject(fn($calon) => $calon['id'] === $pemilikId)
                ->values(),
            'pengelolaTerpilih' => $this->terpilih($kegiatan),
            'namaPembuat'       => $kegiatan->exists
                ? ($kegiatan->creator?->name ?? '-')
                : (Auth::user()?->name ?? '-'),
        ];
    }

    /**
     * Id pengurus yang sedang tercantum sebagai pengelola.
     *
     * Setelah validasi gagal, isian terakhir user (old()) yang dipakai, bukan DB —
     * pola yang sama dengan chip panitia di _scripts.blade.php. Penandanya
     * `akses_kelola_dikirim`, bukan `pengelola_ids`: kalau semua chip dihapus,
     * `pengelola_ids` tidak terkirim sama sekali dan chip lama akan muncul lagi.
     *
     * @return list<int>
     */
    public function terpilih(Kegiatan $kegiatan): array
    {
        if (old('akses_kelola_dikirim') !== null) {
            return array_map('intval', (array) old('pengelola_ids', []));
        }

        if (!$kegiatan->exists) {
            return [];
        }

        // Pengelola lama yang kini tidak memenuhi syarat (mis. ketua bidang lain dari
        // sebelum aturan satu-bidang) tidak dimunculkan, sehingga ikut terlepas saat
        // form disimpan — hak aksesnya sendiri sudah dicabut KegiatanPolicy::update.
        return $kegiatan->pengelola
            ->filter(fn(User $user) => KegiatanPolicy::bisaJadiPengelola($user))
            ->map(fn(User $user) => (int) $user->id)
            ->values()
            ->all();
    }

    /**
     * Simpan daftar pengelola dari bagian "Akses Kelola".
     *
     * Hanya diproses bila bagian itu memang dirender (penanda akses_kelola_dikirim)
     * DAN penyimpan berhak mengatur akses. Pengelola biasa tidak melihat bagian ini;
     * kalau ia memalsukan `pengelola_ids`, isinya diabaikan.
     */
    public function sync(Kegiatan $kegiatan, Request $request): void
    {
        if (!$request->has('akses_kelola_dikirim') || Gate::denies('aturAkses', $kegiatan)) {
            return;
        }

        $calon     = $this->calonPengelola()->keyBy('id');
        $pemilikId = $this->pemilikId($kegiatan);

        $data = [];
        foreach ((array) $request->input('pengelola_ids', []) as $id) {
            $id = (int) $id;

            // Hanya pengurus yang memang bisa dipilih; pemilik tidak perlu dicantumkan.
            // Ketua bidang/unit lain yang dikirim lewat request palsu ikut tersaring di sini.
            if (!$calon->has($id) || $id === $pemilikId) {
                continue;
            }

            // Pengelola tambahan tidak pernah boleh menghapus (KegiatanPolicy::delete).
            $data[$id] = ['boleh_hapus' => false];
        }

        $kegiatan->pengelola()->sync($data);
    }

    /**
     * Pesan untuk orang yang role-nya boleh mengelola tetapi bukan pengelola
     * kegiatan ini (dipanggil hanya bila ia memang ditolak KegiatanPolicy).
     * Nama pembuat hanya disebut bila ia memang masih pemiliknya.
     */
    public function pesanTolak(Kegiatan $kegiatan): string
    {
        // Ketua Himpunan selalu boleh mengedit (KegiatanPolicy::EDIT_SEMUA), jadi
        // bila ia ditolak, yang ditolak pasti penghapusan.
        if (Auth::user()?->hasAnyRole(KegiatanPolicy::EDIT_SEMUA)) {
            return 'Ketua Himpunan bisa mengedit semua proker, tetapi menghapus hanya '
                . 'bisa dilakukan pembuatnya atau admin.';
        }

        // Para ketua bidang/unit tidak bisa ditambahkan di Akses Kelola, jadi
        // jangan sarankan hal yang mustahil.
        if (Auth::user()?->hasAnyRole(KegiatanPolicy::JABATAN_KETUA)) {
            return 'Kegiatan ini bukan buatan Anda. Ketua hanya bisa mengelola proker '
                . 'yang ia buat sendiri; perubahan proker ini dilakukan pembuatnya atau admin.';
        }

        $pemilik = $this->pemilikId($kegiatan) !== null ? $kegiatan->creator : null;

        return 'Anda bukan pengelola kegiatan ini. Minta '
            . ($pemilik ? "pembuatnya ({$pemilik->name}) atau " : '')
            . 'admin menambahkan Anda di bagian Akses Kelola.';
    }

    /**
     * Id pemilik kegiatan — sama dengan KegiatanPolicy: pembuatnya, selama ia
     * masih memegang role pembuat proker. Null bila tidak ada pemilik aktif
     * (mis. dibuat akun yang kini view-only), sehingga hanya override yang mengurus.
     */
    private function pemilikId(Kegiatan $kegiatan): ?int
    {
        if (!$kegiatan->exists) {
            return Auth::id() !== null ? (int) Auth::id() : null;
        }

        $pembuat = $kegiatan->creator;

        return $pembuat && $pembuat->hasAnyRole(KegiatanPolicy::PEMBUAT_PROKER)
            ? (int) $pembuat->id
            : null;
    }
}
