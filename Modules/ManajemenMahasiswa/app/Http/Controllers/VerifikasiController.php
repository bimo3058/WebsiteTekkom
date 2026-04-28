<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\VerifikasiBukti;

class VerifikasiController extends Controller
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

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

    private function isVerificator(): bool
    {
        return $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'gpm');
    }

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

        // Semua jenis pengurus himpunan menggunakan layout admin
        $pengurus = ['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'];
        foreach ($pengurus as $role) {
            if (\in_array($role, $roles)) {
                return 'manajemenmahasiswa::layouts.admin';
            }
        }

        return 'manajemenmahasiswa::layouts.mahasiswa';
    }

    // -------------------------------------------------------------------------
    // Helper — Auto-provision student + kemahasiswaan record jika belum ada
    // Digunakan agar pengurus (ketua_unit, dll) bisa langsung submit tanpa
    // harus didaftarkan manual oleh admin terlebih dahulu.
    // -------------------------------------------------------------------------

    private function ensureStudentRecord(\App\Models\User $user): Student
    {
        // Cari atau buat record mk_kemahasiswaan terlebih dahulu
        $mhs = Kemahasiswaan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama'     => $user->name,
                'nim'      => 'PENGURUS-' . $user->id,
                'angkatan' => (int) date('Y'),
                'status'   => Kemahasiswaan::STATUS_AKTIF,
            ]
        );

        // Cari atau buat record students
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => $mhs->nim,
                'cohort_year'    => $mhs->angkatan ?? (int) date('Y'),
            ]
        );

        return $student;
    }

    // -------------------------------------------------------------------------
    // Index — Render view sesuai role
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->isVerificator()) {
            return $this->adminIndex($request);
        }

        return $this->mahasiswaIndex($request);
    }

    // -------------------------------------------------------------------------
    // Admin View — Dashboard verifikasi semua data
    // -------------------------------------------------------------------------

    private function adminIndex(Request $request)
    {
        $tab    = $request->get('tab', 'riwayat');
        $status = $request->get('status', 'pending');
        $search = $request->get('search');
        $angkatan = $request->get('angkatan');

        // ── Riwayat Kegiatan (manual only) ──
        $riwayatQuery = RiwayatKegiatan::with(['student.user', 'kegiatan', 'verifiedBy', 'buktiFiles'])
            ->manualOnly();

        if ($status && $status !== 'semua') {
            $riwayatQuery->where('verification_status', $status);
        }

        if ($search) {
            $riwayatQuery->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%");
            });
        }

        if ($angkatan && $angkatan !== 'semua') {
            $riwayatQuery->whereHas('student', function ($q) use ($angkatan) {
                $q->where('cohort_year', $angkatan);
            });
        }

        $riwayatData = $riwayatQuery->orderByDesc('created_at')->paginate(15, ['*'], 'riwayat_page');

        // ── Prestasi ──
        $prestasiQuery = Prestasi::with(['kemahasiswaan.user', 'verifiedBy', 'buktiFiles']);

        if ($status && $status !== 'semua') {
            $prestasiQuery->where('verification_status', $status);
        }

        if ($search) {
            $prestasiQuery->whereHas('kemahasiswaan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($angkatan && $angkatan !== 'semua') {
            $prestasiQuery->whereHas('kemahasiswaan', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        $prestasiData = $prestasiQuery->orderByDesc('created_at')->paginate(15, ['*'], 'prestasi_page');

        // Counters
        $pendingRiwayat  = RiwayatKegiatan::manualOnly()->pending()->count();
        $pendingPrestasi = Prestasi::pending()->count();

        // Angkatan list for filter
        $angkatanList = Kemahasiswaan::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('manajemenmahasiswa::verifikasi.admin', compact(
            'riwayatData',
            'prestasiData',
            'pendingRiwayat',
            'pendingPrestasi',
            'tab',
            'status',
            'search',
            'angkatan',
            'angkatanList',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Mahasiswa View — Data pengajuan milik sendiri
    // -------------------------------------------------------------------------

    private function mahasiswaIndex(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $mhs = Kemahasiswaan::where('user_id', $user->id)->first();

        $riwayatData  = collect();
        $prestasiData = collect();
        $stats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

        if ($student) {
            $riwayatData = RiwayatKegiatan::with(['kegiatan', 'verifiedBy', 'buktiFiles'])
                ->where('student_id', $student->id)
                ->manualOnly()
                ->orderByDesc('created_at')
                ->get();

            $stats['pending']  += $riwayatData->where('verification_status', 'pending')->count();
            $stats['approved'] += $riwayatData->where('verification_status', 'approved')->count();
            $stats['rejected'] += $riwayatData->where('verification_status', 'rejected')->count();
        }

        if ($mhs) {
            $prestasiData = Prestasi::with(['verifiedBy', 'buktiFiles'])
                ->where('kemahasiswaan_id', $mhs->id)
                ->orderByDesc('created_at')
                ->get();

            $stats['pending']  += $prestasiData->where('verification_status', 'pending')->count();
            $stats['approved'] += $prestasiData->where('verification_status', 'approved')->count();
            $stats['rejected'] += $prestasiData->where('verification_status', 'rejected')->count();
        }

        return view('manajemenmahasiswa::verifikasi.mahasiswa', compact(
            'riwayatData',
            'prestasiData',
            'stats',
            'mhs',
            'student',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Store Riwayat — Mahasiswa/Pengurus/Alumni ajukan riwayat kegiatan
    // -------------------------------------------------------------------------

    public function storeRiwayat(Request $request)
    {
        $request->validate([
            'nama_kegiatan_manual' => 'required|string|max:255',
            'peran_manual'         => 'required|string|max:255',
            'tanggal_kegiatan'     => 'nullable|date',
            'bukti_images'         => 'nullable|array|max:5',
            'bukti_images.*'       => 'file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'bukti_docs'           => 'nullable|array|max:5',
            'bukti_docs.*'         => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $user    = Auth::user();
        $student = $this->ensureStudentRecord($user);

        $riwayat = RiwayatKegiatan::create([
            'student_id'           => $student->id,
            'kegiatan_id'          => null,
            'peran'                => null,
            'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
            'peran_manual'         => $request->peran_manual,
            'tanggal_kegiatan'     => $request->tanggal_kegiatan,
            'verification_status'  => 'pending',
        ]);

        // Upload bukti files ke Supabase
        $this->uploadBuktiFiles($request, 'riwayat', $riwayat->id);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index')
            ->with('success', 'Riwayat kegiatan berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Store Prestasi — Mahasiswa/Pengurus/Alumni ajukan prestasi
    // -------------------------------------------------------------------------

    public function storePrestasi(Request $request)
    {
        $request->validate([
            'nama_prestasi' => 'required|string|max:255',
            'tingkat'       => 'required|in:' . implode(',', Prestasi::TINGKAT_LIST),
            'tanggal'       => 'required|date',
            'bukti_images'  => 'nullable|array|max:5',
            'bukti_images.*'=> 'file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'bukti_docs'    => 'nullable|array|max:5',
            'bukti_docs.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $user = Auth::user();

        // Auto-provision mk_kemahasiswaan jika belum ada (untuk pengurus seperti ketua_unit)
        $mhs = Kemahasiswaan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama'     => $user->name,
                'nim'      => 'PENGURUS-' . $user->id,
                'angkatan' => (int) date('Y'),
                'status'   => Kemahasiswaan::STATUS_AKTIF,
            ]
        );

        $prestasi = Prestasi::create([
            'kemahasiswaan_id'    => $mhs->id,
            'nama_prestasi'       => $request->nama_prestasi,
            'tingkat'             => $request->tingkat,
            'tanggal'             => $request->tanggal,
            'verification_status' => 'pending',
        ]);

        // Upload bukti files ke Supabase
        $this->uploadBuktiFiles($request, 'prestasi', $prestasi->id);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index')
            ->with('success', 'Prestasi berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Approve Riwayat
    // -------------------------------------------------------------------------

    public function approveRiwayat(int $id)
    {
        $riwayat = RiwayatKegiatan::findOrFail($id);
        $riwayat->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan berhasil disetujui.');
    }

    // -------------------------------------------------------------------------
    // Reject Riwayat
    // -------------------------------------------------------------------------

    public function rejectRiwayat(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'required|string|max:500',
        ]);

        $riwayat = RiwayatKegiatan::findOrFail($id);
        $riwayat->update([
            'verification_status' => 'rejected',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan telah ditolak.');
    }

    // -------------------------------------------------------------------------
    // Approve Prestasi
    // -------------------------------------------------------------------------

    public function approvePrestasi(int $id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi berhasil disetujui.');
    }

    // -------------------------------------------------------------------------
    // Reject Prestasi
    // -------------------------------------------------------------------------

    public function rejectPrestasi(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'required|string|max:500',
        ]);

        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update([
            'verification_status' => 'rejected',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi telah ditolak.');
    }

    // -------------------------------------------------------------------------
    // Helper — Upload bukti files ke Supabase
    // -------------------------------------------------------------------------

    private function uploadBuktiFiles(Request $request, string $type, int $parentId): void
    {
        $supabase = app(SupabaseStorage::class);

        // Upload gambar
        if ($request->hasFile('bukti_images')) {
            foreach ($request->file('bukti_images') as $file) {
                $folder = 'mk_verifikasi/' . $type . '/images';
                $path = $supabase->upload($file, $folder);

                if ($path) {
                    VerifikasiBukti::create([
                        'bukti_type' => $type,
                        'bukti_id'   => $parentId,
                        'nama_file'  => $file->getClientOriginalName(),
                        'path_file'  => $path,
                        'tipe_file'  => VerifikasiBukti::TIPE_IMAGE,
                    ]);
                }
            }
        }

        // Upload dokumen
        if ($request->hasFile('bukti_docs')) {
            foreach ($request->file('bukti_docs') as $file) {
                $folder = 'mk_verifikasi/' . $type . '/docs';
                $path = $supabase->upload($file, $folder);

                if ($path) {
                    VerifikasiBukti::create([
                        'bukti_type' => $type,
                        'bukti_id'   => $parentId,
                        'nama_file'  => $file->getClientOriginalName(),
                        'path_file'  => $path,
                        'tipe_file'  => VerifikasiBukti::TIPE_DOCUMENT,
                    ]);
                }
            }
        }
    }
}
