<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Policies\KemahasiswaanPolicy;
use Modules\ManajemenMahasiswa\Services\AlumniService;
use Modules\ManajemenMahasiswa\Support\KontakPribadi;
use Modules\ManajemenMahasiswa\Support\PerPage;

class DirektoriAlumniController extends Controller
{
    protected AlumniService $alumniService;

    public function __construct(AlumniService $alumniService)
    {
        $this->alumniService = $alumniService;
    }

    // -------------------------------------------------------------------------
    // Connection Helpers (sama pola dengan DirektoriMahasiswaController)
    // -------------------------------------------------------------------------

    /** Cache roles per request — hanya 1 query ke Supabase */
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
     * Retry otomatis jika koneksi Supabase/pgBouncer terputus.
     * Mendeteksi SQLSTATE 08006/08003 dan pesan error koneksi,
     * lalu reconnect + coba ulang maks $maxAttempts kali.
     */
    private function withRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                $attempt++;
                $msg = $e->getMessage();
                $code = (string) $e->getCode();

                $isConnectionError =
                    in_array($code, ['08006', '08003', '57P01', '7'])
                    || str_contains($msg, 'server closed the connection')
                    || str_contains($msg, 'SSL negotiation')
                    || str_contains($msg, 'could not connect')
                    || str_contains($msg, 'connection unexpectedly')
                    || str_contains($msg, 'pooler.supabase.com');

                if ($isConnectionError && $attempt < $maxAttempts) {
                    usleep(200_000 * $attempt); // 200ms, 400ms, ...
                    try {
                        DB::reconnect();
                    } catch (\Throwable) {
                        // reconnect juga bisa gagal, lanjut retry
                    }
                    $this->userRoles = null;
                    continue;
                }
                throw $e;
            }
        }
    }

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
        return 'manajemenmahasiswa::layouts.mahasiswa';
    }

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

    // -------------------------------------------------------------------------
    // Permission Helpers
    // — Pisahkan "siapa yang bisa lihat" vs "siapa yang bisa kelola"
    //   agar mudah diperluas tanpa menyentuh logika view.
    // -------------------------------------------------------------------------

    /**
     * Role yang bisa MELIHAT riwayat kegiatan & prestasi alumni.
     * Tambahkan role baru di sini jika diperlukan.
     */
    private function canSeeHistory(): bool
    {
        return $this->hasRole(
            'superadmin',
            'admin_kemahasiswaan',
            'gpm',
            'dosen',
            'pengurus_himpunan',
            'ketua_departemen'
        );
    }

    /**
     * Role yang bisa MENGELOLA (tambah/edit/hapus) riwayat kegiatan & prestasi alumni.
     * Tambahkan role baru di sini jika diperlukan.
     */
    private function canManageHistory(): bool
    {
        return $this->hasRole(
            'superadmin',
            'admin_kemahasiswaan'
            // Tambahkan role lain di sini jika suatu saat dibutuhkan:
            // 'gpm',
            // 'pengurus_himpunan',
        );
    }

    // -------------------------------------------------------------------------
    // Riwayat Kegiatan — Merged Build
    // — Menggabungkan 3 sumber: manual (mk_riwayat_kegiatan approved) +
    //   otomatis dari ketua pelaksana + otomatis dari panitia kegiatan.
    // -------------------------------------------------------------------------

    /**
     * Bangun koleksi riwayat kegiatan unified untuk satu user.
     * Sama persis dengan logika di DirektoriMahasiswaController;
     * jika perlu refactor ke shared service, pindahkan method ini.
     *
     * @param int $userId  — users.id (bukan students.id)
     */
    private function buildMergedRiwayat(int $userId): Collection
    {
        // 1. Resolve students.id dari user_id
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return collect();
        }

        $studentId = $student->id;

        // 2. Riwayat manual yang sudah disetujui
        $riwayatManual = RiwayatKegiatan::with('kegiatan')
            ->where('student_id', $studentId)
            ->where('verification_status', 'approved')
            ->get();

        // 3. Sebagai ketua pelaksana
        //    Hanya kegiatan berstatus "selesai" yang dihitung sebagai riwayat
        //    (konsisten dengan Laporan & Arsip; menyembunyikan draft/legacy).
        $kegiatanAsKetua = Kegiatan::where('ketua_pelaksana_id', $studentId)
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->get();

        // 4. Sebagai panitia (via pivot)
        $kegiatanAsPanitia = Kegiatan::whereHas('panitia', fn($q) => $q->where('students.id', $studentId))
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->with(['panitia' => fn($q) => $q->where('students.id', $studentId)])
            ->get();

        // 5. ID yang sudah dicakup riwayat manual
        $existingKegiatanIds = $riwayatManual->pluck('kegiatan_id')->filter()->toArray();

        // 6. Auto-riwayat dari ketua (yang belum ada di manual)
        $autoRiwayat = $kegiatanAsKetua
            ->filter(fn($kg) => !in_array($kg->id, $existingKegiatanIds))
            ->map(function ($kg) use ($studentId) {
                $item = new \stdClass();
                $item->id                    = null;
                $item->student_id            = $studentId;
                $item->kegiatan_id           = $kg->id;
                $item->peran                 = 'ketua';
                $item->peran_manual          = null;
                $item->nama_kegiatan_manual  = null;
                $item->tanggal_kegiatan      = null;
                $item->kegiatan              = $kg;
                $item->is_auto               = true;
                $item->created_at            = $kg->created_at;
                $item->updated_at            = $kg->updated_at;
                return $item;
            });

        // 7. ID yang sudah dicakup manual + ketua
        $coveredByKetua = $autoRiwayat->pluck('kegiatan_id')->toArray();
        $allCoveredIds  = array_merge($existingKegiatanIds, $coveredByKetua);

        // 8. Auto-riwayat dari panitia (yang belum dicakup)
        $autoPanitia = $kegiatanAsPanitia
            ->filter(fn($kg) => !in_array($kg->id, $allCoveredIds))
            ->map(function ($kg) use ($studentId) {
                $item = new \stdClass();
                $item->id                    = null;
                $item->student_id            = $studentId;
                $item->kegiatan_id           = $kg->id;

                $peran         = 'panitia';
                $panitiaCurrent = $kg->panitia->first();
                if ($panitiaCurrent && $panitiaCurrent->pivot->peran) {
                    $peran = $panitiaCurrent->pivot->peran;
                }
                $item->peran                = $peran;
                $item->peran_manual         = null;
                $item->nama_kegiatan_manual = null;
                $item->tanggal_kegiatan     = null;
                $item->kegiatan             = $kg;
                $item->is_auto              = true;
                $item->created_at           = $kg->created_at;
                $item->updated_at           = $kg->updated_at;
                return $item;
            });

        // 9. Tandai riwayat manual
        $riwayatManual->each(fn($r) => $r->is_auto = false);

        // 10. Gabungkan & urutkan terbaru dulu
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
    // Helpers — Sync dari mk_kemahasiswaan
    // -------------------------------------------------------------------------

    /**
     * Sinkronisasi dua arah antara mk_kemahasiswaan dan mk_alumni.
     */
    private function syncFromKemahasiswaan(): void
    {
        $this->withRetry(function () {
            $changed = false;

            // 1. Ambil semua user yang berstatus role 'alumni'
            $alumniUserIds = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'alumni'))
                ->pluck('id')
                ->toArray();

            // Buat record mk_alumni untuk user alumni yang belum ada
            $existingAlumniUserIds = Alumni::whereIn('user_id', $alumniUserIds)->pluck('user_id')->toArray();
            $missingUserIds = array_diff($alumniUserIds, $existingAlumniUserIds);

            foreach ($missingUserIds as $userId) {
                $user = \App\Models\User::with('student')->find($userId);
                if (!$user)
                    continue;

                $km = Kemahasiswaan::where('user_id', $userId)->first();
                $student = $user->student;

                Alumni::create([
                    'user_id' => $userId,
                    'nim' => $km?->nim ?? $student?->student_number ?? '-',
                    'angkatan' => $km?->angkatan ?? $student?->cohort_year ?? date('Y'),
                    'tahun_lulus' => $km?->tahun_lulus ?? (int) date('Y'),
                    'program_studi' => 'S1 Teknik Komputer',
                ]);

                // PERINGATAN (S-53, 29 Okt 2026): status kemahasiswaan sudah dicabut dari tampilan
                // Direktori Mahasiswa & Alumni. Penulisan status di sini dipertahankan HANYA supaya
                // Dashboard Analitik (Devarlo / Syahbana) tetap konsisten dengan role alumni.
                // Sekaligus pastikan status km juga konsisten
                if ($km && $km->status !== Kemahasiswaan::STATUS_ALUMNI) {
                    $km->update(['status' => Kemahasiswaan::STATUS_ALUMNI]);
                }

                $changed = true;
            }

            // 2. Hapus: alumni di mk_alumni yang user-nya SUDAH TIDAK lagi berole alumni
            $allAlumniInTable = Alumni::pluck('user_id')->toArray();
            $noLongerAlumni = array_diff($allAlumniInTable, $alumniUserIds);

            if (!empty($noLongerAlumni)) {
                Alumni::whereIn('user_id', $noLongerAlumni)->delete();
                // PERINGATAN (S-53, 29 Okt 2026): status kemahasiswaan sudah dicabut dari tampilan
                // Direktori Mahasiswa & Alumni. Penulisan status di sini dipertahankan HANYA supaya
                // Dashboard Analitik (Devarlo / Syahbana) tetap konsisten dengan role alumni.
                // Kembalikan status ke aktif jika record km-nya ada
                Kemahasiswaan::whereIn('user_id', $noLongerAlumni)
                    ->where('status', Kemahasiswaan::STATUS_ALUMNI)
                    ->update(['status' => Kemahasiswaan::STATUS_AKTIF]);
                $changed = true;
            }

            if ($changed) {
                \Illuminate\Support\Facades\Cache::forget('mk.alumni.summary');
                \Illuminate\Support\Facades\Cache::forget('mk.dashboard.snapshot');
            }
        });
    }

    // -------------------------------------------------------------------------
    // Index — Daftar semua alumni
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        // Auto-sync (silent fail agar halaman tidak crash)
        try {
            $this->syncFromKemahasiswaan();
        } catch (\Throwable) {
            // Sync gagal — lanjutkan
        }

        // `angkatan` ikut dibaca: filternya ada di panel, tapi dulu tidak pernah diteruskan
        // ke query. Nilai berbentuk array (?search[]=...) dibuang, sama dengan Direktori Mahasiswa.
        $filters = $request->only(['angkatan', 'tahun_lulus', 'status_karir', 'bidang_industri', 'search']);
        $filters = array_filter($filters, fn($v) => is_string($v) && $v !== 'semua' && $v !== '');

        // Jumlah baris per halaman — pilihan yang sama dengan Direktori Mahasiswa.
        $perPage = PerPage::resolve($request);

        $error = null;

        try {
            [
                $alumni,
                $angkatanList,
                $tahunLulusList,
                $summary,
            ] = $this->withRetry(function () use ($filters, $perPage) {
                $alumni = $this->alumniService->listAlumni($filters, $perPage);
                $angkatanList = Alumni::select('angkatan')->distinct()->orderByDesc('angkatan')->pluck('angkatan');
                $tahunLulusList = Alumni::select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');
                $summary = $this->alumniService->getSummary();
                return [$alumni, $angkatanList, $tahunLulusList, $summary];
            });
        } catch (\Throwable $e) {
            Log::error('Direktori alumni gagal memuat data', ['error' => $e->getMessage()]);

            $alumni = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $angkatanList = collect();
            $tahunLulusList = collect();
            $summary = ['total' => 0, 'per_status' => []];
            // Tanpa pesan ini tabel kosong mudah dibaca sebagai "data alumni hilang".
            $error = 'Koneksi database sedang tidak stabil sehingga data alumni belum bisa ditampilkan. '
                . 'Data Anda aman — silakan muat ulang halaman dalam beberapa saat.';
        }

        $statusKarirOptions = Alumni::STATUS_LABELS;
        $bidangIndustriOptions = Alumni::BIDANG_INDUSTRI_LIST;
        $isAdmin = $this->hasRole('superadmin', 'admin_kemahasiswaan');
        // Alumni melihat menu Edit pada barisnya sendiri.
        $userId = (int) Auth::id();

        $totalAlumni = $summary['total'];
        $bekerja = $summary['per_status']['bekerja'] ?? 0;
        $wirausaha = $summary['per_status']['wirausaha'] ?? 0;
        $studiLanjut = $summary['per_status']['studi_lanjut'] ?? 0;
        $belumTerdata = $summary['per_status']['belum_terdata'] ?? 0;

        return view('manajemenmahasiswa::direktori.alumni-index', compact(
            'alumni',
            'angkatanList',
            'tahunLulusList',
            'statusKarirOptions',
            'bidangIndustriOptions',
            'isAdmin',
            'userId',
            'totalAlumni',
            'bekerja',
            'wirausaha',
            'studiLanjut',
            'belumTerdata',
            'error',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Show — Detail profil alumni
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        // find() (bukan findOrFail di dalam try) agar "data tidak ada" berakhir 404,
        // bukan dilaporkan sebagai "koneksi database tidak stabil".
        try {
            $alumni = $this->withRetry(fn() => Alumni::with('user')->find($id));
        } catch (\Throwable $e) {
            Log::error('Detail alumni gagal dimuat', ['id' => $id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }

        abort_if(!$alumni, 404, 'Data alumni tidak ditemukan.');

        try {
            // Ambil data kemahasiswaan (untuk prestasi) via user_id
            $kemahasiswaan = $this->withRetry(
                fn() => Kemahasiswaan::with([
                    'prestasi' => fn($q) => $q->where('verification_status', 'approved')->orderByDesc('tanggal'),
                ])->where('user_id', $alumni->user_id)->first()
            );

            // Ambil riwayat kegiatan (merged: manual + otomatis)
            $riwayatKegiatan = $this->withRetry(
                fn() => $this->buildMergedRiwayat($alumni->user_id)
            );

            // Semua kegiatan untuk dropdown tambah riwayat manual
            $semuaKegiatan = $this->withRetry(
                fn() => Kegiatan::orderBy('judul')->get()
            );

            // Permission flags — mudah diperluas lewat canSeeHistory() / canManageHistory()
            $isAdmin         = $this->hasRole('superadmin', 'admin_kemahasiswaan');
            $canSeeHistory   = $this->canSeeHistory();
            $canManageHistory = $this->canManageHistory();
            // Role yang boleh mengunduh CV alumni — sumber kebenarannya CvProfilePolicy,
            // sama dengan gerbang route dan Policy di generateCv().
            $canDownloadCv = $this->hasRole(...\App\Policies\CvProfilePolicy::PENGELOLA_CV);
            // Halaman detail merangkap profil sendiri (sama dengan Direktori Mahasiswa):
            // pemilik melihat tombol Download CV + Edit miliknya.
            $isSelf = (int) $alumni->user_id === (int) Auth::id();
            // Kontak pribadi alumni dibatasi sama persis dengan Direktori Mahasiswa —
            // status lulus tidak membuat kontaknya terbuka untuk semua orang.
            $bisaLihatKontakPribadi = $isSelf || $this->hasRole(...KemahasiswaanPolicy::LIHAT_KONTAK_PRIBADI);

            return view('manajemenmahasiswa::direktori.alumni-show', compact(
                'alumni',
                'kemahasiswaan',
                'riwayatKegiatan',
                'semuaKegiatan',
                'isAdmin',
                'canSeeHistory',
                'canManageHistory',
                'canDownloadCv',
                'isSelf',
                'bisaLihatKontakPribadi',
            ))->with('layout', $this->resolveLayout());

        } catch (\Throwable $e) {
            Log::error('Detail alumni gagal dimuat', ['id' => $id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }
    }

    // -------------------------------------------------------------------------
    // Profil — Jalan pintas ke halaman detail milik sendiri
    //
    // Dulu halaman "Profil Karir" tersendiri yang tidak punya tautan di menu mana pun
    // (sidebar alumni mengarah ke daftar), sehingga alumni praktis tidak bisa mengubah
    // datanya. Kini edit lewat tombol Edit di halaman detail sendiri, sama dengan
    // Direktori Mahasiswa; route ini dipertahankan sebagai redirect untuk tautan lama.
    // -------------------------------------------------------------------------

    public function profil()
    {
        $user = Auth::user();

        try {
            $alumni = $this->withRetry(fn() => Alumni::where('user_id', $user->id)->first());

            if (!$alumni) {
                $mhs = $this->withRetry(fn() => Kemahasiswaan::where('user_id', $user->id)->first());

                // PERINGATAN (S-53): jalan pintas ini bergantung pada status 'alumni' yang kini hanya
                // ditulis Permission (ubah role / Cek Alumni Otomatis); form Direktori Mahasiswa tak lagi menulisnya.
                if ($mhs && $mhs->status === Kemahasiswaan::STATUS_ALUMNI) {
                    $alumni = $this->withRetry(fn() => Alumni::create([
                        'user_id'       => $user->id,
                        'nim'           => $mhs->nim,
                        'angkatan'      => $mhs->angkatan,
                        'tahun_lulus'   => $mhs->tahun_lulus ?? date('Y'),
                        'program_studi' => 'S1 Teknik Komputer',
                    ]));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Profil alumni gagal dibuka', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('manajemenmahasiswa.dashboard')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }

        if (!$alumni) {
            // Tujuan tetap, BUKAN back(): back() bisa menunjuk ke halaman ini sendiri
            // dan membuat browser terjebak redirect berulang.
            return redirect()
                ->route('manajemenmahasiswa.dashboard')
                ->with('error', 'Anda belum terdaftar sebagai alumni.');
        }

        return redirect()->route('manajemenmahasiswa.direktori.alumni.show', $alumni->id);
    }

    // -------------------------------------------------------------------------
    // Edit — Dua wajah, satu halaman (pola Direktori Mahasiswa):
    //   • pengelola membuka baris ORANG LAIN → tahun lulus + kontak + karir,
    //     tiap perubahan tercatat di audit log;
    //   • pemilik baris (alumni itu sendiri) → kontak + karir miliknya.
    // NIM dan Angkatan milik SSO dan tidak bisa diubah siapa pun di sini.
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $alumni = Alumni::with('user')->findOrFail($id);

        // Route menyaring "role apa"; ini menyaring "baris siapa".
        $this->authorize('update', $alumni);

        $sebagaiPengelola = Auth::user()->can('ubahDataAlumni', $alumni);
        $kontakCadangan   = Kemahasiswaan::where('user_id', $alumni->user_id)->value('kontak');

        return view('manajemenmahasiswa::direktori.alumni-edit', compact('alumni', 'sebagaiPengelola', 'kontakCadangan'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Update — Simpan (pengelola: + tahun lulus & audit log; pemilik: kontak + karir)
    // -------------------------------------------------------------------------

    public function update(Request $request, int $id)
    {
        $alumni = Alumni::with('user')->findOrFail($id);

        $this->authorize('update', $alumni);

        $sebagaiPengelola = Auth::user()->can('ubahDataAlumni', $alumni);

        $aturan = [
            'status_karir'        => 'nullable|string|in:' . implode(',', Alumni::STATUS_LIST),
            'perusahaan'          => 'nullable|string|max:255',
            'jabatan'             => 'nullable|string|max:255',
            'bidang_industri'     => 'nullable|string|in:' . implode(',', array_keys(Alumni::BIDANG_INDUSTRI_LIST)),
            'tahun_mulai_bekerja' => 'nullable|integer|min:2000|max:' . date('Y'),
            'linkedin'            => 'nullable|url|max:255',
        ];
        // Tahun lulus = data akademik, hanya pengelola. Dari pemilik, kolomnya tidak
        // divalidasi DAN tidak diteruskan — kiriman form yang diutak-atik tidak berefek.
        if ($sebagaiPengelola) {
            $aturan['tahun_lulus'] = 'required|integer|min:2000|max:2099';
        }

        $data = $request->validate(
            $aturan + KontakPribadi::aturan($alumni->user),
            [
                'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
                'linkedin.url'         => 'Tautan LinkedIn harus berupa URL lengkap (https://...).',
            ] + KontakPribadi::pesan(),
            KontakPribadi::atribut(),
        );

        // Hanya kolom mk_alumni yang benar-benar berubah yang ditulis dan diaudit.
        $kolomAlumni = array_keys($aturan);
        $perubahanAlumni = [];
        foreach ($kolomAlumni as $kolom) {
            $lama = $alumni->{$kolom};
            $baru = ($data[$kolom] ?? null) === '' ? null : ($data[$kolom] ?? null);
            if ((string) $lama !== (string) $baru) {
                $perubahanAlumni[$kolom] = [$lama, $baru];
            }
        }

        $kontakCadangan = Kemahasiswaan::where('user_id', $alumni->user_id)->value('kontak');
        $kontak = KontakPribadi::perubahan($request, $alumni->user, $kontakCadangan);

        if (!$perubahanAlumni && !$kontak) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.show', $id)
                ->with('success', 'Tidak ada perubahan yang disimpan.');
        }

        // Penyimpanan, sinkron kontak, dan audit log satu transaksi: bila salah satu
        // gagal, tidak ada yang berubah (dulu kontak bisa tersimpan setengah).
        DB::transaction(function () use ($alumni, $perubahanAlumni, $kontak, $sebagaiPengelola) {
            if ($perubahanAlumni) {
                $alumni->update(array_map(fn ($nilai) => $nilai[1], $perubahanAlumni));

                if (isset($perubahanAlumni['tahun_lulus'])) {
                    Kemahasiswaan::where('user_id', $alumni->user_id)
                        ->update(['tahun_lulus' => $perubahanAlumni['tahun_lulus'][1]]);
                }
            }

            KontakPribadi::tulis($alumni->user_id, $alumni->user, $kontak);

            // Perubahan oleh pemilik sendiri tidak diaudit, sama dengan Direktori Mahasiswa.
            if ($sebagaiPengelola) {
                $semua = $perubahanAlumni + $kontak;
                $ringkasan = array_merge(
                    array_map(fn ($kolom) => str_replace('_', ' ', $kolom), array_keys($perubahanAlumni)),
                    KontakPribadi::ringkasan($kontak),
                );

                \App\Services\AuditLogger::update(
                    module:  'manajemen_mahasiswa',
                    // Deskripsi ikut ke log terpusat: hanya nama kolom, tanpa nilai.
                    desc:    "Mengubah data alumni {$alumni->user?->name} ({$alumni->nim}): " . implode(', ', $ringkasan),
                    subject: $alumni,
                    oldData: array_map(fn ($nilai) => $nilai[0], $semua),
                    newData: array_map(fn ($nilai) => $nilai[1], $semua),
                );
            }
        });

        \Illuminate\Support\Facades\Cache::forget('mk.alumni.summary');
        \Illuminate\Support\Facades\Cache::forget('mk.dashboard.snapshot');

        if ($kontak) {
            $alumni->user?->clearUserCache();
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', $sebagaiPengelola ? 'Data alumni berhasil diperbarui.' : 'Data Anda berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Riwayat Kegiatan — Store (tambah manual)
    // -------------------------------------------------------------------------

    public function storeRiwayat(Request $request, int $id)
    {
        $alumni = Alumni::findOrFail($id);
        $student = Student::where('user_id', $alumni->user_id)->first();
        abort_if(!$student, 404, 'Data student tidak ditemukan untuk alumni ini.');

        $mode = $request->input('input_mode', 'dropdown');

        // Ditambah admin → langsung approved, sama seperti storePrestasi() di
        // bawah. Tanpa ini status jatuh ke default "pending": datanya tidak
        // pernah tampil di profil (profil hanya menampilkan yang disetujui),
        // sekaligus nyangkut di antrean Verifikasi Kegiatan tanpa berkas bukti
        // sebagai pengajuan yang tidak pernah dibuat alumninya.
        $terverifikasi = [
            'verification_status' => RiwayatKegiatan::VERIF_APPROVED,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ];

        if ($mode === 'manual') {
            $request->validate([
                'nama_kegiatan_manual' => 'required|string|max:' . VerifikasiController::MAKS_NAMA,
                'peran_manual'         => 'required|string|max:' . VerifikasiController::MAKS_PERAN,
                'tanggal_kegiatan'     => 'nullable|date|before_or_equal:today',
            ], [
                'tanggal_kegiatan.before_or_equal' => 'Tanggal kegiatan tidak boleh melewati hari ini.',
            ]);

            RiwayatKegiatan::create([
                'student_id'           => $student->id,
                'kegiatan_id'          => null,
                'peran'                => null,
                'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
                'peran_manual'         => $request->peran_manual,
                'tanggal_kegiatan'     => $request->tanggal_kegiatan,
            ] + $terverifikasi);
        } else {
            $request->validate([
                'kegiatan_id' => 'required|exists:mk_kegiatan,id',
                'peran'       => 'required|in:' . implode(',', RiwayatKegiatan::PERAN_LIST),
            ]);

            RiwayatKegiatan::create([
                'student_id'  => $student->id,
                'kegiatan_id' => $request->kegiatan_id,
                'peran'       => $request->peran,
            ] + $terverifikasi);
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', 'Riwayat kegiatan berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // Riwayat Kegiatan — Update
    // -------------------------------------------------------------------------

    public function updateRiwayat(Request $request, int $riwayatId)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:mk_kegiatan,id',
            'peran'       => 'required|in:' . implode(',', RiwayatKegiatan::PERAN_LIST),
        ]);

        $riwayat = RiwayatKegiatan::findOrFail($riwayatId);
        $riwayat->update($request->only(['kegiatan_id', 'peran']));

        // Cari alumni_id untuk redirect — lewat student → user → alumni
        $student = Student::find($riwayat->student_id);
        $alumni  = $student ? Alumni::where('user_id', $student->user_id)->first() : null;

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $alumni?->id ?? 0)
            ->with('success', 'Riwayat kegiatan berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Riwayat Kegiatan — Destroy
    // -------------------------------------------------------------------------

    public function destroyRiwayat(int $riwayatId)
    {
        $riwayat = RiwayatKegiatan::findOrFail($riwayatId);

        $student = Student::find($riwayat->student_id);
        $alumni  = $student ? Alumni::where('user_id', $student->user_id)->first() : null;

        $riwayat->delete();

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $alumni?->id ?? 0)
            ->with('success', 'Riwayat kegiatan berhasil dihapus.');
    }

    // -------------------------------------------------------------------------
    // Prestasi — Store
    // -------------------------------------------------------------------------

    public function storePrestasi(Request $request, int $id)
    {
        $request->validate([
            // Batas & aturan tanggalnya disamakan dengan form pengajuan mahasiswa
            'nama_prestasi' => 'required|string|max:' . VerifikasiController::MAKS_NAMA,
            'tingkat'       => 'required|in:' . implode(',', \Modules\ManajemenMahasiswa\Models\Prestasi::TINGKAT_LIST),
            'tanggal'       => 'nullable|date|before_or_equal:today',
        ], [
            'tanggal.before_or_equal' => 'Tanggal prestasi tidak boleh melewati hari ini.',
        ]);

        $alumni = Alumni::findOrFail($id);

        // Prestasi terhubung ke Kemahasiswaan, bukan Alumni langsung
        $kemahasiswaan = Kemahasiswaan::where('user_id', $alumni->user_id)->first();
        abort_if(!$kemahasiswaan, 404, 'Data kemahasiswaan tidak ditemukan untuk alumni ini.');

        // Ditambah admin → langsung approved (bukan self-report mahasiswa)
        $kemahasiswaan->prestasi()->create([
            'nama_prestasi'       => $request->nama_prestasi,
            'tingkat'             => $request->tingkat,
            'tanggal'             => $request->tanggal,
            'verification_status' => \Modules\ManajemenMahasiswa\Models\Prestasi::VERIF_APPROVED,
            'verified_by'         => auth()->id(),
            'verified_at'         => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', 'Prestasi berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // Prestasi — TIDAK bisa dihapus dari Direktori Alumni.
    //
    // Prestasi/lomba yang tampil di sini sudah lolos Verifikasi Data dan bisa
    // menjadi dasar klaim reward (SK 774). Menghapusnya dari direktori menghilangkan
    // jejak verifikasi tanpa lewat alur resmi, jadi destroyPrestasi() dan route-nya
    // dicabut. Koreksi prestasi dilakukan di Verifikasi Data.
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Generate CV — Halaman CV print-ready (Alumni)
    // -------------------------------------------------------------------------

    public function generateCv(int $id)
    {
        $alumni = $this->alumniService->findById($id);
        $user = $alumni->user;

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

        // Gerbang yang sama persis dengan CV mahasiswa — lihat CvProfilePolicy.
        $this->authorize('view', $cvProfile);

        if ((int) $user->id !== (int) Auth::id()) {
            Log::info('Unduh CV alumni', [
                'pengunduh_id'    => Auth::id(),
                'pemilik_user_id' => $user->id,
                'alumni_id'       => $id,
            ]);
        }

        $data = app(\App\Http\Controllers\CvBuilderController::class)->getAllCvData($user, $cvProfile);
        $data['is_print'] = true;

        return view('profile.cv.template-ats', $data);
    }

    /**
     * CV untuk alumni sendiri (dari halaman Profil)
     */
    public function generateCvSelf()
    {
        $user = Auth::user();

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
