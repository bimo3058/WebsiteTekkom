<?php

namespace Modules\ManajemenMahasiswa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Prestasi extends Model
{
    use HasFactory;

    protected $table = 'mk_prestasi';

    protected $fillable = [
        'kemahasiswaan_id',
        'nama_prestasi',
        'tingkat',
        'tanggal',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_note',
        // Workflow reward prestasi (B.3 claim + B.2 reward / SK FT 774)
        'claim_status',
        'claimed_at',
        'claimed_by',
        'reward_penyelenggara',
        'reward_capaian',
        'reward_is_invention',
        // Dibekukan saat klaim diajukan — lihat SK_BERLAKU
        'reward_kuota_grup',
        'reward_sk_ref',
        'reward_tahun_ajaran',
        'reward_jml_mk_max',
        'reward_sks_max',
        'reward_sks_diajukan',
        'reward_mk_diajukan',
        'reward_mk_disetujui',
        'reward_reviewed_by',
        'reward_reviewed_at',
        'reward_note',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'verified_at'         => 'datetime',
        'claimed_at'          => 'datetime',
        'reward_is_invention' => 'boolean',
        'reward_reviewed_at'  => 'datetime',
        'reward_mk_diajukan'  => 'array',
    ];

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    const TINGKAT_INTERNASIONAL = 'internasional';
    const TINGKAT_NASIONAL      = 'nasional';
    const TINGKAT_REGIONAL      = 'regional';
    const TINGKAT_UNIVERSITAS   = 'universitas';
    const TINGKAT_PRODI         = 'prodi';

    const TINGKAT_LIST = [
        self::TINGKAT_INTERNASIONAL,
        self::TINGKAT_NASIONAL,
        self::TINGKAT_REGIONAL,
        self::TINGKAT_UNIVERSITAS,
        self::TINGKAT_PRODI,
    ];

    // Verification statuses
    const VERIF_PENDING  = 'pending';
    const VERIF_APPROVED = 'approved';
    const VERIF_REJECTED = 'rejected';

    const VERIF_LIST = [
        self::VERIF_PENDING,
        self::VERIF_APPROVED,
        self::VERIF_REJECTED,
    ];

    // Status workflow reward (kolom claim_status, reuse dari B.3)
    // Alur: belum_ajukan -> diajukan -> disetujui | ditolak
    const CLAIM_BELUM_AJUKAN = 'belum_ajukan';
    const CLAIM_DIAJUKAN     = 'diajukan';
    const CLAIM_DISETUJUI    = 'disetujui';
    const CLAIM_DITOLAK      = 'ditolak';

    const CLAIM_LIST = [
        self::CLAIM_BELUM_AJUKAN,
        self::CLAIM_DIAJUKAN,
        self::CLAIM_DISETUJUI,
        self::CLAIM_DITOLAK,
    ];

    const CLAIM_LABELS = [
        self::CLAIM_BELUM_AJUKAN => 'Belum diajukan',
        self::CLAIM_DIAJUKAN     => 'Menunggu Review',
        self::CLAIM_DISETUJUI    => 'Disetujui',
        self::CLAIM_DITOLAK      => 'Ditolak',
    ];

    // -------------------------------------------------------------------------
    // Reward — Penyelenggara & Capaian (dasar SK FT Undip No. 774/2025)
    // -------------------------------------------------------------------------

    /**
     * SK yang berlaku untuk pengajuan reward BARU.
     *
     * Nilainya dicap ke tiap klaim saat diajukan, bersama kelompok kuotanya,
     * supaya keputusan lama tidak pernah dihitung ulang dengan aturan baru.
     *
     * Saat SK berganti, developer cukup mengubah konstanta ini dan menambahkan
     * aturan barunya di hitungJatahReward()/tentukanKuotaGrup(). Tiga hal yang
     * TIDAK boleh dilakukan, karena akan merusak klaim lama:
     *   1. menghapus kategori penyelenggara/capaian lama — baris lama menunjuk ke sana;
     *   2. mengganti nama kode kategori yang sudah pernah tersimpan;
     *   3. mengisi ulang reward_sk_ref / reward_kuota_grup pada klaim yang sudah ada.
     */
    const SK_BERLAKU = 'SK FT Undip No. 774/2025';

    /**
     * Tanggal mulai berlaku tiap SK (SK 774 poin 9: terhitung Januari 2025).
     *
     * Dikunci per SK, bukan satu tanggal tunggal: klaim lama dibaca dengan
     * tanggal milik SK yang dicap padanya, jadi tanda "sebelum masa berlaku"
     * tidak ikut bergeser saat SK_BERLAKU diganti. Saat SK berganti, tambahkan
     * barisnya di sini — jangan ubah baris yang sudah ada.
     */
    const SK_MULAI_BERLAKU = [
        self::SK_BERLAKU => '2025-01-01',
    ];

    const PENYELENGGARA_BELMAWA = 'belmawa_puspresnas'; // Ditjen Belmawa / Puspresnas (poin 1)
    const PENYELENGGARA_LAINNYA = 'lainnya';            // Selain Belmawa/Puspresnas (poin 2)

    const PENYELENGGARA_LIST = [
        self::PENYELENGGARA_BELMAWA,
        self::PENYELENGGARA_LAINNYA,
    ];

    const PENYELENGGARA_LABELS = [
        self::PENYELENGGARA_BELMAWA => 'Belmawa / Puspresnas (Ditjen Dikti)',
        self::PENYELENGGARA_LAINNYA => 'Selain Belmawa / Puspresnas',
    ];

    // Kode capaian/peringkat
    const CAPAIAN_JUARA1_EMAS              = 'juara1_emas';
    const CAPAIAN_JUARA23_PERAK_PERUNGGU  = 'juara23_perak_perunggu';
    const CAPAIAN_FINALIS                 = 'finalis';
    const CAPAIAN_PENDANAAN_PKM           = 'pendanaan_pkm';
    const CAPAIAN_PKM_FINALIS_PIMNAS      = 'pkm_finalis_pimnas';
    const CAPAIAN_FINALIS_PIMNAS_EMAS     = 'finalis_pimnas_juara_emas';
    const CAPAIAN_FINALIS_PIMNAS_PERAK    = 'finalis_pimnas_juara_perak_perunggu';

    const CAPAIAN_LABELS = [
        self::CAPAIAN_JUARA1_EMAS             => 'Juara 1 / Medali Emas',
        self::CAPAIAN_JUARA23_PERAK_PERUNGGU  => 'Juara 2-3 / Medali Perak-Perunggu',
        self::CAPAIAN_FINALIS                 => 'Finalis',
        self::CAPAIAN_PENDANAAN_PKM           => 'Pendanaan PKM / PPK Ormawa / P2MW',
        self::CAPAIAN_PKM_FINALIS_PIMNAS      => 'Pendanaan PKM → Finalis PIMNAS / Abdidaya / KMI Expo',
        self::CAPAIAN_FINALIS_PIMNAS_EMAS     => 'Finalis PIMNAS / Abdidaya / KMI Expo + Medali Emas',
        self::CAPAIAN_FINALIS_PIMNAS_PERAK    => 'Finalis PIMNAS / Abdidaya / KMI Expo + Medali Perak-Perunggu',
    ];

    // Capaian valid per penyelenggara (untuk validasi & opsi form)
    const CAPAIAN_BY_PENYELENGGARA = [
        self::PENYELENGGARA_BELMAWA => [
            self::CAPAIAN_JUARA1_EMAS,
            self::CAPAIAN_JUARA23_PERAK_PERUNGGU,
            self::CAPAIAN_FINALIS,
            self::CAPAIAN_PENDANAAN_PKM,
            self::CAPAIAN_PKM_FINALIS_PIMNAS,
            self::CAPAIAN_FINALIS_PIMNAS_EMAS,
            self::CAPAIAN_FINALIS_PIMNAS_PERAK,
        ],
        self::PENYELENGGARA_LAINNYA => [
            self::CAPAIAN_JUARA1_EMAS,
            self::CAPAIAN_JUARA23_PERAK_PERUNGGU,
            self::CAPAIAN_FINALIS,
        ],
    ];

    // Grup kuota. Kode 'umum' sudah tersimpan di reward_kuota_grup klaim lama,
    // jadi kodenya tetap; yang berubah hanya sebutan dan batasnya.
    const KUOTA_UMUM      = 'umum';
    const KUOTA_INVENTION = 'invention';

    /**
     * Batas jumlah reward per mahasiswa selama studi, per kelompok. null = tanpa batas.
     *
     * Kebijakan departemen (23 Sep 2026) melonggarkan SK 774: reward biasa tanpa
     * batas, invention/expo/fair maks 5×. Batas asli SK disimpan terpisah di
     * SK_BATAS_* — tidak memblokir, hanya memicu peringatan di modal Tinjau
     * admin, karena keputusan final di fakultas masih mengacu ke SK.
     */
    const KUOTA_MAKS = [
        self::KUOTA_UMUM      => null,
        self::KUOTA_INVENTION => 5,
    ];

    // SK 774 poin 4: maks 2× selama kuliah (semua jenis); poin 5: 2.e–2.f maks 1×
    const SK_BATAS_TOTAL     = 2;
    const SK_BATAS_INVENTION = 1;

    // Sebutan grup kuota untuk mahasiswa & admin. Satu tempat, karena label ini
    // muncul di pesan guard, rambu kuota modal, dan banner kuota mahasiswa —
    // ketiganya harus menyebut grup yang sama dengan nama yang sama. Kelompok
    // tanpa batas sengaja tidak diberi nama khusus ("umum" dulu membingungkan):
    // ia hanya "prestasi lainnya" di samping kelompok yang dibatasi.
    const KUOTA_LABELS = [
        self::KUOTA_UMUM      => 'prestasi lainnya',
        self::KUOTA_INVENTION => 'invention/expo/fair',
    ];

    // -------------------------------------------------------------------------
    // Daftar Mata Kuliah — Kurikulum 2024 Prodi S1 Teknik Komputer UNDIP
    // (sumber: tekkom.ft.undip.ac.id, Dokumen Kurikulum 2024). Dipakai untuk
    // dropdown usulan MK saat mahasiswa mengajukan reward. Angka = SKS.
    // Mata kuliah pilihan diberi SKS 3 (standar MK pilihan kurikulum 2024).
    // -------------------------------------------------------------------------

    const MATA_KULIAH = [
        'Semester 1' => [
            'Dasar Komputer & Pemrograman'   => 2,
            'Teknologi Informasi'            => 2,
            'Kalkulus'                       => 4,
            'Fisika Dasar I'                 => 3,
            'Pancasila & Kewarganegaraan'    => 3,
            'Bahasa Indonesia'               => 2,
            'Bahasa Inggris'                 => 2,
            'Pendidikan Agama'               => 2,
        ],
        'Semester 2' => [
            'Praktikum Fisika Dasar I'                 => 1,
            'Praktikum Dasar Komputer dan Pemrograman' => 1,
            'Elektronika Dasar'                        => 3,
            'Algoritma & Pemrograman'                  => 2,
            'Aljabar Linear'                           => 4,
            'Matematika Teknik'                        => 4,
            'Fisika Dasar II'                          => 3,
            'Kimia'                                    => 4,
        ],
        'Semester 3' => [
            'Pengenalan Jaringan Komputer'        => 3,
            'Praktikum Fisika Dasar II'           => 1,
            'Praktikum Elektronika Dasar'         => 1,
            'Sistem Digital'                      => 2,
            'Struktur Data'                       => 2,
            'Transduser dan Sensor'               => 2,
            'Rekayasa Perangkat Lunak'            => 2,
            'Organisasi dan Arsitektur Komputer'  => 3,
            'Probabilitas dan Statistika'         => 3,
            'Olahraga'                            => 1,
            'Internet of Things (IoT)'            => 2,
        ],
        'Semester 4' => [
            'Praktikum Pengenalan Jaringan Komputer' => 1,
            'Switching, Routing dan Jaringan Nirkabel' => 3,
            'Praktikum Sistem Digital'               => 1,
            'Praktikum Rekayasa Perangkat Lunak'     => 1,
            'Multimedia'                             => 2,
            'Sistem Basis Data'                      => 2,
            'Sistem Operasi'                         => 2,
            'Pemrograman Perangkat Bergerak'         => 2,
            'Sistem Tertanam'                        => 2,
            'Metode Numerik'                         => 3,
        ],
        'Semester 5' => [
            'Metodologi Penelitian'                          => 2,
            'Praktikum Switching, Routing dan Jaringan Nirkabel' => 1,
            'Praktikum Pemrograman Perangkat Bergerak'       => 1,
            'Praktikum Sistem Basis Data'                    => 1,
            'Sistem Digital Lanjut'                          => 2,
            'Bahasa Pemrograman Rakitan'                     => 2,
            'Teknik Mikroprosesor dan Antarmuka'             => 3,
            'Pengolahan Sinyal'                              => 3,
            'Kewirausahaan'                                  => 2,
            'Pemrograman Berorientasi Objek'                 => 3,
        ],
        'Semester 6' => [
            'Praktikum Sistem Digital Lanjut'                => 1,
            'Praktikum Teknik Mikroprosesor dan Antarmuka'   => 1,
            'Teknik Kendali dan Otomasi'                     => 3,
            'Kecerdasan Buatan'                              => 3,
            'Rekayasa Perangkat Lunak Berbasis Komponen'     => 2,
            'Interaksi Manusia dan Komputer'                 => 2,
        ],
        'Semester 7' => [
            'Etika Profesi'                                      => 2,
            'Praktikum Multimedia'                              => 1,
            'Praktikum Rekayasa Perangkat Lunak Berbasis Komponen' => 1,
            'Praktikum Teknik Kendali dan Otomasi'             => 1,
            'Sistem Operasi Waktu Nyata'                       => 2,
            'Pemrograman Jaringan'                             => 2,
            'Kriptografi'                                      => 3,
        ],
        'Mata Kuliah Pilihan' => [
            'Kecakapan Antar Personal'              => 3,
            'Perancangan Mikroprosesor'             => 3,
            'Visi Komputer'                         => 3,
            'Keamanan Jaringan Komputer'            => 3,
            'Perancangan System-on-a-Chip (SoC)'    => 3,
            'Jaringan Syaraf Tiruan'                => 3,
            'Logika Fuzzy'                          => 3,
            'Pemrograman Game'                      => 3,
            'Pengolahan Citra dan Pengenalan Pola'  => 3,
            'Sistem Informasi'                      => 3,
            'Software Defined Network'              => 3,
            'Keamanan Sistem Informasi'             => 3,
            'Data Mining'                           => 3,
            'Grafika Komputer'                      => 3,
            'Pemrograman Basis Data'                => 3,
            'Pemrograman Berorientasi Objek Lanjut' => 3,
            'Sistem Tertanam Terdistribusi'         => 3,
            'Manajemen Proyek Teknologi Informasi'  => 3,
        ],
    ];

    /**
     * Daftar mata kuliah dalam bentuk datar [nama => sks] (untuk validasi).
     */
    public static function mataKuliahFlat(): array
    {
        $flat = [];
        foreach (self::MATA_KULIAH as $grup) {
            foreach ($grup as $nama => $sks) {
                $flat[$nama] = $sks;
            }
        }
        return $flat;
    }

    // -------------------------------------------------------------------------
    // Tahun ajaran — semester yang dipilih mahasiswa saat mengajukan klaim
    // -------------------------------------------------------------------------

    /** Tahun ajaran paling awal yang boleh dipilih (ganjil 2022/2023). */
    const TA_TAHUN_MULAI = 2022;

    const SEMESTER_GANJIL = 'ganjil';
    const SEMESTER_GENAP  = 'genap';

    /**
     * Tahun ajaran yang sedang berjalan, dalam bentuk kode "2023-ganjil".
     *
     * Kode memakai tahun pembuka, jadi urutan abjadnya sama dengan urutan waktu
     * ("2023-ganjil" < "2023-genap" < "2024-ganjil") dan bisa langsung diurutkan
     * di query tanpa kolom bantu.
     */
    public static function tahunAjaranSekarang(): string
    {
        $bulan = (int) now()->month;
        $tahun = (int) now()->year;

        // Ganjil berjalan Agustus–Januari, genap Februari–Juli. Januari masih
        // milik tahun ajaran yang dibuka Agustus tahun sebelumnya.
        if ($bulan >= 8) {
            return $tahun . '-' . self::SEMESTER_GANJIL;
        }

        return $bulan === 1
            ? ($tahun - 1) . '-' . self::SEMESTER_GANJIL
            : ($tahun - 1) . '-' . self::SEMESTER_GENAP;
    }

    /**
     * Pilihan tahun ajaran [kode => label], terbaru lebih dulu.
     *
     * Dihitung dari tanggal hari ini, bukan disimpan di tabel atau seeder —
     * itulah yang membuat daftarnya bertambah sendiri tiap semester berganti
     * tanpa ada yang perlu dijalankan.
     */
    public static function tahunAjaranList(): array
    {
        [$tahunKini, $semKini] = self::uraikanTahunAjaran(self::tahunAjaranSekarang());

        $list = [];
        for ($tahun = self::TA_TAHUN_MULAI; $tahun <= $tahunKini; $tahun++) {
            foreach ([self::SEMESTER_GANJIL, self::SEMESTER_GENAP] as $semester) {
                // Genap tahun ajaran berjalan belum dibuka bila sekarang masih ganjil
                if ($tahun === $tahunKini && $semester === self::SEMESTER_GENAP && $semKini === self::SEMESTER_GANJIL) {
                    continue;
                }

                $kode        = $tahun . '-' . $semester;
                $list[$kode] = self::tahunAjaranLabel($kode);
            }
        }

        return array_reverse($list, true);
    }

    /**
     * Label tampilan, mis. "Ganjil 2023/2024".
     *
     * Sengaja bisa memformat kode apa pun yang pernah tersimpan, termasuk yang
     * sudah jatuh di luar tahunAjaranList() — kalau TA_TAHUN_MULAI digeser,
     * klaim lama tetap punya keterangan yang terbaca.
     */
    public static function tahunAjaranLabel(?string $kode): ?string
    {
        if (!$kode) {
            return null;
        }

        $bagian = self::uraikanTahunAjaran($kode);
        if (!$bagian) {
            return $kode;
        }

        [$tahun, $semester] = $bagian;

        return ucfirst($semester) . ' ' . $tahun . '/' . ($tahun + 1);
    }

    /** @return array{0:int,1:string}|null */
    private static function uraikanTahunAjaran(string $kode): ?array
    {
        if (!preg_match('/^(\d{4})-(' . self::SEMESTER_GANJIL . '|' . self::SEMESTER_GENAP . ')$/', $kode, $cocok)) {
            return null;
        }

        return [(int) $cocok[1], $cocok[2]];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function kemahasiswaan(): BelongsTo
    {
        return $this->belongsTo(Kemahasiswaan::class, 'kemahasiswaan_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'claimed_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reward_reviewed_by');
    }

    public function buktiFiles()
    {
        return $this->hasMany(VerifikasiBukti::class, 'bukti_id')
            ->where('bukti_type', VerifikasiBukti::TYPE_PRESTASI);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByTingkat(Builder $query, string $tingkat): Builder
    {
        return $query->where('tingkat', $tingkat);
    }

    public function scopeByTahun(Builder $query, int $tahun): Builder
    {
        return $query->whereYear('tanggal', $tahun);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('verification_status', self::VERIF_REJECTED);
    }

    // -------------------------------------------------------------------------
    // Reward workflow (B.3 claim + B.2 reward / SK FT 774)
    // -------------------------------------------------------------------------

    /**
     * Status reward ter-normalisasi — menangani nilai legacy B.3
     * (belum_claim/sudah_claim) maupun row baru dengan default DB lama.
     */
    public function getRewardStatusAttribute(): string
    {
        return match ($this->claim_status) {
            'sudah_claim'             => self::CLAIM_DISETUJUI,
            'belum_claim', null, ''   => self::CLAIM_BELUM_AJUKAN,
            default                   => $this->claim_status,
        };
    }

    public function getRewardStatusLabelAttribute(): string
    {
        return self::CLAIM_LABELS[$this->reward_status] ?? $this->reward_status;
    }

    public function getRewardPenyelenggaraLabelAttribute(): ?string
    {
        return $this->reward_penyelenggara
            ? (self::PENYELENGGARA_LABELS[$this->reward_penyelenggara] ?? $this->reward_penyelenggara)
            : null;
    }

    public function getRewardCapaianLabelAttribute(): ?string
    {
        return $this->reward_capaian
            ? (self::CAPAIAN_LABELS[$this->reward_capaian] ?? $this->reward_capaian)
            : null;
    }

    public function getRewardTahunAjaranLabelAttribute(): ?string
    {
        return self::tahunAjaranLabel($this->reward_tahun_ajaran);
    }

    public function isRewardDiajukan(): bool
    {
        return $this->reward_status === self::CLAIM_DIAJUKAN;
    }

    public function isRewardDisetujui(): bool
    {
        return $this->reward_status === self::CLAIM_DISETUJUI;
    }

    /** Boleh diajukan ulang bila belum pernah / sebelumnya ditolak. */
    public function rewardBisaDiajukan(): bool
    {
        return in_array($this->reward_status, [self::CLAIM_BELUM_AJUKAN, self::CLAIM_DITOLAK], true);
    }

    public function scopeRewardDiajukan(Builder $query): Builder
    {
        return $query->where('claim_status', self::CLAIM_DIAJUKAN);
    }

    public function scopeRewardDisetujui(Builder $query): Builder
    {
        return $query->where('claim_status', self::CLAIM_DISETUJUI);
    }

    public function scopeRewardDitolak(Builder $query): Builder
    {
        return $query->where('claim_status', self::CLAIM_DITOLAK);
    }

    // -------------------------------------------------------------------------
    // Logika SK FT 774 — perhitungan jatah & grup kuota
    // -------------------------------------------------------------------------

    /**
     * Hitung jatah reward (jumlah MK maks & bobot SKS maks) sesuai matriks SK 774.
     * Satu-satunya tempat aturan SK di-hardcode.
     *
     * @return array{jml_mk_max:int, sks_max:int}
     */
    public static function hitungJatahReward(string $penyelenggara, string $capaian, bool $isInvention = false): array
    {
        // Kategori B (Lainnya) + kegiatan invention/expo/fair: SK 2.e menggugurkan
        // 2.b–2.d, lalu 2.f hanya memberi 1-2 MK / maks 2 SKS kepada peraih
        // juara/medali. Finalis kegiatan jenis ini tidak mendapat reward.
        if ($penyelenggara === self::PENYELENGGARA_LAINNYA && $isInvention) {
            $dapat = \in_array($capaian, [self::CAPAIAN_JUARA1_EMAS, self::CAPAIAN_JUARA23_PERAK_PERUNGGU], true);

            return $dapat
                ? ['jml_mk_max' => 2, 'sks_max' => 2]
                : ['jml_mk_max' => 0, 'sks_max' => 0];
        }

        $matrix = [
            self::PENYELENGGARA_BELMAWA => [
                self::CAPAIAN_JUARA1_EMAS            => [3, 7], // 1.b
                self::CAPAIAN_JUARA23_PERAK_PERUNGGU => [2, 5], // 1.c
                self::CAPAIAN_FINALIS               => [2, 3], // 1.d
                self::CAPAIAN_PENDANAAN_PKM         => [2, 3], // 1.e
                self::CAPAIAN_PKM_FINALIS_PIMNAS    => [2, 2], // 1.f
                self::CAPAIAN_FINALIS_PIMNAS_EMAS   => [2, 3], // 1.g (emas)
                self::CAPAIAN_FINALIS_PIMNAS_PERAK  => [2, 2], // 1.g (perak/perunggu)
            ],
            self::PENYELENGGARA_LAINNYA => [
                self::CAPAIAN_JUARA1_EMAS            => [3, 5], // 2.b
                self::CAPAIAN_JUARA23_PERAK_PERUNGGU => [2, 3], // 2.c
                self::CAPAIAN_FINALIS               => [2, 2], // 2.d
            ],
        ];

        $entry = $matrix[$penyelenggara][$capaian] ?? null;
        if (!$entry) {
            return ['jml_mk_max' => 0, 'sks_max' => 0];
        }

        return ['jml_mk_max' => $entry[0], 'sks_max' => $entry[1]];
    }

    /** Apakah kelompok ini sudah habis. Kelompok tanpa batas tidak pernah habis. */
    public static function kuotaPenuh(string $grup, int $terpakai): bool
    {
        $maks = self::KUOTA_MAKS[$grup] ?? null;

        return $maks !== null && $terpakai >= $maks;
    }

    /**
     * Peringatan untuk admin bila menyetujui klaim ini melewati batas SK 774.
     *
     * Kebijakan departemen lebih longgar dari SK (lihat KUOTA_MAKS), jadi ini
     * bukan penghalang — hanya pengingat bahwa fakultas bisa menolaknya.
     *
     * @param int $rewardLain      reward lain milik mahasiswa yang sudah disetujui (semua kelompok)
     * @param int $inventionLain   bagian dari $rewardLain yang invention/expo/fair
     */
    public static function peringatanBatasSk(int $rewardLain, int $inventionLain, bool $isInvention): ?string
    {
        $alasan = [];

        if ($rewardLain >= self::SK_BATAS_TOTAL) {
            $alasan[] = "mahasiswa ini sudah menerima {$rewardLain} reward lain, sedangkan SK 774 poin 4 membatasi "
                . self::SK_BATAS_TOTAL . "× selama kuliah";
        }

        if ($isInvention && $inventionLain >= self::SK_BATAS_INVENTION) {
            $alasan[] = "sudah ada {$inventionLain} reward invention/expo/fair, sedangkan SK 774 poin 5 membatasi "
                . self::SK_BATAS_INVENTION . "×";
        }

        if (!$alasan) {
            return null;
        }

        return 'Di luar batas SK 774: ' . implode('; ', $alasan)
            . '. Kebijakan departemen mengizinkannya, tetapi keputusan final di Bidang Akademik Fakultas masih mengacu ke SK.';
    }

    /** Grup kuota untuk nilai mentah: invention/expo/fair (poin 2.e–2.f) atau selainnya. */
    public static function tentukanKuotaGrup(?string $penyelenggara, bool $isInvention): string
    {
        return ($penyelenggara === self::PENYELENGGARA_LAINNYA && $isInvention)
            ? self::KUOTA_INVENTION
            : self::KUOTA_UMUM;
    }

    /**
     * Grup kuota untuk instance ini.
     *
     * Memakai kelompok yang dibekukan saat pengajuan bila ada. Menghitung ulang
     * hanya dilakukan untuk baris yang belum sempat dicap — kalau tidak, keputusan
     * lama akan bergeser sendiri begitu aturan pengelompokan di SK berubah.
     */
    public function rewardKuotaGrup(): string
    {
        return $this->reward_kuota_grup
            ?: self::tentukanKuotaGrup($this->reward_penyelenggara, (bool) $this->reward_is_invention);
    }

    /** Klaim ini diputus di bawah SK yang kini sudah diganti. */
    public function rewardSkSudahDiganti(): bool
    {
        return $this->reward_sk_ref !== null && $this->reward_sk_ref !== self::SK_BERLAKU;
    }

    /**
     * Prestasi ini terjadi sebelum SK yang jadi dasar klaimnya mulai berlaku.
     *
     * Hanya penanda untuk peninjau, bukan penghalang: prestasi sebelum 2025
     * masih diatur SE 176/2020, dan keputusannya diserahkan ke departemen.
     */
    public function rewardSebelumMasaBerlaku(): bool
    {
        $mulai = self::SK_MULAI_BERLAKU[$this->reward_sk_ref ?? self::SK_BERLAKU] ?? null;

        return $mulai !== null
            && $this->tanggal !== null
            && $this->tanggal->toDateString() < $mulai;
    }
}