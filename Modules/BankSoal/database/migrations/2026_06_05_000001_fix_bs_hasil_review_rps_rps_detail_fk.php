<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_hasil_review_rps', function (Blueprint $table) {
            try {
                $table->dropForeign(['rps_detail_id']);
            } catch (\Throwable $e) {
                // Ignore when the current foreign key does not match the default name.
            }
        });

        Schema::table('bs_hasil_review_rps', function (Blueprint $table) {
            $table->foreign('rps_detail_id')
                ->references('id')->on('bs_rps_detail')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bs_hasil_review_rps', function (Blueprint $table) {
            try {
                $table->dropForeign(['rps_detail_id']);
            } catch (\Throwable $e) {
                // Ignore when the current foreign key does not match the default name.
            }
        });

        Schema::table('bs_hasil_review_rps', function (Blueprint $table) {
            $table->foreign('rps_detail_id')
                ->references('id')->on('bs_rps_review')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};