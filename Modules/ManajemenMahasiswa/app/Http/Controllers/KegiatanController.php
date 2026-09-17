<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\KegiatanPeserta;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

class KegiatanController extends Controller
{
    /** Batas jumlah foto & dokumen yang boleh tersimpan pada satu kegiatan. */
    private const MAKS_FILE = 10;

    public function __construct(
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService
    ) {}

    /**
     * Halaman utama daftar kegiatan — mahasiswa & alumni.
     */
    public function index(Request $request)
    {
        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();
        
        // Ikut menghitung kegiatan yang kolom `tahun`-nya belum terisi lewat
        // tanggal mulainya — lihat Kegiatan::daftarTahun().
        $tahunList = Kegiatan::daftarTahun(Kegiatan::STATUS_SELESAI);
        // Opsi "Belum ada tanggal" hanya dirender bila memang ada isinya.
        $adaTanpaTahun = Kegiatan::where('status', Kegiatan::STATUS_SELESAI)->tanpaTahun()->exists();

        $query = Kegiatan::with(['bidang', 'bidangs', 'kategoriKegiatan', 'kategoris', 'ketuaPelaksana.user', 'dosenPendampings.user'])
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->orderBy('created_at', 'desc');

        // Filter by bidang or prodi
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                // Kegiatan Prodi = tanpa bidang ATAU berkategori Prodi
                // (selaras dengan filter Prodi di Rencana Proker & Pelaksanaan)
                $query->where(function ($q) {
                    $q->whereDoesntHave('bidangs')
                      ->orWhereHas('kategoris', fn($k) => $k->where('nama_kategori', 'like', '%Prodi%'));
                });
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }

        // Filter by kategori — daftar kategori sudah lama dikirim ke view tapi tidak
        // pernah dipakai menyaring apa pun, padahal badge kategorinya tampil di
        // setiap kartu sehingga user wajar mengira bisa disaring lewat itu.
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->whereHas('kategoris', fn($k) => $k->where('mk_kategori_kegiatan.id', $request->kategori));
        }

        // Filter by tahun — pakai scope supaya kegiatan ber-`tahun` NULL tetap
        // ketemu lewat tahun pada tanggal mulainya, bukan hilang dari daftar.
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            if ($request->tahun === Kegiatan::FILTER_TANPA_TAHUN) {
                $query->tanpaTahun();
            } else {
                $query->filterTahun($request->tahun);
            }
        }

        // Search by judul + deskripsi
        // Dibungkus closure supaya orWhere tidak "membocorkan" filter bidang/tahun di atas.
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('judul', 'like', $term)
                  ->orWhere('deskripsi', 'like', $term);
            });
        }

        $kegiatan = $query->paginate(12);

        // Cek apakah user adalah admin/pengurus (untuk tombol Tambah)
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        // GPM, Kadep & DPM view-only — tidak masuk daftar pengelola (hanya bisa lihat)
        $isAdmin = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        // Tombol "Tambah Kegiatan" (create langsung ke arsip) hanya untuk role kurasi admin
        // di luar alur himpunan — role himpunan wajib lewat Proker → Pelaksanaan → publish.
        // DPM = pembina view-only, tidak menambah kegiatan.
        $canTambahKegiatan = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
        ])->isNotEmpty();

        return view('manajemenmahasiswa::kegiatan.index', compact(
            'kegiatan',
            'bidangList',
            'tahunList',
            'adaTanpaTahun',
            'kategoriList',
            'isAdmin',
            'canTambahKegiatan',
        ));
    }

    /**
     * Detail kegiatan — halaman lengkap.
     */
    public function show($id)
    {
        $kegiatan = Kegiatan::with([
            'bidang',
            'bidangs',
            'kategoriKegiatan',
            'kategoris',
            'repoMulmed',
            'ketuaPelaksana.user',
            'dosenPendampings.user',
            'panitia.user',
        ])->find($id);

        if ($tolak = $this->tolakBilaBukanArsip($kegiatan)) {
            return $tolak;
        }

        // Tombol Edit/Hapus: role pengelola (sinkron dengan route edit/destroy) DAN
        // memang pengelola kegiatan ini (KegiatanPolicy). GPM, Kadep & DPM view-only.
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        $roleKelola = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        $canEdit   = $roleKelola && Gate::allows('update', $kegiatan);
        $canDelete = $roleKelola && Gate::allows('delete', $kegiatan);
        $pesanBukanPengelola = $roleKelola && !$canEdit
            ? $this->pengelolaService->pesanTolak($kegiatan)
            : null;

        return view('manajemenmahasiswa::kegiatan.show', compact('kegiatan', 'canEdit', 'canDelete', 'pesanBukanPengelola'));
    }

    /**
     * Form buat kegiatan baru.
     * Akses: admin_kemahasiswaan, superadmin (role kurasi di luar alur himpunan; DPM view-only).
     */
    public function create()
    {
        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList    = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList        = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        // Catatan: tidak ada $tahunList. Form ini tidak punya kolom Tahun — tahun
        // diturunkan dari tanggal mulai saat menyimpan.
        return view('manajemenmahasiswa::kegiatan.create', compact(
            'bidangList',
            'kategoriList',
            'mahasiswaList',
            'dosenList',
        ) + $this->pengelolaService->dataForm(new Kegiatan()));
    }

    /**
     * Simpan kegiatan baru.
     */
    public function store(Request $request)
    {
        $jam = $this->aturanJam($request);

        $validated = $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'required|string|min:20',
            'kategori_kegiatan_id'=> 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $this->aturanBidang($request),
            'bidang_id.*'         => 'exists:mk_bidang,id',
            'tahun'               => 'nullable|integer|min:2008',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => $jam['mulai'],
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => $jam['selesai'],
            'lokasi'              => 'nullable|string|max:255',
            // Banner wajib diisi saat menambah kegiatan langsung ke Laporan & Arsip (subbab 3).
            'banner'              => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'anggaran'            => 'nullable|numeric|min:0|max:9999999999999',
            'ketua_pelaksana_id'  => 'nullable|exists:students,id',
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'panitia_ids'         => 'nullable|array',
            'panitia_ids.*'       => 'exists:students,id',
            'panitia_peran'       => 'nullable|array',
            'panitia_peran.*'     => 'nullable|string|max:255',
            'target_peserta'      => 'nullable|integer|min:1',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'    => 'nullable|array|max:10',
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], $this->pesanValidasi());

        $validated['status'] = 'selesai';

        // Tahun diturunkan dari tanggal mulai bila tidak diisi eksplisit, agar kegiatan
        // muncul di dropdown & filter Tahun pada Laporan & Arsip (selaras alur Pelaksanaan).
        if (empty($validated['tahun']) && !empty($validated['tanggal_mulai'])) {
            $validated['tahun'] = \Carbon\Carbon::parse($validated['tanggal_mulai'])->year;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        $validated['user_id'] = Auth::id();

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $validated['penanggung_jawab'] = $student?->user?->name;
        }

        // Set backward-compat FK columns (first item)
        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds = $validated['bidang_id'] ?? [];
        $panitiaIds = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $id) {
            $panitiaSyncData[$id] = ['peran' => $panitiaPeran[$id] ?? null];
        }
        $dosenPendampingIds = $validated['dosen_pendamping_ids'] ?? [];

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id'] = $bidangIds[0] ?? null;

        // Remove non-kegiatan fields before creating
        unset($validated['foto_kegiatan'], $validated['dokumen_kegiatan'], $validated['panitia_ids'], $validated['panitia_peran'], $validated['dosen_pendamping_ids']);

        $kegiatan = Kegiatan::create($validated);

        // Sync pivot tables
        $kegiatan->kategoris()->sync($kategoriIds);
        $kegiatan->bidangs()->sync($bidangIds);
        $kegiatan->panitia()->sync($panitiaSyncData);
        $kegiatan->dosenPendampings()->sync($dosenPendampingIds);
        $this->pengelolaService->sync($kegiatan, $request);

        // Handle foto uploads
        $this->handleFileUploads($request, $kegiatan);

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Form edit kegiatan.
     */
    public function edit($id)
    {
        $kegiatan = Kegiatan::with(['repoMulmed', 'kategoris', 'bidangs', 'panitia.user', 'dosenPendampings.user'])
            ->find($id);

        if ($tolak = $this->tolakBilaBukanArsip($kegiatan)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($kegiatan)) {
            return $tolak;
        }

        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList    = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList        = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        // Catatan: tidak ada $tahunList. Form ini tidak punya kolom Tahun — tahun
        // diturunkan dari tanggal mulai saat menyimpan.
        return view('manajemenmahasiswa::kegiatan.edit', compact(
            'kegiatan',
            'bidangList',
            'kategoriList',
            'mahasiswaList',
            'dosenList',
        ) + $this->pengelolaService->dataForm($kegiatan));
    }

    /**
     * Update kegiatan.
     */
    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::find($id);

        if ($tolak = $this->tolakBilaBukanArsip($kegiatan)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($kegiatan)) {
            return $tolak;
        }

        $jam = $this->aturanJam($request);

        // Check if all selected kategori are "Kegiatan Prodi" (bidang not needed)
        $validated = $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'required|string|min:20',
            'kategori_kegiatan_id'=> 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $this->aturanBidang($request),
            'bidang_id.*'         => 'exists:mk_bidang,id',
            'tahun'               => 'nullable|integer|min:2008',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => $jam['mulai'],
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => $jam['selesai'],
            'lokasi'              => 'nullable|string|max:255',
            // Banner wajib ada: kalau kegiatan belum punya banner, unggahan baru diwajibkan;
            // kalau sudah punya, boleh dikosongkan (banner lama dipertahankan).
            'banner'              => ($kegiatan->banner ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png,webp|max:10240',
            'anggaran'            => 'nullable|numeric|min:0|max:9999999999999',
            'ketua_pelaksana_id'  => 'nullable|exists:students,id',
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'panitia_ids'         => 'nullable|array',
            'panitia_ids.*'       => 'exists:students,id',
            'panitia_peran'       => 'nullable|array',
            'panitia_peran.*'     => 'nullable|string|max:255',
            'target_peserta'      => 'nullable|integer|min:1',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'    => 'nullable|array|max:10',
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'hapus_file'          => 'nullable|array',
            'hapus_file.*'        => 'integer|exists:mk_repo_mulmed,id',
        ], $this->pesanValidasi());

        // Batas 10 file berlaku untuk TOTAL yang tersimpan, bukan per sekali simpan.
        $this->pastikanKuotaFile($request, $kegiatan);

        $validated['status'] = 'selesai';

        // Tahun diturunkan dari tanggal mulai bila tidak diisi eksplisit, agar kegiatan
        // muncul di dropdown & filter Tahun pada Laporan & Arsip (selaras alur Pelaksanaan).
        if (empty($validated['tahun']) && !empty($validated['tanggal_mulai'])) {
            $validated['tahun'] = \Carbon\Carbon::parse($validated['tanggal_mulai'])->year;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($kegiatan->banner) {
                $this->supabase->delete($kegiatan->banner);
            }
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $validated['penanggung_jawab'] = $student?->user?->name;
        } else {
            $validated['penanggung_jawab'] = null;
        }

        // Handle file deletions
        if ($request->filled('hapus_file')) {
            foreach ($request->hapus_file as $fileId) {
                $file = RepoMulmed::where('kegiatan_id', $kegiatan->id)->find($fileId);
                if ($file) {
                    $this->repoMulmedService->deletePermanent($file->id);
                }
            }
        }

        // Set backward-compat FK columns (first item)
        $kategoriIds = $validated['kategori_kegiatan_id'];
        $bidangIds = $validated['bidang_id'] ?? [];
        $panitiaIds = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $id) {
            $panitiaSyncData[$id] = ['peran' => $panitiaPeran[$id] ?? null];
        }

        $dosenPendampingIds = $validated['dosen_pendamping_ids'] ?? [];

        $validated['kategori_kegiatan_id'] = $kategoriIds[0] ?? null;
        $validated['bidang_id'] = $bidangIds[0] ?? null;

        // Remove non-kegiatan fields before updating
        unset($validated['foto_kegiatan'], $validated['dokumen_kegiatan'], $validated['hapus_file'], $validated['panitia_ids'], $validated['panitia_peran'], $validated['dosen_pendamping_ids']);

        $kegiatan->update($validated);

        // Sync pivot tables
        $kegiatan->kategoris()->sync($kategoriIds);
        $kegiatan->bidangs()->sync($bidangIds);
        $kegiatan->panitia()->sync($panitiaSyncData);
        $kegiatan->dosenPendampings()->sync($dosenPendampingIds);
        $this->pengelolaService->sync($kegiatan, $request);

        // Handle new file uploads
        $this->handleFileUploads($request, $kegiatan);

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan.
     */
    public function destroy($id)
    {
        $kegiatan = Kegiatan::with('repoMulmed')->find($id);

        if ($tolak = $this->tolakBilaBukanArsip($kegiatan)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($kegiatan, 'delete')) {
            return $tolak;
        }

        if ($kegiatan->banner) {
            $this->supabase->delete($kegiatan->banner);
        }

        // Delete all associated files
        foreach ($kegiatan->repoMulmed as $file) {
            $this->repoMulmedService->deletePermanent($file->id);
        }

        $kegiatan->delete();

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Kegiatan hanya bisa dibuka/diubah dari sini selama benar-benar ada di arsip.
     *
     * Dicari dengan find() lalu diperiksa di sini, bukan findOrFail() yang dibatasi
     * status. Dengan pola lama, membuka detail kegiatan yang belum diarsipkan,
     * menyimpan form Edit setelah kegiatannya dihapus orang lain, atau menekan
     * Hapus dua kali lewat tombol Back, semuanya berakhir di halaman error tanpa
     * keterangan apa pun.
     *
     * Sengaja mengarahkan ke daftar Arsip, bukan ke Rencana Proker / Pelaksanaan:
     * halaman ini terbuka untuk semua peran termasuk mahasiswa, yang justru tidak
     * punya akses ke dua subbab tersebut dan akan berakhir di halaman 403.
     *
     * @return RedirectResponse|null null bila kegiatan valid diproses di sini
     */
    private function tolakBilaBukanArsip(?Kegiatan $kegiatan): ?RedirectResponse
    {
        if ($kegiatan && $kegiatan->status === Kegiatan::STATUS_SELESAI) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('error', $kegiatan === null
                ? 'Kegiatan yang Anda buka sudah tidak ada — kemungkinan sudah dihapus lebih dulu.'
                : 'Kegiatan ini belum diarsipkan, jadi belum tersedia di Laporan & Arsip.');
    }

    /**
     * Role saja tidak cukup: kegiatan hanya boleh diubah/dihapus pemiliknya,
     * pengelola yang ia tunjuk, atau override (lihat KegiatanPolicy). Uji T-1
     * (10 Sep 2026) membuktikan ketua bidang mana pun sempat bisa mengubah dan
     * menghapus arsip milik bidang lain lewat URL langsung.
     *
     * @param string $aksi 'update' atau 'delete'
     * @return RedirectResponse|null null bila boleh
     */
    private function tolakBilaBukanPengelola(Kegiatan $kegiatan, string $aksi = 'update'): ?RedirectResponse
    {
        if (Gate::allows($aksi, $kegiatan)) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.show', $kegiatan->id)
            ->with('error', $this->pengelolaService->pesanTolak($kegiatan));
    }

    /**
     * Aturan Bidang, dipakai bersama store() & update().
     *
     * Bidang wajib kecuali seluruh kategori yang dipilih berkategori Prodi —
     * aturan yang sama persis dengan Rencana Proker & Pelaksanaan. Sebelumnya di
     * subbab ini `bidang_id` selalu nullable, sehingga kegiatan "Kegiatan
     * Himpunan" bisa disimpan tanpa bidang lalu tampil berlabel "Prodi" di kartu
     * & detailnya, padahal kolomnya sudah ditandai wajib dengan bintang merah.
     */
    private function aturanBidang(Request $request): string
    {
        $kategoriDipilih = $request->input('kategori_kegiatan_id', []);
        $isOnlyProdi = is_array($kategoriDipilih) && Kegiatan::hanyaKategoriProdi($kategoriDipilih);

        return $isOnlyProdi ? 'nullable|array' : 'required|array|min:1';
    }

    /**
     * Batas foto & dokumen berlaku untuk TOTAL file yang tersimpan pada kegiatan.
     *
     * Kembaran PelaksanaanController::pastikanKuotaFile() — aturan `max:10` di
     * validasi hanya membatasi satu kali unggah, sehingga user bisa mengunggah 10
     * foto → Simpan → buka Edit → unggah 10 lagi, berulang tanpa batas.
     *
     * @throws ValidationException
     */
    private function pastikanKuotaFile(Request $request, Kegiatan $kegiatan): void
    {
        $akanDihapus = collect($request->input('hapus_file', []))->map(fn($fileId) => (int) $fileId);

        $sisaFileLama = fn(string $tipe) => $kegiatan->repoMulmed
            ->where('tipe_file', $tipe)
            ->reject(fn($file) => $akanDihapus->contains($file->id))
            ->count();

        $pesan = [];

        $totalFoto = $sisaFileLama('image') + count($request->file('foto_kegiatan', []));
        if ($totalFoto > self::MAKS_FILE) {
            $pesan['foto_kegiatan'] = 'Total foto kegiatan maksimal ' . self::MAKS_FILE
                . ", sedangkan unggahan ini membuatnya menjadi {$totalFoto}. Hapus dulu sebagian foto lama.";
        }

        $totalDokumen = $sisaFileLama('document') + count($request->file('dokumen_kegiatan', []));
        if ($totalDokumen > self::MAKS_FILE) {
            $pesan['dokumen_kegiatan'] = 'Total dokumen kegiatan maksimal ' . self::MAKS_FILE
                . ", sedangkan unggahan ini membuatnya menjadi {$totalDokumen}. Hapus dulu sebagian dokumen lama.";
        }

        if ($pesan) {
            throw ValidationException::withMessages($pesan);
        }
    }

    /**
     * Aturan validasi jam, dipakai bersama store() & update().
     *
     * Jam selesai dibandingkan dengan jam mulai HANYA bila kegiatan berlangsung
     * dalam satu hari — kegiatan lintas hari wajar berakhir di jam yang lebih awal.
     * Format H:i:s ikut diterima karena sebagian browser mengirim detik.
     *
     * @return array{mulai: array<int, string>, selesai: array<int, string>}
     */
    private function aturanJam(Request $request): array
    {
        $satuHari = !$request->filled('tanggal_selesai')
            || $request->input('tanggal_selesai') === $request->input('tanggal_mulai');

        $selesai = ['nullable', 'date_format:H:i,H:i:s'];
        if ($satuHari && $request->filled('jam_mulai')) {
            $selesai[] = 'after:jam_mulai';
        }

        return [
            'mulai'   => ['nullable', 'date_format:H:i,H:i:s'],
            'selesai' => $selesai,
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia, dipakai bersama store() & update().
     *
     * @return array<string, string>
     */
    private function pesanValidasi(): array
    {
        return [
            'banner.required'                => 'Banner kegiatan wajib diunggah.',
            'bidang_id.required'             => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'bidang_id.min'                  => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
        ];
    }

    /**
     * Handle upload foto dan dokumen kegiatan ke repo_mulmed.
     */
    private function handleFileUploads(Request $request, Kegiatan $kegiatan): void
    {
        // Upload foto kegiatan
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $this->repoMulmedService->upload($foto, [
                    'kegiatan_id'       => $kegiatan->id,
                    'judul_file'        => pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }

        // Upload dokumen kegiatan
        if ($request->hasFile('dokumen_kegiatan')) {
            foreach ($request->file('dokumen_kegiatan') as $doc) {
                $this->repoMulmedService->upload($doc, [
                    'kegiatan_id'       => $kegiatan->id,
                    'judul_file'        => pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }
    }
}
