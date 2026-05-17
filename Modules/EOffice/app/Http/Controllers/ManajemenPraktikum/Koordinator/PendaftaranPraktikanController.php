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
 */
class PendaftaranPraktikanController extends Controller
{
    public function __construct(protected NotifikasiService $notif) {}

    public function index(Request $request)
    {
        $user      = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->first();

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
        $roleRow    = Role::where('name', 'praktikan')->where('module', 'eoffice')->first();

        if ($targetUser && $roleRow) {
            $targetUser->roles()->syncWithoutDetaching([$roleRow->id]);
        }

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Praktikan Disetujui',
            "IRS Anda untuk praktikum {$pendaftaran->praktikum?->nama} telah disetujui. Masukkan kode kelas dari Koordinator untuk bergabung."
        );

        return back()->with('success', "Pendaftaran {$targetUser?->name} disetujui. Mahasiswa dapat memakai kode praktikum untuk bergabung.");
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
