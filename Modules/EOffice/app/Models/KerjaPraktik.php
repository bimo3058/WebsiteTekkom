<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\EOffice\Database\Factories\KerjaPraktikFactory;

class KerjaPraktik extends Model
{
    use HasFactory;

    protected $table = 'eo_kerja_praktik';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nim',
        'mahasiswa_id',
        'dosen_pembimbing_id',
        'judul_kp',
        'instansi_kp',
        'ipk',
        'kelas',
        'sks_diambil',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_kp',
        'is_acc_admin',
        'periode_id',
    ];

    /**
     * Relasi ke profil mahasiswa KP (eo_kp_mahasiswa)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(KpMahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke user dosen pembimbing (READ ONLY dari tabel global users)
     */
    public function dosenPembimbing()
    {
        return $this->belongsTo(KpDosen::class, 'dosen_pembimbing_id');
    }

    /**
     * Relasi ke tabel dokumen KP
     */
    public function dokumen()
    {
        return $this->hasMany(KpDokumen::class, 'kp_id');
    }

    /**
     * Relasi ke tabel seminar KP (hasOne karena 1 KP punya 1 seminar)
     */
    public function seminar()
    {
        return $this->hasOne(KpSeminar::class, 'kp_id');
    }

    /**
     * Relasi ke tabel penilaian KP (hasOne karena 1 KP punya 1 nilai)
     */
    public function penilaian()
    {
        return $this->hasOne(KpPenilaian::class, 'kp_id');
    }

    /**
     * Relasi ke tabel periode
     */
    public function periode()
    {
        return $this->belongsTo(KpPeriode::class, 'periode_id');
    }

    /**
     * Relasi ke tabel detail penilaian komponen dinamis
     */
    public function nilaiDetail()
    {
        return $this->hasMany(KpNilaiDetail::class, 'kp_id');
    }

    /**
     * Relasi ke tabel balancing (draft/final)
     */
    public function balancing()
    {
        return $this->hasOne(KpBalancing::class, 'kp_id');
    }

    /**
     * Accessor virtual mapping untuk referensi fk periode_id menggunakan relasi waktu
     * demi kompatibilitas Eloquent eager loading ('periode').
     */
    public function getPeriodeIdAttribute()
    {
        static $periodes = null;
        if ($periodes === null) {
            $periodes = KpPeriode::all();
        }

        if (!array_key_exists('created_at', $this->attributes) || !$this->attributes['created_at']) {
            return null;
        }

        $createdAt = \Carbon\Carbon::parse($this->attributes['created_at'])->format('Y-m-d');

        $matched = $periodes->first(function ($p) use ($createdAt) {
            if (!$p->pra_kp_mulai || !$p->pra_kp_akhir)
                return false;
            return $createdAt >= $p->pra_kp_mulai->format('Y-m-d')
                && $createdAt <= $p->pra_kp_akhir->format('Y-m-d');
        });

        return $matched ? $matched->id : null;
    }

    /**
     * Scope: ambil KP milik user yang sedang login
     */
    public function scopeForCurrentUser($query)
    {
        $mahasiswa = KpMahasiswa::where('user_id', auth()->id())->first();
        if ($mahasiswa) {
            return $query->where('mahasiswa_id', $mahasiswa->id);
        }
        return $query->whereRaw('1 = 0'); // return empty jika belum ada profil
    }

    /**
     * Accessor dinamis untuk nilai_seminar_pembimbing (sebelumnya hardcoded di tabel)
     */
    public function getNilaiSeminarPembimbingAttribute()
    {
        $details = $this->nilaiDetail()->whereHas('komponen', function ($q) {
            $q->where('role_penilai', 'dosen_pembimbing');
        })->get();

        if ($details->isEmpty())
            return null;
        return round($details->avg('nilai_angka'), 2);
    }

    /**
     * Accessor dinamis untuk nilai_lapangan (sebelumnya hardcoded di tabel)
     */
    public function getNilaiLapanganAttribute()
    {
        $details = $this->nilaiDetail()->whereHas('komponen', function ($q) {
            $q->where('role_penilai', 'koordinator');
        })->get();

        if ($details->isEmpty())
            return null;
        return round($details->avg('nilai_angka'), 2);
    }

    // protected static function newFactory(): KerjaPraktikFactory
    // {
    //     // return KerjaPraktikFactory::new();
    // }
}
