<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
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

        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }
        if (\in_array('gpm', $roles)) {
            return 'manajemenmahasiswa::layouts.dosen';
        }
        if (\in_array('pengurus_himpunan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
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
    // Helpers — Sync dari mk_kemahasiswaan
    // -------------------------------------------------------------------------

    /**
     * Sinkronisasi dua arah antara mk_kemahasiswaan dan mk_alumni.
     */
    private function syncFromKemahasiswaan(): void
    {
        $this->withRetry(function () {
            $changed = false;

            // 1. Tambah: mahasiswa status=alumni yang belum ada di mk_alumni
            $existingUserIds = Alumni::pluck('user_id')->toArray();

            $alumniMahasiswa = Kemahasiswaan::where('status', Kemahasiswaan::STATUS_ALUMNI)
                ->whereNotIn('user_id', $existingUserIds)
                ->get();

            foreach ($alumniMahasiswa as $mhs) {
                Alumni::create([
                    'user_id' => $mhs->user_id,
                    'nim' => $mhs->nim,
                    'angkatan' => $mhs->angkatan,
                    'tahun_lulus' => $mhs->tahun_lulus ?? (int) date('Y'),
                    'program_studi' => 'Teknik Komputer',
                ]);
                $changed = true;
            }

            // 2. Hapus: alumni di mk_alumni yang statusnya sudah bukan 'alumni'
            $alumniUserIds = Alumni::pluck('user_id')->toArray();
            if (!empty($alumniUserIds)) {
                $noLongerAlumni = Kemahasiswaan::whereIn('user_id', $alumniUserIds)
                    ->where('status', '!=', Kemahasiswaan::STATUS_ALUMNI)
                    ->pluck('user_id')
                    ->toArray();

                if (!empty($noLongerAlumni)) {
                    Alumni::whereIn('user_id', $noLongerAlumni)->delete();
                    $changed = true;
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
            $isAdmin = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');

            return view('manajemenmahasiswa::direktori.alumni-show', compact('alumni', 'isAdmin'))
                ->with('layout', $this->resolveLayout());

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

        return view('manajemenmahasiswa::direktori.alumni-edit', compact('alumni'))
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
            'status_karir' => 'nullable|string|in:' . implode(',', Alumni::STATUS_LIST),
            'perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'bidang_industri' => 'nullable|string',
            'tahun_mulai_bekerja' => 'nullable|integer',
            'linkedin' => 'nullable|url|max:255',
        ]);

        try {
            $this->withRetry(fn() => $this->alumniService->update($id, $validated));
        } catch (\Throwable) {
            return back()->with('error', 'Koneksi database sedang tidak stabil. Silakan coba lagi.');
        }

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', 'Data alumni berhasil diperbarui.');
    }
}
