<?php

namespace Modules\BankSoal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PendaftarUjian;
use Modules\BankSoal\Models\PeriodeUjian;

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
            $pendaftars = $query->latest()->paginate(15)->appends($request->query());
        }

        return view('banksoal::pendaftaran.index', compact(
            'periodes',
            'pendaftars',
            'selectedPeriode',
            'totalCount',
        ));
    }

    /**
     * Simpan pendaftar baru secara manual oleh admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode_ujian_id' => 'required|exists:bs_periode_ujians,id',
            'nim'              => 'required|string|max:50',
            'nama_lengkap'     => 'required|string|max:255',
            'semester_aktif'   => 'required|integer|min:1|max:20',
            'target_wisuda'    => 'nullable|string|max:100',
        ]);

        // Cek duplikat di periode yang sama
        $exists = PendaftarUjian::where('periode_ujian_id', $request->periode_ujian_id)
            ->where('nim', $request->nim)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', "NIM {$request->nim} sudah terdaftar pada periode ini.");
        }

        PendaftarUjian::create([
            'periode_ujian_id'    => $request->periode_ujian_id,
            'mahasiswa_id'        => auth()->id(), // placeholder; admin yg input
            'nim'                 => $request->nim,
            'nama_lengkap'        => $request->nama_lengkap,
            'semester_aktif'      => $request->semester_aktif,
            'target_wisuda'       => $request->target_wisuda,
            'dosen_pembimbing_1_id' => null,
            'dosen_pembimbing_2_id' => null,
            'status_pendaftaran'  => 'approved', // tambah manual langsung approved
            'catatan_admin'       => $request->catatan_admin,
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan secara manual.');
    }

    /**
     * Update status pendaftaran (approve / reject).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pendaftaran' => 'required|in:pending,approved,rejected',
            'catatan_admin'      => 'nullable|string|max:500',
        ]);

        $pendaftar = PendaftarUjian::findOrFail($id);
        $pendaftar->update([
            'status_pendaftaran' => $request->status_pendaftaran,
            'catatan_admin'      => $request->catatan_admin,
        ]);

        $label = match ($request->status_pendaftaran) {
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            default    => 'diperbarui',
        };

        return back()->with('success', "Status pendaftar {$pendaftar->nama_lengkap} berhasil {$label}.");
    }

    /**
     * Soft-delete pendaftar.
     */
    public function destroy($id)
    {
        $pendaftar = PendaftarUjian::findOrFail($id);
        $pendaftar->delete();

        return back()->with('success', "Pendaftar {$pendaftar->nama_lengkap} berhasil dihapus.");
    }
}
