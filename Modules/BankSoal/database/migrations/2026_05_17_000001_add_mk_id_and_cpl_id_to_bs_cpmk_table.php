<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_cpmk', function (Blueprint $table) {
            if (!Schema::hasColumn('bs_cpmk', 'mk_id')) {
                $table->unsignedBigInteger('mk_id')->nullable()->after('deskripsi');
                $table->index('mk_id', 'bs_cpmk_mk_id_index');
            }

            if (!Schema::hasColumn('bs_cpmk', 'cpl_id')) {
                $table->unsignedBigInteger('cpl_id')->nullable()->after('mk_id');
                $table->index('cpl_id', 'bs_cpmk_cpl_id_index');
            }
        });

        if (Schema::hasColumn('bs_cpmk', 'mk_id')) {
            try {
                Schema::table('bs_cpmk', function (Blueprint $table) {
                    $table->foreign('mk_id')
                        ->references('id')->on('bs_mata_kuliah')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();
                });
            } catch (\Throwable $e) {
                // Ignore if the foreign key already exists or could not be created.
            }
        }

        if (Schema::hasColumn('bs_cpmk', 'cpl_id')) {
            try {
                Schema::table('bs_cpmk', function (Blueprint $table) {
                    $table->foreign('cpl_id')
                        ->references('id')->on('bs_cpl')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();
                });
            } catch (\Throwable $e) {
                // Ignore if the foreign key already exists or could not be created.
            }
        }
    }

    public function down(): void
    {
        Schema::table('bs_cpmk', function (Blueprint $table) {
            if (Schema::hasColumn('bs_cpmk', 'mk_id')) {
                try {
                    $table->dropForeign(['mk_id']);
                } catch (\Throwable $e) {
                    // Ignore if the foreign key was not created.
                }

                try {
                    $table->dropIndex('bs_cpmk_mk_id_index');
                } catch (\Throwable $e) {
                    // Ignore if the index was not created.
                }

                $table->dropColumn('mk_id');
            }

            if (Schema::hasColumn('bs_cpmk', 'cpl_id')) {
                try {
                    $table->dropForeign(['cpl_id']);
                } catch (\Throwable $e) {
                    // Ignore if the foreign key was not created.
                }

                try {
                    $table->dropIndex('bs_cpmk_cpl_id_index');
                } catch (\Throwable $e) {
                    // Ignore if the index was not created.
                }

                $table->dropColumn('cpl_id');
            }
        });
    }
};