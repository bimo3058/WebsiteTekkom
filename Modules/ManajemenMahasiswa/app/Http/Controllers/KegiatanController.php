<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\KegiatanPeserta;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;
use Modules\ManajemenMahasiswa\Services\RencanaProkerService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;
use Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan;
use Modules\ManajemenMahasiswa\Support\PerPage;

class KegiatanController extends Controller
{
    /**
     * Batas jumlah foto & dokumen yang boleh tersimpan pada satu kegiatan.
     *
     * Dokumen = 10. Tiap proker memuat Proposal, RAB, notulensi acara, dan LPJ, ditambah
     * surat undangan yang bisa lebih dari satu (satu surat per dosen/tamu) dan proposal
     * MoU bila kerja sama sponsor — jadi jumlah file = 4 + undangan (+ MoU), bukan 5 tetap
     * (keterangan Ketua Bidang Ristek, 26 Okt 2026). Maksimal undangan yang pernah dibuat
     * belum diketahui; 10 memberi ruang sampai 5 undangan + MoU. Public karena angkanya
     * juga dipakai form (label & JavaScript) dan PelaksanaanController — satu sumber
     * supaya batas di server dan di layar tidak pernah berbeda.
     */
    private const MAKS_FILE = 10;
    public const MAKS_DOKUMEN = 10;

    /**
     * Batas ukuran SATU dokumen dalam KB. LPJ asli HIMASKOM (Recreation 2025) berukuran
     * 9,5 MB karena memuat foto dokumentasi, nota, dan daftar hadir hasil pindai, sehingga
     * batas lama 5 MB menolaknya. 10 MB = batas unggah modul (CheckModuleActive,
     * `max_upload` bawaan). Banner & foto tetap 5 MB.
     */
    public const MAKS_UKURAN_DOKUMEN_KB = 10240;

    /**
     * Panjang maksimal judul kegiatan. Nama resmi di proposal HIMASKOM berpola
     * "<nama acara> Himpunan Mahasiswa Teknik Komputer Fakultas Teknik Universitas
     * Diponegoro <tahun>." — akhirannya saja ±80 karakter, dan proposal Recreation 2025
     * tepat 100 karakter, sehingga nama acara yang sedikit lebih panjang ditolak batas
     * lama 100. Kolom `judul` bertipe varchar(255). Dipakai ketiga subbab & form.
     */
    public const MAKS_JUDUL = 150;

    /**
     * Pesan validasi dokumen berbahasa Indonesia — dipakai juga Rencana Proker &
     * Pelaksanaan supaya ketiga form menolak dengan kalimat yang sama.
     *
     * @return array<string, string>
     */
    public static function pesanValidasiDokumen(): array
    {
        return [
            'dokumen_kegiatan.max'     => 'Paling banyak ' . self::MAKS_DOKUMEN . ' dokumen untuk satu kegiatan.',
            'dokumen_kegiatan.*.max'   => 'Ukuran tiap dokumen maksimal ' . intdiv(self::MAKS_UKURAN_DOKUMEN_KB, 1024) . ' MB.',
            'dokumen_kegiatan.*.mimes' => 'Dokumen harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, atau PPTX.',
        ];
    }

    public function __construct(
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService,
        private RencanaProkerService $rencanaProkerService
    ) {}

    /**
     * Halaman utama daftar kegiatan — mahasiswa & alumni.
     */
    public function index(Request $request)
    {
        $bidangList       = Bidang::orderBy('nama_bidang')->get();

        // Ikut menghitung kegiatan yang kolom `tahun`-nya belum terisi lewat
        // tanggal mulainya — lihat Kegiatan::daftarTahun().
        $tahunList = Kegiatan::daftarTahun(Kegiatan::STATUS_SELESAI);
        // Opsi "Belum ada tanggal" hanya dirender bila memang ada isinya.
        $adaTanpaTahun = Kegiatan::where('status', Kegiatan::STATUS_SELESAI)->tanpaTahun()->exists();
        $periodeList   = Kegiatan::daftarPeriode(Kegiatan::STATUS_SELESAI);

        // Relasi yang dibutuhkan kartu bersama (kegiatan-list/_kartu) — sama di ketiga subbab.
        $query = Kegiatan::with(['bidang', 'bidangs', 'kategoriKegiatan', 'kategoris', 'dosenPendampings.user'])
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

        // Tidak ada filter kategori: "Kegiatan Himpunan"/"Kegiatan Prodi" sudah terwakili
        // filter bidang di atas (bidang "prodi" mencakup kegiatan berkategori Prodi).

        // Filter by tahun — pakai scope supaya kegiatan ber-`tahun` NULL tetap
        // ketemu lewat tahun pada tanggal mulainya, bukan hilang dari daftar.
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            if ($request->tahun === Kegiatan::FILTER_TANPA_TAHUN) {
                $query->tanpaTahun();
            } else {
                $query->filterTahun($request->tahun);
            }
        }

        // Filter periode kepengurusan (S-62) — sama dengan Rencana Proker & Pelaksanaan.
        if (PeriodeKepengurusan::ada($request->periode)) {
            $query->filterPeriode((int) $request->periode);
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

        $kegiatan = $query->paginate(PerPage::resolve($request, PerPage::KARTU, 12));

        // Cek apakah user adalah admin/pengurus (untuk tombol Tambah)
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        // GPM, Kadep & DPM view-only — tidak masuk daftar pengelola (hanya bisa lihat)
        $isAdmin = $roles->intersect([
            'superadmin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        // Tombol "Tambah Kegiatan" (create langsung ke arsip) hanya untuk role kurasi admin
        // di luar alur himpunan — role himpunan wajib lewat Proker → Pelaksanaan → publish.
        // DPM = pembina view-only, tidak menambah kegiatan.
        $canTambahKegiatan = $roles->intersect([
            'superadmin', 'admin_kemahasiswaan',
        ])->isNotEmpty();

        return view('manajemenmahasiswa::kegiatan.index', compact(
            'kegiatan',
            'bidangList',
            'tahunList',
            'adaTanpaTahun',
            'periodeList',
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
            'superadmin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        $canEdit   = $roleKelola && Gate::allows('update', $kegiatan);
        $canDelete = $roleKelola && Gate::allows('delete', $kegiatan);
        $pesanBukanPengelola = $roleKelola && !$canEdit
            ? $this->pengelolaService->pesanTolak($kegiatan)
            : null;

        // Badan halaman memakai partial bersama Rencana Proker & Pelaksanaan
        // (kegiatan-detail/_body), jadi variabelnya disiapkan dengan cara yang sama
        // seperti PelaksanaanController::show(). Anggaran & dokumen hanya untuk role
        // pengelola & pemantau (KegiatanPolicy::LIHAT_INTERNAL).
        $canViewRestricted = $user->hasAnyRole(KegiatanPolicy::LIHAT_INTERNAL);
        $images    = $kegiatan->repoMulmed->where('tipe_file', 'image');
        $documents = $kegiatan->repoMulmed->where('tipe_file', 'document');

        // Tab Rencana vs Pelaksanaan (S-70). Rencana Proker internal seperti anggaran,
        // jadi ikut LIHAT_INTERNAL. Null = tanpa tab (mahasiswa, kegiatan tanpa rencana).
        $banding = $canViewRestricted ? $this->rencanaProkerService->bandingkan($kegiatan) : null;

        return view('manajemenmahasiswa::kegiatan.show', compact(
            'kegiatan', 'canEdit', 'canDelete', 'pesanBukanPengelola',
            'canViewRestricted', 'images', 'documents', 'banding'
        ));
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
            'judul'               => 'required|string|max:' . self::MAKS_JUDUL,
            'deskripsi'           => 'required|string|min:20|max:3000',
            'kategori_kegiatan_id'=> 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $this->aturanBidang($request),
            'bidang_id.*'         => 'exists:mk_bidang,id',
            'tahun'               => 'nullable|integer|min:2008',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => $jam['mulai'],
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => $jam['selesai'],
            'lokasi'              => 'nullable|string|max:150',
            // Banner wajib diisi saat menambah kegiatan langsung ke Laporan & Arsip (subbab 3).
            'banner'              => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'anggaran'            => 'nullable|numeric|min:0|max:9999999999999',
            'ketua_pelaksana_id'  => ['nullable', 'exists:students,id', KegiatanPolicy::aturanKetuaPelaksana()],
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'dosen_peran'            => 'nullable|array',
            'dosen_peran.*'          => 'nullable|string|max:60',
            'panitia_ids'         => 'nullable|array',
            'panitia_ids.*'       => 'exists:students,id',
            'panitia_peran'       => 'nullable|array',
            'panitia_peran.*'     => 'nullable|string|max:60',
            'target_peserta'      => 'nullable|integer|min:1',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'    => 'nullable|array|max:' . self::MAKS_DOKUMEN,
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:' . self::MAKS_UKURAN_DOKUMEN_KB,
        ], $this->pesanValidasi());
        $this->repoMulmedService->pastikanJenisDokumen($request, null, false);

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
        $kegiatan->syncDosenPendamping($dosenPendampingIds, $request->input('dosen_peran', []));
        $this->pengelolaService->sync($kegiatan, $request);

        // Handle foto uploads
        $this->repoMulmedService->simpanUnggahan($request, $kegiatan);

        // Ke halaman detail, sama dengan simpan di Rencana Proker & Pelaksanaan.
        return redirect()
            ->route('manajemenmahasiswa.kegiatan.show', $kegiatan->id)
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

        // Banner salinan Rencana Proker terkunci juga di Arsip (S-61).
        $this->repoMulmedService->tolakUbahBannerRencana($request, $kegiatan);

        $jam = $this->aturanJam($request);

        // Check if all selected kategori are "Kegiatan Prodi" (bidang not needed)
        $validated = $request->validate([
            'judul'               => 'required|string|max:' . self::MAKS_JUDUL,
            'deskripsi'           => 'required|string|min:20|max:3000',
            'kategori_kegiatan_id'=> 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $this->aturanBidang($request),
            'bidang_id.*'         => 'exists:mk_bidang,id',
            'tahun'               => 'nullable|integer|min:2008',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => $jam['mulai'],
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => $jam['selesai'],
            'lokasi'              => 'nullable|string|max:150',
            // Banner wajib ada: kalau kegiatan belum punya banner, atau banner lamanya
            // dihapus lewat tombol Hapus, unggahan baru diwajibkan; selain itu boleh
            // dikosongkan (banner lama dipertahankan).
            'banner'              => ($kegiatan->banner && !$request->boolean('hapus_banner') ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png,webp|max:5120',
            'anggaran'            => 'nullable|numeric|min:0|max:9999999999999',
            'ketua_pelaksana_id'  => ['nullable', 'exists:students,id', KegiatanPolicy::aturanKetuaPelaksana($kegiatan)],
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'dosen_peran'            => 'nullable|array',
            'dosen_peran.*'          => 'nullable|string|max:60',
            'panitia_ids'         => 'nullable|array',
            'panitia_ids.*'       => 'exists:students,id',
            'panitia_peran'       => 'nullable|array',
            'panitia_peran.*'     => 'nullable|string|max:60',
            'target_peserta'      => 'nullable|integer|min:1',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'    => 'nullable|array|max:' . self::MAKS_DOKUMEN,
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:' . self::MAKS_UKURAN_DOKUMEN_KB,
            'hapus_file'          => 'nullable|array',
            'hapus_file.*'        => 'integer|exists:mk_repo_mulmed,id',
        ], $this->pesanValidasi());

        // Batas 10 file berlaku untuk TOTAL yang tersimpan, bukan per sekali simpan.
        // Dokumen salinan Rencana Proker terkunci juga di Arsip (S-60); yang diunggah
        // di Pelaksanaan atau di sini tetap bisa dihapus/diganti jenisnya.
        $this->repoMulmedService->tolakHapusDokumenRencana($request, $kegiatan);
        $this->repoMulmedService->pastikanKuota($request, $kegiatan, self::MAKS_FILE, self::MAKS_DOKUMEN);
        $this->repoMulmedService->pastikanJenisDokumen($request, $kegiatan, false, true);

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

        $this->repoMulmedService->hapusTerpilih($request, $kegiatan);
        $this->repoMulmedService->perbaruiJenisDokumen($request, $kegiatan, true);

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
        $kegiatan->syncDosenPendamping($dosenPendampingIds, $request->input('dosen_peran', []));
        $this->pengelolaService->sync($kegiatan, $request);

        // Handle new file uploads
        $this->repoMulmedService->simpanUnggahan($request, $kegiatan);

        // Ke halaman detail, sama dengan simpan di Rencana Proker & Pelaksanaan.
        return redirect()
            ->route('manajemenmahasiswa.kegiatan.show', $kegiatan->id)
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
        // Surat proker dari alur tanda tangan lama — ikut dihapus seperti di Rencana
        // Proker & Pelaksanaan supaya tidak tertinggal sebagai file yatim di storage.
        if ($kegiatan->surat_proker) {
            $this->supabase->delete($kegiatan->surat_proker);
        }

        // Delete all associated files
        foreach ($kegiatan->repoMulmed as $file) {
            $this->repoMulmedService->deletePermanent($file->id);
        }

        // Salinan hasil Ajukan Proker (S-49): rencana & dokumennya berupa baris dan file
        // terpisah, jadi tidak ikut terhapus.
        $punyaRencana = (bool) $kegiatan->rencana_id;
        $kegiatan->delete();

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', $punyaRencana
                ? 'Kegiatan berhasil dihapus. Rencana awalnya tetap tersimpan di Rencana Proker.'
                : 'Kegiatan berhasil dihapus.');
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
     *
     * Paling banyak SATU bidang: satu proker dipegang satu bidang himpunan.
     * Input tetap berbentuk array (`bidang_id[]`) supaya pivot mk_kegiatan_bidang
     * dan kolom lama `bidang_id` tetap terisi lewat alur yang sama.
     */
    private function aturanBidang(Request $request): string
    {
        $kategoriDipilih = $request->input('kategori_kegiatan_id', []);
        $isOnlyProdi = is_array($kategoriDipilih) && Kegiatan::hanyaKategoriProdi($kategoriDipilih);

        return $isOnlyProdi ? 'nullable|array|max:1' : 'required|array|min:1|max:1';
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
            'bidang_id.required'             => 'Bidang pemegang kegiatan wajib dipilih, kecuali kegiatan ini murni Kegiatan Prodi.',
            'bidang_id.min'                  => 'Bidang pemegang kegiatan wajib dipilih, kecuali kegiatan ini murni Kegiatan Prodi.',
            'bidang_id.max'                  => 'Pilih satu bidang saja — satu kegiatan dipegang satu bidang himpunan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
            'ketua_pelaksana_id.in'          => KegiatanPolicy::PESAN_KETUA_PELAKSANA,
        ] + self::pesanValidasiDokumen();
    }
}
