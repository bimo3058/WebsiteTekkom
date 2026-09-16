<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;

/**
 * Koordinator: Seleksi pendaftar asisten praktikum.
 * Flow: Mahasiswa submit → Koor review → approve → auto assign role asprak.
 * Koor hanya melihat pendaftar sesuai matkul praktikum yang diampunya.
 */
class PendaftaranAsprakController extends Controller
{
    public function __construct(protected NotifikasiService $notif)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $praktikum = DashboardController::resolvePraktikum();

        $query = PendaftaranAsprak::with(['user.student', 'praktikum'])
            ->where('praktikum_id', $praktikum?->id);

        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'ipk_tertinggi') {
            $query->orderByDesc('ipk');
        } else {
            $query->orderByDesc('created_at');
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $perPage = request('per_page', 10);
        $pendaftaran = $query->paginate($perPage)->withQueryString();

        $periodeList = $praktikum
            ? PeriodePendaftaran::where('praktikum_id', $praktikum->id)
                ->where('jenis', 'asprak')
                ->with(['dibukaOleh'])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $periodeAktif = $periodeList->firstWhere('is_aktif', true);

        return view('eoffice::manajemen-praktikum.koordinator.pendaftaran-asprak', compact(
            'praktikum',
            'pendaftaran',
            'periodeAktif',
            'periodeList'
        ));
    }

    /**
     * Koor approve → auto assign role asprak + tambah ke asprak_praktikum.
     */
    public function approve(Request $request, int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranAsprak::with(['user', 'praktikum'])->findOrFail($id);

        if ($pendaftaran->praktikum?->koor_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status_koor !== 'menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status_koor' => 'disetujui',
            'catatan_koor' => $request->input('catatan_koor'),
            'direview_oleh' => $user->id,
            'direview_pada' => now(),
            // status tetap 'pending' menunggu admin final approve
        ]);

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Asprak Disetujui Koordinator',
            "Selamat! Pendaftaran Anda sebagai Asisten Praktikum {$pendaftaran->praktikum?->nama} telah disetujui Koordinator. Menunggu persetujuan akhir dari Admin."
        );

        return back()->with('success', "Pendaftaran {$pendaftaran->user?->name} disetujui. Admin akan melakukan final approval.");
    }

    /**
     * Koor reject pendaftaran asprak.
     */
    public function reject(Request $request, int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranAsprak::with(['user', 'praktikum'])->findOrFail($id);

        if ($pendaftaran->praktikum?->koor_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status_koor !== 'menunggu') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status_koor' => 'ditolak',
            'status' => 'rejected',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
            'catatan_koor' => $request->input('catatan_koor'),
            'direview_oleh' => $user->id,
            'direview_pada' => now(),
        ]);

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Asprak Tidak Diterima',
            "Maaf, pendaftaran asprak Anda untuk {$pendaftaran->praktikum?->nama} tidak diterima."
            . ($request->input('alasan_penolakan') ? " Alasan: {$request->input('alasan_penolakan')}" : '')
        );

        return back()->with('success', "Pendaftaran {$pendaftaran->user?->name} ditolak.");
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        $pendaftaran = PendaftaranAsprak::with('praktikum')->findOrFail($id);

        if ($pendaftaran->praktikum?->koor_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak menghapus pendaftaran ini.');
        }

        $pendaftaran->delete();
        return back()->with('success', 'Pendaftaran dihapus.');
    }
}
