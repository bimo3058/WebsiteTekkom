<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('bs_mata_kuliah_cpl', 'mk_id')) {
            return;
        }

        Schema::table('bs_mata_kuliah_cpl', function (Blueprint $table) {
            $table->unsignedBigInteger('mk_id')->nullable();
            $table->index('mk_id', 'bs_mata_kuliah_cpl_mk_id_index');
        });

        Schema::table('bs_mata_kuliah_cpl', function (Blueprint $table) {
            $table->foreign('mk_id')
                ->references('id')->on('bs_mata_kuliah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bs_mata_kuliah_cpl', 'mk_id')) {
            return;
        }

        Schema::table('bs_mata_kuliah_cpl', function (Blueprint $table) {
            $table->dropForeign(['mk_id']);
            $table->dropIndex('bs_mata_kuliah_cpl_mk_id_index');
            $table->dropColumn('mk_id');
        });
    }
};