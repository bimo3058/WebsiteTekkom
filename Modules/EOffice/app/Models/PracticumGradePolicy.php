<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class PracticumGradePolicy extends Model
{
    protected $table = 'eo_prak_grade_policies';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['rules' => 'array', 'created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Skema predikat bersifat tetap. Buat versi baru.'));
        static::deleting(fn () => throw new \LogicException('Riwayat skema predikat tidak dapat dihapus.'));
    }
}
