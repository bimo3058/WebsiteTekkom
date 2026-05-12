<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\ProkerTtd;

class PersuratanController extends Controller
{
    public function __construct(
        private SupabaseStorage $supabase
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar proker di fase persuratan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bidangList = Bidang::orderBy('nama_bidang')->get();
        $tahunList  = range(date('Y') + 1, 2020);

        $user       = Auth::user();
        $roles      = $user->roles->pluck('name');
        $isAdmin    = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage  = $isAdmin || $isPengurus;

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->whereIn('status', [
                Kegiatan::STATUS_DRAFT,
                Kegiatan::STATUS_DIAJUKAN,
                Kegiatan::STATUS_TTD_KETUA,
                Kegiatan::STATUS_TTD_DPM,
                Kegiatan::STATUS_TTD_DEPT,
                Kegiatan::STATUS_DISETUJUI,
                Kegiatan::STATUS_DITOLAK,
            ])
            ->orderBy('created_at', 'desc');

        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                $query->whereDoesntHave('bidangs');
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }

        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $prokerList = $query->paginate(12);

        $stats = [
            'draft'     => Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->count(),
            'diajukan'  => Kegiatan::whereIn('status', [
                Kegiatan::STATUS_DIAJUKAN,
                Kegiatan::STATUS_TTD_KETUA,
                Kegiatan::STATUS_TTD_DPM,
                Kegiatan::STATUS_TTD_DEPT,
            ])->count(),
            'disetujui' => Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->count(),
            'ditolak'   => Kegiatan::where('status', Kegiatan::STATUS_DITOLAK)->count(),
        ];

        return view('manajemenmahasiswa::persuratan.index', compact(
            'prokerList', 'bidangList', 'tahunList',
            'isAdmin', 'isPengurus', 'canManage', 'stats'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail + form upload surat + panel TTD
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendamping.user',
            'panitia.user', 'creator', 'disetujuiOleh',
            'prokerTtd.signedBy',
        ])->whereIn('status', [
            Kegiatan::STATUS_DRAFT,
            Kegiatan::STATUS_DIAJUKAN,
            Kegiatan::STATUS_TTD_KETUA,
            Kegiatan::STATUS_TTD_DPM,
            Kegiatan::STATUS_TTD_DEPT,
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_DITOLAK,
        ])->findOrFail($id);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $isCreator       = $proker->user_id === Auth::id();
        $isKetuaHimpunan = $roles->contains('ketua_himpunan');
        $isBendahara     = $roles->contains('bendahara');
        $isDpm           = $roles->contains('dpm');
        $isKetuaDept     = $roles->contains('ketua_departemen');

        $ttdData = $proker->prokerTtd->keyBy('role');

        return view('manajemenmahasiswa::persuratan.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'isCreator',
            'isKetuaHimpunan', 'isBendahara', 'isDpm', 'isKetuaDept',
            'ttdData'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Ajukan — upload surat PDF lalu ubah status ke menunggu_ttd_ketua
    // ─────────────────────────────────────────────────────────────────────────

    public function ajukan(Request $request, $id)
    {
        $request->validate([
            'surat_proker' => 'nullable|mimes:pdf|max:20480',
        ]);

        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        // Upload PDF jika ada file baru
        if ($request->hasFile('surat_proker') && $request->file('surat_proker')->isValid()) {
            $path = $this->supabase->upload($request->file('surat_proker'), 'mk_proker_surat');
            if ($path) {
                $proker->update([
                    'surat_proker'          => $path,
                    'surat_proker_original' => $path,
                ]);
                $proker->refresh();
            }
        }

        if (!$proker->surat_proker) {
            return back()->with('error', 'Harap upload surat proker (PDF) sebelum mengajukan.');
        }

        $proker->update(['status' => Kegiatan::STATUS_TTD_KETUA]);

        return redirect()
            ->route('manajemenmahasiswa.persuratan.show', $proker->id)
            ->with('success', 'Proker berhasil diajukan. Menunggu tanda tangan Ketua Himpunan dan Bendahara.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tolak
    // ─────────────────────────────────────────────────────────────────────────

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|max:1000',
        ]);

        $proker = Kegiatan::whereIn('status', [
            Kegiatan::STATUS_DIAJUKAN,
            Kegiatan::STATUS_TTD_KETUA,
            Kegiatan::STATUS_TTD_DPM,
            Kegiatan::STATUS_TTD_DEPT,
        ])->findOrFail($id);

        $proker->update([
            'status'            => Kegiatan::STATUS_DITOLAK,
            'catatan_penolakan' => $request->catatan_penolakan,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.persuratan.show', $proker->id)
            ->with('error', 'Proker ditolak. Pengurus dapat merevisi dan mengajukan kembali.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pasang TTD — delegate ke ProkerController
    // ─────────────────────────────────────────────────────────────────────────

    public function pasangTtd(Request $request, $id)
    {
        return app(ProkerController::class)->pasangTtd($request, $id);
    }

    public function batalTtd(Request $request, $id)
    {
        return app(ProkerController::class)->batalTtd($request, $id);
    }
}
