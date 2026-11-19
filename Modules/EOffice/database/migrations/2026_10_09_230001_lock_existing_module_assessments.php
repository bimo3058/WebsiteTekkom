<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $opened=DB::table('modul_praktikum as m')->join('tugas_praktikum as t','t.modul_id','=','m.id')->where('t.is_published',true)->pluck('m.praktikum_id');
        $graded=DB::table('modul_praktikum as m')->join('nilai_jenis_tugas as n','n.modul_id','=','m.id')->whereNotNull('n.nilai')->pluck('m.praktikum_id');
        $submitted=DB::table('modul_praktikum as m')->join('tugas_praktikum as t','t.modul_id','=','m.id')->join('pengumpulan_tugas as p','p.tugas_id','=','t.id')->pluck('m.praktikum_id');
        DB::table('eo_praktikum')->whereIn('id',$opened->merge($graded)->merge($submitted)->unique())->whereNull('komponen_modul_locked_at')->update(['komponen_modul_locked_at'=>now()]);
    }

    public function down(): void
    {
        // A permanent lock is historical state; rolling back must not erase it.
    }
};
