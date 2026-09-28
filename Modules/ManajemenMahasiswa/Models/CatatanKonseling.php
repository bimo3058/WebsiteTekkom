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

    protected $fillable = [
        'nama_mahasiswa',
        'nim',
        'angkatan',
        'tanggal',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'angkatan' => 'integer',
        // Isi konseling bersifat pribadi: disimpan terenkripsi di database.
        'catatan'  => 'encrypted',
    ];

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
