<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;

/**
 * Dosen: Review pendaftaran koordinator.
 * Flow: Mahasiswa submit → Dosen setuju/tolak (status_dosen) → Admin final approve.
 * Dosen cek data mahasiswa (IPK, motivasi, transkrip) lalu approve/disapprove.
 * Setelah dosen approve, masuk ke antrian admin untuk final approval.
 */
class PendaftaranKoorController extends Controller
{
    public function __construct(protected NotifikasiService $notif)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Praktikum yang diampu dosen ini (ambil sebagai array string UUID)
        $praktikumIds = Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $query = PendaftaranKoordinator::with(['user', 'praktikum'])
            ->whereIn('praktikum_id', $praktikumIds);

        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'ipk_tertinggi') {
            $query->orderByDesc('ipk');
        } else {
            $query->orderByDesc('created_at');
        }

        if ($statusDosen = $request->input('status_dosen')) {
            $query->where('status_dosen', $statusDosen);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }
        if ($praktikumId = $request->input('praktikum_id')) {
            $query->where('praktikum_id', (string) $praktikumId);
        }

        $pendaftaran = $query->paginate(15)->withQueryString();
        $praktikumList = Praktikum::whereIn('id', $praktikumIds)->orderBy('nama')->get();

        return view('eoffice::manajemen-praktikum.dosen.pendaftaran-koor', compact(
            'pendaftaran',
            'praktikumList'
        ));
    }

    /**
     * Dosen approve → status_dosen = disetujui → masuk antrian admin.
     */
    public function approve(Request $request, int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranKoordinator::with(['user', 'praktikum'])->findOrFail($id);

        if (!$pendaftaran->praktikum?->dosens->contains('id', $user->id)) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status !== 'pending' || $pendaftaran->status_dosen !== 'menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah pernah diproses oleh dosen.');
        }

        $updated = PendaftaranKoordinator::whereKey($id)->where('status', 'pending')->where('status_dosen', 'menunggu')->update([
            'status_dosen' => 'disetujui',
            'catatan_dosen' => $request->input('catatan_dosen'),
            'direview_oleh' => $user->id,
            'direview_pada' => now(),
            // status tetap 'pending' — menunggu admin final approve
        ]);

        if (!$updated) {
            return back()->with('error', 'Pendaftaran ini sudah diproses. Muat ulang halaman.');
        }

        // Notifikasi ke admin (superadmin & admin_eoffice)
        $adminIds = \App\Models\User::whereHas(
            'roles',
            fn($q) =>
            $q->whereIn('name', ['superadmin', 'admin_eoffice'])
        )->pluck('id');

        $this->notif->kirimBulk(
            $adminIds,
            'Pendaftaran Koor Siap Diproses',
            "Dosen {$user->name} telah menyetujui pendaftaran koordinator {$pendaftaran->user?->name} untuk praktikum {$pendaftaran->praktikum?->nama}. Silakan lakukan final approval."
        );

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Koor Disetujui Dosen',
            "Dosen {$user->name} telah menyetujui pendaftaran koordinator Anda untuk {$pendaftaran->praktikum?->nama}. Menunggu persetujuan akhir dari Admin."
        );

        // Otomatis tolak pendaftar lain untuk praktikum ini
        $pendaftarLain = PendaftaranKoordinator::where('praktikum_id', $pendaftaran->praktikum_id)
            ->where('id', '!=', $pendaftaran->id)
            ->where('status_dosen', 'menunggu')
            ->where('status', 'pending')
            ->get();

        foreach ($pendaftarLain as $lain) {
            // Conditional write protects a concurrent final approval by admin.
            $rejected = PendaftaranKoordinator::whereKey($lain->id)
                ->where('status', 'pending')->where('status_dosen', 'menunggu')->update([
                    'status_dosen' => 'ditolak',
                    'status' => 'rejected',
                    'alasan_penolakan' => 'Sudah ada kandidat lain yang disetujui sebagai Koordinator untuk praktikum ini.',
                    'direview_oleh' => $user->id,
                    'direview_pada' => now(),
                ]);
            if ($rejected) {
                $this->notif->kirim($lain->user_id, 'Pendaftaran Koor Ditolak',
                    "Maaf, pendaftaran koordinator Anda untuk {$pendaftaran->praktikum?->nama} tidak disetujui.");
            }
        }

        return back()->with('success', "Pendaftaran {$pendaftaran->user?->name} disetujui. Admin akan melakukan final approval.");
    }

    /**
     * Dosen disapprove → status_dosen = ditolak.
     */
    public function reject(Request $request, int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranKoordinator::with(['user', 'praktikum'])->findOrFail($id);

        if (!$pendaftaran->praktikum?->dosens->contains('id', $user->id)) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status !== 'pending' || $pendaftaran->status_dosen !== 'menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah pernah diproses oleh dosen.');
        }

        $updated = PendaftaranKoordinator::whereKey($id)->where('status', 'pending')->where('status_dosen', 'menunggu')->update([
            'status_dosen' => 'ditolak',
            'catatan_dosen' => $request->input('catatan_dosen'),
            'alasan_penolakan' => $request->input('alasan_penolakan'),
            'status' => 'rejected', // langsung ditolak, tidak perlu ke admin
            'direview_oleh' => $user->id,
            'direview_pada' => now(),
        ]);

        if (!$updated) {
            return back()->with('error', 'Pendaftaran ini sudah diproses. Muat ulang halaman.');
        }

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Koor Ditolak',
            "Maaf, pendaftaran koordinator Anda untuk {$pendaftaran->praktikum?->nama} tidak disetujui oleh dosen."
            . ($request->input('alasan_penolakan') ? " Alasan: {$request->input('alasan_penolakan')}" : '')
        );

        return back()->with('success', "Pendaftaran {$pendaftaran->user?->name} ditolak.");
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranKoordinator::with('praktikum')->findOrFail($id);

        if (!$pendaftaran->praktikum?->dosens->contains('id', $user->id)) {
            return back()->with('error', 'Anda tidak berhak menghapus pendaftaran ini.');
        }

        if ($pendaftaran->status === 'approved' || !PendaftaranKoordinator::whereKey($id)->where('status', '!=', 'approved')->delete()) {
            return back()->with('error', 'Pendaftaran yang sudah disetujui admin tidak dapat dihapus.');
        }
        return back()->with('success', 'Pendaftaran berhasil dihapus.');
    }
}
