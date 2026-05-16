<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_rps_cpmk', function (Blueprint $table) {
            if (!Schema::hasColumn('bs_rps_cpmk', 'cpl_id')) {
                $table->unsignedBigInteger('cpl_id')->nullable()->after('rps_id');
                $table->index(['rps_id', 'cpl_id']);
                $table->foreign('cpl_id')
                    ->references('id')->on('bs_cpl')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bs_rps_cpmk', function (Blueprint $table) {
            if (Schema::hasColumn('bs_rps_cpmk', 'cpl_id')) {
                $table->dropForeign(['cpl_id']);
                $table->dropIndex(['rps_id', 'cpl_id']);
                $table->dropColumn('cpl_id');
            }
        });
    }
};
