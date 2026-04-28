<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\CvProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Alumni;

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
     * Jalankan closure DB dengan retry otomatis jika koneksi Supabase terputus.
     * pgBouncer (port 6543) kadang menutup koneksi idle → SQLSTATE 08006 / 08003.
     * Solusi: force reconnect lalu coba lagi, maks $maxAttempts kali.
     *
     * @template T
     * @param  callable(): T $callback
     * @param  int           $maxAttempts
     * @return T
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

                // Deteksi error koneksi Supabase/pgBouncer
                $isConnectionError =
                    in_array($code, ['08006', '08003', '57P01', '7'])
                    || str_contains($msg, 'server closed the connection')
                    || str_contains($msg, 'SSL negotiation')
                    || str_contains($msg, 'could not connect')
                    || str_contains($msg, 'connection unexpectedly')
                    || str_contains($msg, 'pooler.supabase.com');

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

            foreach ($studentsNotSynced as $student) {
                if (!$student->user)
                    continue;

                Kemahasiswaan::create([
                    'user_id' => $student->user_id,
                    'nama' => $student->user->name,
                    'nim' => $student->student_number,
                    'angkatan' => $student->cohort_year,
                    'status' => 'aktif',
                ]);
            }
        });
    }

    /**
     * Tentukan layout blade berdasarkan role user yang sedang login.
     */
    private function resolveLayout(): string
    {
        $roles = $this->getUserRoles();

        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }

        if (\in_array('gpm', $roles)) {
            return 'manajemenmahasiswa::layouts.dosen';
        }

        if (\in_array('pengurus_himpunan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
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
        $kegiatanAsKetua = Kegiatan::where('ketua_pelaksana_id', $studentId)->get();

        // 4. Ambil semua kegiatan di mana mahasiswa ini adalah panitia (via pivot)
        $kegiatanAsPanitia = Kegiatan::whereHas('panitia', fn($q) => $q->where('students.id', $studentId))
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
        // Auto-sync data SSO → mk_kemahasiswaan (silent fail agar halaman tidak crash)
        try {
            $this->syncFromSSO();
        } catch (\Throwable) {
            // Sync gagal — lanjutkan, tampilkan data yang sudah ada
        }

        try {
            $query = Kemahasiswaan::with(['user', 'user.student']);

            // Filter angkatan
            if ($request->filled('angkatan') && $request->angkatan !== 'semua') {
                $query->byAngkatan((int) $request->angkatan);
            }

            // Filter status
            if ($request->filled('status') && $request->status !== 'semua') {
                $query->where('status', $request->status);
            }

            // Search nama / NIM
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            [$mahasiswa, $angkatanList] = $this->withRetry(function () use ($query) {
                $mahasiswa = $query->orderBy('angkatan', 'desc')
                    ->orderBy('nama', 'asc')
                    ->paginate(15);

                $angkatanList = Kemahasiswaan::select('angkatan')
                    ->distinct()
                    ->orderBy('angkatan', 'desc')
                    ->pluck('angkatan');

                return [$mahasiswa, $angkatanList];
            });

        } catch (\Throwable) {
            // Koneksi DB benar-benar tidak stabil — tampilkan state kosong dengan pesan error
            $mahasiswa = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $angkatanList = collect();
        }

        $isAdmin = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');
        $isGpm = $this->hasRole('gpm');
        $isPengurus = $this->hasRole('pengurus_himpunan');
        $isMahasiswa = ($this->hasRole('mahasiswa') || $this->hasRole('alumni')) && !$isAdmin && !$isGpm && !$isPengurus;

        return view('manajemenmahasiswa::direktori.mahasiswa-index', compact(
            'mahasiswa',
            'angkatanList',
            'isAdmin',
            'isGpm',
            'isPengurus',
            'isMahasiswa',
        ))->with('layout', $this->resolveLayout())
            ->with('error', isset($mahasiswa) && $mahasiswa->isEmpty() && !$request->hasAny(['search', 'angkatan', 'status'])
                ? 'Koneksi database sedang tidak stabil. Silakan muat ulang halaman.'
                : null);
    }

    // -------------------------------------------------------------------------
    // Show — Detail profil mahasiswa (Admin, GPM, Pengurus)
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        try {
            [$mhs, $semuaKegiatan, $cvProfile] = $this->withRetry(function () use ($id) {
                $mhs = Kemahasiswaan::with([
                    'user',
                    'user.student',
                    'prestasi' => function ($q) {
                        $q->where('verification_status', 'approved');
                    }
                ])->findOrFail($id);

                $semuaKegiatan = Kegiatan::orderBy('judul')->get();
                $cvProfile = CvProfile::where('user_id', $mhs->user_id)->first();

                return [$mhs, $semuaKegiatan, $cvProfile];
            });

            // Ambil riwayat kegiatan: manual + otomatis dari ketua pelaksana
            $riwayatKegiatan = $this->withRetry(fn() => $this->buildMergedRiwayat($mhs->user_id));

            $isAdmin = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');
            $isPengurus = $this->hasRole('pengurus_himpunan');
            $isGpm = $this->hasRole('gpm');
            $isMahasiswa = ($this->hasRole('mahasiswa') || $this->hasRole('alumni')) && !$isAdmin && !$isGpm && !$isPengurus;

            return view('manajemenmahasiswa::direktori.mahasiswa-show', compact(
                'mhs',
                'riwayatKegiatan',
                'semuaKegiatan',
                'isAdmin',
                'isPengurus',
                'isGpm',
                'isMahasiswa',
                'cvProfile',
            ))->with('layout', $this->resolveLayout());

        } catch (\Throwable) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.mahasiswa.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }
    }

    // -------------------------------------------------------------------------
    // Edit — Form edit biodata (Admin only)
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $mhs = Kemahasiswaan::with(['user', 'user.student'])->findOrFail($id);

        return view('manajemenmahasiswa::direktori.mahasiswa-edit', compact('mhs'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Update — Simpan perubahan biodata (Admin only)
    // -------------------------------------------------------------------------

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:30',
            'angkatan' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:' . implode(',', Kemahasiswaan::STATUS_LIST),
            'tahun_lulus' => 'nullable|integer|min:2000|max:2099',
            'profesi' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255',
        ]);

        $mhs = Kemahasiswaan::findOrFail($id);
        $oldStatus = $mhs->status;

        $mhs->update($request->only([
            'nama',
            'nim',
            'angkatan',
            'status',
            'tahun_lulus',
            'profesi',
            'kontak',
        ]));

        // Sinkronisasi: jika status baru = alumni, otomatis buat record di mk_alumni
        if ($mhs->status === Kemahasiswaan::STATUS_ALUMNI && $oldStatus !== Kemahasiswaan::STATUS_ALUMNI) {
            \Modules\ManajemenMahasiswa\Models\Alumni::firstOrCreate(
                ['user_id' => $mhs->user_id],
                [
                    'nim' => $mhs->nim,
                    'angkatan' => $mhs->angkatan,
                    'tahun_lulus' => $mhs->tahun_lulus ?? (int) date('Y'),
                    'program_studi' => 'Teknik Komputer',
                ]
            );
            \Illuminate\Support\Facades\Cache::forget('mk.alumni.summary');
            \Illuminate\Support\Facades\Cache::forget('mk.dashboard.snapshot');
        }
        // Sinkronisasi balik: jika status berubah DARI alumni ke status lain, hapus dari mk_alumni
        elseif ($oldStatus === Kemahasiswaan::STATUS_ALUMNI && $mhs->status !== Kemahasiswaan::STATUS_ALUMNI) {
            \Modules\ManajemenMahasiswa\Models\Alumni::where('user_id', $mhs->user_id)->delete();
            \Illuminate\Support\Facades\Cache::forget('mk.alumni.summary');
            \Illuminate\Support\Facades\Cache::forget('mk.dashboard.snapshot');
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $id)
            ->with('success', 'Biodata mahasiswa berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Profil — Halaman profil untuk mahasiswa sendiri
    // -------------------------------------------------------------------------

    public function profil()
    {
        $user = Auth::user();

        $mhs = $this->withRetry(fn() => Kemahasiswaan::with([
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            },
            'user',
            'user.student'
        ])->where('user_id', $user->id)->first());

        if (!$mhs) {
            return back()->with('error', 'Data kemahasiswaan Anda belum terdaftar dalam sistem.');
        }

        $riwayatKegiatan = $this->withRetry(fn() => $this->buildMergedRiwayat($user->id));

        return view('manajemenmahasiswa::direktori.mahasiswa-profil', compact(
            'mhs',
            'riwayatKegiatan',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Store Riwayat — Tambah catatan riwayat kegiatan (Pengurus)
    // -------------------------------------------------------------------------

    public function storeRiwayat(Request $request, int $id)
    {
        $mode = $request->input('input_mode', 'dropdown');

        if ($mode === 'manual') {
            // Mode manual: ketik nama kegiatan & peran bebas
            $request->validate([
                'nama_kegiatan_manual' => 'required|string|max:255',
                'peran_manual' => 'required|string|max:255',
                'tanggal_kegiatan' => 'nullable|date',
            ]);

            $mhs = Kemahasiswaan::with('user.student')->findOrFail($id);
            $studentId = $mhs->user->student->id ?? null;
            abort_if(!$studentId, 404, 'Data student tidak ditemukan.');

            RiwayatKegiatan::create([
                'student_id' => $studentId,
                'kegiatan_id' => null,
                'peran' => null,
                'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
                'peran_manual' => $request->peran_manual,
                'tanggal_kegiatan' => $request->tanggal_kegiatan,
            ]);
        } else {
            // Mode dropdown: pilih dari list kegiatan
            $request->validate([
                'kegiatan_id' => 'required|exists:mk_kegiatan,id',
                'peran' => 'required|in:' . implode(',', RiwayatKegiatan::PERAN_LIST),
            ]);

            $mhs = Kemahasiswaan::with('user.student')->findOrFail($id);
            $studentId = $mhs->user->student->id ?? null;
            abort_if(!$studentId, 404, 'Data student tidak ditemukan.');

            RiwayatKegiatan::create([
                'student_id' => $studentId,
                'kegiatan_id' => $request->kegiatan_id,
                'peran' => $request->peran,
            ]);
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $id)
            ->with('success', 'Riwayat kegiatan berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // Update Riwayat — Edit catatan riwayat kegiatan (Pengurus)
    // -------------------------------------------------------------------------

    public function updateRiwayat(Request $request, int $riwayatId)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:mk_kegiatan,id',
            'peran' => 'required|in:' . implode(',', RiwayatKegiatan::PERAN_LIST),
        ]);

        $riwayat = RiwayatKegiatan::findOrFail($riwayatId);
        $riwayat->update($request->only(['kegiatan_id', 'peran']));

        // Cari kemahasiswaan untuk redirect
        $mhs = Kemahasiswaan::where('user_id', $riwayat->student_id)->firstOrFail();

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id)
            ->with('success', 'Riwayat kegiatan berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Delete Riwayat — Hapus catatan riwayat (Pengurus)
    // -------------------------------------------------------------------------

    public function destroyRiwayat(int $riwayatId)
    {
        $riwayat = RiwayatKegiatan::findOrFail($riwayatId);
        $mhs = Kemahasiswaan::where('user_id', $riwayat->student_id)->firstOrFail();
        $riwayat->delete();

        return redirect()
            ->route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id)
            ->with('success', 'Riwayat kegiatan berhasil dihapus.');
    }

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

        $riwayatKegiatan = $this->buildMergedRiwayat($mhs->user_id);

        return view('manajemenmahasiswa::direktori.mahasiswa-cv', compact(
            'mhs',
            'riwayatKegiatan',
        ));
    }

    /**
     * CV untuk mahasiswa sendiri.
     */
    public function generateCvSelf()
    {
        $user = Auth::user();
        $mhs = Kemahasiswaan::with([
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            },
            'user',
            'user.student'
        ])->where('user_id', $user->id)->firstOrFail();

        $riwayatKegiatan = $this->buildMergedRiwayat($user->id);

        return view('manajemenmahasiswa::direktori.mahasiswa-cv', compact(
            'mhs',
            'riwayatKegiatan',
        ));
    }

    // -------------------------------------------------------------------------
    // Preview CV Builder — Lihat hasil CV Builder mahasiswa (Admin/Pengurus)
    // -------------------------------------------------------------------------

    public function previewCvBuilder(int $id)
    {
        $mhs = Kemahasiswaan::with([
            'user',
            'user.student',
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            }
        ])->findOrFail($id);

        $user = $mhs->user;
        abort_if(!$user, 404, 'User tidak ditemukan.');

        $cvProfile = CvProfile::where('user_id', $user->id)->first();
        abort_if(!$cvProfile, 404, 'Mahasiswa belum membuat CV melalui CV Builder.');

        $data = $this->buildCvBuilderData($user, $cvProfile);

        return view('profile.cv.template-ats', $data);
    }

    /**
     * Build all CV data for the ATS template (mirrored from CvBuilderController).
     */
    private function buildCvBuilderData($user, CvProfile $cvProfile): array
    {
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'personal_email' => $cvProfile->cv_email ?? $user->personal_email ?? null,
                'whatsapp' => $cvProfile->cv_whatsapp ?? data_get($user, 'whatsapp'),
                'avatar_url' => $user->avatar_url_format ?? $user->avatar_url ?? null,
                'nim' => '-',
                'angkatan' => '-'
            ],
            'cv' => $cvProfile,
            'pendidikan' => $cvProfile->pendidikan ?? [],
            'bahasa' => $cvProfile->bahasa ?? [],
            'pengalaman' => $cvProfile->pengalaman_kerja ?? [],
            'kegiatan_manual' => $cvProfile->kegiatan_organisasi ?? [],
            'kegiatan' => [],
            'proyek' => $cvProfile->proyek ?? [],
            'prestasi' => [],
            'keahlian' => $cvProfile->keahlian ?? [],
            'sertifikasi' => $cvProfile->sertifikasi ?? [],
        ];

        // Populate sync data
        if ($user->hasRole('mahasiswa') && $user->student) {
            $data['user']['nim'] = $user->student->student_number;
            $data['user']['angkatan'] = $user->student->cohort_year;

            $riwayat = RiwayatKegiatan::with('kegiatan')->where('student_id', $user->student->id)->get();
            foreach ($riwayat as $rw) {
                $data['kegiatan'][] = [
                    'nama' => $rw->nama_kegiatan,
                    'peran' => $rw->peran_label,
                    'tanggal' => $rw->tanggal_display,
                ];
            }
        } elseif ($user->hasRole('alumni')) {
            $alumni = Alumni::where('user_id', $user->id)->first();
            if ($alumni) {
                $data['user']['nim'] = $alumni->nim;
                $data['user']['angkatan'] = $alumni->angkatan;

                if ($alumni->perusahaan) {
                    array_unshift($data['pengalaman'], [
                        'perusahaan' => $alumni->perusahaan,
                        'posisi' => $alumni->jabatan,
                        'tahun_mulai' => $alumni->tahun_mulai_bekerja,
                        'tahun_selesai' => 'Sekarang',
                        'deskripsi' => 'Data tersinkronisasi dari direktori alumni.'
                    ]);
                }
            }
        }

        $kemahasiswaan = Kemahasiswaan::with('prestasi')->where('user_id', $user->id)->first();
        if ($kemahasiswaan) {
            array_unshift($data['pendidikan'], [
                'institusi' => 'Universitas Diponegoro',
                'jurusan' => 'S1 Teknik Komputer',
                'tahun_masuk' => $kemahasiswaan->angkatan,
                'tahun_lulus' => $kemahasiswaan->tahun_lulus ?? 'Sekarang'
            ]);

            if ($kemahasiswaan->prestasi) {
                foreach ($kemahasiswaan->prestasi as $p) {
                    $data['prestasi'][] = [
                        'nama' => $p->nama_prestasi,
                        'tingkat' => $p->tingkat,
                        'tahun' => $p->tanggal ? $p->tanggal->format('Y') : null,
                    ];
                }
            }
        }

        return $data;
    }
}
