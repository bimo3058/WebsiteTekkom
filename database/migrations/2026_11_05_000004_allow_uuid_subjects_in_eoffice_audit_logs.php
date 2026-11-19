<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration {
    private const TABLES=['audit_logs','eo_audit_logs'];

    public function up(): void
    {
        DB::transaction(function(){
            $this->limitLocks();
            foreach(self::TABLES as $table)if(Schema::hasTable($table)&&Schema::hasColumn($table,'subject_id')){
                if(DB::getDriverName()==='pgsql')DB::statement('ALTER TABLE '.DB::getQueryGrammar()->wrapTable($table).' ALTER COLUMN "subject_id" TYPE varchar(255) USING "subject_id"::text');
                else Schema::table($table,fn(Blueprint $t)=>$t->string('subject_id',255)->nullable()->change());
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function(){
            $this->limitLocks();
            // Refuse a lossy rollback: UUID references must never be dropped or cast to zero.
            foreach(self::TABLES as $table)if(Schema::hasTable($table)&&Schema::hasColumn($table,'subject_id'))foreach(DB::table($table)->whereNotNull('subject_id')->distinct()->pluck('subject_id') as $id){
                if(filter_var((string)$id,FILTER_VALIDATE_INT)===false)throw new RuntimeException('Rollback ditolak: audit masih memiliki subject_id UUID atau ID non-numerik. Riwayat tetap dipertahankan.');
            }
            foreach(self::TABLES as $table)if(Schema::hasTable($table)&&Schema::hasColumn($table,'subject_id')){
                if(DB::getDriverName()==='pgsql')DB::statement('ALTER TABLE '.DB::getQueryGrammar()->wrapTable($table).' ALTER COLUMN "subject_id" TYPE bigint USING "subject_id"::bigint');
                else Schema::table($table,fn(Blueprint $t)=>$t->unsignedBigInteger('subject_id')->nullable()->change());
            }
        });
    }

    private function limitLocks(): void
    {
        if(DB::getDriverName()==='pgsql'){
            DB::statement("SET LOCAL lock_timeout='5s'");
            DB::statement("SET LOCAL statement_timeout='30s'");
        }
    }
};
