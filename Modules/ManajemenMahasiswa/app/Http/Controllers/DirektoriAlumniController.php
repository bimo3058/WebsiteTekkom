<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Services\AlumniService;

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
        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles) || \in_array('dpm', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }
        // GPM, Dosen, Ketua Departemen → layout dosen
        if (\in_array('gpm', $roles) || \in_array('dosen', $roles) || \in_array('dosen_koordinator', $roles) || \in_array('ketua_departemen', $roles)) {
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
            'admin',
            'admin_kemahasiswaan',
            'gpm',
            'dosen',
            'dosen_koordinator',
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
            'admin',
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
                    'ipk' => $km?->ipk,
                ]);

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
                // Kembalikan status ke aktif jika record km-nya ada
                Kemahasiswaan::whereIn('user_id', $noLongerAlumni)
                    ->where('status', Kemahasiswaan::STATUS_ALUMNI)
                    ->update(['status' => Kemahasiswaan::STATUS_AKTIF]);
                $changed = true;
            }

            // 3. Sinkronisasi IPK: untuk semua alumni yang sudah ada, pastikan IPK sesuai dengan mk_kemahasiswaan
            $alumniRecords = Alumni::whereIn('user_id', $alumniUserIds)->get();
            foreach ($alumniRecords as $alumniRecord) {
                $km = Kemahasiswaan::where('user_id', $alumniRecord->user_id)->first();
                if ($km && $km->ipk !== null && (string) $alumniRecord->ipk !== (string) $km->ipk) {
                    $alumniRecord->update(['ipk' => $km->ipk]);
                }
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

        $filters = $request->only(['angkatan', 'tahun_lulus', 'status_karir', 'bidang_industri', 'search']);
        $filters = array_filter($filters, fn($v) => $v !== 'semua' && $v !== null && $v !== '');

        try {
            [
                $alumni,
                $angkatanList,
                $tahunLulusList,
                $summary,
            ] = $this->withRetry(function () use ($filters) {
                $alumni = $this->alumniService->listAlumni($filters, 15);
                $angkatanList = Alumni::select('angkatan')->distinct()->orderByDesc('angkatan')->pluck('angkatan');
                $tahunLulusList = Alumni::select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');
                $summary = $this->alumniService->getSummary();
                return [$alumni, $angkatanList, $tahunLulusList, $summary];
            });
        } catch (\Throwable) {
            $alumni = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $angkatanList = collect();
            $tahunLulusList = collect();
            $summary = ['total' => 0, 'per_status' => []];
        }

        $statusKarirOptions = Alumni::STATUS_LABELS;
        $bidangIndustriOptions = Alumni::BIDANG_INDUSTRI_LIST;
        $isAdmin = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');

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
            'totalAlumni',
            'bekerja',
            'wirausaha',
            'studiLanjut',
            'belumTerdata'
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Show — Detail profil alumni
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        try {
            $alumni = $this->withRetry(fn() => $this->alumniService->findById($id));

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
            $isAdmin         = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');
            $canSeeHistory   = $this->canSeeHistory();
            $canManageHistory = $this->canManageHistory();
            // Admin group, GPM, DPM, Dosen, dan Ketua Departemen bisa lihat IPK
            $isCanSeeIpk = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'dosen', 'dosen_koordinator', 'ketua_departemen');

            return view('manajemenmahasiswa::direktori.alumni-show', compact(
                'alumni',
                'kemahasiswaan',
                'riwayatKegiatan',
                'semuaKegiatan',
                'isAdmin',
                'canSeeHistory',
                'canManageHistory',
                'isCanSeeIpk',
            ))->with('layout', $this->resolveLayout());

        } catch (\Throwable) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }
    }

    // -------------------------------------------------------------------------
    // Profil — Halaman profil karir untuk alumni sendiri
    // -------------------------------------------------------------------------

    public function profil()
    {
        try {
            $user = Auth::user();

            $alumni = $this->withRetry(fn() => Alumni::where('user_id', $user->id)->first());

            if (!$alumni) {
                $mhs = $this->withRetry(fn() => Kemahasiswaan::where('user_id', $user->id)->first());

                if ($mhs && $mhs->status === Kemahasiswaan::STATUS_ALUMNI) {
                    $alumni = $this->withRetry(fn() => Alumni::create([
                        'user_id' => $user->id,
                        'nim' => $mhs->nim,
                        'angkatan' => $mhs->angkatan,
                        'tahun_lulus' => $mhs->tahun_lulus ?? date('Y'),
                        'program_studi' => 'Teknik Komputer',
                        'ipk' => $mhs->ipk,
                    ]));
                } else {
                    return back()->with('error', 'Akses ditolak. Anda belum terdaftar sebagai alumni.');
                }
            }

            return view('manajemenmahasiswa::direktori.alumni-profil', compact('alumni'))
                ->with('layout', $this->resolveLayout());

        } catch (\Throwable) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi dalam beberapa saat.');
        }
    }

    // -------------------------------------------------------------------------
    // Update Profil — Simpan perubahan karir diri sendiri
    // -------------------------------------------------------------------------

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        try {
            $alumni = $this->withRetry(fn() => Alumni::where('user_id', $user->id)->firstOrFail());
        } catch (\Throwable) {
            return back()->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi.');
        }

        $validated = $request->validate([
            'status_karir' => 'nullable|string|in:' . implode(',', Alumni::STATUS_LIST),
            'perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'bidang_industri' => 'nullable|string|in:' . implode(',', array_keys(Alumni::BIDANG_INDUSTRI_LIST)),
            'tahun_mulai_bekerja' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'linkedin' => 'nullable|url|max:255',
        ]);

        try {
            $this->withRetry(fn() => $this->alumniService->update($alumni->id, $validated));
        } catch (\Throwable) {
            return back()->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi.');
        }

        return redirect()->route('manajemenmahasiswa.direktori.alumni.profil')
            ->with('success', 'Profil karir Anda berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Edit — Form edit data alumni (Admin only)
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        try {
            $alumni = $this->withRetry(fn() => $this->alumniService->findById($id));
        } catch (\Throwable) {
            return redirect()
                ->route('manajemenmahasiswa.direktori.alumni.index')
                ->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi.');
        }

        // IPK = data sensitif. Samakan gating dengan halaman show: hanya admin group/GPM/DPM/Dosen.
        // (Route edit sudah dibatasi admin group sehingga flag ini selalu true utk editor saat ini,
        //  namun tetap digating agar konsisten & aman bila akses edit kelak diperluas.)
        $isCanSeeIpk = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'dosen', 'dosen_koordinator');

        return view('manajemenmahasiswa::direktori.alumni-edit', compact('alumni', 'isCanSeeIpk'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Update — Simpan perubahan data alumni (Admin only)
    // -------------------------------------------------------------------------

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:30',
            'angkatan' => 'required|integer|min:2000|max:2099',
            'tahun_lulus' => 'required|integer|min:2000|max:2099',
            'program_studi' => 'nullable|string|max:255',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'status_karir' => 'nullable|string|in:' . implode(',', Alumni::STATUS_LIST),
            'perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'bidang_industri' => 'nullable|string',
            'tahun_mulai_bekerja' => 'nullable|integer',
            'linkedin' => 'nullable|url|max:255',
            'personal_email' => 'nullable|email|max:255',
        ]);

        // Email pribadi disimpan di tabel users (bukan kolom mk_alumni) — pisahkan dari payload alumni.
        $personalEmail = $validated['personal_email'] ?? null;
        unset($validated['personal_email']);

        // IPK = data sensitif. Hanya role tertentu yang boleh mengubahnya (samakan gating dgn halaman show).
        $canEditIpk = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'dosen', 'dosen_koordinator');
        if (!$canEditIpk) {
            unset($validated['ipk']);
        }

        try {
            $alumni = $this->withRetry(fn() => $this->alumniService->update($id, $validated));

            // Sinkronisasi kontak ke tabel users dan cv_profiles
            if ($request->has('kontak') || $request->has('phone_code')) {
                $kontak = $request->kontak;
                $phoneCode = $request->phone_code ?? '+62';
                
                if ($kontak) {
                    // Bersihkan non-digit
                    $kontak = preg_replace('/[^\d]/', '', $kontak);
                    if ($phoneCode === '+62' && str_starts_with($kontak, '0')) {
                        $kontak = ltrim($kontak, '0');
                    }
                    $fullWa = $phoneCode . $kontak;
                } else {
                    $fullWa = null;
                }

                // Sync ke user
                $userModel = \App\Models\User::find($alumni->user_id);
                if ($userModel) {
                    $userModel->updateQuietly(['whatsapp' => $fullWa]);
                }
                
                // Sync ke cv_profiles
                \App\Models\CvProfile::where('user_id', $alumni->user_id)->update(['cv_whatsapp' => $fullWa]);
                
                // Sync ke kemahasiswaan
                \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::where('user_id', $alumni->user_id)->update(['kontak' => $fullWa]);
            }

            // Sinkronisasi email pribadi ke tabel users
            if ($request->has('personal_email')) {
                $userModel = \App\Models\User::find($alumni->user_id);
                if ($userModel) {
                    $userModel->updateQuietly(['personal_email' => $personalEmail]);
                }
            }

            // Write-through IPK ke mk_kemahasiswaan (sumber utama IPK) agar perubahan tidak ketimpa
            // oleh auto-sync di halaman index. Hanya jika role berhak & field IPK memang dikirim.
            if ($canEditIpk && array_key_exists('ipk', $validated)) {
                \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::where('user_id', $alumni->user_id)
                    ->update(['ipk' => $validated['ipk']]);
            }

        } catch (\Throwable $e) {
            return back()->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi.');
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', 'Data alumni berhasil diperbarui.');
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

        if ($mode === 'manual') {
            $request->validate([
                'nama_kegiatan_manual' => 'required|string|max:255',
                'peran_manual'         => 'required|string|max:255',
                'tanggal_kegiatan'     => 'nullable|date',
            ]);

            RiwayatKegiatan::create([
                'student_id'           => $student->id,
                'kegiatan_id'          => null,
                'peran'                => null,
                'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
                'peran_manual'         => $request->peran_manual,
                'tanggal_kegiatan'     => $request->tanggal_kegiatan,
            ]);
        } else {
            $request->validate([
                'kegiatan_id' => 'required|exists:mk_kegiatan,id',
                'peran'       => 'required|in:' . implode(',', RiwayatKegiatan::PERAN_LIST),
            ]);

            RiwayatKegiatan::create([
                'student_id'  => $student->id,
                'kegiatan_id' => $request->kegiatan_id,
                'peran'       => $request->peran,
            ]);
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
            'nama_prestasi' => 'required|string|max:255',
            'tingkat'       => 'required|in:' . implode(',', \Modules\ManajemenMahasiswa\Models\Prestasi::TINGKAT_LIST),
            'tanggal'       => 'nullable|date',
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
    // Prestasi — Destroy
    // -------------------------------------------------------------------------

    public function destroyPrestasi(int $prestasiId)
    {
        $prestasi = \Modules\ManajemenMahasiswa\Models\Prestasi::findOrFail($prestasiId);

        // Resolve alumni_id untuk redirect — lewat kemahasiswaan → user → alumni
        $kemahasiswaan = Kemahasiswaan::find($prestasi->kemahasiswaan_id);
        $alumni = $kemahasiswaan ? Alumni::where('user_id', $kemahasiswaan->user_id)->first() : null;

        $prestasi->delete();

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $alumni?->id ?? 0)
            ->with('success', 'Prestasi berhasil dihapus.');
    }

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
