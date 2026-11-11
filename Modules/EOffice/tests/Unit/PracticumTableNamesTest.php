<?php

namespace Modules\EOffice\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};
use Tests\TestCase;

class PracticumTableNamesTest extends TestCase
{
    private function migration(): object { return require base_path('Modules/EOffice/database/migrations/2026_11_05_000002_standardize_practicum_table_names.php'); }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'practicum_rename_test','database.connections.practicum_rename_test'=>['driver'=>'sqlite','database'=>':memory:','foreign_key_constraints'=>true]]);
        DB::purge('practicum_rename_test');
        foreach($this->migration()::TABLES as $old=>$new){
            Schema::create($old,function(Blueprint $t)use($old){
                $t->id();$t->text('payload');$t->timestamps();
                if($old==='modul_praktikum')$t->foreignId('praktikum_id')->constrained('eo_praktikum')->cascadeOnDelete();
                $t->index('payload',substr($old.'_fixture_index',0,60));
            });
            $row=['id'=>1,'payload'=>json_encode(['nilai'=>80,'catatan'=>'Tetap sama']),'created_at'=>'2026-11-05 10:00:00','updated_at'=>'2026-11-05 10:00:00'];
            if($old==='modul_praktikum')$row['praktikum_id']=1;
            DB::table($old)->insert($row);
        }
        foreach(['users','eo_notifikasi','eo_audit_logs','eo_mr_peminjamans','eo_prak_score_histories'] as $table){Schema::create($table,fn(Blueprint $t)=>$t->id());DB::table($table)->insert(['id'=>1]);}
    }

    public function test_rename_and_rollback_preserve_every_row_and_index(): void
    {
        $migration=$this->migration();$snapshot=[];
        foreach($migration::TABLES as $old=>$new)$snapshot[$old]=json_encode(DB::table($old)->orderBy('id')->get());
        $migration->up();
        foreach($migration::TABLES as $old=>$new){
            $this->assertFalse(Schema::hasTable($old));$this->assertTrue(Schema::hasTable($new));
            $this->assertSame($snapshot[$old],json_encode(DB::table($new)->orderBy('id')->get()));
            $this->assertTrue(Schema::hasIndex($new,substr($old.'_fixture_index',0,60)));
        }
        $migration->up();$migration->down();
        foreach($migration::TABLES as $old=>$new)$this->assertSame($snapshot[$old],json_encode(DB::table($old)->orderBy('id')->get()));
    }

    public function test_foreign_keys_still_enforce_and_cascade_after_rename(): void
    {
        $this->migration()->up();
        $foreign=Schema::getForeignKeys('eo_prak_modul_praktikum');$this->assertSame('eo_prak_praktikum',$foreign[0]['foreign_table']);
        DB::table('eo_prak_praktikum')->where('id',1)->delete();$this->assertSame(0,DB::table('eo_prak_modul_praktikum')->count());
        foreach(['users','eo_notifikasi','eo_audit_logs','eo_mr_peminjamans','eo_prak_score_histories'] as $table)$this->assertSame(1,DB::table($table)->count());
    }

    public function test_conflicting_target_aborts_before_any_table_is_renamed(): void
    {
        Schema::create('eo_prak_modul_praktikum',fn(Blueprint $t)=>$t->id());
        try{$this->migration()->up();$this->fail('Existing target was overwritten');}catch(\RuntimeException $error){$this->assertStringContainsString('data tidak ditimpa',$error->getMessage());}
        $this->assertTrue(Schema::hasTable('eo_praktikum'));$this->assertFalse(Schema::hasTable('eo_prak_praktikum'));
        $this->assertSame(1,DB::table('modul_praktikum')->count());
    }
}
