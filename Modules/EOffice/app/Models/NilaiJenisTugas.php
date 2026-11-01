<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Nilai per jenis tugas per modul per praktikan.
 * Ini adalah sumber kebenaran tunggal untuk nilai Tugas Pendahuluan,
 * Praktikum, Laporan, dan Responsi — independen dari keberadaan form tugas.
 *
 * Tabel: nilai_jenis_tugas
 */
class NilaiJenisTugas extends Model
{
    private bool $auditBeforeLoaded=false;
    private mixed $auditBefore=null;
    // Serialize score writes with the single practicum-wide publication action.
    public function save(array $options = [])
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results')) return parent::save($options);
        return \Illuminate\Support\Facades\DB::transaction(function () use ($options) {
            $praktikum=$this->lockedPracticum();
            $this->auditBeforeLoaded=true;
            $this->auditBefore=$this->exists ? \Illuminate\Support\Facades\DB::table($this->getTable())->where($this->getKeyName(),$this->getKey())->value('nilai') : null;
            $changed=$this->isDirty(['nilai','modul_id','daftar_praktikan_id','jenis_tugas']);
            $result=parent::save($options);
            if ($result && $changed && $praktikum) $this->invalidateFinalPublication($praktikum);
            return $result;
        });
    }

    public function delete()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results')) return parent::delete();
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $praktikum=$this->lockedPracticum();$result=parent::delete();
            if ($result && $praktikum) $this->invalidateFinalPublication($praktikum);
            return $result;
        });
    }

    private function lockedPracticum(): ?Praktikum
    {
        $module=Modul::find($this->modul_id);
        return $module ? Praktikum::whereKey($module->praktikum_id)->lockForUpdate()->first() : null;
    }

    private function invalidateFinalPublication(Praktikum $praktikum): void
    {
        Nilai::whereIn('daftar_praktikan_id',DaftarPraktikan::where('praktikum_id',$praktikum->id)->select('id'))
            ->update(['disetujui_koor'=>false,'disetujui_dosen'=>false,'dipublikasikan'=>false]);
        TaNilai::whereIn('tugas_akhir_id',TugasAkhir::where('praktikum_id',$praktikum->id)->select('id'))
            ->update(['disetujui_koor'=>false,'disetujui_dosen'=>false]);
        \Illuminate\Support\Facades\DB::table('eo_prak_grade_batches')->where('praktikum_id',$praktikum->id)->where('status','pending')->update(['status'=>'withdrawn']);
    }

    protected static function booted(): void
    {
        static::saved(function(self $score){
            $created=$score->getRawOriginal($score->getKeyName())===null;
            if(!$created && !$score->wasChanged('nilai'))return;
            $module=Modul::find($score->modul_id);
            if($module)app(\Modules\EOffice\Services\ManajemenPraktikum\ScoreHistory::class)->record((string)$module->praktikum_id,'module',(string)$score->modul_id,(string)$score->daftar_praktikan_id,$score->jenis_tugas,$score->auditBeforeLoaded?$score->auditBefore:($created?null:$score->getRawOriginal('nilai')),$score->getAttribute('nilai'));
        });
        static::deleted(function(self $score){
            $module=Modul::find($score->modul_id);
            if($module)app(\Modules\EOffice\Services\ManajemenPraktikum\ScoreHistory::class)->record((string)$module->praktikum_id,'module',(string)$score->modul_id,(string)$score->daftar_praktikan_id,$score->jenis_tugas,$score->getRawOriginal('nilai'),null);
        });
        static::saving(function (self $score) {
            if ($score->nilai === null) return;
            $modul=Modul::find($score->modul_id);
            if ($modul) Praktikum::whereKey($modul->praktikum_id)->whereNull('komponen_modul_locked_at')->update(['komponen_modul_locked_at'=>now()]);
        });
    }
    protected $table = 'nilai_jenis_tugas';

    /**
     * Jenis tugas yang didukung.
     */
    const JENIS = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'praktikum'         => 'Praktikum',
        'laporan'           => 'Laporan',
        'responsi'          => 'Responsi',
    ];

    protected $fillable = [
        'daftar_praktikan_id',
        'modul_id',
        'jenis_tugas',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function daftarPraktikan()
    {
        return $this->belongsTo(DaftarPraktikan::class, 'daftar_praktikan_id');
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }
}
