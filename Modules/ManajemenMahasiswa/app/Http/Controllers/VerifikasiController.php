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
use Modules\ManajemenMahasiswa\Models\RewardAturan;

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
        return $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'dpm');
    }

    /**
     * Pengawas mutu (read-only): GPM & Ketua Departemen.
     * Boleh MELIHAT seluruh daftar verifikasi, tetapi TIDAK boleh menyetujui/menolak.
     * Selaras dengan akses view-only mereka di Manajemen Kegiatan dan dashboard
     * analitik (scope evaluasi mutu).
     */
    private function isPengawas(): bool
    {
        return $this->hasRole('gpm', 'ketua_departemen');
    }

    /**
     * Boleh membuka halaman Klaim Reward Prestasi.
     * Verifikator (kelola) + pengawas mutu GPM & Ketua Departemen (read-only).
     */
    private function canAccessReward(): bool
    {
        return $this->isVerificator() || $this->isPengawas();
    }

    private function resolveLayout(): string
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles) || \in_array('dpm', $roles)
            || \in_array('gpm', $roles) || \in_array('ketua_departemen', $roles)) {
            // GPM & Ketua Departemen melihat tabel monitoring read-only → gunakan shell admin
            return 'manajemenmahasiswa::layouts.admin';
        }


        // Semua jenis pengurus himpunan menggunakan layout admin
        $pengurus = ['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'];
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

        // GPM & Ketua Departemen — pengawas mutu: lihat seluruh daftar verifikasi
        // dalam mode read-only (tanpa tombol setujui/tolak/klaim reward).
        if ($this->isPengawas()) {
            return $this->adminIndex($request, readOnly: true);
        }

        return $this->mahasiswaIndex($request);
    }

    // -------------------------------------------------------------------------
    // Admin View — Dashboard verifikasi semua data
    // -------------------------------------------------------------------------

    private function adminIndex(Request $request, bool $readOnly = false)
    {
        $tab    = $request->get('tab', 'prestasi');
        $status = $request->get('status', 'semua');
        $search = $request->get('search');
        $angkatan = $request->get('angkatan');

        // ── Riwayat Kegiatan (manual only) ──
        $riwayatQuery = RiwayatKegiatan::with(['student.user', 'kegiatan', 'verifiedBy', 'buktiFiles'])
            ->manualOnly();

        if ($status && $status !== 'semua') {
            $riwayatQuery->where('verification_status', $status);
        }

        if ($search) {
            $riwayatQuery->where(function ($query) use ($search) {
                $query->whereHas('student.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('student', function ($q) use ($search) {
                    $q->where('student_number', 'like', "%{$search}%");
                });
            });
        }

        if ($angkatan && $angkatan !== 'semua') {
            // Samakan sumber angkatan dengan dropdown (mk_kemahasiswaan.angkatan),
            // dipetakan ke student lewat user_id agar konsisten dengan tab prestasi.
            $userIds = Kemahasiswaan::where('angkatan', $angkatan)->pluck('user_id');
            $riwayatQuery->whereHas('student', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            });
        }

        $riwayatData = $riwayatQuery->orderByDesc('created_at')->paginate(15, ['*'], 'riwayat_page');

        // ── Prestasi ──
        $prestasiQuery = Prestasi::with(['kemahasiswaan.user', 'verifiedBy', 'claimedBy', 'reviewedBy', 'buktiFiles']);

        if ($status && $status !== 'semua') {
            $prestasiQuery->where('verification_status', $status);
        }

        if ($search) {
            $prestasiQuery->whereHas('kemahasiswaan', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                          ->orWhere('nim', 'like', "%{$search}%");
                });
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
        $pendingPrestasiReward = Prestasi::rewardDiajukan()->count(); // utk badge tombol "Klaim Reward"

        // ── Stat cards — global counts per tab (tidak terpengaruh filter search/angkatan) ──
        if ($tab === 'riwayat') {
            $adminStats = [
                'pending'  => RiwayatKegiatan::manualOnly()->pending()->count(),
                'approved' => RiwayatKegiatan::manualOnly()->where('verification_status', 'approved')->count(),
                'rejected' => RiwayatKegiatan::manualOnly()->where('verification_status', 'rejected')->count(),
            ];
        } else {
            $adminStats = [
                'pending'  => Prestasi::pending()->count(),
                'approved' => Prestasi::where('verification_status', 'approved')->count(),
                'rejected' => Prestasi::where('verification_status', 'rejected')->count(),
            ];
        }

        // Angkatan list for filter
        $angkatanList = Kemahasiswaan::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // Pengawas (GPM/Kadep) hanya melihat — sembunyikan tombol setujui/tolak.
        $canVerify = !$readOnly;
        // Tombol "Klaim Reward" tetap tampil untuk pengawas (akses halaman read-only).
        $canViewReward = $this->canAccessReward();

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
            'pendingPrestasiReward',
            'adminStats',
            'canVerify',
            'canViewReward',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Admin View — Halaman khusus daftar Klaim Reward Prestasi (Request Bu Bellia / B.2)
    // -------------------------------------------------------------------------

    public function rewardIndex(Request $request)
    {
        $reward   = $request->get('reward', 'menunggu');
        $search   = $request->get('search');
        $angkatan = $request->get('angkatan');

        $rewardStatusMap = [
            'menunggu'  => Prestasi::CLAIM_DIAJUKAN,
            'disetujui' => Prestasi::CLAIM_DISETUJUI,
            'ditolak'   => Prestasi::CLAIM_DITOLAK,
        ];
        $claimStatus = $rewardStatusMap[$reward] ?? Prestasi::CLAIM_DIAJUKAN;

        $rewardQuery = Prestasi::with(['kemahasiswaan.user', 'reviewedBy', 'buktiFiles'])
            ->where('claim_status', $claimStatus);

        if ($search) {
            $rewardQuery->whereHas('kemahasiswaan', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                          ->orWhere('nim', 'like', "%{$search}%");
                });
            });
        }

        if ($angkatan && $angkatan !== 'semua') {
            $rewardQuery->whereHas('kemahasiswaan', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        $rewardData = $rewardQuery->orderByDesc('claimed_at')->paginate(15);

        $pendingPrestasiReward = Prestasi::rewardDiajukan()->count();

        // Hitungan status klaim untuk kartu statistik (selaras dgn Verifikasi Prestasi)
        $rewardStats = [
            'menunggu'  => $pendingPrestasiReward,
            'disetujui' => Prestasi::rewardDisetujui()->count(),
            'ditolak'   => Prestasi::rewardDitolak()->count(),
        ];

        $kuotaMap = $this->rewardKuotaMap(
            $rewardData->pluck('kemahasiswaan_id')->filter()->unique()->all()
        );

        $angkatanList = Kemahasiswaan::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        $rewardAturan = RewardAturan::with('uploadedBy')->latest()->get();
        $canManageRewardAturan = $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan');
        // Pengawas mutu (GPM/Kadep) hanya melihat — sembunyikan tinjau/setujui/tolak/batalkan.
        $canReview = $this->isVerificator();

        return view('manajemenmahasiswa::verifikasi.reward', compact(
            'rewardData',
            'reward',
            'search',
            'angkatan',
            'angkatanList',
            'pendingPrestasiReward',
            'rewardStats',
            'kuotaMap',
            'rewardAturan',
            'canManageRewardAturan',
            'canReview',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Mahasiswa View — Data pengajuan milik sendiri
    // -------------------------------------------------------------------------

    private function mahasiswaIndex(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'prestasi');
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
            $prestasiData = Prestasi::with(['verifiedBy', 'reviewedBy', 'buktiFiles'])
                ->where('kemahasiswaan_id', $mhs->id)
                ->orderByDesc('created_at')
                ->get();

            $stats['pending']  += $prestasiData->where('verification_status', 'pending')->count();
            $stats['approved'] += $prestasiData->where('verification_status', 'approved')->count();
            $stats['rejected'] += $prestasiData->where('verification_status', 'rejected')->count();
        }

        $kuota = $mhs
            ? $this->rewardKuotaTerpakai($mhs->id)
            : [Prestasi::KUOTA_UMUM => 0, Prestasi::KUOTA_INVENTION => 0];

        $rewardAturan = RewardAturan::latest()->get();

        $isAlumni = $this->hasRole('alumni');

        return view('manajemenmahasiswa::verifikasi.mahasiswa', compact(
            'riwayatData',
            'prestasiData',
            'stats',
            'mhs',
            'student',
            'kuota',
            'tab',
            'rewardAturan',
            'isAlumni',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Store Riwayat — Mahasiswa/Pengurus/Alumni ajukan riwayat kegiatan
    // -------------------------------------------------------------------------

    public function storeRiwayat(Request $request)
    {
        if ($this->hasRole('alumni')) {
            return redirect()->back()->with('error', 'Role alumni tidak dapat mengajukan data baru.');
        }

        $request->validate([
            'nama_kegiatan_manual' => 'required|string|max:50',
            'peran_manual'         => 'required|string|max:50',
            'tanggal_kegiatan'     => 'required|date',
            'bukti_docs'           => 'required|array|size:1',
            'bukti_docs.*'         => 'file|mimes:pdf|max:10240',
        ], [], ['bukti_docs' => 'bukti kegiatan']);

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
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Store Prestasi — Mahasiswa/Pengurus/Alumni ajukan prestasi
    // -------------------------------------------------------------------------

    public function storePrestasi(Request $request)
    {
        if ($this->hasRole('alumni')) {
            return redirect()->back()->with('error', 'Role alumni tidak dapat mengajukan data baru.');
        }

        $request->validate([
            'nama_prestasi' => 'required|string|max:50',
            'tingkat'       => 'required|in:' . implode(',', Prestasi::TINGKAT_LIST),
            'tanggal'       => 'required|date',
            'bukti_docs'    => 'required|array|size:1',
            'bukti_docs.*'  => 'file|mimes:pdf|max:10240',
        ], [], ['bukti_docs' => 'bukti kegiatan']);

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
            'tahun'               => date('Y', strtotime($request->tanggal)),
            'verification_status' => 'pending',
            'claim_status'        => Prestasi::CLAIM_BELUM_AJUKAN,
        ]);

        // Upload bukti files ke Supabase
        $this->uploadBuktiFiles($request, 'prestasi', $prestasi->id);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Approve Riwayat
    // -------------------------------------------------------------------------

    public function approveRiwayat(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'nullable|string|max:200',
        ]);

        $riwayat = RiwayatKegiatan::with('student')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($riwayat->verification_status !== 'pending') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Riwayat kegiatan ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi pengajuannya sendiri
        if ($this->ownsRiwayat($riwayat)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Anda tidak dapat memverifikasi riwayat kegiatan yang Anda ajukan sendiri.');
        }

        $riwayat->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note ?: null,
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
            'verification_note' => 'required|string|max:200',
        ]);

        $riwayat = RiwayatKegiatan::with('student')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($riwayat->verification_status !== 'pending') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Riwayat kegiatan ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi pengajuannya sendiri
        if ($this->ownsRiwayat($riwayat)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Anda tidak dapat memverifikasi riwayat kegiatan yang Anda ajukan sendiri.');
        }

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

    public function approvePrestasi(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'nullable|string|max:200',
        ]);

        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($prestasi->verification_status !== Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi prestasinya sendiri
        if ($this->ownsPrestasi($prestasi)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Anda tidak dapat memverifikasi prestasi yang Anda ajukan sendiri.');
        }

        $prestasi->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note ?: null,
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
            'verification_note' => 'required|string|max:200',
        ]);

        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($prestasi->verification_status !== Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi prestasinya sendiri
        if ($this->ownsPrestasi($prestasi)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Anda tidak dapat memverifikasi prestasi yang Anda ajukan sendiri.');
        }

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
    // Ajukan Reward — Mahasiswa pemilik mengajukan reward prestasinya
    // (Request Bu Bellia / B.2 — dasar SK FT 774/2025)
    // -------------------------------------------------------------------------

    public function ajukanReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Hanya pemilik prestasi yang boleh mengajukan
        if (!$this->ownsPrestasi($prestasi)) {
            abort(403, 'Anda hanya dapat mengajukan reward untuk prestasi milik sendiri.');
        }

        // Guard 1: hanya prestasi yang sudah diverifikasi (approved)
        if ($prestasi->verification_status !== Prestasi::VERIF_APPROVED) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi harus diverifikasi (disetujui) dulu sebelum bisa diajukan reward.');
        }

        // Guard 2: belum ada pengajuan aktif (boleh ajukan ulang bila sebelumnya ditolak)
        if (!$prestasi->rewardBisaDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Reward prestasi ini sudah diajukan atau sudah disetujui.');
        }

        $validated = $request->validate([
            'reward_penyelenggara' => 'required|in:' . implode(',', Prestasi::PENYELENGGARA_LIST),
            'reward_capaian'       => 'required|string',
            'reward_is_invention'  => 'nullable|boolean',
        ]);

        $penyelenggara = $validated['reward_penyelenggara'];
        $capaian       = $validated['reward_capaian'];
        // is_invention hanya relevan untuk kategori "lainnya"
        $isInvention   = $penyelenggara === Prestasi::PENYELENGGARA_LAINNYA
            ? $request->boolean('reward_is_invention')
            : false;

        // Validasi capaian harus valid untuk penyelenggara yang dipilih
        if (!\in_array($capaian, Prestasi::CAPAIAN_BY_PENYELENGGARA[$penyelenggara], true)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Kombinasi penyelenggara dan capaian tidak valid.');
        }

        $jatah = Prestasi::hitungJatahReward($penyelenggara, $capaian, $isInvention);

        // Usulan MK: hanya MK kurikulum yang valid, jumlah maks sesuai jatah (SK 774)
        $mkFlat     = Prestasi::mataKuliahFlat();
        $mkValid    = array_keys($mkFlat);
        $mkInput    = (array) $request->input('reward_mk_diajukan', []);
        $mkDiajukan = array_values(array_unique(array_filter(
            $mkInput,
            fn ($mk) => \in_array($mk, $mkValid, true)
        )));

        if (empty($mkDiajukan)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Pilih minimal satu mata kuliah yang ingin dinaikkan nilainya sebelum mengajukan reward.');
        }

        if (count($mkDiajukan) > $jatah['jml_mk_max']) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', "Maksimal {$jatah['jml_mk_max']} mata kuliah untuk capaian ini.");
        }

        // Total SKS usulan tidak boleh melebihi plafon SKS jatah (SK 774)
        $totalSks = array_sum(array_map(fn ($mk) => $mkFlat[$mk] ?? 0, $mkDiajukan));
        if ($totalSks > $jatah['sks_max']) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', "Total SKS mata kuliah yang dipilih ({$totalSks} SKS) melebihi batas {$jatah['sks_max']} SKS untuk capaian ini.");
        }

        $prestasi->update([
            'reward_penyelenggara' => $penyelenggara,
            'reward_capaian'       => $capaian,
            'reward_is_invention'  => $isInvention,
            'reward_jml_mk_max'    => $jatah['jml_mk_max'],
            'reward_sks_max'       => $jatah['sks_max'],
            'reward_mk_diajukan'   => $mkDiajukan,
            'claim_status'         => Prestasi::CLAIM_DIAJUKAN,
            'claimed_by'           => Auth::id(),
            'claimed_at'           => now(),
            // bersihkan jejak review lama jika ini pengajuan ulang setelah ditolak
            'reward_mk_disetujui'  => null,
            'reward_reviewed_by'   => null,
            'reward_reviewed_at'   => null,
            'reward_note'          => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Pengajuan reward berhasil dikirim. Menunggu persetujuan departemen.');
    }

    // -------------------------------------------------------------------------
    // Batalkan Reward — Mahasiswa pemilik membatalkan pengajuan (selama diajukan)
    // -------------------------------------------------------------------------

    public function batalkanReward(int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$this->ownsPrestasi($prestasi)) {
            abort(403, 'Anda hanya dapat membatalkan pengajuan reward milik sendiri.');
        }

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Hanya pengajuan yang masih menunggu persetujuan yang dapat dibatalkan.');
        }

        $prestasi->update([
            'claim_status'         => Prestasi::CLAIM_BELUM_AJUKAN,
            'claimed_by'           => null,
            'claimed_at'           => null,
            'reward_penyelenggara' => null,
            'reward_capaian'       => null,
            'reward_is_invention'  => false,
            'reward_jml_mk_max'    => null,
            'reward_sks_max'       => null,
            'reward_mk_diajukan'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Pengajuan reward berhasil dibatalkan.');
    }

    // -------------------------------------------------------------------------
    // Setujui Reward — Admin/Departemen menyetujui & menetapkan MK
    // -------------------------------------------------------------------------

    public function setujuiReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Hanya pengajuan yang menunggu persetujuan yang dapat disetujui.');
        }

        $request->validate([
            'reward_note' => 'nullable|string|max:300',
        ]);

        // MK final = usulan mahasiswa (admin hanya melihat, tidak mengubah)
        $mkFinal = implode(', ', $prestasi->reward_mk_diajukan ?? []);
        if ($mkFinal === '') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Pengajuan ini belum memuat usulan mata kuliah dari mahasiswa, tidak dapat disetujui.');
        }

        // Guard kuota keras (SK 774 poin 4 = umum maks 2x, poin 5 = invention maks 1x)
        $grup     = $prestasi->rewardKuotaGrup();
        $maks     = Prestasi::KUOTA_MAKS[$grup];
        $terpakai = $this->rewardKuotaTerpakai($prestasi->kemahasiswaan_id);

        if (($terpakai[$grup] ?? 0) >= $maks) {
            $labelGrup = $grup === Prestasi::KUOTA_INVENTION ? 'invention/expo/fair' : 'umum';
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', "Kuota reward {$labelGrup} mahasiswa ini sudah penuh (maks {$maks}×). Pengajuan tidak dapat disetujui.");
        }

        $prestasi->update([
            'claim_status'        => Prestasi::CLAIM_DISETUJUI,
            'reward_mk_disetujui' => $mkFinal,
            'reward_note'         => $request->reward_note ?: null,
            'reward_reviewed_by'  => Auth::id(),
            'reward_reviewed_at'  => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Pengajuan reward disetujui.');
    }

    // -------------------------------------------------------------------------
    // Tolak Reward — Admin/Departemen menolak pengajuan dengan alasan
    // -------------------------------------------------------------------------

    public function tolakReward(Request $request, int $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Hanya pengajuan yang menunggu persetujuan yang dapat ditolak.');
        }

        $request->validate([
            'reward_note' => 'required|string|max:300',
        ]);

        $prestasi->update([
            'claim_status'       => Prestasi::CLAIM_DITOLAK,
            'reward_note'        => $request->reward_note,
            'reward_reviewed_by' => Auth::id(),
            'reward_reviewed_at' => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Pengajuan reward ditolak.');
    }

    // -------------------------------------------------------------------------
    // Batalkan Persetujuan Reward — admin membatalkan reward yg sudah disetujui
    // (mis. Fakultas menolak menaikkan nilai). Jadi DITOLAK + alasan, kuota balik.
    // -------------------------------------------------------------------------

    public function batalkanPersetujuanReward(Request $request, int $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        if (!$prestasi->isRewardDisetujui()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index', ['reward' => 'disetujui'])
                ->with('error', 'Hanya reward yang sudah disetujui yang dapat dibatalkan.');
        }

        $request->validate([
            'reward_note' => 'required|string|max:300',
        ]);

        $prestasi->update([
            'claim_status'       => Prestasi::CLAIM_DITOLAK,
            'reward_note'        => $request->reward_note,
            'reward_reviewed_by' => Auth::id(),
            'reward_reviewed_at' => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index', ['reward' => 'ditolak'])
            ->with('success', 'Persetujuan reward dibatalkan. Status diubah menjadi ditolak & kuota mahasiswa dikembalikan.');
    }

    // -------------------------------------------------------------------------
    // Helper — Cek apakah prestasi milik user yang sedang login
    // -------------------------------------------------------------------------

    private function ownsPrestasi(Prestasi $prestasi): bool
    {
        return $prestasi->kemahasiswaan
            && $prestasi->kemahasiswaan->user_id === Auth::id();
    }

    // -------------------------------------------------------------------------
    // Helper — Cek apakah riwayat kegiatan milik user yang sedang login
    // -------------------------------------------------------------------------

    private function ownsRiwayat(RiwayatKegiatan $riwayat): bool
    {
        return $riwayat->student
            && $riwayat->student->user_id === Auth::id();
    }

    // -------------------------------------------------------------------------
    // Helper — Hitung kuota reward terpakai (disetujui) per mahasiswa
    // -------------------------------------------------------------------------

    private function rewardKuotaTerpakai(int $kemahasiswaanId): array
    {
        $terpakai = [Prestasi::KUOTA_UMUM => 0, Prestasi::KUOTA_INVENTION => 0];

        $rows = Prestasi::rewardDisetujui()
            ->where('kemahasiswaan_id', $kemahasiswaanId)
            ->get(['reward_penyelenggara', 'reward_is_invention']);

        foreach ($rows as $r) {
            $grup = Prestasi::tentukanKuotaGrup($r->reward_penyelenggara, (bool) $r->reward_is_invention);
            $terpakai[$grup]++;
        }

        return $terpakai;
    }

    // -------------------------------------------------------------------------
    // Helper — Peta kuota terpakai untuk banyak mahasiswa sekaligus (admin view)
    // -------------------------------------------------------------------------

    private function rewardKuotaMap(array $kemahasiswaanIds): array
    {
        if (empty($kemahasiswaanIds)) {
            return [];
        }

        $rows = Prestasi::rewardDisetujui()
            ->whereIn('kemahasiswaan_id', $kemahasiswaanIds)
            ->get(['kemahasiswaan_id', 'reward_penyelenggara', 'reward_is_invention']);

        $map = [];
        foreach ($rows as $r) {
            $grup = Prestasi::tentukanKuotaGrup($r->reward_penyelenggara, (bool) $r->reward_is_invention);
            $map[$r->kemahasiswaan_id][$grup] = ($map[$r->kemahasiswaan_id][$grup] ?? 0) + 1;
        }

        return $map;
    }

    // -------------------------------------------------------------------------
    // Dokumen Aturan Reward (SK FT 774) — admin upload/hapus, dirujuk 2 role
    // -------------------------------------------------------------------------

    public function aturanStore(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ], [], ['file' => 'dokumen']);

        $file     = $request->file('file');
        $supabase = app(SupabaseStorage::class);
        $path     = $supabase->upload($file, 'mk_verifikasi/aturan');

        if (!$path) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Gagal mengunggah dokumen. Coba lagi.');
        }

        $isImage = str_starts_with((string) $file->getMimeType(), 'image/');

        RewardAturan::create([
            'judul'       => $request->judul,
            'nama_file'   => $file->getClientOriginalName(),
            'path_file'   => $path,
            'tipe_file'   => $isImage ? RewardAturan::TIPE_IMAGE : RewardAturan::TIPE_DOCUMENT,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Dokumen aturan reward berhasil diunggah.');
    }

    public function aturanDestroy(int $id)
    {
        $aturan = RewardAturan::findOrFail($id);

        app(SupabaseStorage::class)->delete($aturan->path_file);
        $aturan->delete();

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Dokumen aturan reward dihapus.');
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
