<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\NotifikasiService;

/**
 * Admin: Lihat & final-approve pendaftaran koor.
 * Flow: Mahasiswa → Dosen review (status_dosen) → Admin final approve (status).
 * Admin dapat mengesampingkan review dosen secara eksplisit dengan alasan audit.
 */
class PendaftaranKoorController extends Controller
{
    public function __construct(
        protected NotifikasiService $notif,
        protected KoorPraktikumService $koorService
    ) {}

    public function index(Request $request)
    {
        $query = PendaftaranKoordinator::with(['user.student', 'praktikum', 'direviewOleh'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($statusDosen = $request->input('status_dosen')) {
            $query->where('status_dosen', $statusDosen);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->whereRaw('LOWER(name) like ?', ['%' . strtolower($search) . '%']));
        }
        if ($praktikumId = $request->input('praktikum_id')) {
            $query->where('praktikum_id', $praktikumId);
        }

        $pendaftaran = $query->paginate($request->input('per_page', 5))->withQueryString();
        
        $praktikumList = \Modules\EOffice\Models\Praktikum::with('matkul')
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-koor', compact('pendaftaran', 'praktikumList'));
    }

    /**
     * Admin final-approve: assign role koor_prak otomatis.
     * Review asli dosen tetap disimpan ketika admin melakukan override.
     */
    public function approve(Request $request, int $id)
    {
        abort_unless($request->user()?->hasAnyRole(['superadmin', 'admin_eoffice']), 403);
        $data = $request->validate([
            'override_dosen' => 'sometimes|boolean',
            'override_reason' => 'required_if:override_dosen,1|nullable|string|min:10|max:1000',
        ], ['override_reason.required_if' => 'Tuliskan alasan override persetujuan dosen.']);
        $override = $request->boolean('override_dosen');

        $pendaftaran = DB::transaction(function () use ($id, $request, $data, $override) {
            $pendaftaran = PendaftaranKoordinator::with('user')->lockForUpdate()->findOrFail($id);
            $canReopen = $override && $pendaftaran->status === 'rejected' && $pendaftaran->status_dosen === 'ditolak';
            if ($pendaftaran->status !== 'pending' && !$canReopen) {
                throw ValidationException::withMessages(['approval' => 'Pendaftaran ini sudah diproses.']);
            }
            if (!$override && $pendaftaran->status_dosen !== 'disetujui') {
                throw ValidationException::withMessages(['approval' => 'Dosen belum menyetujui. Gunakan Override & Setujui dengan alasan jika ingin melanjutkan.']);
            }
            if ($override && mb_strlen(trim($data['override_reason'] ?? '')) < 10) {
                throw ValidationException::withMessages(['override_reason' => 'Alasan override minimal 10 karakter.']);
            }

            $praktikum = Praktikum::lockForUpdate()->findOrFail($pendaftaran->praktikum_id);
            if ($praktikum->koor_id && (string) $praktikum->koor_id !== (string) $pendaftaran->user_id) {
                throw ValidationException::withMessages(['approval' => 'Praktikum ini sudah memiliki koordinator lain. Kelola penugasan koordinator terlebih dahulu.']);
            }
            abort_unless($pendaftaran->user, 422, 'Akun mahasiswa tidak tersedia.');
            $before = $pendaftaran->only(['status', 'status_dosen', 'catatan_dosen', 'direview_oleh', 'direview_pada']);
            $pendaftaran->update(['status' => 'approved']);
            $this->koorService->assign($praktikum, $pendaftaran->user);
            $pendaftaran->setRelation('praktikum', $praktikum);

            $after = $pendaftaran->only(array_keys($before));
            if ($override) {
                $after['override_reason'] = trim($data['override_reason']);
            }
            AuditLogger::log('eoffice', $override ? 'OVERRIDE_APPROVAL' : 'APPROVE',
                $override ? 'Override persetujuan dosen untuk pendaftaran koordinator oleh admin.' : 'Persetujuan akhir pendaftaran koordinator oleh admin.',
                $pendaftaran, $before, $after, $request->user()->id);
            $this->notif->kirim($pendaftaran->user_id, 'Pendaftaran Koordinator Diterima',
                "Anda diterima sebagai Koordinator Praktikum {$praktikum->nama} melalui persetujuan admin.");

            return $pendaftaran;
        });

        return back()->with('success', "Pendaftaran koordinator {$pendaftaran->user->name} berhasil disetujui. Akses koordinator dan asisten praktikum telah diberikan.");
    }

    public function reject(Request $request, int $id)
    {
        abort_unless($request->user()?->hasAnyRole(['superadmin', 'admin_eoffice']), 403);
        $pendaftaran = PendaftaranKoordinator::with('user')->findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $updated = PendaftaranKoordinator::whereKey($id)->where('status', 'pending')->update([
            'status'           => 'rejected',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
        ]);
        if (!$updated) {
            return back()->with('error', 'Pendaftaran ini sudah diproses. Muat ulang halaman.');
        }

        $this->notif->kirim(
            $pendaftaran->user_id,
            'Pendaftaran Koordinator Ditolak',
            "Pendaftaran koordinator Anda untuk praktikum {$pendaftaran->praktikum?->nama} tidak diterima."
                . ($request->input('alasan_penolakan') ? " Alasan: {$request->input('alasan_penolakan')}" : '')
        );

        return back()->with('success', "Pendaftaran koordinator {$pendaftaran->user?->name} ditolak.");
    }
}
