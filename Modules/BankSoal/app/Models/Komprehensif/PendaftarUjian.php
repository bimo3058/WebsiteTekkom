<?php

namespace Modules\BankSoal\Models\Komprehensif;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BankSoal\Enums\PendaftaranStatus;

class PendaftarUjian extends Model
{
    use SoftDeletes;

    protected $table = 'bs_pendaftar_ujians';

    protected $fillable = [
        'periode_ujian_id',
        'mahasiswa_id',
        'nim',
        'nama_lengkap',
        'kontak_wa',
        'semester_aktif',
        'target_wisuda',
        'dosen_pembimbing_1_id',
        'dosen_pembimbing_2_id',
        'status_pendaftaran',
        'jadwal_ujian_id',
        'catatan_admin',
        'ditambahkan_oleh',
    ];

    protected $casts = [
        'status_pendaftaran' => PendaftaranStatus::class,
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeUjian::class, 'periode_ujian_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function dosenPembimbing1()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_1_id');
    }

    public function dosenPembimbing2()
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_2_id');
    }

    public function ditambahkanOleh()
    {
        return $this->belongsTo(User::class, 'ditambahkan_oleh');
    }
}
