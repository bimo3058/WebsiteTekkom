<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;
use Modules\ManajemenMahasiswa\Services\TinjauanKegiatanService;
use Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan;
use Modules\ManajemenMahasiswa\Support\PerPage;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;
use Modules\ManajemenMahasiswa\Support\SumberDana;
use Modules\ManajemenMahasiswa\Services\JadwalRuanganService;

class PelaksanaanController extends Controller
{
    /** Batas jumlah foto & dokumen yang boleh tersimpan pada satu kegiatan. */
    private const MAKS_FILE = 10;
    /** Sama dengan form Laporan & Arsip — diatur di KegiatanController::MAKS_DOKUMEN. */
    private const MAKS_DOKUMEN = KegiatanController::MAKS_DOKUMEN;

    /**
     * Peninjau (S-82): menyetujui kiriman ketua pelaksana lewat "Setujui & Unggah ke
     * Arsip", mengembalikan dengan catatan, mengganti ketua pelaksana, menghapus.
     * Kegiatan MANA diputuskan KegiatanPolicy::tinjau/delete. Sinkron dengan route.
     */
    private const ROLE_TINJAU = [
        'superadmin', 'admin_kemahasiswaan',
        ...RoleHimpunan::INTI, ...RoleHimpunan::PIMPINAN_BIDANG,
    ];

    /** Membuka form edit (S-82): ketua pelaksana & Akses Kelola, serta admin. */
    private const ROLE_EDIT = ['superadmin', 'admin_kemahasiswaan', RoleHimpunan::STAFF];

    public function __construct(
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService,
        private TinjauanKegiatanService $tinjauan
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar proker yang siap/sedang/sudah dilaksanakan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        // KAHIM/WAKAHIM & admin melihat semua bidang/unit; KABID, WAKABID, KANIT,
        // WAKANIT & Staff hanya bidang/unitnya (Kegiatan::scopeTerlihatOleh), termasuk
        // isi dropdown filter.
        $bidangTerkunci = RoleHimpunan::terbatasBidang($user);
        $bidangList     = RoleHimpunan::daftarBidangUntuk($user);
        // Ikut menghitung kegiatan yang kolom `tahun`-nya belum terisi lewat
        // tanggal mulainya — lihat Kegiatan::daftarTahun().
        $tahunList = Kegiatan::daftarTahun(Kegiatan::STATUS_DISETUJUI, $user);
        // Opsi "Belum ada tanggal" hanya dirender bila memang ada isinya (proker lama
        // yang diajukan sebelum tanggal mulai jadi syarat Ajukan Proker).
        $adaTanpaTahun = Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->terlihatOleh($user)->tanpaTahun()->exists();
        $periodeList   = Kegiatan::daftarPeriode(Kegiatan::STATUS_DISETUJUI, $user);

        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', ...RoleHimpunan::POSISI])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only

        // Relasi yang dibutuhkan kartu bersama (kegiatan-list/_kartu) — sama di ketiga subbab.
        $query = Kegiatan::with(['bidang', 'bidangs', 'kategoriKegiatan', 'kategoris', 'dosenPendampings.user'])
            ->where('status', Kegiatan::STATUS_DISETUJUI)
            ->terlihatOleh($user)
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

        // Filter tahun — pakai scope supaya kegiatan ber-`tahun` NULL tetap
        // ketemu lewat tahun pada tanggal mulainya, bukan hilang dari daftar.
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            if ($request->tahun === Kegiatan::FILTER_TANPA_TAHUN) {
                $query->tanpaTahun();
            } else {
                $query->filterTahun($request->tahun);
            }
        }

        // Filter periode kepengurusan (S-62) — sama dengan Rencana Proker & Laporan/Arsip.
        if (PeriodeKepengurusan::ada($request->periode)) {
            $query->filterPeriode((int) $request->periode);
        }

        // Filter rentang tanggal (S-81) — sama dengan Rencana Proker & Laporan/Arsip.
        [$dari, $sampai] = Kegiatan::rentangDariRequest($request);
        if ($dari || $sampai) {
            $query->filterRentang($dari, $sampai);
        }

        // Search judul + deskripsi
        // Dibungkus closure supaya orWhere tidak "membocorkan" filter bidang/tahun di atas.
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('judul', 'like', $term)
                  ->orWhere('deskripsi', 'like', $term);
            });
        }

        // Filter status tinjauan (S-82) — juga tujuan tautan lonceng notifikasi.
        // Tanpa filter ini (S-86): satu baris per status, maks 4 kartu, plus "View all →".
        $tinjauan        = (string) $request->input('tinjauan');
        // Peninjau: "Menunggu Tinjauan" di atas; staff/ketua pelaksana: "Menunggu Dilengkapi".
        $peninjauTinjau  = $roles->intersect(self::ROLE_TINJAU)->isNotEmpty();
        $urutanTinjauan  = Kegiatan::urutkanUntukPeninjau(Kegiatan::TINJAUAN_LABELS, $peninjauTinjau);
        $pelaksanaanList = null;
        $kelompok        = null;
        $statusAktif     = null;

        if (array_key_exists($tinjauan, Kegiatan::TINJAUAN_LABELS)) {
            $statusAktif     = ['kunci' => $tinjauan, 'label' => Kegiatan::TINJAUAN_LABELS[$tinjauan]];
            $pelaksanaanList = $query->tinjauan($tinjauan)
                ->paginate(PerPage::resolve($request, PerPage::KARTU, 12));
        } else {
            $kelompok = [];
            foreach ($urutanTinjauan as $kunci => $label) {
                $q = (clone $query)->tinjauan($kunci);
                $kelompok[$kunci] = [
                    'label' => $label,
                    'total' => (clone $q)->count(),
                    'items' => $q->take(4)->get(),
                ];
            }
        }

        return view('manajemenmahasiswa::pelaksanaan.index', compact(
            'pelaksanaanList', 'kelompok', 'statusAktif', 'bidangList', 'tahunList', 'adaTanpaTahun', 'periodeList',
            'isAdmin', 'isPengurus', 'canManage', 'bidangTerkunci', 'peninjauTinjau'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator',
            'repoMulmed',
        ])->find($id);

        // 'selesai' tetap boleh DILIHAT agar halaman detail masih bisa dibuka
        // setelah diarsipkan — berbeda dari edit/update yang dibatasi 'disetujui'.
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_SELESAI,
        ])) {
            return $tolak;
        }

        // Kegiatan bidang/unit lain tidak boleh dibuka lewat URL langsung.
        Gate::authorize('view', $proker);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', ...RoleHimpunan::POSISI])->isNotEmpty();
        // Anggaran & Dokumen hanya untuk role pengelola & pemantau (KegiatanPolicy::LIHAT_INTERNAL).
        $canViewRestricted = $user->hasAnyRole(KegiatanPolicy::LIHAT_INTERNAL);

        // Alur tinjau (S-82), sama dengan Rencana Proker: ketua pelaksana & Akses Kelola
        // mengisi realisasi lalu ketua pelaksana mengirim; KABID dkk. menyetujui (unggah
        // ke Arsip) atau mengembalikan dengan catatan. GPM, Kadep & DPM view-only.
        $berjalan   = $proker->status === Kegiatan::STATUS_DISETUJUI;
        $ditinjau   = $proker->sedang_ditinjau;
        $bolehUbah  = Gate::allows('update', $proker);
        $roleEdit   = $roles->intersect(self::ROLE_EDIT)->isNotEmpty();
        $roleTinjau = $roles->intersect(self::ROLE_TINJAU)->isNotEmpty();
        $canManage  = $roleEdit && $bolehUbah && $berjalan && !$ditinjau;
        $canTinjau  = $berjalan && $roleTinjau && Gate::allows('tinjau', $proker);
        $canArsip   = $canTinjau && $ditinjau;
        $canTolak   = $canTinjau && $ditinjau;
        $canGantiKetua = $canTinjau && Gate::allows('gantiKetua', $proker);
        $canKirim   = $berjalan && !$ditinjau && Gate::allows('kirimTinjauan', $proker);
        $canDelete  = $berjalan && $roleTinjau && Gate::allows('delete', $proker);
        $pesanBukanPengelola = $berjalan && $roleEdit && !$bolehUbah && !$canTinjau
            ? $this->pengelolaService->pesanTolak($proker)
            : null;
        $kurangArsip = $this->kurangSyaratArsip($proker);
        $calonKetua  = $canGantiKetua ? $this->tinjauan->calonKetua($proker) : collect();
        $proker->loadMissing('catatan.penulis', 'catatan.pemerbaiki');

        $images    = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'image') : collect();
        $documents = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'document') : collect();

        return view('manajemenmahasiswa::pelaksanaan.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'canManage', 'canArsip', 'canDelete',
            'canViewRestricted', 'images', 'documents', 'pesanBukanPengelola',
            'canTolak', 'canKirim', 'canGantiKetua', 'calonKetua', 'kurangArsip'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit — form edit data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator',
            'repoMulmed',
        ])->find($id);

        // Hanya tahap Pelaksanaan. Kegiatan yang sudah diarsipkan diubah dari
        // subbab Arsip — lihat catatan wewenang di tolakBilaStatusTakSesuai().
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }

        Gate::authorize('view', $proker);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', ...RoleHimpunan::POSISI])->isNotEmpty();
        $canManage = $roles->intersect(self::ROLE_EDIT)->isNotEmpty(); // KABID/KAHIM pengawas (S-82); GPM, Kadep & DPM view-only

        if (!$canManage) {
            abort(403, 'Akses ditolak.');
        }
        if ($tolak = $this->tolakBilaSedangDitinjau($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

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
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosenIds    = old('dosen_pendamping_ids', $proker->dosenPendampings->pluck('id')->toArray());
        $existingDosen       = $dosenList->whereIn('id', $existingDosenIds);

        return view('manajemenmahasiswa::pelaksanaan.edit', compact(
            'proker', 'kategoriList', 'tahunList',
            'mahasiswaList', 'dosenList',
            'existingFoto', 'existingDokumen',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds',
            'isAdmin', 'isPengurus', 'canManage'
        ) + [
            'wajibDariRencana' => $this->wajibDariRencana($proker),
            'kunciDariRencana' => true,
            // Ketua pelaksana ditunjuk KABID; hanya peninjau/admin yang menggantinya (S-82).
            'ketuaTerkunci'    => Gate::denies('gantiKetua', $proker),
        ] + RoleHimpunan::dataFormKegiatan($user) + $this->pengelolaService->dataForm($proker));
    }

    /**
     * Isian Pelaksanaan yang wajib karena sudah terisi di Rencana Proker-nya (S-59).
     *
     * Keputusan Surya 30 Okt 2026: apa yang diisi di rencana harus tetap ada di
     * pelaksanaan — boleh diubah sesuai realisasi, tidak boleh dikosongkan; isian
     * yang memang kosong di rencana boleh tetap kosong. Isian yang sudah selalu
     * wajib di sini (judul, kategori, bidang, deskripsi, tanggal mulai, lokasi,
     * ketua pelaksana, anggaran) tidak dicantumkan. Akses Kelola tidak termasuk:
     * itu pengaturan hak akses, bukan data kegiatan. Banner & dokumen rencana juga
     * tidak — keduanya terkunci penuh (Kegiatan::bannerDariRencana, S-61; dokumen
     * `dari_rencana`, S-59). Kegiatan lama tanpa rencana → [].
     *
     * @return list<string> nama isian form
     */
    private function wajibDariRencana(Kegiatan $proker): array
    {
        $rencana = $proker->rencana()->with(['panitia', 'dosenPendampings'])->first();
        if (!$rencana) {
            return [];
        }

        return array_keys(array_filter([
            'jam_mulai'            => filled($rencana->jam_mulai),
            'tanggal_selesai'      => filled($rencana->tanggal_selesai),
            'jam_selesai'          => filled($rencana->jam_selesai),
            'target_peserta'       => filled($rencana->target_peserta),
            'dosen_pendamping_ids' => $rencana->dosenPendampings->isNotEmpty(),
            'panitia_ids'          => $rencana->panitia->isNotEmpty(),
        ]));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Update — simpan perubahan data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::find($id);

        // Sinkron dengan edit(): kegiatan yang sudah diarsipkan tidak boleh
        // ditulis dari sini, termasuk oleh permintaan PUT langsung.
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }
        Gate::authorize('view', $proker);
        if ($tolak = $this->tolakBilaSedangDitinjau($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        // Ketua pelaksana tidak bisa mengganti dirinya lewat form (S-82).
        if (Gate::denies('gantiKetua', $proker)) {
            $request->merge(['ketua_pelaksana_id' => $proker->ketua_pelaksana_id]);
        }

        // Banner salinan Rencana Proker terkunci (S-61).
        $this->repoMulmedService->tolakUbahBannerRencana($request, $proker);

        RoleHimpunan::kunciIsianKegiatan($request, Auth::user());
        $jadwal = app(JadwalRuanganService::class);

        // Bidang/unit WAJIB untuk setiap kegiatan, termasuk Kegiatan Prodi
        // (2 Nov 2026) — sama dengan Rencana Proker. Visibilitas kegiatan ditentukan
        // bidang/unitnya (Kegiatan::scopeTerlihatOleh). Tepat satu bidang: satu
        // proker dipegang satu bidang himpunan.
        $bidangRule = 'required|array|min:1|max:1';

        // Jam selesai hanya dibandingkan dengan jam mulai bila kegiatan berlangsung
        // dalam SATU hari — kegiatan lintas hari wajar berakhir di jam yang lebih awal.
        $satuHari = !$request->filled('tanggal_selesai')
            || $request->input('tanggal_selesai') === $request->input('tanggal_mulai');

        // Isian yang terisi di Rencana Proker tidak boleh dikosongkan di sini (S-59).
        $wajib  = $this->wajibDariRencana($proker);
        $aturan = fn(string $isian, string $sisa) => (in_array($isian, $wajib, true) ? 'required|' : 'nullable|') . $sisa;
        $pesanRencana = fn(string $label) => "{$label} wajib diisi karena sudah terisi di Rencana Proker. Boleh diubah sesuai realisasi, tapi tidak boleh dikosongkan.";

        $jamSelesaiRules = [in_array('jam_selesai', $wajib, true) ? 'required' : 'nullable', 'date_format:H:i,H:i:s'];
        if ($satuHari && $request->filled('jam_mulai')) {
            $jamSelesaiRules[] = 'after:jam_mulai';
        }

        $validated = $request->validate([
            'judul'                     => 'required|string|max:' . KegiatanController::MAKS_JUDUL,
            // Disamakan dengan aturan di Laporan & Arsip agar kegiatan yang diarsipkan
            // selalu lolos validasi saat diedit di subbab Arsip (hindari "jebakan" validasi).
            'kategori_kegiatan_id'      => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*'    => 'integer|exists:mk_kategori_kegiatan,id',
            'bidang_id'                 => $bidangRule,
            'bidang_id.*'               => RoleHimpunan::aturanBidangKegiatan(Auth::user()),
            'deskripsi'                 => 'required|string|min:20|max:3000',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => $aturan('tanggal_selesai', 'date|after_or_equal:tanggal_mulai'),
            // Kolom jam di database bertipe TIME: `string` bebas membuat isian ngawur
            // lolos ke query dan memunculkan halaman error saat disimpan.
            'jam_mulai'                 => $aturan('jam_mulai', 'date_format:H:i,H:i:s'),
            'jam_selesai'               => $jamSelesaiRules,
            // Lokasi, ketua pelaksana, dan anggaran sudah wajib sejak Ajukan Proker; di tahap
            // Pelaksanaan boleh diubah sesuai realisasi tetapi tidak boleh dikosongkan lagi.
            // Ruangan kampus (EOffice) ATAU lokasi teks bebas (S-81).
            'lokasi'                    => 'required_without:ruangan_id|nullable|string|max:150',
            'ruangan_id'                => $jadwal->aturanRuangan(),
            'target_peserta'            => $aturan('target_peserta', 'integer|min:1'),
            'ketua_pelaksana_id'        => ['required', 'exists:students,id', KegiatanPolicy::aturanKetuaSebidang($proker, (int) collect($request->input('bidang_id'))->first() ?: null)],
            'dosen_pendamping_ids'      => $aturan('dosen_pendamping_ids', 'array'),
            'dosen_pendamping_ids.*'    => 'exists:lecturers,id',
            'dosen_peran'               => 'nullable|array',
            'dosen_peran.*'             => 'nullable|string|max:60',
            'panitia_ids'               => $aturan('panitia_ids', 'array'),
            'panitia_ids.*'             => 'exists:students,id',
            'panitia_peran'             => 'nullable|array',
            'panitia_peran.*'           => 'nullable|string|max:60',
            'banner'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_kegiatan'             => 'nullable|array|max:10',
            'foto_kegiatan.*'           => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'          => 'nullable|array|max:' . self::MAKS_DOKUMEN,
            'dokumen_kegiatan.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:' . KegiatanController::MAKS_UKURAN_DOKUMEN_KB,
            'hapus_file'                => 'nullable|array',
            'hapus_file.*'              => 'integer|exists:mk_repo_mulmed,id',
            // Realisasi anggaran wajib dirinci per sumber dana (S-80).
        ] + SumberDana::aturan(true), [
            // Pesan bawaan Laravel masih berbahasa Inggris dan menyebut nama kolom mentah.
            'bidang_id.required'             => 'Bidang/unit pemegang proker wajib dipilih.',
            'bidang_id.min'                  => 'Bidang/unit pemegang proker wajib dipilih.',
            'bidang_id.max'                  => 'Pilih satu bidang saja — satu proker dipegang satu bidang himpunan.',
            'bidang_id.*.in'                 => RoleHimpunan::PESAN_BIDANG_TERKUNCI,
            'tanggal_mulai.required'         => 'Tanggal mulai wajib diisi. Kegiatan yang sudah masuk tahap pelaksanaan harus punya tanggal pasti supaya bisa muncul di filter Tahun dan diarsipkan.',
            'lokasi.required_without'        => 'Lokasi wajib diisi — pilih ruangan kampus atau tuliskan lokasi sebenarnya kegiatan berlangsung.',
            'ketua_pelaksana_id.required'    => 'Ketua pelaksana wajib dipilih.',
            'ketua_pelaksana_id.in'          => KegiatanPolicy::PESAN_KETUA_SEBIDANG,
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
            'jam_mulai.required'             => $pesanRencana('Jam mulai'),
            'tanggal_selesai.required'       => $pesanRencana('Tanggal selesai'),
            'jam_selesai.required'           => $pesanRencana('Jam selesai'),
            'target_peserta.required'        => $pesanRencana('Peserta'),
            'dosen_pendamping_ids.required'  => $pesanRencana('Dosen pendamping (minimal 1)'),
            'panitia_ids.required'           => $pesanRencana('Panitia (minimal 1)'),
        ] + SumberDana::pesan() + KegiatanController::pesanValidasiDokumen());

        // Dokumen salinan Rencana Proker tidak boleh dihapus di sini (S-59).
        $this->repoMulmedService->tolakHapusDokumenRencana($request, $proker);
        // Batas 10 file berlaku untuk TOTAL yang tersimpan, bukan per sekali simpan.
        $this->repoMulmedService->pastikanKuota($request, $proker, self::MAKS_FILE, self::MAKS_DOKUMEN);
        $this->repoMulmedService->pastikanJenisDokumen($request, $proker, false, true);

        // Update data utama — lokasi dari ruangan EOffice/teks (S-81), anggaran = jumlah
        // sumber dana (S-80).
        $proker->update($jadwal->kolomLokasi($validated) + SumberDana::kolom($validated, $proker) + [
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
            'target_peserta'     => $validated['target_peserta'] ?? null,
            'ketua_pelaksana_id' => $validated['ketua_pelaksana_id'] ?? null,
            // Penanda bahwa data pelaksanaan sudah pernah diperbarui — ini yang
            // membuka tombol "Unggah ke Arsip" di halaman detail. Naik saat form
            // Pelaksanaan disimpan; sekali true tidak diturunkan lagi.
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

        // Sync dosen pendamping (many-to-many)
        $proker->syncDosenPendamping($validated['dosen_pendamping_ids'] ?? [], $request->input('dosen_peran', []));

        // Daftar pengelola — hanya diproses bila penyimpan berhak mengatur akses
        $this->pengelolaService->sync($proker, $request);

        // Upload banner — simpan ke kolom `banner` di mk_kegiatan (bukan repo_mulmed)
        if ($request->hasFile('banner')) {
            // Hapus banner lama jika ada
            if ($proker->banner) {
                $this->supabase->delete($proker->banner);
            }
            $proker->update([
                'banner' => $this->supabase->upload($request->file('banner'), 'mk_mulmed/image'),
            ]);
        } elseif ($request->boolean('hapus_banner') && $proker->banner) {
            // Tombol Hapus di kotak banner tanpa unggahan pengganti. Banner hasil
            // salinan Ajukan adalah objek tersendiri, jadi rencananya tidak ikut
            // kehilangan banner; Unggah ke Arsip tetap menagih banner baru.
            $this->supabase->delete($proker->banner);
            $proker->update(['banner' => null]);
        }

        $this->repoMulmedService->hapusTerpilih($request, $proker);
        $this->repoMulmedService->perbaruiJenisDokumen($request, $proker, true);
        $this->repoMulmedService->simpanUnggahan($request, $proker);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Data pelaksanaan berhasil diperbarui. Bila realisasinya sudah lengkap, tekan "Kirim untuk Ditinjau".');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Publish ke Arsip — pindahkan kegiatan ke subbab 3 (status = selesai)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Peninjau menyetujui kiriman ketua pelaksana — "Setujui & Unggah ke Arsip" (S-82).
     * Syarat arsip (banner, foto, lokasi, ketua, anggaran, sudah diedit) tetap dicek
     * di sini; ketua pelaksana sudah ditagih syarat yang sama sebelum boleh mengirim.
     */
    public function publishToArsip($id)
    {
        // Dicari by id dulu, status diperiksa sesudahnya. Kalau findOrFail langsung
        // dibatasi STATUS_DISETUJUI, permintaan publish kedua — klik ganda pada modal,
        // atau tombol Back lalu klik lagi — melempar ModelNotFoundException dan user
        // dibuang ke halaman error, padahal kegiatannya justru sudah berhasil masuk arsip.
        $proker = Kegiatan::findOrFail($id);
        Gate::authorize('view', $proker);

        if ($proker->status === Kegiatan::STATUS_SELESAI) {
            return redirect()
                ->route('manajemenmahasiswa.kegiatan.show', $proker->id)
                ->with('success', 'Kegiatan ini sudah berada di Laporan & Arsip.');
        }

        if ($proker->status !== Kegiatan::STATUS_DISETUJUI) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Hanya kegiatan yang berada di tahap Pelaksanaan yang bisa diunggah ke arsip.');
        }

        // Hanya peninjau kegiatan ini (KABID dkk., KAHIM, admin), saat Menunggu Tinjauan.
        if ($tolak = $this->tolakBilaBukanPeninjau($proker)) {
            return $tolak;
        }

        // Dicek ulang di sini (bukan hanya sebelum Kirim) supaya kegiatan lama yang
        // formnya disimpan sebelum aturan ini tetap tertahan.
        $kurang = $this->kurangSyaratArsip($proker);
        if ($kurang) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
                ->with('error', 'Belum bisa diunggah ke arsip. Lengkapi dulu: ' . implode(', ', $kurang) . '. Kembalikan ke ketua pelaksana dengan catatan agar ia melengkapinya.');
        }

        $proker->update(['status' => Kegiatan::STATUS_SELESAI, 'status_tinjauan' => null]);

        // Konsisten dengan alur subbab 1 → 2 (ajukan() yang mengarahkan ke
        // pelaksanaan.index): setelah unggah ke arsip, arahkan ke daftar
        // subbab 3 (Laporan & Arsip), bukan kembali ke detail pelaksanaan.
        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Laporan pelaksanaan disetujui dan kegiatan diunggah ke Laporan & Arsip.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Alur tinjau ketua pelaksana ↔ KABID (S-82) — sama dengan Rencana Proker
    // ─────────────────────────────────────────────────────────────────────────

    /** Ketua pelaksana mengirim realisasi yang sudah lengkap → Menunggu Tinjauan. */
    public function kirim($id)
    {
        $proker = Kegiatan::findOrFail($id);
        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }
        if (Gate::denies('kirimTinjauan', $proker)) {
            return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
                ->with('error', 'Hanya ketua pelaksana kegiatan ini yang bisa mengirimnya untuk ditinjau.');
        }

        $kurang = $this->kurangSyaratArsip($proker);
        if ($kurang) {
            return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
                ->with('error', 'Data pelaksanaan belum lengkap. Lengkapi dulu: ' . implode(', ', $kurang) . '.');
        }

        try {
            $this->tinjauan->kirim($proker, Auth::user());
        } catch (\DomainException $e) {
            return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)->with('error', $e->getMessage());
        }

        return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Laporan pelaksanaan dikirim untuk ditinjau. Data dikunci sampai KABID menyetujui (diunggah ke Arsip) atau mengembalikannya dengan catatan.');
    }

    /** Peninjau mengembalikan kiriman dengan catatan → Perlu Perbaikan. */
    public function tolak(Request $request, $id)
    {
        $proker = Kegiatan::findOrFail($id);
        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanPeninjau($proker)) {
            return $tolak;
        }

        $data = $request->validate(TinjauanKegiatanService::ATURAN_CATATAN, TinjauanKegiatanService::PESAN_CATATAN);

        try {
            $this->tinjauan->tolak($proker, Auth::user(), $data['isi']);
        } catch (\DomainException $e) {
            return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)->with('error', $e->getMessage());
        }

        return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Laporan pelaksanaan dikembalikan ke ketua pelaksana dengan catatan. Statusnya kini Perlu Perbaikan.');
    }

    /** KABID dkk. mengganti ketua pelaksana (S-82). */
    public function gantiKetua(Request $request, $id)
    {
        $proker = Kegiatan::with('bidangs')->findOrFail($id);
        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanPeninjau($proker, false)) {
            return $tolak;
        }

        $nama = $this->tinjauan->gantiKetuaDariRequest($request, $proker);

        return redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', "Ketua pelaksana diganti menjadi {$nama}. Ia kini yang mengisi dan mengirim laporan pelaksanaan ini.");
    }

    /**
     * Syarat sebelum dikirim ketua pelaksana maupun diunggah ke Arsip: data sudah
     * pernah diedit, banner, lokasi, ketua pelaksana, anggaran, dan minimal 1 foto.
     *
     * @return list<string> label syarat yang belum terpenuhi
     */
    private function kurangSyaratArsip(Kegiatan $proker): array
    {
        return array_keys(array_filter([
            'data pelaksanaan (simpan form Edit minimal sekali)' => !$proker->is_pelaksanaan_updated,
            'banner'          => empty($proker->banner),
            'lokasi'          => blank($proker->lokasi),
            'ketua pelaksana' => empty($proker->ketua_pelaksana_id),
            'anggaran'        => $proker->anggaran === null,
            // Arsip kegiatan harus punya bukti kegiatan sudah berlangsung.
            'minimal 1 foto kegiatan' => !$proker->repoMulmed()->where('tipe_file', 'image')->exists(),
        ]));
    }

    /** Data dikunci selama Menunggu Tinjauan (S-82), termasuk untuk admin. */
    private function tolakBilaSedangDitinjau(Kegiatan $proker): ?RedirectResponse
    {
        if (!$proker->sedang_ditinjau) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('error', 'Laporan pelaksanaan ini sedang ditinjau, jadi datanya dikunci sampai disetujui atau dikembalikan dengan catatan.');
    }

    /** Hanya peninjau kegiatan ini, di tahap Pelaksanaan, dan (bila diminta) saat Menunggu Tinjauan. */
    private function tolakBilaBukanPeninjau(Kegiatan $proker, bool $harusDitinjau = true): ?RedirectResponse
    {
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }

        $ke = redirect()->route('manajemenmahasiswa.pelaksanaan.show', $proker->id);

        if (Auth::user()->roles->pluck('name')->intersect(self::ROLE_TINJAU)->isEmpty()
            || Gate::denies('tinjau', $proker)) {
            return $ke->with('error', 'Anda tidak berhak meninjau kegiatan ini.');
        }
        if ($harusDitinjau && !$proker->sedang_ditinjau) {
            return $ke->with('error', 'Laporan pelaksanaan ini belum dikirim ketua pelaksana untuk ditinjau.');
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Hapus — Hapus pelaksanaan kegiatan (termasuk foto/dokumen)
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        // Hanya kegiatan berstatus 'disetujui' yang boleh dihapus dari Pelaksanaan.
        // Kegiatan yang sudah diarsipkan (selesai) harus dihapus dari subbab Arsip.
        //
        // Statusnya diperiksa SETELAH pencarian by id — sama seperti publishToArsip():
        // dengan findOrFail yang dibatasi status, klik Hapus kedua kali (mis. setelah
        // tombol Back) berakhir di halaman error alih-alih pesan yang menjelaskan.
        $proker = Kegiatan::with('repoMulmed')->findOrFail($id);
        Gate::authorize('view', $proker);

        if ($proker->status !== Kegiatan::STATUS_DISETUJUI) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', $proker->status === Kegiatan::STATUS_SELESAI
                    ? 'Kegiatan ini sudah diarsipkan, jadi penghapusannya dilakukan dari subbab Laporan & Arsip.'
                    : 'Hanya kegiatan yang berada di tahap Pelaksanaan yang bisa dihapus dari halaman ini.');
        }

        if ($tolak = $this->tolakBilaBukanPengelola($proker, 'delete')) {
            return $tolak;
        }

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

        // Salinan hasil Ajukan Proker (S-49): rencana & dokumennya berupa baris dan file
        // terpisah, jadi tidak ikut terhapus.
        $punyaRencana = (bool) $proker->rencana_id;
        $proker->delete();

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', $punyaRencana
                ? 'Data pelaksanaan kegiatan berhasil dihapus. Rencana awalnya tetap tersimpan di Rencana Proker.'
                : 'Data pelaksanaan kegiatan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pastikan kegiatan memang berada di tahap yang ditangani halaman ini.
     *
     * Dua alasan:
     *
     * 1. Wewenang. Kegiatan yang sudah diarsipkan punya aturan edit sendiri di
     *    subbab Arsip (form, validasi, hak per role). Selama edit()/update() di sini
     *    masih menerima status 'selesai', aturan itu bisa dilewati lewat
     *    /pelaksanaan/{id}/edit. Karena itu edit & update dibatasi ke 'disetujui'
     *    saja; kegiatan yang sudah diarsipkan diubah dari subbab Arsip (sejak S-82
     *    oleh ketua pelaksananya atau admin).
     *
     * 2. Pesan, bukan halaman error. findOrFail yang dibatasi status melempar
     *    ModelNotFoundException untuk id yang statusnya belum/sudah lewat tahap
     *    ini, sehingga user yang mengetik URL atau menekan Back mendarat di
     *    halaman error tanpa keterangan.
     *
     * @param array<int, string> $statusDiizinkan
     * @return RedirectResponse|null null bila kegiatan boleh diproses di sini
     */
    private function tolakBilaStatusTakSesuai(?Kegiatan $proker, array $statusDiizinkan): ?RedirectResponse
    {
        if ($proker && in_array($proker->status, $statusDiizinkan, true)) {
            return null;
        }

        if ($proker === null) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Kegiatan yang Anda buka sudah tidak ada — kemungkinan sudah dihapus lebih dulu.');
        }

        return match ($proker->status) {
            Kegiatan::STATUS_DRAFT => redirect()
                ->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', 'Kegiatan ini masih berada di tahap Rencana Proker, jadi belum bisa dikelola dari halaman Pelaksanaan.'),
            Kegiatan::STATUS_SELESAI => redirect()
                ->route('manajemenmahasiswa.kegiatan.show', $proker->id)
                ->with('error', 'Kegiatan ini sudah diarsipkan. Perubahan datanya kini dilakukan dari subbab Laporan & Arsip.'),
            default => redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Kegiatan ini tidak berada di tahap Pelaksanaan.'),
        };
    }

    /**
     * Role saja tidak cukup: kegiatan hanya boleh diubah/dihapus pemiliknya,
     * pengelola yang ia tunjuk, atau override (lihat KegiatanPolicy).
     *
     * @param string $aksi 'update' atau 'delete'
     * @return RedirectResponse|null null bila boleh
     */
    private function tolakBilaBukanPengelola(Kegiatan $proker, string $aksi = 'update'): ?RedirectResponse
    {
        if (Gate::allows($aksi, $proker)) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('error', $this->pengelolaService->pesanTolak($proker));
    }
}
