<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PendaftaranPraktikan;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\NotifikasiService;

/**
 * Koordinator: verifikasi pendaftaran praktikan (Cetak IRS).
 *
 * BUG FIX:
 *  - index() sebelumnya pakai ->first() untuk resolve praktikum, sehingga
 *    kalau koor punya >1 praktikum aktif, data bisa tercampur antara praktikum.
 *    Sekarang pakai DashboardController::resolvePraktikum() supaya ikut session
 *    yang sama dengan halaman lain (switch praktikum dari dashboard).
 *
 *  - approve() sebelumnya assign role 'praktikan' secara global ke user,
 *    sehingca mahasiswa yang disetujui di SRWE otomatis punya role praktikan
 *    dan bisa terlihat sebagai "sudah disetujui" di SBD padahal belum pernah
 *    mendaftar. Role assignment dipertahankan (diperlukan untuk akses menu),
 *    tapi sekarang kita TIDAK lagi bergantung pada role untuk menentukan
 *    status per-praktikum — status di tabel pendaftaran_praktikan sudah cukup
 *    dan di-scope per praktikum_id.
 */
class PendaftaranPraktikanController extends Controller
{
    public function __construct(protected NotifikasiService $notif) {}

    /**
     * Resolve praktikum aktif untuk koor ini, mengikuti session
     * (sama persis dengan DashboardController::resolvePraktikum).
     */
    private function resolvePraktikum(): ?Praktikum
    {
        return DashboardController::resolvePraktikum();
    }

    public function index(Request $request)
    {
        $user      = auth()->user();

        // FIXED: pakai resolvePraktikum() supaya ikut session switch praktikum,
        // bukan ambil ->first() yang bisa return praktikum yang salah.
        $praktikum = $this->resolvePraktikum();

        // Guard: koor hanya boleh melihat praktikum miliknya
        if ($praktikum && $praktikum->koor_id !== $user->id) {
            $praktikum = null;
        }

        $query = PendaftaranPraktikan::with(['user', 'praktikum'])
            ->where('praktikum_id', $praktikum?->id)
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $pendaftaran = $query->paginate(15)->withQueryString();

        $periodeAktif = PeriodePendaftaran::where('praktikum_id', $praktikum?->id)
            ->where('jenis', 'praktikan')
            ->where('is_aktif', true)
            ->first();

        return view('eoffice::manajemen-praktikum.koordinator.pendaftaran-praktikan', compact(
            'praktikum',
            'pendaftaran',
            'periodeAktif'
        ));
    }

    public function approve(Request $request, int $id)
    {
        $user        = auth()->user();
        $pendaftaran = PendaftaranPraktikan::with(['user', 'praktikum'])->findOrFail($id);

        // Pastikan pendaftaran ini milik praktikum yang dikoor oleh user ini
        if ($pendaftaran->praktikum?->koor_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status !== PendaftaranPraktikan::STATUS_PENDING) {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status'         => PendaftaranPraktikan::STATUS_APPROVED,
            'catatan_koor'   => $request->input('catatan_koor'),
            'direview_oleh'  => $user->id,
            'direview_pada'  => now(),
            'validasi_mode'  => 'manual',
        ]);

        $targetUser = $pendaftaran->user;

        $roleRow = Role::where('name', 'praktikan')->where('module', 'eoffice')->first();
        if ($targetUser && $roleRow) {
            $targetUser->roles()->syncWithoutDetaching([$roleRow->id]);
        }

        // Otomatis masukkan ke daftar praktikan
        \Modules\EOffice\Models\DaftarPraktikan::firstOrCreate([
            'user_id'      => $pendaftaran->user_id,
            'praktikum_id' => $pendaftaran->praktikum_id,
        ]);

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Praktikan Disetujui',
            "IRS Anda untuk praktikum {$pendaftaran->praktikum?->nama} telah disetujui. Anda sekarang resmi tergabung sebagai praktikan."
        );

        return back()->with('success', "Pendaftaran {$targetUser?->name} disetujui dan otomatis tergabung sebagai praktikan.");
    }

    public function reject(Request $request, int $id)
    {
        $user        = auth()->user();
        $pendaftaran = PendaftaranPraktikan::with(['user', 'praktikum'])->findOrFail($id);

        if ($pendaftaran->praktikum?->koor_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak mengelola pendaftaran ini.');
        }
        if ($pendaftaran->status !== PendaftaranPraktikan::STATUS_PENDING) {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $alasan = $request->input('alasan_penolakan')
            ?: PendaftaranPraktikan::PESAN_TOLAK_IRS_DEFAULT;

        $pendaftaran->update([
            'status'            => PendaftaranPraktikan::STATUS_REJECTED,
            'alasan_penolakan'  => $alasan,
            'catatan_koor'      => $request->input('catatan_koor'),
            'direview_oleh'     => $user->id,
            'direview_pada'     => now(),
            'validasi_mode'     => $request->boolean('otomatis_irs') ? 'otomatis_irs' : 'manual',
        ]);

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Praktikan Tidak Disetujui',
            "Pendaftaran praktikum {$pendaftaran->praktikum?->nama} tidak disetujui. Alasan: {$alasan}"
        );

        return back()->with('success', "Pendaftaran {$pendaftaran->user?->name} ditolak.");
    }

    /**
     * Tolak cepat dengan alasan standar (belum ambil IRS praktikum di semester ini).
     */
    public function rejectIrsDefault(Request $request, int $id)
    {
        $request->merge([
            'alasan_penolakan' => PendaftaranPraktikan::PESAN_TOLAK_IRS_DEFAULT,
            'otomatis_irs'     => true,
        ]);

        return $this->reject($request, $id);
    }
}