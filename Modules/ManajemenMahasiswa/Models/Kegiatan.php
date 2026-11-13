<?php

namespace Modules\ManajemenMahasiswa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'mk_kegiatan';

    protected $fillable = [
        'user_id',
        'kategori_kegiatan_id',
        'bidang_id',
        'tahun',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'lokasi',
        'banner',
        'surat_proker',
        'surat_proker_original',
        'anggaran',
        // Rincian anggaran per sumber dana (S-80); `anggaran` = jumlah ketiganya.
        'dana_departemen',
        'kas_himpunan',
        'kas_bidang',
        // Ruangan kampus dari master EOffice (S-81); null = luar kampus/daring.
        'ruangan_id',
        'penanggung_jawab',
        'ketua_pelaksana_id',
        'target_peserta',
        'status',
        // Alur tinjau ketua pelaksana ↔ KABID (S-82); lihat konstanta TINJAU_*.
        'status_tinjauan',
        'dikirim_tinjau_at',
        'rencana_id',
        'is_pelaksanaan_updated',
    ];

    protected $casts = [
        'dikirim_tinjau_at'      => 'datetime',
        'tanggal_mulai'          => 'date',
        'tanggal_selesai'        => 'date',
        'anggaran'               => 'decimal:2',
        'dana_departemen'        => 'decimal:2',
        'kas_himpunan'           => 'decimal:2',
        'kas_bidang'             => 'decimal:2',
        'is_pelaksanaan_updated' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Attribute Accessors for Time
    // -------------------------------------------------------------------------

    public function getJamMulaiFormattedAttribute(): ?string
    {
        return $this->jam_mulai ? \Carbon\Carbon::parse($this->jam_mulai)->format('H:i') : null;
    }

    public function getJamSelesaiFormattedAttribute(): ?string
    {
        return $this->jam_selesai ? \Carbon\Carbon::parse($this->jam_selesai)->format('H:i') : null;
    }

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    // ── Status Aktif (alur yang digunakan saat ini) ────────────────────────
    const STATUS_DRAFT     = 'draft';      // Subbab 1 — baru dibuat, belum diajukan
    const STATUS_DIAJUKAN  = 'diajukan';   // Subbab 1 — rencana sudah diajukan & terkunci (S-49)
    const STATUS_DISETUJUI = 'disetujui';  // Subbab 2 — salinan rencana, siap dilaksanakan
    const STATUS_SELESAI   = 'selesai';    // Subbab 3 — selesai, masuk arsip

    // ── Status Legacy (tidak digunakan lagi, disimpan untuk backward compat) ─
    /** @deprecated Tidak digunakan dalam alur bisnis aktif */
    const STATUS_AKAN_DATANG = 'akan_datang';
    /** @deprecated Tidak digunakan dalam alur bisnis aktif */
    const STATUS_BERLANGSUNG = 'berlangsung';

    /** Nilai dropdown filter tahun untuk kegiatan yang belum punya tanggal sama sekali. */
    const FILTER_TANPA_TAHUN = 'tanpa-tahun';

    const STATUS_LIST = [
        self::STATUS_DRAFT,
        self::STATUS_DISETUJUI,
        self::STATUS_SELESAI,
    ];

    /** Status yang masih dalam fase perencanaan (Subbab 1) */
    const STATUS_PERENCANAAN = [
        self::STATUS_DRAFT,
    ];

    /**
     * Isi daftar Rencana Proker (S-49): draft + rencana yang sudah diajukan.
     *
     * Rencana yang sudah diajukan tetap tampil di Subbab 1 (terkunci) — Pelaksanaan
     * bekerja pada salinannya. Sengaja konstanta terpisah dari STATUS_PERENCANAAN,
     * yang dipakai Dashboard Analitik untuk "proker terbaru".
     */
    const STATUS_DAFTAR_RENCANA = [
        self::STATUS_DRAFT,
        self::STATUS_DIAJUKAN,
    ];

    /**
     * Tahap sebuah rencana dilihat dari salinannya (badge & filter Rencana Proker).
     * Kunci = nilai ?status_rencana= di daftar.
     */
    const TAHAP_RENCANA_LABELS = [
        'draft'            => 'Di Rencana Proker',
        'pelaksanaan'      => 'Di Pelaksanaan Kegiatan',
        'arsip'            => 'Di Laporan & Arsip',
        'tidak_terlaksana' => 'Tidak terlaksana',
    ];

    /**
     * Pilihan filter "Status Rencana" (11 Nov 2026): tahap dan status tinjauan digabung
     * jadi satu dropdown. "Di Rencana Proker" tidak lagi jadi pilihan sendiri karena
     * sudah terwakili tiga status tinjauan (rencana yang masih di tahap ini selalu
     * berada di salah satunya). Tanpa "Tidak terlaksana" dan tanpa "Semua" — filter
     * dikosongkan lewat Reset. Kunci = nilai ?status_rencana=.
     */
    const TAHAP_FILTER_LABELS = [
        self::TINJAU_DILENGKAPI => 'Menunggu Dilengkapi',
        self::TINJAU_DITINJAU   => 'Menunggu Tinjauan',
        self::TINJAU_PERBAIKAN  => 'Perlu Perbaikan',
        'pelaksanaan'           => 'Di Pelaksanaan Kegiatan',
        'arsip'                 => 'Di Laporan & Arsip',
    ];

    /** Warna badge tahap — kelas .mm-status--{nada} di partials/sitkom-ui. */
    const TAHAP_RENCANA_NADA = [
        'draft'            => 'neutral',
        'pelaksanaan'      => 'sky',
        'arsip'            => 'success',
        'tidak_terlaksana' => 'error',
    ];

    // ── Alur tinjau (S-82, 11 Nov 2026) ────────────────────────────────────
    // KABID membuat proker dan menunjuk ketua pelaksana, lalu hanya memantau. Ketua
    // pelaksana mengisi dan mengirim; KABID menyetujui (rencana diteruskan ke
    // Pelaksanaan, pelaksanaan diunggah ke Arsip) atau mengembalikan dengan catatan.
    // Berlaku pada baris berstatus draft (Rencana) dan disetujui (Pelaksanaan).
    const TINJAU_DILENGKAPI = 'dilengkapi';
    const TINJAU_DITINJAU   = 'ditinjau';
    const TINJAU_PERBAIKAN  = 'perbaikan';

    /** Status baris yang memakai alur tinjau. */
    const STATUS_BERTINJAU = [self::STATUS_DRAFT, self::STATUS_DISETUJUI];

    const TINJAUAN_LABELS = [
        self::TINJAU_DILENGKAPI => 'Menunggu Dilengkapi',
        self::TINJAU_DITINJAU   => 'Menunggu Tinjauan',
        self::TINJAU_PERBAIKAN  => 'Perlu Perbaikan',
    ];

    /**
     * Urutan bagian/filter status untuk peninjau (KABID dkk., KAHIM, admin): "Menunggu
     * Tinjauan" naik ke paling atas karena itu yang perlu mereka kerjakan. Staff /
     * ketua pelaksana memakai urutan asli (Menunggu Dilengkapi di atas).
     */
    public static function urutkanUntukPeninjau(array $labels, bool $peninjau): array
    {
        if (! $peninjau || ! isset($labels[self::TINJAU_DITINJAU])) {
            return $labels;
        }

        return [self::TINJAU_DITINJAU => $labels[self::TINJAU_DITINJAU]] + $labels;
    }

    /** Warna badge — kelas .mm-status--{nada} di partials/sitkom-ui. */
    const TINJAUAN_NADA = [
        self::TINJAU_DILENGKAPI => 'warning',
        self::TINJAU_DITINJAU   => 'sky',
        self::TINJAU_PERBAIKAN  => 'error',
    ];

    /** Status yang sudah disetujui / dalam proses pelaksanaan (Subbab 2) */
    const STATUS_PELAKSANAAN = [
        self::STATUS_DISETUJUI,
    ];

    /** Label status untuk ditampilkan di UI */
    const STATUS_LABELS = [
        self::STATUS_DRAFT     => 'Draft',
        self::STATUS_DIAJUKAN  => 'Rencana Diajukan',
        self::STATUS_DISETUJUI => 'Pelaksanaan',
        self::STATUS_SELESAI   => 'Selesai',
    ];

    /** Warna badge per status */
    const STATUS_COLORS = [
        self::STATUS_DRAFT     => 'secondary',
        self::STATUS_DIAJUKAN  => 'secondary',
        self::STATUS_DISETUJUI => 'primary',
        self::STATUS_SELESAI   => 'success',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kategoriKegiatan(): BelongsTo
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_kegiatan_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    /**
     * Many-to-many: Kegiatan can have multiple Kategori (max 2).
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(KategoriKegiatan::class, 'mk_kegiatan_kategori', 'kegiatan_id', 'kategori_kegiatan_id')->withTimestamps();
    }

    /**
     * Many-to-many: Kegiatan can have multiple Bidang.
     */
    public function bidangs(): BelongsToMany
    {
        return $this->belongsToMany(Bidang::class, 'mk_kegiatan_bidang', 'kegiatan_id', 'bidang_id')->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function ketuaPelaksana(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'ketua_pelaksana_id');
    }

    /**
     * Many-to-many: Kegiatan dapat didampingi lebih dari satu dosen.
     */
    public function dosenPendampings(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Lecturer::class, 'mk_kegiatan_dosen_pendamping', 'kegiatan_id', 'lecturer_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

    /**
     * Simpan daftar dosen beserta perannya (isian bebas, boleh kosong).
     *
     * @param  array<int|string>  $ids
     * @param  array<int|string, string|null>  $peran  dikunci per id dosen
     */
    public function syncDosenPendamping(array $ids, array $peran = []): void
    {
        $data = [];
        foreach ($ids as $id) {
            $data[$id] = ['peran' => filled($peran[$id] ?? null) ? trim($peran[$id]) : null];
        }
        $this->dosenPendampings()->sync($data);
    }

    /**
     * Many-to-many: Kegiatan dapat memiliki banyak panitia (mahasiswa).
     */
    public function panitia(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Student::class, 'mk_kegiatan_panitia', 'kegiatan_id', 'student_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

    /**
     * Many-to-many: pengurus yang ditunjuk pemilik untuk ikut mengelola kegiatan ini.
     *
     * Pengelola hanya staff himpunan dan hanya boleh mengedit — ketua bidang/unit
     * lain tidak bisa ditunjuk (KegiatanPolicy::bisaJadiPengelola). Kolom
     * `boleh_hapus` peninggalan aturan lama dan kini selalu false; penentunya
     * tetap KegiatanPolicy::delete.
     */
    public function pengelola(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'mk_kegiatan_pengelola', 'kegiatan_id', 'user_id')
                    ->withPivot('boleh_hapus')
                    ->withTimestamps();
    }

    public function riwayatKegiatan(): HasMany
    {
        return $this->hasMany(RiwayatKegiatan::class, 'kegiatan_id');
    }

    public function repoMulmed(): HasMany
    {
        return $this->hasMany(RepoMulmed::class, 'kegiatan_id');
    }

    /**
     * Catatan penolakan KABID dkk. pada rencana (S-79), terbaru dulu. Hanya dipakai
     * saat rencana dikembalikan ke staff untuk diperbaiki.
     */
    public function catatan(): HasMany
    {
        return $this->hasMany(KegiatanCatatan::class, 'kegiatan_id')->latest()->latest('id');
    }

    /** Catatan yang belum ditandai "sudah diperbaiki" → badge "Perlu perbaikan". */
    public function catatanTerbuka(): HasMany
    {
        return $this->hasMany(KegiatanCatatan::class, 'kegiatan_id')->whereNull('diperbaiki_at');
    }

    /**
     * Rencana asal salinan ini (S-49). Hanya terisi pada baris Pelaksanaan/Arsip yang
     * lahir dari "Ajukan Proker"; kegiatan lama & tambahan langsung ke Arsip NULL.
     */
    public function rencana(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rencana_id');
    }

    /**
     * Banner hasil salinan Rencana Proker terkunci di Pelaksanaan & Arsip (S-61):
     * semua yang diunggah di rencana tidak bisa diganti atau dihapus sesudahnya.
     * Kegiatan lama tanpa rencana, atau yang rencananya tanpa banner, tetap bebas.
     */
    public function bannerDariRencana(): bool
    {
        return $this->rencana_id !== null && filled($this->rencana?->banner);
    }

    /**
     * Salinan Pelaksanaan/Arsip milik rencana ini (S-49) — paling banyak satu,
     * karena rencana tidak bisa diajukan ulang.
     */
    public function salinan(): HasOne
    {
        return $this->hasOne(self::class, 'rencana_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByTahun($query, int $tahun)
    {
        return $query->whereYear('tanggal_mulai', $tahun);
    }

    public function scopeByBidang($query, int $bidangId)
    {
        return $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $bidangId));
    }

    /**
     * Kegiatan yang boleh dilihat user di Rencana Proker & Pelaksanaan.
     *
     * KAHIM/WAKAHIM, admin, dan pengawas melihat semua. Pemegang role berbidang
     * (KABID, WAKABID, KANIT, WAKANIT, Staff) hanya kegiatan yang diset ke
     * bidang/unitnya. Kegiatan tanpa bidang otomatis tidak ikut. Yang belum diset
     * bidangnya tidak melihat apa pun. Sinkron dengan KegiatanPolicy::view().
     */
    public function scopeTerlihatOleh($query, User $user)
    {
        if (RoleHimpunan::bisaLihatSemuaProker($user)) {
            return $query;
        }

        $bidangId = RoleHimpunan::bidangId($user);

        return $bidangId && $user->hasAnyRole(RoleHimpunan::BERBIDANG)
            ? $query->byBidang($bidangId)
            : $query->whereRaw('1 = 0');
    }

    /**
     * Apakah SEMUA kategori yang dipilih merupakan kategori Prodi?
     *
     * Hanya untuk kondisi itulah Bidang boleh dikosongkan. Definisinya ditaruh di
     * model supaya Rencana Proker & Pelaksanaan memakai aturan yang sama persis:
     * sebelumnya Subbab 2 menerima `bidang_id` apa adanya (nullable), sehingga
     * kegiatan berkategori "Kegiatan Himpunan" bisa disimpan tanpa bidang lalu
     * ditampilkan berlabel "Prodi" — padahal aksi yang sama ditolak di Subbab 1.
     *
     * Sengaja memakai "semua yang dipilih berkategori Prodi", identik dengan
     * toggleBidangField() di form (partials/kegiatan-form/_scripts.blade.php),
     * supaya kolom Bidang tidak pernah disembunyikan JS tapi tetap diwajibkan server.
     *
     * @param array<int, mixed> $kategoriIds
     */
    public static function hanyaKategoriProdi(array $kategoriIds): bool
    {
        $kategoriIds = array_filter($kategoriIds, fn($id) => $id !== null && $id !== '');

        if (empty($kategoriIds)) {
            return false;
        }

        return !KategoriKegiatan::whereIn('id', $kategoriIds)
            ->where('nama_kategori', 'not like', '%Prodi%')
            ->exists();
    }

    /**
     * Filter tahun yang tahan terhadap kolom `tahun` yang belum terisi.
     *
     * Sebagian kegiatan tersimpan dengan `tahun` NULL walau `tanggal_mulai`-nya
     * jelas ada — kolom `tahun` dulu tidak ikut diisi oleh alur Proker →
     * Pelaksanaan. Kalau difilter dengan where('tahun', ...) saja, kegiatan itu
     * lenyap dari daftar begitu user memilih tahun mana pun, padahal tanggalnya
     * terpampang di kartunya. Karena itu `tanggal_mulai` dipakai sebagai cadangan.
     */
    public function scopeFilterTahun($query, $tahun)
    {
        return $query->where(function ($q) use ($tahun) {
            $q->where('tahun', $tahun)
              ->orWhere(fn($sub) => $sub->whereNull('tahun')->whereYear('tanggal_mulai', $tahun));
        });
    }

    /**
     * Kegiatan yang tidak punya penanda waktu sama sekali.
     *
     * Sebagian kegiatan lama tersimpan tanpa `tahun` MAUPUN `tanggal_mulai`,
     * jadi tahunnya memang tidak diketahui dan tidak boleh ditebak. Tanpa filter
     * khusus, kegiatan seperti ini cuma bisa dilihat lewat "Semua Tahun" dan
     * praktis tidak pernah ketemu — padahal justru itu yang perlu dilengkapi.
     */
    public function scopeTanpaTahun($query)
    {
        return $query->whereNull('tahun')->whereNull('tanggal_mulai');
    }

    /**
     * Daftar tahun untuk dropdown filter.
     *
     * Memakai `tanggal_mulai` sebagai cadangan dengan alasan yang sama seperti
     * scopeFilterTahun(): tanpa itu, tahun milik kegiatan ber-`tahun` NULL tidak
     * pernah muncul sebagai pilihan sehingga kegiatannya mustahil ditemukan.
     *
     * Berurutan tanpa celah dari tahun kegiatan tertua sampai tahun berjalan (atau
     * tahun kegiatan terjauh bila sudah ada rencana tahun depan), sehingga tahun
     * baru otomatis muncul begitu tahun berganti (S-63).
     *
     * @param  string|array<int, string>  $status
     * @param  User|null  $user  bila diisi, hanya kegiatan yang terlihat olehnya (scopeTerlihatOleh)
     * @return array<int, int> tahun terbaru lebih dulu; minimal berisi tahun berjalan
     */
    public static function daftarTahun(string|array $status, ?User $user = null): array
    {
        $daftar = static::query()
            ->whereIn('status', (array) $status)
            ->when($user, fn($q) => $q->terlihatOleh($user))
            ->where(fn($q) => $q->whereNotNull('tahun')->orWhereNotNull('tanggal_mulai'))
            ->get(['tahun', 'tanggal_mulai'])
            ->map(fn($kegiatan) => $kegiatan->tahun ?? $kegiatan->tanggal_mulai?->year)
            ->filter()
            ->map(fn($tahun) => (int) $tahun)
            ->push((int) date('Y'));

        return range($daftar->max(), $daftar->min());
    }

    /**
     * Filter Periode Kepengurusan (S-62): kegiatan yang tanggal mulainya jatuh
     * dalam masa jabatan kabinet itu (lihat Support\PeriodeKepengurusan).
     *
     * Kegiatan Prodi ikut periode juga (S-64, keputusan Surya). Kegiatan tanpa
     * tanggal mulai tidak punya periode — cari lewat filter Tahun "Belum ada tanggal".
     */
    public function scopeFilterPeriode($query, int $periode)
    {
        [$mulai, $selesai] = PeriodeKepengurusan::rentang($periode);

        return $query
            ->whereDate('tanggal_mulai', '>=', $mulai)
            ->whereDate('tanggal_mulai', '<=', $selesai);
    }

    /**
     * Daftar periode untuk dropdown filter (S-63): berurutan tanpa celah dari
     * periode kegiatan tertua sampai periode berjalan — atau periode kegiatan
     * terjauh bila ada rencana untuk kabinet berikutnya.
     *
     * @param  string|array<int, string>  $status
     * @param  User|null  $user  bila diisi, hanya kegiatan yang terlihat olehnya (scopeTerlihatOleh)
     * @return array<int, int> tahun awal periode, terbaru lebih dulu
     */
    public static function daftarPeriode(string|array $status, ?User $user = null): array
    {
        $periode = static::query()
            ->whereIn('status', (array) $status)
            ->when($user, fn($q) => $q->terlihatOleh($user))
            ->whereNotNull('tanggal_mulai')
            ->pluck('tanggal_mulai')
            ->map(fn ($tanggal) => PeriodeKepengurusan::untukTanggal($tanggal))
            ->push(PeriodeKepengurusan::sekarang())
            ->filter();

        return range($periode->max(), $periode->min());
    }

    /**
     * Filter rentang tanggal (S-81): kegiatan yang berlangsung di dalam rentang itu,
     * termasuk yang mulai sebelum `dari` tetapi masih berjalan. Tanggal kosong =
     * tanpa batas di sisi itu. Kegiatan tanpa tanggal mulai tidak ikut.
     */
    public function scopeFilterRentang($query, ?string $dari, ?string $sampai)
    {
        return $query
            ->whereNotNull('tanggal_mulai')
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_mulai', '<=', $sampai))
            ->when($dari, fn ($q) => $q->where(fn ($w) => $w
                ->whereDate('tanggal_selesai', '>=', $dari)
                ->orWhere(fn ($t) => $t->whereNull('tanggal_selesai')->whereDate('tanggal_mulai', '>=', $dari))));
    }

    /**
     * Ambil rentang tanggal yang sah dari request filter (`dari`, `sampai`, format
     * Y-m-d). Nilai rusak diabaikan; bila terbalik, ditukar.
     *
     * @return array{0: ?string, 1: ?string}
     */
    public static function rentangDariRequest(\Illuminate\Http\Request $request): array
    {
        $tgl = function ($v) {
            if (!is_string($v) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                return null;
            }
            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', $v)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        };
        $dari   = $tgl($request->query('dari'));
        $sampai = $tgl($request->query('sampai'));
        if ($dari && $sampai && $dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari, $sampai];
    }

    public function scopeByKategori($query, int $kategoriId)
    {
        return $query->whereHas('kategoris', fn($q) => $q->where('mk_kategori_kegiatan.id', $kategoriId));
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->banner) : null;
    }

    public function getSuratProkerUrlAttribute(): ?string
    {
        return $this->surat_proker ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->surat_proker) : null;
    }

    /**
     * URL PDF asli sebelum ada TTD — digunakan untuk regenerasi saat cancel/ulang TTD.
     */
    public function getSuratProkerOriginalUrlAttribute(): ?string
    {
        return $this->surat_proker_original
            ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($this->surat_proker_original)
            : $this->surat_proker_url; // fallback ke surat_proker jika belum ada original
    }


    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status ?? '');
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Apakah proker ini masih dalam tahap perencanaan (Subbab 1)?
     */
    public function getIsPerencanaanAttribute(): bool
    {
        return in_array($this->status, self::STATUS_PERENCANAAN);
    }

    /**
     * Apakah proker ini sudah disetujui / dalam pelaksanaan (Subbab 2)?
     */
    public function getIsPelaksanaanAttribute(): bool
    {
        return in_array($this->status, self::STATUS_PELAKSANAAN);
    }

    /**
     * Apakah proker ini sudah selesai (Subbab 3)?
     */
    public function getIsSelesaiAttribute(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    /**
     * Tahap rencana untuk badge Rencana Proker (kunci TAHAP_RENCANA_LABELS), atau null
     * bila baris ini bukan rencana. Di daftar, muat relasi `salinan` lebih dulu.
     */
    public function getTahapRencanaAttribute(): ?string
    {
        if ($this->status === self::STATUS_DRAFT) {
            return 'draft';
        }
        if ($this->status !== self::STATUS_DIAJUKAN) {
            return null;
        }

        return match ($this->salinan?->status) {
            self::STATUS_DISETUJUI => 'pelaksanaan',
            self::STATUS_SELESAI   => 'arsip',
            default                => 'tidak_terlaksana',
        };
    }

    /**
     * Status tinjauan yang berlaku (kunci TINJAUAN_LABELS), atau null bila baris ini
     * tidak sedang dalam alur tinjau (rencana yang sudah diteruskan, arsip). Baris
     * lama yang kolomnya masih kosong dianggap Menunggu Dilengkapi.
     */
    public function getTahapTinjauanAttribute(): ?string
    {
        if (!in_array($this->status, self::STATUS_BERTINJAU, true)) {
            return null;
        }

        return array_key_exists((string) $this->status_tinjauan, self::TINJAUAN_LABELS)
            ? $this->status_tinjauan
            : self::TINJAU_DILENGKAPI;
    }

    /** Data dikunci selama KABID meninjau (S-82). */
    public function getSedangDitinjauAttribute(): bool
    {
        return $this->tahap_tinjauan === self::TINJAU_DITINJAU;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Saring per status tinjauan (?tinjauan= di daftar Rencana & Pelaksanaan, dan
     * tautan lonceng notifikasi). Baris lama berkolom kosong ikut "dilengkapi".
     */
    public function scopeTinjauan($query, string $kunci)
    {
        if (!array_key_exists($kunci, self::TINJAUAN_LABELS)) {
            return $query;
        }

        return $query->whereIn('status', self::STATUS_BERTINJAU)
            ->where(fn ($q) => $kunci === self::TINJAU_DILENGKAPI
                ? $q->where('status_tinjauan', $kunci)->orWhereNull('status_tinjauan')
                : $q->where('status_tinjauan', $kunci));
    }

    public function scopePerencanaan($query)
    {
        return $query->whereIn('status', self::STATUS_PERENCANAAN);
    }

    public function scopePelaksanaan($query)
    {
        return $query->whereIn('status', self::STATUS_PELAKSANAAN);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    /**
     * Filter daftar Rencana Proker per tahap (kunci TAHAP_RENCANA_LABELS).
     * Kunci yang tidak dikenal diabaikan.
     */
    public function scopeTahapRencana($query, string $tahap)
    {
        $diajukan = fn ($q) => $q->where('status', self::STATUS_DIAJUKAN);

        // Tiga status tinjauan hanya berlaku untuk rencana yang masih berstatus draft.
        if (array_key_exists($tahap, self::TINJAUAN_LABELS)) {
            return $query->where('status', self::STATUS_DRAFT)->tinjauan($tahap);
        }

        return match ($tahap) {
            'draft'            => $query->where('status', self::STATUS_DRAFT),
            'pelaksanaan'      => $diajukan($query)->whereHas('salinan', fn ($s) => $s->where('status', self::STATUS_DISETUJUI)),
            'arsip'            => $diajukan($query)->whereHas('salinan', fn ($s) => $s->where('status', self::STATUS_SELESAI)),
            'tidak_terlaksana' => $diajukan($query)->doesntHave('salinan'),
            default            => $query,
        };
    }
}