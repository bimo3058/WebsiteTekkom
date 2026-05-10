<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\ProkerTtd;

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
        $tahunList    = range(date('Y') + 1, 2020);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan',
                                         'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $isAdmin || $isPengurus;

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

        $prokerList = $query->paginate(12);

        // Statistik per status
        $stats = [
            'draft'     => Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->count(),
            'diajukan'  => Kegiatan::whereIn('status', [Kegiatan::STATUS_DIAJUKAN, Kegiatan::STATUS_TTD_KETUA, Kegiatan::STATUS_TTD_DPM, Kegiatan::STATUS_TTD_DEPT])->count(),
            'disetujui' => Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->count(),
            'ditolak'   => Kegiatan::where('status', Kegiatan::STATUS_DITOLAK)->count(),
        ];

        return view('manajemenmahasiswa::proker.index', compact(
            'prokerList', 'bidangList', 'tahunList', 'kategoriList',
            'isAdmin', 'isPengurus', 'canManage', 'stats'
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
        $isCreator        = $proker->user_id === Auth::id();
        $isKetuaHimpunan  = $roles->contains('ketua_himpunan');
        $isBendahara      = $roles->contains('bendahara');
        $isDpm            = $roles->contains('dpm');
        $isKetuaDept      = $roles->contains('ketua_departemen');

        // TTD yang sudah dipasang
        $ttdData = $proker->prokerTtd->keyBy('role');

        return view('manajemenmahasiswa::proker.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'isCreator',
            'isKetuaHimpunan', 'isBendahara', 'isDpm', 'isKetuaDept',
            'ttdData'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create & Store
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $tahunList    = range(date('Y') + 1, 2020);
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList    = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        return view('manajemenmahasiswa::proker.create', compact(
            'bidangList', 'kategoriList', 'tahunList', 'mahasiswaList', 'dosenList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProker($request);

        $validated['user_id'] = Auth::id();
        $validated['status']  = Kegiatan::STATUS_DRAFT;

        // Set backward-compat fields
        $this->setCompatFields($validated, $request);

        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds   = $validated['bidang_id'] ?? [];
        $panitiaSync = $this->buildPanitiaSync($request);

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id']            = $bidangIds[0] ?? null;
        unset($validated['panitia_ids'], $validated['panitia_peran']);

        $proker = Kegiatan::create($validated);
        $proker->kategoris()->sync($kategoriIds);
        $proker->bidangs()->sync($bidangIds);
        $proker->panitia()->sync($panitiaSync);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil disimpan sebagai draft.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit & Update
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with(['kategoris', 'bidangs', 'panitia.user'])
            ->whereIn('status', [Kegiatan::STATUS_DRAFT, Kegiatan::STATUS_DITOLAK])
            ->findOrFail($id);

        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $tahunList    = range(date('Y') + 1, 2020);
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList    = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        return view('manajemenmahasiswa::proker.edit', compact(
            'proker', 'bidangList', 'kategoriList', 'tahunList', 'mahasiswaList', 'dosenList'
        ));
    }

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::whereIn('status', [Kegiatan::STATUS_DRAFT, Kegiatan::STATUS_DITOLAK])
            ->findOrFail($id);

        $validated = $this->validateProker($request);
        $this->setCompatFields($validated, $request);

        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds   = $validated['bidang_id'] ?? [];
        $panitiaSync = $this->buildPanitiaSync($request);

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id']            = $bidangIds[0] ?? null;
        unset($validated['panitia_ids'], $validated['panitia_peran']);

        // Reset status jika sebelumnya ditolak
        if ($proker->status === Kegiatan::STATUS_DITOLAK) {
            $validated['status'] = Kegiatan::STATUS_DRAFT;
            $validated['catatan_penolakan'] = null;
        }

        $proker->update($validated);
        $proker->kategoris()->sync($kategoriIds);
        $proker->bidangs()->sync($bidangIds);
        $proker->panitia()->sync($panitiaSync);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Workflow: Ajukan → TTD Ketua → TTD DPM → TTD Dept → Disetujui
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pengurus mengajukan proker — status berubah ke menunggu_ttd_ketua.
     */
    public function ajukan($id)
    {
        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        if (!$proker->surat_proker) {
            return back()->with('error', 'Harap upload surat proker (PDF) sebelum mengajukan.');
        }

        $proker->update(['status' => Kegiatan::STATUS_TTD_KETUA]);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Proker berhasil diajukan. Menunggu tanda tangan Ketua Himpunan dan Bendahara.');
    }

    /**
     * Upload tanda tangan digital dan simpan posisinya di PDF.
     * Dipakai oleh semua role signer.
     */
    public function pasangTtd(Request $request, $id)
    {
        $request->validate([
            'role'            => 'required|in:ketua_himpunan,bendahara,dpm,ketua_departemen',
            'signature_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
            'signed_pdf'      => 'nullable|mimes:pdf|max:51200', // PDF bertanda tangan dari browser (maks 50 MB)
            'page_number'     => 'required|integer|min:1',
            'pos_x_percent'   => 'required|numeric|min:0|max:100',
            'pos_y_percent'   => 'required|numeric|min:0|max:100',
            'width_percent'   => 'required|numeric|min:1|max:100',
            'height_percent'  => 'required|numeric|min:1|max:100',
        ]);

        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        $role  = $request->role;

        // Validasi hak akses per role
        $this->authorizeRoleTtd($role, $roles, $id);

        $proker = Kegiatan::findOrFail($id);

        // Upload gambar TTD ke Supabase
        $signaturePath = $this->supabase->upload($request->file('signature_image'), 'mk_proker_ttd');

        // Simpan / update TTD (upsert berdasarkan kegiatan_id + role)
        $ttd = ProkerTtd::updateOrCreate(
            ['kegiatan_id' => $proker->id, 'role' => $role],
            [
                'signed_by'            => $user->id,
                'signature_image_path' => $signaturePath,
                'page_number'          => $request->page_number,
                'pos_x_percent'        => $request->pos_x_percent,
                'pos_y_percent'        => $request->pos_y_percent,
                'width_percent'        => $request->width_percent,
                'height_percent'       => $request->height_percent,
                'signed_at'            => now(),
            ]
        );

        // Jika browser mengirimkan PDF yang sudah ditandatangani, update surat_proker
        // Ini membuat link "Lihat/Download" menampilkan PDF dengan TTD terbenam
        if ($request->hasFile('signed_pdf') && $request->file('signed_pdf')->isValid()) {
            // Auto-set surat_proker_original jika masih null
            // (untuk proker lama yang dibuat sebelum fitur cancel TTD ada)
            if (!$proker->surat_proker_original && $proker->surat_proker) {
                $proker->update(['surat_proker_original' => $proker->surat_proker]);
                $proker->refresh();
            }

            $signedPdfPath = $this->supabase->upload(
                $request->file('signed_pdf'),
                'mk_proker_surat'
            );
            if ($signedPdfPath) {
                $proker->update(['surat_proker' => $signedPdfPath]);
            }
        }

        // Cek apakah perlu advance status
        $this->tryAdvanceStatus($proker);

        return response()->json([
            'success'       => true,
            'message'       => 'Tanda tangan berhasil dipasang dan PDF telah diperbarui.',
            'signature_url' => $ttd->signature_url,
            'new_status'    => $proker->fresh()->status,
        ]);
    }

    /**
     * Admin menolak proker dengan catatan.
     */
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
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', 'Proker ditolak. Pengurus dapat merevisi dan mengajukan kembali.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destroy
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $proker = Kegiatan::whereIn('status', [
            Kegiatan::STATUS_DRAFT,
            Kegiatan::STATUS_DITOLAK,
        ])->findOrFail($id);

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

    /**
     * Validasi hak akses pasang TTD berdasarkan role dan status proker.
     */
    private function authorizeRoleTtd(string $role, $userRoles, $prokerId): void
    {
        $proker = Kegiatan::findOrFail($prokerId);

        $allowed = match($role) {
            'ketua_himpunan' => $userRoles->contains('ketua_himpunan') &&
                                $proker->status === Kegiatan::STATUS_TTD_KETUA,
            'bendahara'      => $userRoles->contains('bendahara') &&
                                $proker->status === Kegiatan::STATUS_TTD_KETUA,
            'dpm'            => $userRoles->contains('dpm') &&
                                $proker->status === Kegiatan::STATUS_TTD_DPM,
            'ketua_departemen' => $userRoles->contains('ketua_departemen') &&
                                $proker->status === Kegiatan::STATUS_TTD_DEPT,
            default          => false,
        };

        if (!$allowed) {
            abort(403, 'Anda tidak berwenang atau status proker tidak sesuai.');
        }
    }

    /**
     * Cek apakah kondisi terpenuhi untuk advance ke status berikutnya.
     */
    private function tryAdvanceStatus(Kegiatan $proker): void
    {
        $proker->refresh();
        $ttds = $proker->prokerTtd->keyBy('role');

        if ($proker->status === Kegiatan::STATUS_TTD_KETUA) {
            // Butuh KEDUANYA: ketua_himpunan DAN bendahara
            if ($ttds->has('ketua_himpunan') && $ttds->has('bendahara')) {
                $proker->update(['status' => Kegiatan::STATUS_TTD_DPM]);
            }
        } elseif ($proker->status === Kegiatan::STATUS_TTD_DPM) {
            if ($ttds->has('dpm')) {
                $proker->update(['status' => Kegiatan::STATUS_TTD_DEPT]);
            }
        } elseif ($proker->status === Kegiatan::STATUS_TTD_DEPT) {
            if ($ttds->has('ketua_departemen')) {
                $proker->update([
                    'status'         => Kegiatan::STATUS_DISETUJUI,
                    'disetujui_oleh' => Auth::id(),
                    'disetujui_at'   => now(),
                ]);
            }
        }
    }

    private function validateProker(Request $request): array
    {
        return $request->validate([
            'judul'                => 'required|string|max:255',
            'deskripsi'            => 'required|string|min:20',
            'kategori_kegiatan_id' => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'            => 'nullable|array',
            'bidang_id.*'          => 'exists:mk_bidang,id',
            'tahun'                => 'nullable|integer|min:2020',
            'tanggal_mulai'        => 'required|date',
            'jam_mulai'            => 'nullable|date_format:H:i',
            'tanggal_selesai'      => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'          => 'nullable|date_format:H:i',
            'lokasi'               => 'nullable|string|max:255',
            'banner'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'surat_proker'         => 'nullable|mimes:pdf|max:20480',
            'anggaran'             => 'nullable|numeric|min:0|max:9999999999999',
            'ketua_pelaksana_id'   => 'nullable|exists:students,id',
            'dosen_pendamping_id'  => 'nullable|exists:lecturers,id',
            'panitia_ids'          => 'nullable|array',
            'panitia_ids.*'        => 'exists:students,id',
            'panitia_peran'        => 'nullable|array',
            'panitia_peran.*'      => 'nullable|string|max:255',
            'target_peserta'       => 'nullable|integer|min:1',
        ]);
    }

    private function setCompatFields(array &$validated, Request $request): void
    {
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $validated['penanggung_jawab'] = $student?->user?->name;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        // Handle surat proker PDF upload
        if ($request->hasFile('surat_proker')) {
            $path = $this->supabase->upload($request->file('surat_proker'), 'mk_proker_surat');
            $validated['surat_proker'] = $path;
            // Simpan juga sebagai original (tidak pernah diubah) agar bisa dipakai regenerasi
            $validated['surat_proker_original'] = $path;
        }
    }

    private function buildPanitiaSync(Request $request): array
    {
        $panitiaIds  = $request->panitia_ids ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $sync = [];
        foreach ($panitiaIds as $id) {
            $sync[$id] = ['peran' => $panitiaPeran[$id] ?? null];
        }
        return $sync;
    }

    // ───────────────────────────────────────────────────────────────────────────
    // Batal TTD — hapus TTD role tertentu, PDF akan di-regenerasi saat re-sign
    // ───────────────────────────────────────────────────────────────────────────

    /**
     * Hapus TTD satu role. Broadcast JSON agar JS bisa reload halaman.
     * Saat user re-sign, PDF akan di-regenerasi dari surat_proker_original
     * dengan re-embed semua TTD yang masih valid.
     */
    public function batalTtd(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:ketua_himpunan,bendahara,dpm,ketua_departemen',
        ]);

        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        $role  = $validated['role'];

        // Hanya pemilik role tersebut yang boleh membatalkan TTD-nya sendiri
        $allowed = match($role) {
            'ketua_himpunan'  => $roles->contains('ketua_himpunan'),
            'bendahara'       => $roles->contains('bendahara'),
            'dpm'             => $roles->contains('dpm'),
            'ketua_departemen'=> $roles->contains('ketua_departemen'),
            default           => false,
        };

        if (!$allowed) {
            return response()->json(['success' => false, 'message' => 'Anda tidak berwenang membatalkan TTD ini.'], 403);
        }

        $proker = Kegiatan::findOrFail($id);

        // Hapus record TTD role ini
        ProkerTtd::where('kegiatan_id', $proker->id)
            ->where('role', $role)
            ->delete();

        // BUG FIX: Reset surat_proker ke versi original
        // Sebelumnya hanya record DB yang dihapus tapi file PDF tidak direset,
        // sehingga download masih menampilkan TTD yang sudah dibatalkan.
        // Saat signer re-sign, JS akan load original + re-embed TTD lain yang masih valid.
        if ($proker->surat_proker_original) {
            $proker->update(['surat_proker' => $proker->surat_proker_original]);
        }

        return response()->json([
            'success' => true,
            'message' => 'TTD berhasil dibatalkan. PDF telah direset. Silakan tanda tangan ulang.',
        ]);
    }
}
