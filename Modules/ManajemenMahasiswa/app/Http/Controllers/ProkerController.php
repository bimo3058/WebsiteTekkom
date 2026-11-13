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
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;
use Modules\ManajemenMahasiswa\Services\JadwalRuanganService;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;
use Modules\ManajemenMahasiswa\Services\RencanaProkerService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;
use Modules\ManajemenMahasiswa\Services\TinjauanKegiatanService;
use Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan;
use Modules\ManajemenMahasiswa\Support\PerPage;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;
use Modules\ManajemenMahasiswa\Support\SumberDana;

class ProkerController extends Controller
{
    /**
     * Role peninjau — menyetujui ("Setujui & Teruskan ke Pelaksanaan", dulu "Ajukan
     * Proker"), mengembalikan dengan catatan, dan mengganti ketua pelaksana (S-82).
     * Sinkron dengan route `proker.ajukan`/`tolak`/`ketua`. Proker MANA yang boleh
     * ditinjau diputuskan KegiatanPolicy::tinjau. admin_kemahasiswaan setara
     * superadmin (S-66).
     */
    private const ROLE_AJUKAN = [
        'superadmin', 'admin_kemahasiswaan',
        ...RoleHimpunan::INTI, ...RoleHimpunan::PIMPINAN_BIDANG,
    ];

    /**
     * Role yang membuka form edit Rencana (S-82): ketua pelaksana & Akses Kelola
     * (staff_himpunan) serta admin. KABID/KAHIM pengawas, tidak mengedit.
     * Sinkron dengan route `proker.edit`/`update`.
     */
    private const ROLE_EDIT = ['superadmin', 'admin_kemahasiswaan', RoleHimpunan::STAFF];

    /** Mode tampilan daftar Rencana Proker untuk admin (S-87). */
    private const TAMPILAN = ['kartu', 'daftar'];

    /**
     * Field yang wajib terisi sebelum proker boleh diajukan ke Subbab 2 — sama
     * dengan kolom bertanda * di form Rencana Proker. Saat menyimpan draft cukup
     * Judul; sisanya boleh menyusul (lihat validateProker()).
     * Label dipakai untuk pesan error saat ajukan() dan daftar "lengkapi dulu"
     * di samping tombol "Ajukan Proker". Urutannya mengikuti urutan form.
     *
     * Yang sengaja TIDAK wajib (keputusan Surya 30 Okt 2026): Akses Kelola, Dosen
     * Pendamping, Tanggal Selesai, serta Surat Undangan & Surat Peminjaman.
     *
     * Jam Mulai & Jam Selesai WAJIB sejak 10 Nov 2026 (S-81, MENGGANTIKAN keputusan
     * 30 Okt): peminjaman ruang di EOffice mewajibkan keduanya, dan cek bentrok
     * proker butuh jam. Anggaran kini dirinci per sumber dana (S-80).
     */
    private const SYARAT_AJUKAN = [
        'kategori'           => 'Kategori',
        'bidang'             => 'Bidang/Unit',
        'deskripsi'          => 'Deskripsi (min. 20 karakter)',
        'tanggal_mulai'      => 'Tanggal mulai',
        'jam_mulai'          => 'Jam mulai',
        'jam_selesai'        => 'Jam selesai',
        'lokasi'             => 'Lokasi',
        'ketua_pelaksana_id' => 'Ketua pelaksana',
        'panitia'            => 'Panitia kegiatan',
        'target_peserta'     => 'Peserta',
        'anggaran'           => 'Anggaran per sumber dana',
        'banner'             => 'Banner kegiatan',
        // Dokumen Rencana berjenis (S-56); undangan & peminjaman tidak wajib karena
        // tidak semua proker mengundang tamu atau meminjam ruang (keputusan Surya 30 Okt 2026).
        'dokumen_proposal'   => 'Dokumen Proposal',
        'dokumen_rab'        => 'Dokumen RAB',
    ];

    public function __construct(
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService,
        private RepoMulmedService $repoMulmedService,
        private RencanaProkerService $rencanaService,
        private JadwalRuanganService $jadwal,
        private TinjauanKegiatanService $tinjauan
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar rencana proker
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        // KAHIM/WAKAHIM & admin melihat semua bidang/unit; KABID, WAKABID, KANIT,
        // WAKANIT & Staff hanya bidang/unitnya (Kegiatan::scopeTerlihatOleh). Opsi
        // dropdown ikut dibatasi supaya tidak membocorkan tahun/periode bidang lain.
        $bidangTerkunci = RoleHimpunan::terbatasBidang($user);
        $bidangList     = RoleHimpunan::daftarBidangUntuk($user);
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        // Filter Tahun sama dengan Pelaksanaan & Laporan/Arsip (panel kegiatan-list/_filter).
        // Draft boleh belum bertanggal, jadi opsi "Belum ada tanggal" cukup sering terpakai.
        // Daftar berisi draft + rencana yang sudah diajukan (S-49): rencana tetap di sini
        // walau salinannya sudah di Pelaksanaan/Arsip.
        $tahunList     = Kegiatan::daftarTahun(Kegiatan::STATUS_DAFTAR_RENCANA, $user);
        $adaTanpaTahun = Kegiatan::whereIn('status', Kegiatan::STATUS_DAFTAR_RENCANA)->terlihatOleh($user)->tanpaTahun()->exists();
        $tahapList     = Kegiatan::TAHAP_FILTER_LABELS;
        $periodeList   = Kegiatan::daftarPeriode(Kegiatan::STATUS_DAFTAR_RENCANA, $user);

        $roles   = $user->roles->pluck('name');
        // Peninjau melihat "Menunggu Tinjauan" paling atas; staff/ketua pelaksana tetap
        // "Menunggu Dilengkapi" di atas (bagian halaman + dropdown filter).
        $tahapList = Kegiatan::urutkanUntukPeninjau($tahapList, $roles->intersect(self::ROLE_AJUKAN)->isNotEmpty());
        // DPM disertakan agar flag $isAdmin konsisten antar-subbab (Proker show, Pelaksanaan, Arsip).
        // Sebelumnya 'dpm' hilang di sini sehingga DPM diperlakukan berbeda di daftar Rencana Proker.
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', ...RoleHimpunan::POSISI])->isNotEmpty();
        // $canManage merender tombol "Buat Proker". WAJIB whitelist eksplisit, JANGAN
        // pakai $isPengurus: staff_himpunan boleh masuk & mengedit Rencana Proker,
        // tapi TIDAK boleh membuat proker baru — proker dibuat oleh ketua.
        $canManage = $roles->intersect(KegiatanPolicy::PEMBUAT_PROKER)->isNotEmpty();

        // Mode tampilan Kartu / Daftar (S-87) — hanya admin; role lain selalu kartu
        // ($tampilan null = pengalih tidak dirender). Pilihan disimpan di sesi supaya
        // tetap terpakai saat kembali dari detail, berpindah halaman, atau "View all →".
        $tampilan = null;
        if ($isAdmin) {
            if (in_array($request->query('tampilan'), self::TAMPILAN, true)) {
                $request->session()->put('mm_proker_tampilan', $request->query('tampilan'));
            }
            $tampilan = $request->session()->get('mm_proker_tampilan', 'kartu');
            if (!in_array($tampilan, self::TAMPILAN, true)) {
                $tampilan = 'kartu';
            }
        }

        // Relasi yang dibutuhkan kartu bersama (kegiatan-list/_kartu) — sama di ketiga subbab.
        // `salinan` khusus di sini: badge tahap rencana (Draft / Di Pelaksanaan / Diarsipkan).
        $query = Kegiatan::with(['bidang', 'bidangs', 'kategoriKegiatan', 'kategoris', 'dosenPendampings.user', 'salinan'])
            // Badge "Perlu perbaikan" di kartu draft (S-79)
            ->withCount('catatanTerbuka')
            ->whereIn('status', Kegiatan::STATUS_DAFTAR_RENCANA)
            ->terlihatOleh($user)
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

        // Filter tahun — scope yang sama dengan Pelaksanaan & Laporan/Arsip.
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            if ($request->tahun === Kegiatan::FILTER_TANPA_TAHUN) {
                $query->tanpaTahun();
            } else {
                $query->filterTahun($request->tahun);
            }
        }

        // Filter periode kepengurusan (S-62) — sama dengan Pelaksanaan & Laporan/Arsip.
        if (PeriodeKepengurusan::ada($request->periode)) {
            $query->filterPeriode((int) $request->periode);
        }

        // Filter rentang tanggal (S-81) — sama dengan Pelaksanaan & Laporan/Arsip.
        [$dari, $sampai] = Kegiatan::rentangDariRequest($request);
        if ($dari || $sampai) {
            $query->filterRentang($dari, $sampai);
        }

        // Search judul + deskripsi
        // Dibungkus closure supaya orWhere tidak "membocorkan" filter bidang di atas.
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('judul', 'like', $term)
                  ->orWhere('deskripsi', 'like', $term);
            });
        }

        // Filter status rencana: tahap dan status tinjauan (S-82) digabung dalam satu
        // dropdown. ?tinjauan= tetap diterima sebagai alias karena tautan lonceng
        // notifikasi memakainya. "tidak_terlaksana" bukan pilihan dropdown, tapi tetap
        // diterima sebagai tujuan "View all →" baris Tidak terlaksana (S-86).
        $statusRencana = (string) ($request->input('status_rencana') ?: $request->input('tinjauan'));
        $statusValid   = array_key_exists($statusRencana, $tahapList) || $statusRencana === 'tidak_terlaksana';

        $prokerList = null;
        $kelompok   = null;
        $statusAktif = null;

        if ($statusRencana !== '' && $statusValid) {
            $statusAktif = [
                'kunci' => $statusRencana,
                'label' => $tahapList[$statusRencana] ?? Kegiatan::TAHAP_RENCANA_LABELS[$statusRencana],
            ];
            $prokerList = $query->tahapRencana($statusRencana)
                ->paginate(PerPage::resolve($request, PerPage::KARTU, 12));
        } else {
            // Tanpa filter status (S-86): satu baris per status, maks 4 kartu per baris,
            // lengkap dengan "View all →". Filter lain & pencarian tetap berlaku.
            $kelompok = $this->susunKelompok($query, $tahapList);
            // Rencana tidak terlaksana tidak ada di dropdown status — tampil di akhir
            // hanya bila ada, supaya tidak hilang dari tampilan kelompok.
            $sisa = $this->susunKelompok($query, ['tidak_terlaksana' => Kegiatan::TAHAP_RENCANA_LABELS['tidak_terlaksana']]);
            if ($sisa['tidak_terlaksana']['total'] > 0) {
                $kelompok += $sisa;
            }
        }

        return view('manajemenmahasiswa::proker.index', compact(
            'prokerList', 'kelompok', 'statusAktif',
            'bidangList', 'kategoriList', 'tahunList', 'adaTanpaTahun', 'tahapList', 'periodeList',
            'isAdmin', 'isPengurus', 'canManage', 'bidangTerkunci', 'tampilan'
        ));
    }

    /**
     * Isi tiap baris kelompok status di daftar Rencana Proker (S-86).
     *
     * @param  array<string, string>  $labels  kunci status => judul baris
     * @return array<string, array{label: string, total: int, items: \Illuminate\Support\Collection}>
     */
    private function susunKelompok($query, array $labels): array
    {
        $hasil = [];
        foreach ($labels as $kunci => $label) {
            $q = (clone $query)->tahapRencana($kunci);
            $hasil[$kunci] = [
                'label' => $label,
                'total' => (clone $q)->count(),
                'items' => $q->take(4)->get(),
            ];
        }

        return $hasil;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail proker
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator', 'repoMulmed',
            'catatan.penulis', 'catatan.pemerbaiki',
        ])->findOrFail($id);
        // Catatan: TIDAK difilter status agar detail proker tetap bisa dibuka
        // setelah diajukan (rencana terkunci, S-49) maupun untuk baris lama yang
        // berpindah status ke Pelaksanaan/Arsip sebelum S-49.

        // Proker bidang/unit lain tidak boleh dibuka lewat URL langsung.
        Gate::authorize('view', $proker);

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', ...RoleHimpunan::POSISI])->isNotEmpty();
        $draft      = $proker->status === Kegiatan::STATUS_DRAFT;
        $ditinjau   = $proker->sedang_ditinjau;

        // Alur tinjau (S-82). Semua daftar putih + KegiatanPolicy:
        //  • ketua pelaksana & Akses Kelola → Edit (kecuali sedang ditinjau)
        //  • ketua pelaksana saja           → Kirim untuk Ditinjau
        //  • KABID dkk., KAHIM, admin       → Setujui / Tolak dengan catatan (saat
        //                                     ditinjau), Ganti ketua pelaksana, Hapus
        //  • gpm/kadep/dpm                  → pengawas view-only
        $bolehUbah = Gate::allows('update', $proker);
        $roleEdit  = $roles->intersect(self::ROLE_EDIT)->isNotEmpty();
        $canEdit   = $roleEdit && $bolehUbah && $draft && !$ditinjau;
        $canTinjau = $draft && $roles->intersect(self::ROLE_AJUKAN)->isNotEmpty()
            && Gate::allows('tinjau', $proker);
        $canAjukan = $canTinjau && $ditinjau;
        $canTolak  = $canTinjau && $ditinjau;
        $canGantiKetua = $canTinjau && Gate::allows('gantiKetua', $proker);
        $canKirim  = $draft && !$ditinjau && Gate::allows('kirimTinjauan', $proker);
        // Staff sebidang yang bukan ketua pelaksana/pengelola: jelaskan kenapa hanya bisa melihat.
        $pesanBukanPengelola = $draft && $roleEdit && !$bolehUbah && !$canTinjau
            ? $this->pengelolaService->pesanTolak($proker)
            : null;
        // Hapus (sinkron dengan route destroy): peninjau saja, dan bergantung pada tahap —
        // draft, atau rencana diajukan yang salinannya sudah tidak ada (destroy()).
        $canDelete = $roles->intersect(self::ROLE_AJUKAN)->isNotEmpty()
            && Gate::allows('delete', $proker) && $this->bolehDihapus($proker);
        $calonKetua = $canGantiKetua ? $this->tinjauan->calonKetua($proker) : collect();

        // Anggaran & Dokumen hanya untuk role pengelola & pemantau — sama di ketiga subbab.
        $canViewRestricted = $user->hasAnyRole(KegiatanPolicy::LIHAT_INTERNAL);
        $isCreator = $proker->user_id === Auth::id();
        $documents = $proker->repoMulmed->where('tipe_file', 'document');

        // Dipakai view untuk mengaktifkan/menonaktifkan tombol "Ajukan Proker"
        // sekaligus menyusun tooltip berisi field yang masih kosong.
        $kelengkapan = $this->cekKelengkapan($proker);

        // Cek jadwal & ruangan (S-81) — hanya untuk draft yang sudah bertanggal.
        $cekJadwal = $draft && $proker->tanggal_mulai ? $this->cekJadwalUntuk($proker) : null;

        return view('manajemenmahasiswa::proker.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'isCreator', 'canAjukan', 'canEdit', 'canDelete',
            'canViewRestricted', 'kelengkapan', 'pesanBukanPengelola', 'documents',
            'canTolak', 'canKirim', 'canGantiKetua', 'calonKetua', 'cekJadwal'
        ));
    }

    /**
     * Ringkasan cek jadwal untuk detail rencana: proker lain yang beririsan + status
     * ruangan terpilih di EOffice.
     *
     * @return array{bentrok: list<array>, ruangan: ?array, tanpaJam: bool}
     */
    private function cekJadwalUntuk(Kegiatan $proker): array
    {
        $slot = $this->slotDari($proker->only(['tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'ruangan_id', 'lokasi']));

        $ruangan = null;
        if ($proker->ruangan_id) {
            $status = $this->jadwal->statusRuangan($slot, $proker)[$proker->ruangan_id] ?? null;
            $info   = $this->jadwal->daftarRuangan()->firstWhere('id', (int) $proker->ruangan_id);
            $ruangan = $status && $info ? $info + $status : null;
        }

        return [
            'bentrok' => $this->jadwal->bentrok($slot, $proker),
            'ruangan' => $ruangan,
            'tanpaJam' => !$proker->jam_mulai || !$proker->jam_selesai,
        ];
    }

    private function slotDari(array $data): array
    {
        $tgl = fn ($v) => $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v;

        return [
            'tanggal_mulai'   => $tgl($data['tanggal_mulai'] ?? null),
            'tanggal_selesai' => $tgl($data['tanggal_selesai'] ?? null),
            'jam_mulai'       => $data['jam_mulai'] ?? null,
            'jam_selesai'     => $data['jam_selesai'] ?? null,
            'ruangan_id'      => $data['ruangan_id'] ?? null,
            'lokasi'          => $data['lokasi'] ?? null,
        ];
    }

    /**
     * Cek jadwal langsung dari form Rencana Proker & Pelaksanaan (S-81, JSON).
     *
     * Mengembalikan status setiap ruangan EOffice pada slot itu dan proker lain dari
     * SEMUA bidang yang tanggal/jamnya beririsan — hanya judul, bidang, tanggal, jam,
     * dan lokasi (keputusan Surya 10 Nov 2026: pengurus boleh melihat jadwal bidang lain
     * sebatas itu). `kecuali` = id kegiatan yang sedang diedit, supaya tidak bentrok
     * dengan dirinya sendiri; hanya dipakai bila pengguna boleh melihatnya.
     */
    public function cekJadwal(Request $request)
    {
        $kecuali = $request->integer('kecuali') ? Kegiatan::find($request->integer('kecuali')) : null;
        if ($kecuali && !Gate::allows('view', $kecuali)) {
            $kecuali = null;
        }

        $slot = $this->slotDari($request->only(['tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'ruangan_id', 'lokasi']));
        $status = $this->jadwal->statusRuangan($slot, $kecuali);

        return response()->json([
            'eoffice' => $this->jadwal->tersedia(),
            'ruangan' => $this->jadwal->daftarRuangan()
                ->map(fn ($r) => $r + ($status[$r['id']] ?? ['status' => 'belum_dicek', 'alasan' => null]))
                ->values(),
            'bentrok' => $this->jadwal->bentrok($slot, $kecuali),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Alur tinjau ketua pelaksana ↔ KABID (S-82; catatan penolakan sejak S-79)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ketua pelaksana mengirim rencana yang sudah lengkap untuk ditinjau →
     * Menunggu Tinjauan. Data dikunci sampai disetujui atau dikembalikan.
     */
    public function kirim($id)
    {
        $proker = Kegiatan::with(['kategoris', 'bidangs', 'panitia', 'repoMulmed'])->findOrFail($id);
        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
        if (Gate::denies('kirimTinjauan', $proker)) {
            return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', 'Hanya ketua pelaksana proker ini yang bisa mengirimnya untuk ditinjau.');
        }

        $kurang = collect($this->cekKelengkapan($proker))->reject(fn ($i) => $i['terisi'])->pluck('label');
        if ($kurang->isNotEmpty()) {
            return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', 'Rencana belum lengkap. Lengkapi dulu: ' . $kurang->implode(', ') . '.');
        }

        try {
            $this->tinjauan->kirim($proker, Auth::user());
        } catch (\DomainException $e) {
            return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)->with('error', $e->getMessage());
        }

        return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana dikirim untuk ditinjau. Data dikunci sampai KABID menyetujui atau mengembalikannya dengan catatan.');
    }

    /**
     * Peninjau mengembalikan kiriman dengan catatan → Perlu Perbaikan (usulan Pak
     * Maman L-41, S-79; sejak S-82 hanya saat Menunggu Tinjauan).
     */
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
            return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)->with('error', $e->getMessage());
        }

        return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana dikembalikan ke ketua pelaksana dengan catatan. Statusnya kini Perlu Perbaikan.');
    }

    /** KABID dkk. mengganti ketua pelaksana (S-82) — satu-satunya isian yang masih bisa ia ubah. */
    public function gantiKetua(Request $request, $id)
    {
        $proker = Kegiatan::with('bidangs')->findOrFail($id);
        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPeninjau($proker, false)) {
            return $tolak;
        }

        $nama = $this->tinjauan->gantiKetuaDariRequest($request, $proker);

        return redirect()->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', "Ketua pelaksana diganti menjadi {$nama}. Ia kini yang melengkapi dan mengirim rencana ini.");
    }

    /**
     * Hanya peninjau proker ini (route + KegiatanPolicy::tinjau), di tahap Rencana,
     * dan — untuk setujui/tolak — hanya saat Menunggu Tinjauan.
     */
    private function tolakBilaBukanPeninjau(Kegiatan $proker, bool $harusDitinjau = true): ?RedirectResponse
    {
        $ke = redirect()->route('manajemenmahasiswa.proker.show', $proker->id);

        if (Auth::user()->roles->pluck('name')->intersect(self::ROLE_AJUKAN)->isEmpty()
            || Gate::denies('tinjau', $proker)) {
            return $ke->with('error', 'Anda tidak berhak meninjau rencana proker ini.');
        }
        if ($proker->status !== Kegiatan::STATUS_DRAFT) {
            return $ke->with('error', 'Rencana proker ini sudah diteruskan ke Pelaksanaan.');
        }
        if ($harusDitinjau && !$proker->sedang_ditinjau) {
            return $ke->with('error', 'Rencana ini belum dikirim ketua pelaksana untuk ditinjau.');
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create & Store
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        // Model kosong supaya partial form bisa dipakai seragam untuk create & edit.
        $proker = new Kegiatan();

        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingPanitia     = collect();
        $existingPanitiaIds  = old('panitia_ids', []);
        $selectedKategoriIds = old('kategori_kegiatan_id', []);
        $selectedBidangIds   = old('bidang_id', []);
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosen       = $dosenList->whereIn('id', old('dosen_pendamping_ids', []));
        $existingDokumen     = collect();

        return view('manajemenmahasiswa::proker.create', compact(
            'proker', 'kategoriList',
            'mahasiswaList', 'dosenList',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds', 'existingDokumen'
        ) + RoleHimpunan::dataFormKegiatan(Auth::user()) + $this->pengelolaService->dataForm($proker));
    }

    /**
     * KABID dkk. membuat proker (S-82): cukup Judul, Kategori (S-85), Bidang/Unit, dan Ketua Pelaksana.
     * Selebihnya diisi ketua pelaksana, yang langsung melihat tugas "Menunggu
     * Dilengkapi" di lonceng notifikasinya.
     */
    public function store(Request $request)
    {
        RoleHimpunan::kunciIsianKegiatan($request, Auth::user());
        $bidangId = (int) collect($request->input('bidang_id'))->first() ?: null;

        $validated = $request->validate([
            'judul'                  => 'required|string|max:' . KegiatanController::MAKS_JUDUL,
            'kategori_kegiatan_id'   => 'nullable|array|max:2',
            'kategori_kegiatan_id.*' => 'integer|exists:mk_kategori_kegiatan,id',
            // Bidang wajib untuk semua proker sejak S-82 (menggantikan pengecualian Prodi
            // S-73): ketua pelaksana harus staff sebidang, jadi bidang harus ada dulu.
            'bidang_id'              => 'required|array|min:1|max:1',
            'bidang_id.*'            => RoleHimpunan::aturanBidangKegiatan(Auth::user()),
            'ketua_pelaksana_id'     => ['required', 'exists:students,id', KegiatanPolicy::aturanKetuaSebidang(null, $bidangId)],
        ], [
            'bidang_id.required'          => 'Bidang/unit pemegang proker wajib dipilih.',
            'bidang_id.min'               => 'Bidang/unit pemegang proker wajib dipilih.',
            'bidang_id.max'               => 'Pilih satu bidang saja — satu proker dipegang satu bidang himpunan.',
            'bidang_id.*.in'              => RoleHimpunan::PESAN_BIDANG_TERKUNCI,
            'ketua_pelaksana_id.required' => 'Ketua pelaksana wajib dipilih — dialah yang akan melengkapi data proker ini.',
            'ketua_pelaksana_id.in'       => KegiatanPolicy::PESAN_KETUA_SEBIDANG,
        ]);

        $proker = Kegiatan::create($this->kolomKegiatan($validated) + [
            'user_id'         => Auth::id(),
            // Proker baru SELALU dimulai sebagai draft (Subbab 1) dan menunggu dilengkapi
            // ketua pelaksana; maju ke Subbab 2 lewat "Setujui & Teruskan" setelah ditinjau.
            'status'          => Kegiatan::STATUS_DRAFT,
            'status_tinjauan' => Kegiatan::TINJAU_DILENGKAPI,
        ]);

        $this->syncRelasiKegiatan($proker, $validated, $request);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Proker dibuat. ' . ($proker->fresh('ketuaPelaksana.user')->ketuaPelaksana?->user?->name ?? 'Ketua pelaksana')
                . ' mendapat notifikasi untuk melengkapi datanya. Anda akan diberi tahu saat rencana dikirim untuk ditinjau.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit & Update
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'repoMulmed',
        ])->findOrFail($id);

        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaSedangDitinjau($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        $existingDokumen = $proker->repoMulmed->where('tipe_file', 'document');

        $kategoriList  = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingPanitia = $proker->panitia ?? collect();

        $selectedKategoriIds = old('kategori_kegiatan_id', $proker->kategoris->pluck('id')->toArray());
        $selectedBidangIds   = old('bidang_id', $proker->bidangs->pluck('id')->toArray());
        $existingPanitiaIds  = old('panitia_ids', $existingPanitia->pluck('id')->toArray());
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosenIds    = old('dosen_pendamping_ids', $proker->dosenPendampings->pluck('id')->toArray());
        $existingDosen       = $dosenList->whereIn('id', $existingDosenIds);

        return view('manajemenmahasiswa::proker.edit', compact(
            'proker', 'kategoriList',
            'mahasiswaList', 'dosenList',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds', 'existingDokumen'
        ) + [
            // Ketua pelaksana ditunjuk KABID; hanya peninjau/admin yang menggantinya (S-82).
            'ketuaTerkunci' => Gate::denies('gantiKetua', $proker),
        ] + RoleHimpunan::dataFormKegiatan(Auth::user()) + $this->pengelolaService->dataForm($proker));
    }

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::with('repoMulmed')->findOrFail($id);

        Gate::authorize('view', $proker);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
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

        $validated = $this->validateProker($request, $proker);

        // Rencana hanya menyimpan dokumen (foto = dokumentasi acara, di Pelaksanaan),
        // jadi kuota foto 0; batas dokumen sama dengan Pelaksanaan & Arsip.
        $this->repoMulmedService->pastikanKuota($request, $proker, 0, KegiatanController::MAKS_DOKUMEN);
        $this->repoMulmedService->pastikanJenisDokumen($request, $proker, true);

        $proker->update($this->kolomKegiatan($validated, $proker));

        // Handle banner upload — hapus banner lama dulu supaya tidak meninggalkan
        // file yatim di storage (perilaku sama dengan subbab Pelaksanaan).
        if ($request->hasFile('banner')) {
            if ($proker->banner) {
                $this->supabase->delete($proker->banner);
            }
            $proker->update([
                'banner' => $this->supabase->upload($request->file('banner'), 'mk_mulmed/image'),
            ]);
        } elseif ($request->boolean('hapus_banner') && $proker->banner) {
            // Tombol Hapus di kotak banner tanpa unggahan pengganti. Rencana boleh
            // tanpa banner sebagai draft; kelengkapannya ditagih saat Ajukan.
            $this->supabase->delete($proker->banner);
            $proker->update(['banner' => null]);
        }

        $this->syncRelasiKegiatan($proker, $validated, $request);
        $this->pengelolaService->sync($proker, $request);
        $this->repoMulmedService->hapusTerpilih($request, $proker);
        $this->repoMulmedService->perbaruiJenisDokumen($request, $proker);
        $this->repoMulmedService->simpanUnggahan($request, $proker, ['dokumen_kegiatan']);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil diperbarui. Bila datanya sudah lengkap, tekan "Kirim untuk Ditinjau".');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Workflow: Ajukan → Subbab 2 (Pelaksanaan)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Peninjau menyetujui kiriman ketua pelaksana — "Setujui & Teruskan ke
     * Pelaksanaan" (S-82; dulu "Ajukan Proker", S-49): rencana dikunci dan tetap
     * tampil di Rencana Proker, sedangkan Pelaksanaan Kegiatan mendapat salinannya —
     * termasuk dokumen rencana (lihat RencanaProkerService::ajukan()).
     */
    public function ajukan($id)
    {
        // Dicari by id dulu, statusnya diperiksa sesudahnya: klik kedua kali
        // (tombol Back, tab lain) harus berakhir di pesan, bukan halaman 404.
        $proker = Kegiatan::findOrFail($id);
        Gate::authorize('view', $proker);

        // Hanya peninjau proker ini, dan hanya saat Menunggu Tinjauan.
        if ($tolak = $this->tolakBilaBukanPeninjau($proker)) {
            return $tolak;
        }

        // Rencana wajib lengkap sebelum boleh naik ke tahap Pelaksanaan (Subbab 2).
        $kurang = collect($this->cekKelengkapan($proker))
            ->reject(fn($item) => $item['terisi'])
            ->pluck('label');

        if ($kurang->isNotEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Rencana proker belum lengkap. Lengkapi dulu: ' . $kurang->implode(', ') . '.');
        }

        try {
            $this->rencanaService->ajukan($proker);
        } catch (\DomainException $e) {
            return redirect()
                ->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', 'Gagal menyalin data dan dokumen rencana ke Pelaksanaan. Rencana tetap tersimpan sebagai draft — coba ajukan lagi.');
        }

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', 'Rencana disetujui dan diteruskan ke Pelaksanaan Kegiatan. Rencananya tetap tersimpan di Rencana Proker; ketua pelaksana kini mengisi data realisasinya.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destroy
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        // Cari proker by id saja (TIDAK difilter status). Sebelumnya findOrFail
        // dibatasi STATUS_DRAFT — sama seperti bug lama di show() — sehingga begitu
        // status proker bukan 'draft' lagi (mis. halaman daftar/detail basi karena
        // bfcache), DELETE melempar ModelNotFoundException → handler global
        // mengalihkan ke /error/404 alih-alih kembali ke daftar rencana proker.
        $proker = Kegiatan::with('repoMulmed')->findOrFail($id);

        Gate::authorize('view', $proker);

        // Draft boleh dihapus; rencana yang sudah diajukan hanya bila salinan
        // Pelaksanaan/Arsip-nya sudah tidak ada (S-49). Selain itu kembali dengan pesan,
        // bukan halaman 404.
        if (!$this->bolehDihapus($proker)) {
            return redirect()
                ->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', $proker->status === Kegiatan::STATUS_DIAJUKAN
                    ? 'Rencana yang sudah diajukan baru bisa dihapus setelah data Pelaksanaan atau Arsip-nya dihapus.'
                    : 'Hanya rencana proker yang dapat dihapus dari halaman ini.');
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
        // Dokumen rencana (proposal, RAB, surat) — salinannya di Pelaksanaan/Arsip
        // adalah file terpisah, jadi aman dihapus di sini.
        foreach ($proker->repoMulmed as $file) {
            $this->repoMulmedService->deletePermanent($file->id);
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
     * Rencana proker hanya boleh diubah selama masih berstatus draft.
     *
     * Prokernya dicari by id dulu, statusnya baru diperiksa di sini — pola yang
     * sama dengan destroy(). Kalau findOrFail langsung dibatasi STATUS_DRAFT
     * (perilaku lama edit() & update()), proker yang keburu diajukan dari tab,
     * perangkat, atau akun lain akan melempar ModelNotFoundException: user
     * dibuang ke halaman error dengan seluruh isian form hilang dan tanpa
     * penjelasan apa pun. Sekarang ia diarahkan ke detail proker + pesan.
     *
     * @return RedirectResponse|null null bila proker masih draft (boleh diubah)
     */
    private function tolakBilaBukanDraft(Kegiatan $proker): ?RedirectResponse
    {
        if ($proker->status === Kegiatan::STATUS_DRAFT) {
            return null;
        }

        $lanjutanNya = $proker->status === Kegiatan::STATUS_SELESAI
            ? 'Laporan & Arsip'
            : 'Pelaksanaan Kegiatan';

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', 'Rencana proker ini sudah diajukan, jadi tidak bisa lagi diubah dari halaman Rencana Proker. Perubahan datanya sekarang dilakukan di subbab ' . $lanjutanNya . '.');
    }

    /** Data dikunci selama Menunggu Tinjauan (S-82), termasuk untuk admin. */
    private function tolakBilaSedangDitinjau(Kegiatan $proker): ?RedirectResponse
    {
        if (!$proker->sedang_ditinjau) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', 'Rencana ini sedang ditinjau, jadi datanya dikunci sampai disetujui atau dikembalikan dengan catatan.');
    }

    /**
     * Rencana boleh dihapus selama masih draft, atau sudah diajukan tetapi salinan
     * Pelaksanaan/Arsip-nya tidak ada lagi (S-49, keputusan Surya 27 Okt 2026).
     * Rencana yang salinannya dihapus tidak bisa diajukan ulang — buat rencana baru.
     */
    private function bolehDihapus(Kegiatan $proker): bool
    {
        return $proker->status === Kegiatan::STATUS_DRAFT
            || ($proker->status === Kegiatan::STATUS_DIAJUKAN && !$proker->salinan()->exists());
    }

    /**
     * Role saja tidak cukup: proker hanya boleh diubah/dihapus pemiliknya,
     * pengelola yang ia tunjuk, atau override (lihat KegiatanPolicy). Tanpa cek
     * ini ketua bidang mana pun bisa mengubah proker bidang lain lewat URL langsung.
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
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', $this->pengelolaService->pesanTolak($proker));
    }

    private function validateProker(Request $request, ?Kegiatan $proker = null): array
    {
        RoleHimpunan::kunciIsianKegiatan($request, Auth::user());

        // Bidang/unit dan ketua pelaksana wajib sejak S-82 (11 Nov 2026), menggantikan
        // pengecualian Kegiatan Prodi tanpa bidang (S-73): ketua pelaksana staff sebidang
        // yang mengisi rencana, jadi bidangnya harus ada. Visibilitas proker juga
        // ditentukan bidang/unitnya (Kegiatan::scopeTerlihatOleh). Tepat satu bidang:
        // satu proker dipegang satu bidang himpunan.
        $bidangRule = 'required|array|min:1|max:1';
        $bidangItemRule = RoleHimpunan::aturanBidangKegiatan(Auth::user());

        // Jam selesai hanya dibandingkan dengan jam mulai bila kegiatan berlangsung
        // dalam SATU hari. Kegiatan lintas hari (mis. 15 Jan 15.00 → 16 Jan 09.00)
        // memang wajar punya jam selesai yang lebih awal, jadi jangan diblokir.
        $satuHari = !$request->filled('tanggal_selesai')
            || $request->input('tanggal_selesai') === $request->input('tanggal_mulai');

        $jamSelesaiRules = ['nullable', 'date_format:H:i,H:i:s'];
        if ($satuHari && $request->filled('jam_mulai')) {
            $jamSelesaiRules[] = 'after:jam_mulai';
        }

        // Field perencanaan diduplikat dari Subbab 2 (Pelaksanaan) supaya rencana
        // bisa disusun lengkap sejak awal. Selain Judul (penanda draft di daftar) dan
        // Bidang/Unit, semuanya `nullable` saat menyimpan: kelengkapan baru ditegakkan di
        // ajukan() — ketua bisa menyimpan kerangka, staff_himpunan yang melengkapi
        // kemudian. Deskripsi pun tanpa min:20 di sini supaya deskripsi yang baru
        // separuh ditulis tetap bisa disimpan; minimalnya ditagih saat Ajukan.
        //
        // Foto kegiatan TIDAK ada di sini: dokumentasi acara yang sudah berlangsung,
        // diunggah di Subbab 2. Dokumen ada sejak S-49: proposal, RAB, surat undangan,
        // dan surat peminjaman disusun di tahap rencana, lalu ikut disalin ke
        // Pelaksanaan saat proker diajukan.
        return $request->validate([
            'judul'                  => 'required|string|max:' . KegiatanController::MAKS_JUDUL,
            'deskripsi'              => 'nullable|string|max:3000',
            'kategori_kegiatan_id'   => 'nullable|array|max:2',
            'kategori_kegiatan_id.*' => 'integer|exists:mk_kategori_kegiatan,id',
            'bidang_id'              => $bidangRule,
            'bidang_id.*'            => $bidangItemRule,
            // Tanggal mulai tetap boleh kosong (draft), TAPI tidak boleh kosong bila
            // tanggal selesai sudah diisi — kombinasi itu menghasilkan kegiatan
            // "Belum ditentukan — 05 Januari 2026" sekaligus membuat kolom `tahun`
            // ikut kosong, sehingga kegiatan hilang dari filter Tahun di Subbab 2 & 3.
            'tanggal_mulai'          => 'nullable|date|required_with:tanggal_selesai',
            'tanggal_selesai'        => 'nullable|date|after_or_equal:tanggal_mulai',
            // Kolom jam di database bertipe TIME: `string` bebas membuat isian ngawur
            // lolos ke query dan memunculkan halaman error saat disimpan.
            'jam_mulai'              => 'nullable|date_format:H:i,H:i:s',
            'jam_selesai'            => $jamSelesaiRules,
            'lokasi'                 => 'nullable|string|max:150',
            // Ruangan kampus dari master EOffice (S-81); kosong = luar kampus/daring.
            'ruangan_id'             => $this->jadwal->aturanRuangan(),
            'target_peserta'         => 'nullable|integer|min:1',
            'ketua_pelaksana_id'     => ['required', 'exists:students,id', KegiatanPolicy::aturanKetuaSebidang($proker, (int) collect($request->input('bidang_id'))->first() ?: null)],
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'dosen_peran'            => 'nullable|array',
            'dosen_peran.*'          => 'nullable|string|max:60',
            'panitia_ids'            => 'nullable|array',
            'panitia_ids.*'          => 'exists:students,id',
            'panitia_peran'          => 'nullable|array',
            'panitia_peran.*'        => 'nullable|string|max:60',
            'banner'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'       => 'nullable|array|max:' . KegiatanController::MAKS_DOKUMEN,
            'dokumen_kegiatan.*'     => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:' . KegiatanController::MAKS_UKURAN_DOKUMEN_KB,
            'hapus_file'             => 'nullable|array',
            'hapus_file.*'           => 'integer|exists:mk_repo_mulmed,id',
        ] + SumberDana::aturan(false), [
            // Pesan bawaan Laravel masih berbahasa Inggris dan menyebut nama kolom
            // mentah ("The tanggal mulai field is required when..."), jadi aturan
            // baru di atas diberi pesan sendiri supaya jelas di kotak error form.
            'bidang_id.required'             => 'Bidang/unit pemegang proker wajib dipilih.',
            'bidang_id.min'                  => 'Bidang/unit pemegang proker wajib dipilih.',
            'ketua_pelaksana_id.required'    => 'Ketua pelaksana wajib dipilih.',
            'bidang_id.max'                  => 'Pilih satu bidang saja — satu proker dipegang satu bidang himpunan.',
            'bidang_id.*.in'                 => RoleHimpunan::PESAN_BIDANG_TERKUNCI,
            'tanggal_mulai.required_with'    => 'Tanggal mulai wajib diisi kalau tanggal selesai sudah ditentukan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
            'ruangan_id.integer'             => 'Pilihan ruangan tidak valid.',
            'ketua_pelaksana_id.in'          => KegiatanPolicy::PESAN_KETUA_SEBIDANG,
        ] + SumberDana::pesan() + KegiatanController::pesanValidasiDokumen());
    }

    /**
     * Petakan hasil validasi ke kolom mk_kegiatan.
     *
     * Catatan penting: `is_pelaksanaan_updated` sengaja TIDAK disentuh di sini.
     * Flag itu milik Subbab 2 dan menjadi penanda "sudah dicek sesuai realisasi"
     * sebelum kegiatan boleh diunggah ke Arsip.
     */
    private function kolomKegiatan(array $validated, ?Kegiatan $lama = null): array
    {
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds   = $validated['bidang_id'] ?? [];

        // Lokasi dari ruangan EOffice atau teks bebas (S-81); anggaran = jumlah sumber dana (S-80).
        return $this->jadwal->kolomLokasi($validated) + SumberDana::kolom($validated, $lama) + [
            'judul'              => $validated['judul'],
            // Kolom `deskripsi` NOT NULL, sedangkan draft boleh tanpa deskripsi
            'deskripsi'          => $validated['deskripsi'] ?? '',
            'tanggal_mulai'      => $validated['tanggal_mulai'] ?? null,
            // Isi kolom `tahun` dari tanggal mulai supaya kegiatan hasil alur himpunan
            // tidak ber-`tahun` NULL dan tetap muncul di filter tahun Pelaksanaan & Arsip.
            'tahun'              => !empty($validated['tanggal_mulai'])
                ? \Carbon\Carbon::parse($validated['tanggal_mulai'])->year
                : null,
            'tanggal_selesai'    => $validated['tanggal_selesai'] ?? null,
            'jam_mulai'          => $validated['jam_mulai'] ?? null,
            'jam_selesai'        => $validated['jam_selesai'] ?? null,
            'target_peserta'     => $validated['target_peserta'] ?? null,
            'ketua_pelaksana_id' => $validated['ketua_pelaksana_id'] ?? null,
            // Kolom FK lama disinkronkan dengan elemen pertama agar view lama tetap konsisten
            'kategori_kegiatan_id' => $kategoriIds[0] ?? null,
            'bidang_id'            => $bidangIds[0] ?? null,
        ];
    }

    /**
     * Sinkronisasi seluruh relasi many-to-many + penanggung jawab.
     * Dipakai bersama oleh store() dan update().
     */
    private function syncRelasiKegiatan(Kegiatan $proker, array $validated, Request $request): void
    {
        // Penanggung jawab diturunkan dari ketua pelaksana (kolom legacy)
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $proker->update(['penanggung_jawab' => $student?->user?->name]);
        } else {
            $proker->update(['penanggung_jawab' => null]);
        }

        // Kategori & bidang — detach bila kosong supaya pivot tidak "nyangkut"
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds   = $validated['bidang_id'] ?? [];

        if (!empty($kategoriIds)) {
            $proker->kategoris()->sync($kategoriIds);
        } else {
            $proker->kategoris()->detach();
        }
        if (!empty($bidangIds)) {
            $proker->bidangs()->sync($bidangIds);
        } else {
            $proker->bidangs()->detach();
        }

        // Panitia + jabatan masing-masing
        $panitiaIds   = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $pid) {
            $panitiaSyncData[$pid] = ['peran' => $panitiaPeran[$pid] ?? null];
        }
        $proker->panitia()->sync($panitiaSyncData);

        // Dosen pendamping (many-to-many)
        $proker->syncDosenPendamping($validated['dosen_pendamping_ids'] ?? [], $request->input('dosen_peran', []));
    }

    /**
     * Checklist kelengkapan rencana sebelum boleh diajukan ke Subbab 2.
     *
     * @return array<int, array{field: string, label: string, terisi: bool}>
     */
    private function cekKelengkapan(Kegiatan $proker): array
    {
        $kategoriIds = $proker->kategoris->pluck('id')->all();

        return collect(self::SYARAT_AJUKAN)->map(fn($label, $field) => [
            'field'  => $field,
            'label'  => $label,
            'terisi' => match ($field) {
                'kategori'  => !empty($kategoriIds),
                // Bidang/unit wajib, termasuk Kegiatan Prodi (2 Nov 2026) — draft lama
                // tanpa bidang harus dilengkapi dulu sebelum diajukan.
                'bidang'    => $proker->bidangs->isNotEmpty(),
                'deskripsi' => mb_strlen(trim((string) $proker->deskripsi)) >= 20,
                'panitia'   => $proker->panitia->isNotEmpty(),
                // Minimal satu sumber dana terisi (S-80); 0 sah (kegiatan tanpa biaya).
                'anggaran'  => SumberDana::terisi($proker),
                'dokumen_proposal' => $this->punyaDokumen($proker, 'proposal'),
                'dokumen_rab'      => $this->punyaDokumen($proker, 'rab'),
                default     => !empty($proker->{$field}),
            },
        ])->values()->all();
    }

    private function punyaDokumen(Kegiatan $proker, string $jenis): bool
    {
        return $proker->repoMulmed
            ->where('tipe_file', RepoMulmed::TIPE_DOCUMENT)
            ->where('jenis_dokumen', $jenis)
            ->isNotEmpty();
    }

}
