<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Tentukan layout blade berdasarkan role user yang sedang login.
     */
    private function resolveLayout(): string
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }

        if (\in_array('gpm', $roles)) {
            return 'manajemenmahasiswa::layouts.dosen';
        }

        if (\in_array('pengurus_himpunan', $roles)) {
            return 'manajemenmahasiswa::layouts.admin';
        }

        // Default: mahasiswa
        return 'manajemenmahasiswa::layouts.mahasiswa';
    }

    /**
     * Cek apakah user memiliki role tertentu.
     */
    private function hasRole(string ...$roles): bool
    {
        $userRoles = Auth::user()->roles->pluck('name')->toArray();
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
     * Sinkronisasi dua arah antara mk_kemahasiswaan dan mk_alumni:
     * 1. Buat record mk_alumni untuk mahasiswa berstatus 'alumni' yang belum ada
     * 2. Hapus record mk_alumni jika statusnya sudah bukan 'alumni' lagi
     */
    private function syncFromKemahasiswaan(): void
    {
        $changed = false;

        // 1. Tambah: mahasiswa status=alumni yang belum ada di mk_alumni
        $existingUserIds = Alumni::pluck('user_id')->toArray();

        $alumniMahasiswa = Kemahasiswaan::where('status', Kemahasiswaan::STATUS_ALUMNI)
            ->whereNotIn('user_id', $existingUserIds)
            ->get();

        foreach ($alumniMahasiswa as $mhs) {
            Alumni::create([
                'user_id'       => $mhs->user_id,
                'nim'           => $mhs->nim,
                'angkatan'      => $mhs->angkatan,
                'tahun_lulus'   => $mhs->tahun_lulus ?? (int) date('Y'),
                'program_studi' => 'Teknik Komputer',
            ]);
            $changed = true;
        }

        // 2. Hapus: alumni di mk_alumni yang statusnya sudah bukan 'alumni' di mk_kemahasiswaan
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

        // Flush cache jika ada perubahan
        if ($changed) {
            \Illuminate\Support\Facades\Cache::forget('mk.alumni.summary');
            \Illuminate\Support\Facades\Cache::forget('mk.dashboard.snapshot');
        }
    }

    // -------------------------------------------------------------------------
    // Index — Daftar semua alumni (Admin, GPM, Pengurus, Dosen)
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        // Auto-sync: cek mk_kemahasiswaan status=alumni → mk_alumni
        $this->syncFromKemahasiswaan();

        $filters = $request->only(['angkatan', 'tahun_lulus', 'status_karir', 'bidang_industri', 'search']);
        
        // Remove 'semua' values from filters
        $filters = array_filter($filters, fn($value) => $value !== 'semua' && $value !== null && $value !== '');

        $alumni = $this->alumniService->listAlumni($filters, 15);

        // Data for filters
        $angkatanList = Alumni::select('angkatan')->distinct()->orderByDesc('angkatan')->pluck('angkatan');
        $tahunLulusList = Alumni::select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');
        $statusKarirOptions = Alumni::STATUS_LABELS;
        $bidangIndustriOptions = Alumni::BIDANG_INDUSTRI_LIST;

        $isAdmin    = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');
        
        // Summary for Stat Cards (flush cache dulu karena mungkin baru ada sync)
        $summary = $this->alumniService->getSummary();
        $totalAlumni = $summary['total'];
        $bekerja = $summary['per_status']['bekerja'] ?? 0;
        $wirausaha = $summary['per_status']['wirausaha'] ?? 0;
        $studiLanjut = $summary['per_status']['studi_lanjut'] ?? 0;
        $belumTerdata = $summary['per_status']['belum_terdata'] ?? 0;

        return view('manajemenmahasiswa::direktori.alumni-index', compact(
            'alumni', 'angkatanList', 'tahunLulusList', 'statusKarirOptions', 'bidangIndustriOptions', 'isAdmin',
            'totalAlumni', 'bekerja', 'wirausaha', 'studiLanjut', 'belumTerdata'
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Show — Detail profil alumni (Admin, GPM, Pengurus, Dosen)
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        $alumni = $this->alumniService->findById($id);
        $isAdmin = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');

        return view('manajemenmahasiswa::direktori.alumni-show', compact('alumni', 'isAdmin'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Profil — Halaman profil karir untuk alumni sendiri
    // -------------------------------------------------------------------------

    public function profil()
    {
        $user = Auth::user();
        
        // Cek apakah dia terdaftar di mk_alumni
        $alumni = Alumni::where('user_id', $user->id)->first();

        // Jika belum ada di mk_alumni, cek di kemahasiswaan apakah dia sudah lulus
        if (!$alumni) {
            $mhs = Kemahasiswaan::where('user_id', $user->id)->first();
            if ($mhs && $mhs->status === Kemahasiswaan::STATUS_ALUMNI) {
                // Auto create alumni record based on kemahasiswaan data
                $alumni = Alumni::create([
                    'user_id' => $user->id,
                    'nim' => $mhs->nim,
                    'angkatan' => $mhs->angkatan,
                    'tahun_lulus' => $mhs->tahun_lulus ?? date('Y'),
                    'program_studi' => 'Teknik Komputer', // Default
                ]);
            } else {
                return back()->with('error', 'Akses ditolak. Anda belum terdaftar sebagai alumni.');
            }
        }

        return view('manajemenmahasiswa::direktori.alumni-profil', compact('alumni'))
            ->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Update Profil — Simpan perubahan karir diri sendiri
    // -------------------------------------------------------------------------

    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $alumni = Alumni::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'status_karir' => 'nullable|string|in:' . implode(',', Alumni::STATUS_LIST),
            'perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'bidang_industri' => 'nullable|string|in:' . implode(',', array_keys(Alumni::BIDANG_INDUSTRI_LIST)),
            'tahun_mulai_bekerja' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'linkedin' => 'nullable|url|max:255',
        ]);

        $this->alumniService->update($alumni->id, $validated);

        return redirect()->route('manajemenmahasiswa.direktori.alumni.profil')
            ->with('success', 'Profil karir Anda berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // Edit — Form edit data alumni (Admin only)
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $alumni = $this->alumniService->findById($id);
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

        $this->alumniService->update($id, $validated);

        return redirect()
            ->route('manajemenmahasiswa.direktori.alumni.show', $id)
            ->with('success', 'Data alumni berhasil diperbarui.');
    }
}
