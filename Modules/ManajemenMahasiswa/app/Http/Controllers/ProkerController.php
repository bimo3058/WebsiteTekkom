<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;

class ProkerController extends Controller
{
    public function __construct(
        private SupabaseStorage $supabase
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar rencana proker
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        // GPM & Kadep view-only — admin, DPM & pengurus yang boleh kelola
        // (DPM disertakan agar sinkron dengan akses route create/edit/destroy & flag canEdit/canDelete di show())
        $canManage = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'dpm'])->isNotEmpty() || $isPengurus;

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->where('status', Kegiatan::STATUS_DRAFT)
            ->orderBy('created_at', 'desc');

        // Filter bidang
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                $query->where(function($q) {
                    $q->whereDoesntHave('bidangs')
                      ->orWhereHas('kategoris', fn($k) => $k->where('nama_kategori', 'like', '%Prodi%'));
                });
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }


        // Search
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $prokerList = $query->paginate(12);


        return view('manajemenmahasiswa::proker.index', compact(
            'prokerList', 'bidangList', 'kategoriList',
            'isAdmin', 'isPengurus', 'canManage'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail proker
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendamping.user',
            'panitia.user', 'creator', 'disetujuiOleh',
        ])->findOrFail($id);
        // Catatan: TIDAK difilter status agar detail proker tetap bisa dibuka
        // setelah diajukan (disetujui) / diarsipkan (selesai). Sebelumnya findOrFail
        // dibatasi STATUS_DRAFT sehingga URL proker/{id} 404 begitu proker diajukan.

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        // Hanya role tertentu yang boleh menekan tombol "Ajukan Proker"
        // (staff_himpunan, dosen_dpm, dosen_gpm TIDAK termasuk)
        $canAjukan = $roles->intersect([
            'superadmin', 'ketua_himpunan', 'wakil_ketua_himpunan',
            'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        // Role yang boleh edit proker (sinkron dengan route middleware edit) — GPM & Kadep view-only
        $canEdit = $roles->intersect([
            'superadmin', 'admin_kemahasiswaan', 'dpm',
            'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        // Role yang boleh hapus proker (sinkron dengan route middleware destroy) — GPM & Kadep view-only
        $canDelete = $roles->intersect([
            'superadmin', 'admin_kemahasiswaan', 'dpm',
            'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        $isCreator = $proker->user_id === Auth::id();

        return view('manajemenmahasiswa::proker.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'isCreator', 'canAjukan', 'canEdit', 'canDelete'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create & Store
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();

        return view('manajemenmahasiswa::proker.create', compact(
            'bidangList', 'kategoriList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProker($request);

        $validated['user_id'] = Auth::id();
        // Proker baru SELALU dimulai sebagai draft (Subbab 1).
        // Untuk maju ke Subbab 2, user harus klik "Ajukan Proker" di halaman detail.
        $validated['status'] = Kegiatan::STATUS_DRAFT;

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds   = $validated['bidang_id'] ?? [];

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id']            = $bidangIds[0] ?? null;

        $proker = Kegiatan::create($validated);
        $proker->kategoris()->sync($kategoriIds);
        $proker->bidangs()->sync($bidangIds);

        // Selalu redirect ke detail proker (Subbab 1) setelah dibuat
        $message = $request->input('save_as_draft') === '1'
            ? 'Rencana proker berhasil disimpan sebagai draft.'
            : 'Rencana proker berhasil dibuat. Klik "Ajukan Proker" untuk melanjutkan ke tahap pelaksanaan.';

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit & Update
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with(['kategoris', 'bidangs'])
            ->where('status', Kegiatan::STATUS_DRAFT)
            ->findOrFail($id);

        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();

        return view('manajemenmahasiswa::proker.edit', compact(
            'proker', 'bidangList', 'kategoriList'
        ));
    }

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        $validated = $this->validateProker($request);

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds   = $validated['bidang_id'] ?? [];

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id']            = $bidangIds[0] ?? null;

        $proker->update($validated);
        $proker->kategoris()->sync($kategoriIds);
        $proker->bidangs()->sync($bidangIds);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Workflow: Ajukan → Subbab 2 (Pelaksanaan)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pengurus mengajukan proker — status berubah ke disetujui dan masuk Subbab 2.
     */
    public function ajukan($id)
    {
        // Proteksi backend: hanya role yang berhak boleh mengajukan proker
        $allowedRoles = ['superadmin', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit'];
        $userRoles = Auth::user()->roles->pluck('name');
        $canAjukan = $userRoles->intersect($allowedRoles)->isNotEmpty();

        if (!$canAjukan) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengajukan proker.');
        }

        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        $proker->update(['status' => Kegiatan::STATUS_DISETUJUI]);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', 'Rencana Proker berhasil diajukan dan masuk ke tahap pelaksanaan kegiatan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destroy
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        if ($proker->banner) {
            $this->supabase->delete($proker->banner);
        }
        if ($proker->surat_proker) {
            $this->supabase->delete($proker->surat_proker);
        }

        $proker->delete();

        return redirect()
            ->route('manajemenmahasiswa.proker.index')
            ->with('success', 'Rencana proker berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function validateProker(Request $request): array
    {
        $kategoriDipilih = $request->input('kategori_kegiatan_id', []);
        $isOnlyProdi = false;
        
        if (is_array($kategoriDipilih) && count($kategoriDipilih) > 0) {
            $prodiId = KategoriKegiatan::where('nama_kategori', 'like', '%Prodi%')->value('id');
            if (count($kategoriDipilih) === 1 && in_array($prodiId, $kategoriDipilih)) {
                $isOnlyProdi = true;
            }
        }

        $bidangRule = $isOnlyProdi ? 'nullable|array' : 'required|array|min:1';

        // Subbab 1 hanya menangani: judul, deskripsi, kategori, bidang, dan banner.
        // Field lain (jadwal, lokasi, anggaran, target, personil, surat proker)
        // diisi saat Subbab 2 (Pelaksanaan Kegiatan).
        return $request->validate([
            'judul'                  => 'required|string|max:255',
            'deskripsi'              => 'required|string|min:20|max:3000',
            'kategori_kegiatan_id'   => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'              => $bidangRule,
            'bidang_id.*'            => 'exists:mk_bidang,id',
            'banner'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);
    }

}

