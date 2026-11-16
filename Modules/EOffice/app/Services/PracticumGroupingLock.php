<?php

namespace Modules\EOffice\Services;

use Illuminate\Support\Facades\DB;

class PracticumGroupingLock
{
    public const MESSAGE = 'Kelompok dan shift dikunci karena sudah ada absensi, pengumpulan tugas, atau penilaian. Pembagian tidak dapat diubah lagi.';

    public function isLocked(string $practicumId): bool
    {
        $participants = fn () => DB::table('eo_prak_daftar_praktikan')
            ->select('id')->where('praktikum_id', $practicumId);

        foreach (['eo_prak_absensi_praktikum', 'eo_prak_pengumpulan_tugas', 'eo_prak_nilai_praktikum', 'eo_prak_nilai_jenis_tugas'] as $table) {
            $query = DB::table($table)->whereIn('daftar_praktikan_id', $participants());
            if ($table === 'eo_prak_nilai_jenis_tugas') {
                $query->whereNotNull('nilai');
            }
            if ($query->exists()) {
                return true;
            }
        }
        foreach (['eo_prak_ta_absensi', 'eo_prak_ta_pengumpulan', 'eo_prak_ta_nilai'] as $table) {
            if (DB::table($table)->whereIn('praktikan_id', $participants())->exists()) {
                return true;
            }
        }

        return false;
    }
}
