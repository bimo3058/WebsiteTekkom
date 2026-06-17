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
        'tahun',
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
        'reward_jml_mk_max',
        'reward_sks_max',
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
        self::CLAIM_DIAJUKAN     => 'Menunggu persetujuan',
        self::CLAIM_DISETUJUI    => 'Disetujui',
        self::CLAIM_DITOLAK      => 'Ditolak',
    ];

    // -------------------------------------------------------------------------
    // Reward — Penyelenggara & Capaian (dasar SK FT Undip No. 774/2025)
    // -------------------------------------------------------------------------

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

    // Grup kuota (SK 774 poin 4 & 5)
    const KUOTA_UMUM      = 'umum';      // maks 2x
    const KUOTA_INVENTION = 'invention'; // maks 1x

    const KUOTA_MAKS = [
        self::KUOTA_UMUM      => 2,
        self::KUOTA_INVENTION => 1,
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
        // Kategori B (Lainnya) + kegiatan invention/expo/fair (SK 2.e & 2.f):
        // ketentuan 2.b–2.d tidak berlaku, jatah tetap 1-2 MK / maks 2 SKS.
        if ($penyelenggara === self::PENYELENGGARA_LAINNYA && $isInvention) {
            return ['jml_mk_max' => 2, 'sks_max' => 2];
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

    /** Grup kuota untuk nilai mentah (poin 4 = umum maks 2x, poin 5 = invention maks 1x). */
    public static function tentukanKuotaGrup(?string $penyelenggara, bool $isInvention): string
    {
        return ($penyelenggara === self::PENYELENGGARA_LAINNYA && $isInvention)
            ? self::KUOTA_INVENTION
            : self::KUOTA_UMUM;
    }

    /** Grup kuota untuk instance ini. */
    public function rewardKuotaGrup(): string
    {
        return self::tentukanKuotaGrup($this->reward_penyelenggara, (bool) $this->reward_is_invention);
    }
}