<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\NotifikasiService;

/**
 * Admin: Lihat & final-approve pendaftaran koor.
 * Flow: Mahasiswa → Dosen review (status_dosen) → Admin final approve (status).
 * Admin hanya bisa approve jika dosen sudah approve (status_dosen = disetujui).
 */
class PendaftaranKoorController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected KoorPraktikumService $koorService
    ) {}

    public function index(Request $request)
    {
        $query = PendaftaranKoordinator::with(['user', 'praktikum', 'direviewOleh'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($statusDosen = $request->input('status_dosen')) {
            $query->where('status_dosen', $statusDosen);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }
        if ($praktikumId = $request->input('praktikum_id')) {
            $query->where('praktikum_id', $praktikumId);
        }

        $pendaftaran = $query->paginate(15)->withQueryString();
        
        $praktikumList = \Modules\EOffice\Models\Praktikum::with('matkul')
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-koor', compact('pendaftaran', 'praktikumList'));
    }

    /**
     * Admin final-approve: assign role koor_prak otomatis.
     * Hanya bisa jika dosen sudah setujui.
     */
    public function approve(Request $request, int $id)
    {
        $pendaftaran = PendaftaranKoordinator::with(['user', 'praktikum'])->findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }
        if ($pendaftaran->status_dosen !== 'disetujui') {
            return back()->with('error', 'Pendaftaran ini belum disetujui oleh dosen pengampu. Tunggu review dosen terlebih dulu.');
        }

        $pendaftaran->update(['status' => 'approved']);

        $user = $pendaftaran->user;
        if ($user && $pendaftaran->praktikum) {
            $this->koorService->assign($pendaftaran->praktikum, $user);
        }

        // Notifikasi ke user
        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Koordinator Diterima',
            "Selamat! Anda diterima sebagai Koordinator Praktikum {$pendaftaran->praktikum?->nama}."
        );

        return back()->with('success', "Pendaftaran koordinator {$user?->name} berhasil diterima. Role koor_prak dan asprak telah di-assign.");
    }

    public function reject(Request $request, int $id)
    {
        $pendaftaran = PendaftaranKoordinator::with('user')->findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status'           => 'rejected',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
        ]);

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Koordinator Ditolak',
            "Pendaftaran koordinator Anda untuk praktikum {$pendaftaran->praktikum?->nama} tidak diterima."
                . ($request->input('alasan_penolakan') ? " Alasan: {$request->input('alasan_penolakan')}" : '')
        );

        return back()->with('success', "Pendaftaran koordinator {$pendaftaran->user?->name} ditolak.");
    }
}
