<?php

namespace Modules\ManajemenMahasiswa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Satu catatan sesi konseling yang ditulis GPM. Tidak berelasi ke akun
 * mahasiswa — nama/NIM/angkatan hanyalah teks yang diketik dosen.
 */
class CatatanKonseling extends Model
{
    use SoftDeletes;

    protected $table = 'mk_catatan_konseling';

    /**
     * Opsi dropdown kategori kasus. Kunci = nilai di database,
     * nilai = label tampilan. Dipakai di controller (validasi)
     * dan view (dropdown + badge).
     */
    public const KATEGORI_KASUS = [
        'kesehatan_mental'  => 'Kesehatan Mental',
        'kekerasan_verbal'  => 'Kekerasan Verbal',
        'kaderisasi'        => 'Kaderisasi',
        'kekerasan_seksual' => 'Kekerasan Seksual',
        'lainnya'           => 'Lainnya',
    ];

    /** Status pemantauan kasus. Kunci = nilai di database, nilai = label tampilan. */
    public const STATUS_KASUS = [
        'baru'         => 'Baru',
        'dalam_proses' => 'Dalam Proses',
        'selesai'      => 'Selesai',
    ];

    protected $fillable = [
        'nama_mahasiswa',
        'nim',
        'angkatan',
        'tanggal',
        'kategori_kasus',
        'status_kasus',
        'kronologi',
        'keinginan_pelapor',
        'tindak_lanjut',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'angkatan'         => 'integer',
        // Isi konseling bersifat pribadi: disimpan terenkripsi di database.
        'catatan'          => 'encrypted',
        'kronologi'        => 'encrypted',
        'keinginan_pelapor' => 'encrypted',
        'tindak_lanjut'    => 'encrypted',
    ];

    /** Label tampilan untuk kategori kasus. */
    public function getLabelKategoriAttribute(): ?string
    {
        return self::KATEGORI_KASUS[$this->kategori_kasus] ?? $this->kategori_kasus;
    }

    /** Label tampilan untuk status kasus. */
    public function getLabelStatusAttribute(): ?string
    {
        return self::STATUS_KASUS[$this->status_kasus] ?? $this->status_kasus;
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
