<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Http\Requests\Komprehensif\BulkPendaftarRequest;
use Modules\BankSoal\Http\Requests\Komprehensif\StoreAdminPendaftarRequest;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Support\SemesterCalculator;

class PendaftarAdminController extends Controller
{
    /**
     * Tampilkan daftar pendaftar ujian dengan filter periode, status, dan pencarian.
     */
    public function index(Request $request)
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();

        // Default ke periode aktif jika tidak ada filter di URL
        if (!$request->filled('periode_id')) {
            $activePeriodeId = $periodes->firstWhere('status', 'aktif')?->id ?? $periodes->first()?->id;
            if ($activePeriodeId) {
                return redirect()->route('banksoal.pendaftaran.index', ['periode_id' => $activePeriodeId]);
            }
        }

        $pendaftars = collect();
        $selectedPeriode = null;
        $totalCount = 0;

        $periodeId = $request->query('periode_id');

        if ($periodeId) {
            $selectedPeriode = PeriodeUjian::find($periodeId);

            $query = PendaftarUjian::query()
                ->where('periode_ujian_id', $periodeId);

            // Filter status
            if ($request->filled('status')) {
                $query->where('status_pendaftaran', $request->status);
            }

            // Search NIM atau Nama
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function (Builder $q) use ($search) {
                    $q->where('nim', 'like', "%{$search}%")
                        ->orWhere('nama_lengkap', 'like', "%{$search}%");
                });
            }

            $totalCount = $query->count();
            $perPage = $request->get('per_page', 5);
            $pendaftars = $query
                ->with(['mahasiswa', 'dosenPembimbing1', 'dosenPembimbing2', 'ditambahkanOleh'])
                ->withCount('sesiSelesai')
                ->latest()
                ->paginate($perPage)
                ->appends($request->query());
        }

        // Ambil semua dosen untuk dropdown
        $dosenList = User::role('dosen')
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
    public function store(StoreAdminPendaftarRequest $request)
    {
        // Cek duplikat di periode yang sama — hanya record aktif (pending/approved)
        // Mahasiswa yang sudah ditolak (soft-deleted) boleh didaftarkan ulang
        $exists = PendaftarUjian::where('periode_ujian_id', $request->periode_ujian_id)
            ->where('nim', $request->nim)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM ini sudah terdaftar pada periode ujian tersebut.'], 'pendaftar')->withInput();
        }

        // Cari ID Mahasiswa dari tabel students (kolom student_number) beserta relasi usernya
        $student = Student::with('user')->where('student_number', $request->nim)->first();

        if (!$student || !$student->user) {
            return back()->withErrors(['nim' => 'Mahasiswa dengan NIM tersebut belum terdaftar di sistem.'], 'pendaftar')->withInput();
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
            'status_pendaftaran' => PendaftaranStatus::Approved->value,
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
            'status_pendaftaran' => PendaftaranStatus::Approved->value,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Pendaftaran {$pendaftar->nama_lengkap} berhasil disetujui.");
    }

    /**
     * Setujui banyak pendaftar sekaligus (Bulk Approve).
     * Hanya memproses yang masih berstatus pending — yang sudah approved diabaikan.
     */
    public function bulkApprove(BulkPendaftarRequest $request)
    {
        $updated = PendaftarUjian::whereIn('id', $request->ids)
            ->where('status_pendaftaran', '!=', PendaftaranStatus::Approved->value)
            ->update(['status_pendaftaran' => PendaftaranStatus::Approved->value]);

        return back()->with('success', "{$updated} pendaftar berhasil disetujui.");
    }

    /**
     * Tolak & hapus banyak pendaftar sekaligus (Bulk Reject).
     * Hanya memproses yang masih berstatus pending — yang sudah approved dilindungi.
     */
    public function bulkReject(BulkPendaftarRequest $request)
    {
        $pendaftars = PendaftarUjian::whereIn('id', $request->ids)
            ->where('status_pendaftaran', '!=', PendaftaranStatus::Approved->value)
            ->get();

        $count = $pendaftars->count();

        foreach ($pendaftars as $pendaftar) {
            $pendaftar->update([
                'jadwal_ujian_id' => null,
                'status_pendaftaran' => PendaftaranStatus::Rejected->value,
            ]);
            $pendaftar->delete(); // soft-delete
        }

        return back()->with('success', "{$count} pendaftar berhasil ditolak dan dihapus.");
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
        if ($pendaftar->status_pendaftaran === PendaftaranStatus::Approved) {
            return back()->with('error', 'Pendaftar yang sudah disetujui tidak dapat ditolak atau dihapus.');
        }

        // Cabut alokasi sesi sebelum dihapus agar kuota kembali
        $pendaftar->update([
            'jadwal_ujian_id' => null,
            'status_pendaftaran' => PendaftaranStatus::Rejected->value,
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
        $student = Student::with('user')->where('student_number', $nim)->first();

        if (!$student || !$student->user) {
            return response()->json(['found' => false, 'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan di sistem.']);
        }

        // Hitung semester aktif menggunakan kalkulator terpusat
        $semester = SemesterCalculator::fromCohortYear((int) $student->cohort_year, 7);

        return response()->json([
            'found'    => true,
            'nama'     => $student->user->name,
            'nim'      => $student->student_number,
            'semester' => $semester,
        ]);
    }
}
