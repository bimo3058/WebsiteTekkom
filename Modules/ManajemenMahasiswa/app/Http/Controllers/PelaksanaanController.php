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
        $tahunList = Kegiatan::select('tahun')
            ->whereNotNull('tahun')
            ->where('status', Kegiatan::STATUS_DISETUJUI)
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->where('status', Kegiatan::STATUS_DISETUJUI)
            ->orderBy('created_at', 'desc');

        // Filter bidang
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                // Selaras dengan filter Prodi di Rencana Proker: tanpa bidang ATAU berkategori Prodi
                $query->where(function ($q) {
                    $q->whereDoesntHave('bidangs')
                      ->orWhereHas('kategoris', fn($k) => $k->where('nama_kategori', 'like', '%Prodi%'));
                });
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }

        // Filter tahun
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $query->where('tahun', $request->tahun);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $pelaksanaanList = $query->paginate(12);


        return view('manajemenmahasiswa::pelaksanaan.index', compact(
            'pelaksanaanList', 'bidangList', 'tahunList',
            'isAdmin', 'isPengurus', 'canManage'
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
            Kegiatan::STATUS_SELESAI,   // tetap include agar halaman detail masih bisa dibuka setelah diarsipkan
        ])->findOrFail($id);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only
        // Anggaran & Dokumen tampil untuk SEMUA role kecuali mahasiswa & alumni murni.
        // (denylist agar konsisten dengan halaman Arsip dan tidak ada role pengelola yang terlewat — mis. DPM)
        $canViewRestricted = $roles->diff(['mahasiswa', 'alumni'])->isNotEmpty();
        // Hanya role tertentu yang boleh menekan "Unggah ke Arsip"
        // (staff_himpunan TIDAK termasuk, dosen DPM/GPM juga tidak — hanya pengurus inti + admin)
        $canArsip = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        // Hak hapus pelaksanaan: admin + ketua-ketua himpunan (GPM, Kadep & DPM view-only)
        $canDelete = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        $images    = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'image') : collect();
        $documents = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'document') : collect();

        return view('manajemenmahasiswa::pelaksanaan.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'canManage', 'canArsip', 'canDelete',
            'canViewRestricted', 'images', 'documents'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit — form edit data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendamping.user',
            'panitia.user', 'creator',
            'repoMulmed',
        ])->whereIn('status', [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_SELESAI,
        ])->findOrFail($id);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only

        if (!$canManage) {
            abort(403, 'Akses ditolak.');
        }

        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $tahunList    = Kegiatan::select('tahun')->whereNotNull('tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingFoto    = $proker->repoMulmed->where('tipe_file', 'image');
        $existingDokumen = $proker->repoMulmed->where('tipe_file', 'document');
        $existingPanitia = $proker->panitia ?? collect();

        $selectedKategoriIds = old('kategori_kegiatan_id', $proker->kategoris->pluck('id')->toArray());
        $selectedBidangIds   = old('bidang_id', $proker->bidangs->pluck('id')->toArray());
        $existingPanitiaIds  = old('panitia_ids', $existingPanitia->pluck('id')->toArray());

        return view('manajemenmahasiswa::pelaksanaan.edit', compact(
            'proker', 'bidangList', 'kategoriList', 'tahunList',
            'mahasiswaList', 'dosenList',
            'existingFoto', 'existingDokumen',
            'existingPanitia', 'existingPanitiaIds',
            'selectedKategoriIds', 'selectedBidangIds',
            'isAdmin', 'isPengurus', 'canManage'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Update — simpan perubahan data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::whereIn('status', [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_SELESAI,
        ])->findOrFail($id);

        $validated = $request->validate([
            'judul'                     => 'required|string|max:255',
            // Disamakan dengan aturan di Laporan & Arsip agar kegiatan yang diarsipkan
            // selalu lolos validasi saat diedit di subbab Arsip (hindari "jebakan" validasi).
            'kategori_kegiatan_id'      => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*'    => 'integer|exists:mk_kategori_kegiatan,id',
            'bidang_id'                 => 'nullable|array',
            'bidang_id.*'               => 'integer|exists:mk_bidang,id',
            'deskripsi'                 => 'required|string|min:20',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai'                 => 'nullable|string',
            'jam_selesai'               => 'nullable|string',
            'lokasi'                    => 'nullable|string|max:255',
            'target_peserta'            => 'nullable|integer|min:1',
            'anggaran'                  => 'nullable|numeric|min:0',
            'ketua_pelaksana_id'        => 'nullable|exists:students,id',
            'dosen_pendamping_id'       => 'nullable|exists:lecturers,id',
            'panitia_ids'               => 'nullable|array',
            'panitia_ids.*'             => 'exists:students,id',
            'panitia_peran'             => 'nullable|array',
            'panitia_peran.*'           => 'nullable|string|max:255',
            'banner'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'foto_kegiatan'             => 'nullable|array|max:10',
            'foto_kegiatan.*'           => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'          => 'nullable|array|max:10',
            'dokumen_kegiatan.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'hapus_file'                => 'nullable|array',
            'hapus_file.*'              => 'integer|exists:mk_repo_mulmed,id',
        ]);

        // Update data utama
        $proker->update([
            'judul'              => $validated['judul'],
            'deskripsi'          => $validated['deskripsi'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            // Isi kolom `tahun` dari tahun tanggal mulai. Tanpa ini, kegiatan hasil
            // alur himpunan (Proker → Pelaksanaan → Arsip) selalu ber-`tahun` NULL
            // sehingga hilang dari filter tahun di Pelaksanaan & Laporan/Arsip.
            'tahun'              => \Carbon\Carbon::parse($validated['tanggal_mulai'])->year,
            'tanggal_selesai'    => $validated['tanggal_selesai'] ?? null,
            'jam_mulai'          => $validated['jam_mulai'] ?? null,
            'jam_selesai'        => $validated['jam_selesai'] ?? null,
            'lokasi'             => $validated['lokasi'] ?? null,
            'target_peserta'     => $validated['target_peserta'] ?? null,
            'anggaran'           => $validated['anggaran'] ?? null,
            'ketua_pelaksana_id' => $validated['ketua_pelaksana_id'] ?? null,
            'dosen_pendamping_id'=> $validated['dosen_pendamping_id'] ?? null,
            'is_pelaksanaan_updated' => true,
        ]);

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $proker->update(['penanggung_jawab' => $student?->user?->name]);
        } else {
            $proker->update(['penanggung_jawab' => null]);
        }

        // Sync kategori & bidang (pivot tables)
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds = $validated['bidang_id'] ?? [];

        if (!empty($kategoriIds)) {
            $proker->kategoris()->sync($kategoriIds);
        } else {
            // Simetris dengan bidang: kosongkan pivot bila tak ada kategori dipilih
            // (mencegah kategori lama "nyangkut" sementara kolom FK sudah null).
            $proker->kategoris()->detach();
        }
        if (!empty($bidangIds)) {
            $proker->bidangs()->sync($bidangIds);
        } else {
            $proker->bidangs()->detach();
        }

        // Sync backward-compat FK columns (first item) — agar view lama tetap konsisten
        $proker->update([
            'kategori_kegiatan_id' => $kategoriIds[0] ?? null,
            'bidang_id'            => $bidangIds[0] ?? null,
        ]);

        // Sync panitia
        $panitiaIds = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $pid) {
            $panitiaSyncData[$pid] = ['peran' => $panitiaPeran[$pid] ?? null];
        }
        $proker->panitia()->sync($panitiaSyncData);

        // Upload banner — simpan ke kolom `banner` di mk_kegiatan (bukan repo_mulmed)
        if ($request->hasFile('banner')) {
            // Hapus banner lama jika ada
            if ($proker->banner) {
                $this->supabase->delete($proker->banner);
            }
            $proker->update([
                'banner' => $this->supabase->upload($request->file('banner'), 'mk_mulmed/image'),
            ]);
        }

        // Handle file deletions
        if ($request->filled('hapus_file')) {
            foreach ($request->hapus_file as $fileId) {
                $file = RepoMulmed::where('kegiatan_id', $proker->id)->find($fileId);
                if ($file) {
                    $this->repoMulmedService->deletePermanent($file->id);
                }
            }
        }

        $this->handleFileUploads($request, $proker);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Data pelaksanaan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Publish ke Arsip — pindahkan kegiatan ke subbab 3 (status = selesai)
    // ─────────────────────────────────────────────────────────────────────────

    public function publishToArsip($id)
    {
        // Proteksi backend: sinkron dengan route middleware + $canArsip di show()
        // GPM & DPM adalah view-only — TIDAK boleh melakukan arsip
        $allowedRoles = [
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ];
        $userRoles = Auth::user()->roles->pluck('name');
        $canArsip = $userRoles->intersect($allowedRoles)->isNotEmpty();

        if (!$canArsip) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengunggah kegiatan ke arsip.');
        }

        $proker = Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->findOrFail($id);

        if (!$proker->is_pelaksanaan_updated) {
            return redirect()
                ->back()
                ->with('error', 'Silakan edit/update data pelaksanaan kegiatan terlebih dahulu sebelum mengunggah ke arsip.');
        }

        // Banner wajib diisi sebelum kegiatan boleh diunggah ke arsip (subbab 3).
        if (empty($proker->banner)) {
            return redirect()
                ->back()
                ->with('error', 'Banner kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip.');
        }

        $proker->update(['status' => Kegiatan::STATUS_SELESAI]);

        // Konsisten dengan alur subbab 1 → 2 (ajukan() yang mengarahkan ke
        // pelaksanaan.index): setelah unggah ke arsip, arahkan ke daftar
        // subbab 3 (Laporan & Arsip), bukan kembali ke detail pelaksanaan.
        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diunggah ke Laporan & Arsip.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Hapus — Hapus pelaksanaan kegiatan (termasuk foto/dokumen)
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        // Hanya kegiatan berstatus 'disetujui' yang boleh dihapus dari Pelaksanaan.
        // Kegiatan yang sudah diarsipkan (selesai) harus dihapus dari subbab Arsip.
        $proker = Kegiatan::with('repoMulmed')
            ->where('status', Kegiatan::STATUS_DISETUJUI)
            ->findOrFail($id);

        if ($proker->banner) {
            $this->supabase->delete($proker->banner);
        }
        if ($proker->surat_proker) {
            $this->supabase->delete($proker->surat_proker);
        }

        // Hapus semua file foto & dokumen dari repo
        if ($proker->repoMulmed) {
            foreach ($proker->repoMulmed as $file) {
                $this->repoMulmedService->deletePermanent($file->id);
            }
        }

        $proker->delete();

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', 'Data pelaksanaan kegiatan berhasil dihapus.');
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
