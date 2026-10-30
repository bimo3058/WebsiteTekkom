<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\CvProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Policies\KemahasiswaanPolicy;
use Modules\ManajemenMahasiswa\Support\KontakPribadi;
use Modules\ManajemenMahasiswa\Support\PerPage;

class DirektoriMahasiswaController extends Controller
{
    /**
     * Roles user yang sedang login — di-cache agar hanya ada 1 query per request.
     * Menghindari multiple round-trip ke Supabase (penyebab QueryException pada koneksi yang tidak stabil).
     */
    private ?array $userRoles = null;

    private function getUserRoles(): array
    {
        if ($this->userRoles !== null) {
            return $this->userRoles;
        }

        try {
            $this->userRoles = Auth::user()?->roles?->pluck('name')?->toArray() ?? [];
        } catch (\Throwable) {
            $this->userRoles = [];
        }

        return $this->userRoles;
    }

    /**
     * Apakah user boleh melihat kontak pribadi mahasiswa lain.
     * Daftar role ada di KemahasiswaanPolicy::LIHAT_KONTAK_PRIBADI — satu sumber kebenaran.
     */
    private function bisaLihatKontakPribadi(): bool
    {
        return $this->hasRole(...KemahasiswaanPolicy::LIHAT_KONTAK_PRIBADI);
    }

    /**
     * Jalankan closure DB dengan retry otomatis jika koneksi Supabase terputus.
     * pgBouncer (port 6543) kadang menutup koneksi idle → SQLSTATE 08006 / 08003.
     * Solusi: force reconnect lalu coba lagi, maks $maxAttempts kali.
     *
     * @template T
     * @param  callable(): T $callback
     * @param  int           $maxAttempts
     * @return T
     */
    /**
     * Deteksi error koneksi Supabase/pgBouncer (bukan error data/validasi).
     */
    private function isConnectionError(\Throwable $e): bool
    {
        $msg  = $e->getMessage();
        $code = (string) $e->getCode();

        return in_array($code, ['08006', '08003', '57P01', '7'])
            || str_contains($msg, 'server closed the connection')
            || str_contains($msg, 'SSL negotiation')
            || str_contains($msg, 'could not connect')
            || str_contains($msg, 'connection unexpectedly')
            || str_contains($msg, 'pooler.supabase.com');
    }

    private function withRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                $attempt++;

                $isConnectionError = $this->isConnectionError($e);

                if ($isConnectionError && $attempt < $maxAttempts) {
                    // Jeda sebelum retry: beri waktu pgBouncer memulihkan pool
                    usleep(200_000 * $attempt); // 200ms, 400ms, ...
                    try {
                        DB::reconnect();
                    } catch (\Throwable) {
                        // reconnect juga bisa gagal, tetap lanjut retry
                    }
                    $this->userRoles = null;
                    continue;
                }

                throw $e;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Kecualikan mahasiswa yang sudah tercatat di mk_alumni (sumber Direktori Alumni).
     * Dulu memakai kolom `status` = 'alumni'; sejak S-53 direktori ini tidak membaca status lagi.
     * NOT EXISTS (bukan NOT IN) supaya user_id NULL di mk_alumni tidak mengosongkan hasil.
     */
    private function bukanAlumni($query)
    {
        return $query->whereNotExists(function ($q) {
            $q->selectRaw('1')
                ->from('mk_alumni')
                ->whereColumn('mk_alumni.user_id', 'mk_kemahasiswaan.user_id');
        });
    }

    /**
     * Sinkronisasi data SSO (tabel students + users) ke mk_kemahasiswaan.
     * Otomatis membuat entry untuk mahasiswa yang belum terdaftar di direktori.
     */
    private function syncFromSSO(): void
    {
        $this->withRetry(function () {
            $existingUserIds = Kemahasiswaan::pluck('user_id')->toArray();

            $studentsNotSynced = Student::with('user')
                ->whereNotIn('user_id', $existingUserIds)
                ->get();

            if ($studentsNotSynced->isEmpty()) {
                return;
            }

            // Mahasiswa yang sudah tercatat di mk_alumni jangan masuk direktori sebagai "aktif".
            $alumniUserIds = Alumni::whereIn('user_id', $studentsNotSynced->pluck('user_id'))
                ->pluck('user_id')
                ->toArray();

            foreach ($studentsNotSynced as $student) {
                if (!$student->user)
                    continue;

                // Satu baris data yang bermasalah tidak boleh menghentikan sinkronisasi
                // mahasiswa lain di belakangnya. Error koneksi tetap dilempar ke withRetry.
                try {
                    // firstOrCreate (bukan create) mencegah baris ganda bila dua request
                    // membuka halaman ini bersamaan — kolom user_id belum punya unique index.
                    // PERINGATAN (S-53, 29 Okt 2026): `status` tidak lagi dikelola di Direktori
                    // Mahasiswa (sumber resmi = SIAP Undip). Nilai ini diisi hanya karena kolomnya
                    // NOT NULL dan Dashboard Analitik / Permission (Devarlo, Syahbana) masih membacanya.
                    Kemahasiswaan::firstOrCreate(
                        ['user_id' => $student->user_id],
                        [
                            'nama'     => $student->user->name,
                            'nim'      => $student->student_number,
                            'angkatan' => $student->cohort_year,
                            'status'   => \in_array($student->user_id, $alumniUserIds)
                                ? Kemahasiswaan::STATUS_ALUMNI
                                : Kemahasiswaan::STATUS_AKTIF,
                        ]
                    );
                } catch (\Throwable $e) {
                    if ($this->isConnectionError($e)) {
                        throw $e;
                    }

                    \Illuminate\Support\Facades\Log::warning(
                        'Sync SSO → mk_kemahasiswaan gagal untuk satu mahasiswa, dilewati.',
                        [
                            'user_id' => $student->user_id,
                            'nim'     => $student->student_number,
                            'error'   => $e->getMessage(),
                        ]
                    );
                }
            }
        });
    }

    /**
     * Tentukan layout blade berdasarkan role user yang sedang login.
     */
    private function resolveLayout(): string
    {
        $roles = $this->getUserRoles();

        // Admin group + DPM → layout admin
        if (\in_array('superadmin', $roles) || \in_array('admin_kemahasiswaan', $roles) || \in_array('dpm', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }

        // GPM, Dosen, Ketua Departemen → layout dosen
        if (\in_array('gpm', $roles) || \in_array('dosen', $roles) || \in_array('ketua_departemen', $roles)) {
            return 'manajemenmahasiswa::layouts.dosen';
        }

        // Semua jenis pengurus himpunan → layout admin
        $pengurus = ['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'];
        foreach ($pengurus as $role) {
            if (\in_array($role, $roles)) {
                return 'manajemenmahasiswa::layouts.admin';
            }
        }

        // Default: mahasiswa / alumni
        return 'manajemenmahasiswa::layouts.mahasiswa';
    }

    /**
     * Cek apakah user memiliki role tertentu.
     */
    private function hasRole(string ...$roles): bool
    {
        $userRoles = $this->getUserRoles();
        foreach ($roles as $r) {
            if (\in_array($r, $userRoles)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gabungkan riwayat kegiatan manual (mk_riwayat_kegiatan)
     * dengan data otomatis dari mk_kegiatan:
     *   - sebagai ketua pelaksana (ketua_pelaksana_id)
     *   - sebagai panitia (pivot mk_kegiatan_panitia)
     * Hasilnya: satu collection unified tanpa duplikasi.
     */
    private function buildMergedRiwayat(int $userId): Collection
    {
        // 1. Cari students.id dari user_id
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return collect();
        }

        $studentId = $student->id;

        // 2. Ambil riwayat manual (menggunakan students.id) yang sudah disetujui
        $riwayatManual = RiwayatKegiatan::with('kegiatan')
            ->where('student_id', $studentId)
            ->where('verification_status', 'approved')
            ->get();

        // 3. Ambil semua kegiatan di mana mahasiswa ini adalah ketua pelaksana
        //    Hanya kegiatan berstatus "selesai" yang dihitung sebagai riwayat
        //    (konsisten dengan Laporan & Arsip; menyembunyikan draft/legacy).
        $kegiatanAsKetua = Kegiatan::where('ketua_pelaksana_id', $studentId)
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->get();

        // 4. Ambil semua kegiatan di mana mahasiswa ini adalah panitia (via pivot)
        $kegiatanAsPanitia = Kegiatan::whereHas('panitia', fn($q) => $q->where('students.id', $studentId))
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->with(['panitia' => fn($q) => $q->where('students.id', $studentId)])
            ->get();

        // 5. Kegiatan_id yang sudah ada di riwayat manual (untuk hindari duplikat)
        $existingKegiatanIds = $riwayatManual->pluck('kegiatan_id')->filter()->toArray();

        // 6. Buat pseudo-riwayat dari data ketua pelaksana yang belum ada di manual
        $autoRiwayat = $kegiatanAsKetua
            ->filter(fn($kg) => !in_array($kg->id, $existingKegiatanIds))
            ->map(function ($kg) use ($studentId) {
                $item = new \stdClass();
                $item->id = null;
                $item->student_id = $studentId;
                $item->kegiatan_id = $kg->id;
                $item->peran = 'ketua';
                $item->peran_manual = null;
                $item->nama_kegiatan_manual = null;
                $item->tanggal_kegiatan = null;
                $item->kegiatan = $kg;
                $item->is_auto = true;
                $item->created_at = $kg->created_at;
                $item->updated_at = $kg->updated_at;
                return $item;
            });

        // 7. Kegiatan_id yang sudah dicakup oleh riwayat manual + ketua
        $coveredByKetua = $autoRiwayat->pluck('kegiatan_id')->toArray();
        $allCoveredIds = array_merge($existingKegiatanIds, $coveredByKetua);

        // 8. Buat pseudo-riwayat dari data panitia yang belum ada di sumber lain
        $autoPanitia = $kegiatanAsPanitia
            ->filter(fn($kg) => !in_array($kg->id, $allCoveredIds))
            ->map(function ($kg) use ($studentId) {
                $item = new \stdClass();
                $item->id = null;
                $item->student_id = $studentId;
                $item->kegiatan_id = $kg->id;

                $peran = 'panitia';
                $panitiaCurrent = $kg->panitia->first();
                if ($panitiaCurrent && $panitiaCurrent->pivot->peran) {
                    $peran = $panitiaCurrent->pivot->peran;
                }
                $item->peran = $peran;
                $item->peran_manual = null;
                $item->nama_kegiatan_manual = null;
                $item->tanggal_kegiatan = null;
                $item->kegiatan = $kg;
                $item->is_auto = true;
                $item->created_at = $kg->created_at;
                $item->updated_at = $kg->updated_at;
                return $item;
            });

        // 9. Tandai riwayat manual agar bisa dibedakan di view
        $riwayatManual->each(function ($r) {
            $r->is_auto = false;
        });

        // 10. Gabungkan semua sumber dan urutkan berdasarkan tanggal kegiatan (terbaru dulu)
        return $riwayatManual
            ->concat($autoRiwayat)
            ->concat($autoPanitia)
            ->sortByDesc(function ($item) {
                $kegiatan = is_object($item->kegiatan ?? null) ? $item->kegiatan : null;
                if ($kegiatan && $kegiatan->tanggal_mulai) {
                    return $kegiatan->tanggal_mulai;
                }
                if (isset($item->tanggal_kegiatan) && $item->tanggal_kegiatan) {
                    return $item->tanggal_kegiatan;
                }
                return $item->created_at;
            })->values();
    }

    // -------------------------------------------------------------------------
    // Index — Daftar semua mahasiswa (Admin, GPM, Pengurus)
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        // Status kemahasiswaan (aktif/cuti/DO/pindah studi/wafat/mangkir) tidak lagi ditampilkan
        // maupun bisa difilter — tidak bisa disinkronkan dengan SIAP Undip (bimbingan Bu Eve &
        // Pak Sidik, 29 Okt 2026, S-53). Direktori hanya menyaring per angkatan + nama/NIM.
        // Filter berbentuk array (?search[]=...) dibuang: selain bisa memicu error 500,
        // galatnya tertangkap sebagai "koneksi database tidak stabil" yang menyesatkan.
        foreach (['search', 'angkatan'] as $kunci) {
            if (!is_string($request->input($kunci))) {
                $request->query->remove($kunci);
                $request->request->remove($kunci);
            }
        }

        // Benar-benar ada kriteria yang mempersempit hasil? "Semua Angkatan" /
        // kotak pencarian kosong tidak dihitung sebagai filter,
        // supaya tombol Reset dan pesan "tidak ada hasil" tidak muncul sia-sia.
        $isFiltered = $request->filled('search')
            || ($request->filled('angkatan') && $request->angkatan !== 'semua');

        // Sedang menjelajah (submit form / pindah halaman / ganti "Per page"), apa pun kriterianya.
        $isBrowsing = $request->hasAny(['search', 'angkatan', 'per_page']) || $request->filled('page');

        // Jumlah baris per halaman — pilihan yang sama dengan tabel Audit Log global.
        $perPage = PerPage::resolve($request);

        // Auto-sync data SSO → mk_kemahasiswaan (silent fail agar halaman tidak crash).
        // Hanya dijalankan pada pemuatan halaman polos: menyisir seluruh data SSO setiap
        // kali user mengganti filter atau pindah halaman membuat direktori terasa lambat.
        $syncFailed = false;
        if (!$isBrowsing) {
            try {
                $this->syncFromSSO();
            } catch (\Throwable $e) {
                // Sync gagal — lanjutkan, tampilkan data yang sudah ada
                $syncFailed = true;
                \Illuminate\Support\Facades\Log::warning('Sinkronisasi SSO direktori mahasiswa gagal', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $dbError = false;

        try {
            // Direktori Mahasiswa tidak menampilkan alumni — mereka ada di Direktori Alumni
            $query = $this->bukanAlumni(Kemahasiswaan::with(['user', 'user.student']));

            // Filter angkatan
            if ($request->filled('angkatan') && $request->angkatan !== 'semua') {
                $query->byAngkatan((int) $request->angkatan);
            }

            // Search nama / NIM
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            [$mahasiswa, $angkatanList] = $this->withRetry(function () use ($query, $perPage) {
                $mahasiswa = $query->orderBy('angkatan', 'desc')
                    ->orderBy('nama', 'asc')
                    ->paginate($perPage);

                // Daftar angkatan untuk dropdown filter.
                // Alumni & angkatan kosong dikecualikan: tabel di halaman ini tidak
                // menampilkan alumni, jadi opsi tersebut pasti berujung "hasil kosong".
                $angkatanList = $this->bukanAlumni(Kemahasiswaan::select('angkatan'))
                    ->whereNotNull('angkatan')
                    ->distinct()
                    ->orderBy('angkatan', 'desc')
                    ->pluck('angkatan');

                return [$mahasiswa, $angkatanList];
            });

        } catch (\Throwable $e) {
            // Koneksi DB benar-benar tidak stabil — tampilkan state kosong dengan pesan error
            $dbError = true;
            \Illuminate\Support\Facades\Log::error('Direktori mahasiswa gagal memuat data', [
                'error' => $e->getMessage(),
            ]);

            $mahasiswa = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $angkatanList = collect();
        }

        $isAdmin    = $this->hasRole('superadmin', 'admin_kemahasiswaan');
        $isGpm      = $this->hasRole('gpm');
        $isPengurus = $this->hasRole('pengurus_himpunan');
        $isMahasiswa = ($this->hasRole('mahasiswa') || $this->hasRole('alumni')) && !$isAdmin && !$isGpm && !$isPengurus;

        // Pesan gangguan — dibedakan antara "database tidak bisa dibaca" dan
        // "sinkronisasi SSO gagal" supaya tabel kosong tidak salah dibaca sebagai data hilang.
        $error = null;
        if ($dbError) {
            $error = 'Koneksi database sedang tidak stabil sehingga data mahasiswa belum bisa ditampilkan. '
                . 'Data Anda aman — silakan muat ulang halaman dalam beberapa saat.';
        } elseif ($syncFailed) {
            $error = 'Sinkronisasi data mahasiswa sedang bermasalah. '
                . 'Daftar di bawah menampilkan data yang sudah tersimpan, mahasiswa terbaru mungkin belum muncul.';
        }

        return view('manajemenmahasiswa::direktori.mahasiswa-index', compact(
            'mahasiswa',
            'angkatanList',
            'isAdmin',
            'isGpm',
            'isPengurus',
            'isMahasiswa',
            'isFiltered',
            'error',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Show — Detail profil mahasiswa (Admin, GPM, Pengurus)
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        try {
            // find() (bukan findOrFail) agar "data tidak ada" bisa dibedakan dari
            // "koneksi bermasalah" — dua situasi ini butuh respons yang berbeda.
            $mhs = $this->withRetry(fn() => Kemahasiswaan::with([
                'user',
                'user.student',
                'prestasi' => function ($q) {
                    $q->where('verification_status', 'approved');
                }
            ])->find($id));

            // Ambil riwayat kegiatan: manual + otomatis dari ketua pelaksana
            $riwayatKegiatan = $mhs
                ? $this->withRetry(fn() => $this->buildMergedRiwayat($mhs->user_id))
                : collect();

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Detail mahasiswa gagal dimuat', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('manajemenmahasiswa.direktori.mahasiswa.index')
                ->with('error', 'Koneksi database sedang tidak stabil sehingga profil mahasiswa belum bisa dibuka. Silakan coba lagi dalam beberapa saat.');
        }

        // Profil yang dituju memang tidak ada (link lama / ID salah) → 404 yang jelas,
        // bukan dilempar diam-diam ke halaman daftar tanpa penjelasan.
        abort_if(!$mhs, 404, 'Data mahasiswa tidak ditemukan.');

        // Profil berstatus apa pun (termasuk DO, mangkir, pindah studi, wafat) boleh
        // dibuka semua role yang mengakses direktori; yang dibatasi hanya kontak pribadi.
        $bisaLihatKontakPribadi = $this->bisaLihatKontakPribadi();

        $isAdmin   = $this->hasRole('superadmin', 'admin_kemahasiswaan');
        $isPengurus = $this->hasRole('pengurus_himpunan');
        $isGpm      = $this->hasRole('gpm');
        $isMahasiswa = ($this->hasRole('mahasiswa') || $this->hasRole('alumni')) && !$isAdmin && !$isGpm && !$isPengurus;
        // Role yang boleh mengunduh CV mahasiswa. Daftarnya diambil dari
        // CvProfilePolicy, bukan disalin ulang di sini — salinan manual itulah yang
        // dulu membuat tombol di UI dan gerbang route bisa berbeda isinya.
        $canDownloadCv = $this->hasRole(...\App\Policies\CvProfilePolicy::PENGELOLA_CV);
        // Pemilik baris ini sedang melihat dirinya sendiri. Halaman detail merangkap
        // "Profil Saya" sejak halaman terpisah itu dihapus: yang membedakannya hanya
        // tombol Download CV + Edit di header.
        $isSelf = (int) $mhs->user_id === (int) Auth::id();

        return view('manajemenmahasiswa::direktori.mahasiswa-show', compact(
            'mhs',
            'riwayatKegiatan',
            'isAdmin',
            'isPengurus',
            'isGpm',
            'isMahasiswa',
            'canDownloadCv',
            'isSelf',
            'bisaLihatKontakPribadi',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Edit — Dua wajah, satu halaman:
    //   • pengelola membuka baris ORANG LAIN → Status Kemahasiswaan + kontak
    //     (email pribadi, WhatsApp), tiap perubahan tercatat di audit log;
    //   • pemilik baris (termasuk pengelola yang membuka barisnya sendiri)
    //     → hanya data pribadinya (email pribadi, WhatsApp).
    // Nama, NIM, dan Angkatan milik SSO dan tidak bisa diubah siapa pun di sini.
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $mhs = Kemahasiswaan::with(['user', 'user.student'])->findOrFail($id);

        // Gerbang role di route hanya menyaring "role apa"; ini yang menyaring
        // "baris siapa". Mahasiswa yang mengetik id orang lain berhenti di sini (403).
        $this->authorize('update', $mhs);

        $sebagaiPengelola = Auth::user()->can('ubahDataMahasiswa', $mhs);

        return view('manajemenmahasiswa::direktori.mahasiswa-edit', compact('mhs', 'sebagaiPengelola'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Update — Simpan kontak (pengelola: dengan audit log; pemilik: tanpa audit)
    // -------------------------------------------------------------------------

    public function update(Request $request, int $id)
    {
        $mhs = Kemahasiswaan::with('user')->findOrFail($id);

        // Sama seperti edit(): route menyaring role, baris ini menyaring pemilik.
        $this->authorize('update', $mhs);

        if (Auth::user()->can('ubahDataMahasiswa', $mhs)) {
            return $this->simpanDataPengelola($request, $mhs);
        }

        // Jalur pemilik. Nama, NIM, dan Angkatan milik SSO UNDIP dan tidak pernah
        // ditulis di sini. Status, tahun_lulus, dan profesi juga tidak: aturannya tidak
        // dipasang DAN kolomnya tidak diteruskan, sehingga kiriman `status=...` dari
        // form yang diutak-atik tidak berefek — bukan sekadar disembunyikan di tampilan.
        $request->validate(KontakPribadi::aturan($mhs->user), KontakPribadi::pesan(), KontakPribadi::atribut());

        $kontak = KontakPribadi::perubahan($request, $mhs->user, $mhs->kontak);

        if ($kontak) {
            DB::transaction(fn () => KontakPribadi::tulis($mhs->user_id, $mhs->user, $kontak));
            $mhs->user?->clearUserCache();
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $id)
            ->with('success', $kontak ? 'Data Anda berhasil diperbarui.' : 'Tidak ada perubahan yang disimpan.');
    }

    // -------------------------------------------------------------------------
    // Simpan data oleh pengelola pada baris orang lain (KemahasiswaanPolicy::ubahDataMahasiswa):
    // kontak (email pribadi, WhatsApp).
    //
    // Kontak sebenarnya milik mahasiswa — jadi tiap perubahan tercatat di audit log
    // beserta pelakunya (siapa, kapan, dari apa ke apa). Penyimpanan dan log satu
    // transaksi: bila log gagal tertulis, tidak ada yang berubah.
    //
    // Status kemahasiswaan (dan jalur menjadikan Alumni dari form ini) dicabut sejak S-53
    // (bimbingan Bu Eve & Pak Sidik, 29 Okt 2026): tidak bisa disinkronkan dengan SIAP Undip.
    // Alumni kini hanya lewat Permission (ubah role / Cek Alumni Otomatis).
    // -------------------------------------------------------------------------

    private function simpanDataPengelola(Request $request, Kemahasiswaan $mhs)
    {
        $request->validate(KontakPribadi::aturan($mhs->user), KontakPribadi::pesan(), KontakPribadi::atribut());

        $kontak = KontakPribadi::perubahan($request, $mhs->user, $mhs->kontak);

        if (!$kontak) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id)
                ->with('success', 'Tidak ada perubahan yang disimpan.');
        }

        DB::transaction(function () use ($mhs, $kontak) {
            $lama = [];
            $baru = [];

            KontakPribadi::tulis($mhs->user_id, $mhs->user, $kontak);

            foreach ($kontak as $kolom => [$nilaiLama, $nilaiBaru]) {
                $lama[$kolom] = $nilaiLama;
                $baru[$kolom] = $nilaiBaru;
            }

            // Nilai kontak hanya disimpan di old/new data log modul, tidak di deskripsi
            // yang juga ditulis ke log terpusat.
            \App\Services\AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Mengubah data {$mhs->nama} ({$mhs->nim}): " . implode(', ', KontakPribadi::ringkasan($kontak)),
                subject: $mhs,
                oldData: $lama,
                newData: $baru,
            );
        });

        $mhs->user?->clearUserCache();

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id)
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Profil — Jalan pintas ke halaman detail milik sendiri
    //
    // Dulu halaman tersendiri ("Profil Saya") dengan isi yang sama persis dengan
    // halaman detail; yang membedakannya cuma tombol di header. Halaman itu dihapus
    // dan tombolnya dipindah ke detail, tapi route ini dipertahankan sebagai redirect
    // supaya bookmark lama tidak berujung 404 — sekaligus tetap menjadi jaring
    // pengaman bagi mahasiswa yang barisnya belum ada di direktori.
    // -------------------------------------------------------------------------

    public function profil()
    {
        $user = Auth::user();

        $loadProfil = fn() => Kemahasiswaan::with([
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            },
            'user',
            'user.student'
        ])->where('user_id', $user->id)->first();

        $mhs = $this->withRetry($loadProfil);

        // Belum punya baris di direktori — biasanya karena mahasiswa ini login lebih dulu
        // sebelum ada admin yang membuka halaman daftar (sinkronisasi SSO berjalan di sana).
        // Daftarkan dari data SSO miliknya sendiri supaya tidak perlu menunggu admin.
        if (!$mhs) {
            try {
                $student = Student::where('user_id', $user->id)->first();

                if ($student) {
                    // PERINGATAN (S-53): `status` diisi hanya karena kolomnya NOT NULL dan
                    // Dashboard Analitik / Permission masih membacanya — lihat syncFromSSO().
                    Kemahasiswaan::firstOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama'     => $user->name,
                            'nim'      => $student->student_number,
                            'angkatan' => $student->cohort_year,
                            'status'   => Alumni::where('user_id', $user->id)->exists()
                                ? Kemahasiswaan::STATUS_ALUMNI
                                : Kemahasiswaan::STATUS_AKTIF,
                        ]
                    );

                    $mhs = $this->withRetry($loadProfil);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mendaftarkan profil mahasiswa dari SSO', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        if (!$mhs) {
            // Tujuan tetap (dashboard modul), BUKAN back(). Dengan back(), "halaman
            // sebelumnya" bisa menunjuk ke halaman ini sendiri saat di-refresh sehingga
            // browser terjebak redirect berulang (ERR_TOO_MANY_REDIRECTS).
            return redirect()
                ->route('manajemenmahasiswa.dashboard')
                ->with('error', 'Data kemahasiswaan Anda belum terdaftar dalam sistem. Silakan hubungi Admin Kemahasiswaan untuk didaftarkan.');
        }

        return redirect()->route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id);
    }

    // -------------------------------------------------------------------------
    // Catatan: storeRiwayat / updateRiwayat / destroyRiwayat sudah dihapus.
    //
    // Penambahan riwayat kegiatan manual kini hanya lewat modul Verifikasi Data
    // (mahasiswa mengajukan → diverifikasi pengurus/admin). Pintu tambah manual di
    // direktori ini tidak pernah punya tombol di halaman, dan data yang masuk lewat
    // sana berstatus "pending" sehingga tidak akan tampil di profil — jalur kedua yang
    // tidak terverifikasi ini ditutup agar tidak menimbulkan data ganda.
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Generate CV — Halaman CV print-ready
    // -------------------------------------------------------------------------

    public function generateCv(int $id)
    {
        $mhs = Kemahasiswaan::with([
            'user',
            'user.student',
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            }
        ])->findOrFail($id);
        $user = $mhs->user;

        // Ambil CV Profile jika ada, atau buat instance kosong tanpa disimpan
        $cvProfile = \App\Models\CvProfile::where('user_id', $user->id)->first() ?? new \App\Models\CvProfile([
            'user_id' => $user->id,
            'tentang_diri' => '',
            'pendidikan' => [],
            'pengalaman_kerja' => [],
            'keahlian' => [],
            'sertifikasi' => [],
            'template' => 'modern'
        ]);

        // Lapis kedua di belakang middleware role: route menentukan role apa yang
        // boleh memakai aksi ini, Policy menentukan CV milik siapa. Tanpa ini,
        // siapa pun yang lolos gerbang role bisa mengenumerasi {id} 1..N dan
        // mengunduh email pribadi, WhatsApp, serta domisili seluruh mahasiswa.
        $this->authorize('view', $cvProfile);

        // Modul ini belum punya fasilitas audit log, jadi jejaknya dicatat ke log
        // aplikasi — mengunduh data pribadi orang lain tidak boleh tanpa bekas.
        if ((int) $user->id !== (int) Auth::id()) {
            Log::info('Unduh CV mahasiswa', [
                'pengunduh_id'     => Auth::id(),
                'pemilik_user_id'  => $user->id,
                'kemahasiswaan_id' => $id,
            ]);
        }

        $data = app(\App\Http\Controllers\CvBuilderController::class)->getAllCvData($user, $cvProfile);
        $data['is_print'] = true;

        return view('profile.cv.template-ats', $data);
    }

    /**
     * CV untuk mahasiswa sendiri.
     */
    public function generateCvSelf()
    {
        $user = Auth::user();

        // Tidak pakai firstOrFail Kemahasiswaan: mahasiswa yang datanya hanya ada di
        // tabel `students` (login Microsoft, belum punya baris mk_kemahasiswaan) tetap
        // bisa mengunduh CV. getAllCvData() sudah query Kemahasiswaan + fallback students.

        // Ambil CV Profile jika ada, atau buat instance kosong tanpa disimpan
        $cvProfile = \App\Models\CvProfile::where('user_id', $user->id)->first() ?? new \App\Models\CvProfile([
            'user_id' => $user->id,
            'tentang_diri' => '',
            'pendidikan' => [],
            'pengalaman_kerja' => [],
            'keahlian' => [],
            'sertifikasi' => [],
            'template' => 'modern'
        ]);

        $data = app(\App\Http\Controllers\CvBuilderController::class)->getAllCvData($user, $cvProfile);
        $data['is_print'] = true;

        return view('profile.cv.template-ats', $data);
    }

}

