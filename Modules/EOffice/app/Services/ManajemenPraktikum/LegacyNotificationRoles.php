<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\DB;

class LegacyNotificationRoles
{
    public static function restore(): int
    {
        // Only events with an unambiguous recipient role. Do not rewrite dates or read flags.
        $roles = [
            'Pengajuan modul baru'=>'dosen', 'Pengajuan modul ditarik'=>'dosen',
            'Pengajuan modul disetujui'=>'koor', 'Pengajuan modul ditolak'=>'koor',
            'Pengajuan modul perlu direvisi'=>'koor', 'Pendaftaran Koor Siap Diproses'=>'admin',
            'Nilai Tugas Akhir'=>'mahasiswa', 'Pendaftaran Asprak Disetujui Koordinator'=>'mahasiswa',
            'Pendaftaran Koor Disetujui Dosen'=>'mahasiswa', 'Pendaftaran Koor Ditolak'=>'mahasiswa',
            'Pendaftaran Koordinator Diterima'=>'mahasiswa', 'Pendaftaran Praktikan Disetujui'=>'mahasiswa',
            'Pendaftaran Praktikan Ditutup'=>'mahasiswa',
        ];
        return DB::transaction(function () use ($roles) {
            $count=0;
            foreach ($roles as $title=>$role) $count+=DB::table('eo_notifikasi')->whereNull('practicum_role')
                ->where('judul',$title)->update(['practicum_role'=>$role]);
            return $count;
        });
    }
}
