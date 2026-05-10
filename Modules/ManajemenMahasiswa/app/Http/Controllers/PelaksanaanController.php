<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

class PelaksanaanController extends Controller
{
    public function __construct(
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar proker yang siap/sedang/sudah dilaksanakan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bidangList = Bidang::orderBy('nama_bidang')->get();
        $tahunList  = range(date('Y') + 1, 2020);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $isAdmin || $isPengurus;

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->whereIn('status', [
                Kegiatan::STATUS_DISETUJUI,
                Kegiatan::STATUS_AKAN_DATANG,
                Kegiatan::STATUS_BERLANGSUNG,
                Kegiatan::STATUS_SELESAI,
            ])
            ->orderBy('tanggal_mulai', 'desc');

        // Filter bidang
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                $query->whereDoesntHave('bidangs');
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }

        // Filter tahun
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $query->where('tahun', $request->tahun);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $pelaksanaanList = $query->paginate(12);

        // Statistik
        $stats = [
            'disetujui'  => Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->count(),
            'berlangsung' => Kegiatan::where('status', Kegiatan::STATUS_BERLANGSUNG)->count(),
            'selesai'    => Kegiatan::where('status', Kegiatan::STATUS_SELESAI)->count(),
        ];

        return view('manajemenmahasiswa::pelaksanaan.index', compact(
            'pelaksanaanList', 'bidangList', 'tahunList',
            'isAdmin', 'isPengurus', 'canManage', 'stats'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendamping.user',
            'panitia.user', 'creator', 'disetujuiOleh',
            'repoMulmed',
        ])->whereIn('status', [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_AKAN_DATANG,
            Kegiatan::STATUS_BERLANGSUNG,
            Kegiatan::STATUS_SELESAI,
        ])->findOrFail($id);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $isAdmin || $isPengurus;
        $canViewRestricted = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm',
                                                'dosen_koordinator', 'dosen', 'pengurus_himpunan',
                                                'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang',
                                                'ketua_unit', 'staff_himpunan'])->isNotEmpty();

        $images    = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'image') : collect();
        $documents = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'document') : collect();

        return view('manajemenmahasiswa::pelaksanaan.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'canManage',
            'canViewRestricted', 'images', 'documents'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Realisasi — simpan data pelaksanaan aktual
    // ─────────────────────────────────────────────────────────────────────────

    public function storeRealisasi(Request $request, $id)
    {
        $proker = Kegiatan::whereIn('status', [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_BERLANGSUNG,
        ])->findOrFail($id);

        $validated = $request->validate([
            'realisasi_tanggal_mulai'   => 'required|date',
            'realisasi_tanggal_selesai' => 'nullable|date|after_or_equal:realisasi_tanggal_mulai',
            'realisasi_lokasi'          => 'nullable|string|max:255',
            'realisasi_peserta'         => 'nullable|integer|min:0',
            'realisasi_anggaran'        => 'nullable|numeric|min:0|max:9999999999999',
            'catatan_pelaksanaan'       => 'nullable|string',
            'status_realisasi'          => 'required|in:berlangsung,selesai',
            'foto_kegiatan'             => 'nullable|array|max:20',
            'foto_kegiatan.*'           => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'          => 'nullable|array|max:10',
            'dokumen_kegiatan.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $proker->update([
            'realisasi_tanggal_mulai'   => $validated['realisasi_tanggal_mulai'],
            'realisasi_tanggal_selesai' => $validated['realisasi_tanggal_selesai'] ?? null,
            'realisasi_lokasi'          => $validated['realisasi_lokasi'] ?? null,
            'realisasi_peserta'         => $validated['realisasi_peserta'] ?? null,
            'realisasi_anggaran'        => $validated['realisasi_anggaran'] ?? null,
            'catatan_pelaksanaan'       => $validated['catatan_pelaksanaan'] ?? null,
            'status'                    => $validated['status_realisasi'],
        ]);

        // Upload foto & dokumen
        $this->handleFileUploads($request, $proker);

        $msg = $validated['status_realisasi'] === 'selesai'
            ? 'Kegiatan ditandai selesai! Data kini tersedia di halaman Laporan & Arsip.'
            : 'Data realisasi berhasil disimpan. Status kegiatan: Berlangsung.';

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', $msg);
    }

    public function updateRealisasi(Request $request, $id)
    {
        $proker = Kegiatan::whereIn('status', [
            Kegiatan::STATUS_BERLANGSUNG,
            Kegiatan::STATUS_SELESAI,
        ])->findOrFail($id);

        $validated = $request->validate([
            'realisasi_tanggal_mulai'   => 'required|date',
            'realisasi_tanggal_selesai' => 'nullable|date|after_or_equal:realisasi_tanggal_mulai',
            'realisasi_lokasi'          => 'nullable|string|max:255',
            'realisasi_peserta'         => 'nullable|integer|min:0',
            'realisasi_anggaran'        => 'nullable|numeric|min:0|max:9999999999999',
            'catatan_pelaksanaan'       => 'nullable|string',
            'status_realisasi'          => 'required|in:berlangsung,selesai',
            'foto_kegiatan'             => 'nullable|array|max:20',
            'foto_kegiatan.*'           => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'          => 'nullable|array|max:10',
            'dokumen_kegiatan.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'hapus_file'                => 'nullable|array',
            'hapus_file.*'              => 'integer|exists:mk_repo_mulmed,id',
        ]);

        // Handle file deletions
        if ($request->filled('hapus_file')) {
            foreach ($request->hapus_file as $fileId) {
                $file = RepoMulmed::where('kegiatan_id', $proker->id)->find($fileId);
                if ($file) {
                    $this->repoMulmedService->deletePermanent($file->id);
                }
            }
        }

        $proker->update([
            'realisasi_tanggal_mulai'   => $validated['realisasi_tanggal_mulai'],
            'realisasi_tanggal_selesai' => $validated['realisasi_tanggal_selesai'] ?? null,
            'realisasi_lokasi'          => $validated['realisasi_lokasi'] ?? null,
            'realisasi_peserta'         => $validated['realisasi_peserta'] ?? null,
            'realisasi_anggaran'        => $validated['realisasi_anggaran'] ?? null,
            'catatan_pelaksanaan'       => $validated['catatan_pelaksanaan'] ?? null,
            'status'                    => $validated['status_realisasi'],
        ]);

        $this->handleFileUploads($request, $proker);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Data realisasi berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function handleFileUploads(Request $request, Kegiatan $proker): void
    {
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $this->repoMulmedService->upload($foto, [
                    'kegiatan_id'       => $proker->id,
                    'judul_file'        => pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }

        if ($request->hasFile('dokumen_kegiatan')) {
            foreach ($request->file('dokumen_kegiatan') as $doc) {
                $this->repoMulmedService->upload($doc, [
                    'kegiatan_id'       => $proker->id,
                    'judul_file'        => pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }
    }
}
