<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvProfile extends Model
{
    use HasFactory;

    protected $table = 'cv_profiles';

    protected $fillable = [
        'user_id',
        'tentang_diri',
        'pendidikan',
        'pengalaman_kerja',
        'keahlian',
        'sertifikasi',
        'template',
        'cv_email',
        'cv_whatsapp',
        'cv_domisili',
        'cv_portfolio',
        'kegiatan_organisasi',
        'proyek',
        'bahasa',
    ];

    protected $casts = [
        'pendidikan' => 'array',
        'pengalaman_kerja' => 'array',
        'keahlian' => 'array',
        'sertifikasi' => 'array',
        'kegiatan_organisasi' => 'array',
        'proyek' => 'array',
        'bahasa' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
