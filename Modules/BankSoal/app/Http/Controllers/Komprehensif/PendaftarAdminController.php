<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PendaftarUjian;
use Modules\BankSoal\Models\PeriodeUjian;
use App\Models\User;

class PendaftarAdminController extends Controller
{
    /**
     * Tampilkan daftar pendaftar ujian dengan filter periode, status, dan pencarian.
     */
    public function index(Request $request)
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();

        $pendaftars = collect();
        $selectedPeriode = null;
        $totalCount = 0;

        $periodeId = $request->query('periode_id');

        if ($periodeId) {
            $selectedPeriode = PeriodeUjian::find($periodeId);

            $query = PendaftarUjian::with(['mahasiswa'])
                ->where('periode_ujian_id', $periodeId);

            // Filter status
            if ($request->filled('status')) {
                $query->where('status_pendaftaran', $request->status);
            }

            // Search NIM atau Nama
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                    $q->where('nim', 'like', "%{$search}%")
                        ->orWhere('nama_lengkap', 'like', "%{$search}%");
                });
            }

            $totalCount = $query->count();
            $perPage = $request->get('per_page', 5);
            $pendaftars = $query->with(['mahasiswa', 'dosenPembimbing1', 'dosenPembimbing2', 'ditambahkanOleh'])->latest()->paginate($perPage)->appends($request->query());
        }

        // Ambil semua dosen untuk dropdown
        $dosenList = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('banksoal::pendaftaran.index', compact(
            'periodes',
            'pendaftars',
            'selectedPeriode',
            'totalCount',
            'dosenList',
        ));
    }

    /**
     * Simpan pendaftar baru secara manual oleh admin.
     * Status langsung 'approved'. Kolom ditambahkan_oleh diisi ID admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode_ujian_id' => 'required|exists:bs_periode_ujians,id',
            'nim' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'semester_aktif' => 'required|integer|min:1|max:20',
            'target_wisuda' => 'nullable|string|max:100',
            'dosen_pembimbing_1_id' => 'nullable|exists:users,id',
            'dosen_pembimbing_2_id' => 'nullable|exists:users,id',
        ]);

        // Cek duplikat di periode yang sama (termasuk yang sudah di-soft-delete / ditolak)
        // Mahasiswa yang pernah ditolak TIDAK boleh mendaftar ulang di periode yang sama
        $exists = PendaftarUjian::withTrashed()
            ->where('periode_ujian_id', $request->periode_ujian_id)
            ->where('nim', $request->nim)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM ini sudah pernah terdaftar pada periode ujian tersebut.'])->withInput();
        }

        // Cari ID Mahasiswa dari tabel students (kolom student_number) beserta relasi usernya
        $student = \App\Models\Student::with('user')->where('student_number', $request->nim)->first();

        if (!$student || !$student->user) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM tersebut belum terdaftar di sistem.'])->withInput();
        }

        $mahasiswa = $student->user;

        PendaftarUjian::create([
            'periode_ujian_id' => $request->periode_ujian_id,
            'mahasiswa_id' => $mahasiswa->id,
            'nim' => $request->nim,
            'nama_lengkap' => $request->nama_lengkap,
            'semester_aktif' => $request->semester_aktif,
            'target_wisuda' => $request->target_wisuda,
            'dosen_pembimbing_1_id' => $request->dosen_pembimbing_1_id ?: null,
            'dosen_pembimbing_2_id' => $request->dosen_pembimbing_2_id ?: null,
            'status_pendaftaran' => 'approved',
            'catatan_admin' => $request->catatan_admin,
            'ditambahkan_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Update status pendaftaran — hanya untuk approve.
     * Reject tidak menggunakan fungsi ini lagi (langsung destroy).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pendaftaran' => 'required|in:approved',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pendaftar = PendaftarUjian::findOrFail($id);

        $pendaftar->update([
            'status_pendaftaran' => 'approved',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Pendaftaran {$pendaftar->nama_lengkap} berhasil disetujui.");
    }

    /**
     * Tolak & Hapus pendaftar (gabungan reject + soft-delete).
     * 
     * Guard:
     * - Tidak bisa hapus jika status sudah 'approved'
     * - Cabut alokasi sesi sebelum soft-delete
     */
    public function destroy($id)
    {
        $pendaftar = PendaftarUjian::findOrFail($id);

        // Guard: Pendaftar yang sudah disetujui tidak boleh dihapus
        if ($pendaftar->status_pendaftaran === 'approved') {
            return back()->with('error', 'Pendaftar yang sudah disetujui tidak dapat ditolak atau dihapus.');
        }

        // Cabut alokasi sesi sebelum dihapus agar kuota kembali
        $pendaftar->update([
            'jadwal_ujian_id' => null,
            'status_pendaftaran' => 'rejected',
        ]);

        $pendaftar->delete();

        return back()->with('success', "Pendaftar {$pendaftar->nama_lengkap} berhasil ditolak dan dihapus.");
    }

    /**
     * AJAX: Lookup mahasiswa berdasarkan NIM (username).
     * Mengembalikan JSON untuk auto-fill nama di modal tambah manual.
     */
    public function lookupNIM(Request $request)
    {
        $nim = $request->query('nim');

        if (!$nim) {
            return response()->json(['found' => false, 'message' => 'NIM tidak boleh kosong.']);
        }

        // Cari dari tabel students beserta data user-nya
        $student = \App\Models\Student::with('user')->where('student_number', $nim)->first();

        if (!$student || !$student->user) {
            return response()->json(['found' => false, 'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan di sistem.']);
        }

        // Kalkulasi semester berdasarkan cohort_year
        // Jika bulan >= 8 (Agustus), maka masuk semester ganjil tahun ajaran baru (+1)
        // Jika bulan < 8, maka masuk semester genap (+0)
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        $cohortYear = (int) $student->cohort_year;
        $semester = 7; // Default minimal

        if ($cohortYear > 2000) {
            $semester = (($currentYear - $cohortYear) * 2) + ($currentMonth >= 8 ? 1 : 0);
            $semester = max(1, $semester); // Pastikan tidak negatif atau 0
        }

        return response()->json([
            'found' => true,
            'nama' => $student->user->name,
            'nim' => $student->student_number,
            'semester' => $semester,
        ]);
    }
}
