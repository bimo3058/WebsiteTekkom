<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function(){
            if(DB::getDriverName()==='pgsql')DB::statement("SET LOCAL lock_timeout='5s'");
            Schema::table('eo_audit_logs',function(Blueprint $table){
                $table->timestamp('cerc_deleted_at')->nullable();
                $table->unsignedBigInteger('cerc_deleted_by')->nullable();
            });
        });
    }
    public function down(): void
    {
        if(DB::table('eo_audit_logs')->whereNotNull('cerc_deleted_at')->exists())throw new RuntimeException('Rollback ditolak: metadata soft delete riwayat CERC masih digunakan.');
        Schema::table('eo_audit_logs',fn(Blueprint $table)=>$table->dropColumn(['cerc_deleted_at','cerc_deleted_by']));
    }
};
