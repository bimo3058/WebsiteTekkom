<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add creation_method column to bs_rps_detail
        if (Schema::hasTable('bs_rps_detail') && !Schema::hasColumn('bs_rps_detail', 'creation_method')) {
            Schema::table('bs_rps_detail', function (Blueprint $table) {
                $table->string('creation_method')->default('upload')->after('catatan');
            });
        }

        // 2. Create bs_rps_generate_detail table
        Schema::create('bs_rps_generate_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_detail_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('deskripsi_mk')->nullable();
            $table->json('penilaian_data')->nullable();
            $table->json('pertemuan_data')->nullable();
            $table->json('referensi_data')->nullable();
            $table->timestamps();

            $table->foreign('rps_detail_id')
                ->references('id')
                ->on('bs_rps_detail')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_rps_generate_detail');

        if (Schema::hasTable('bs_rps_detail') && Schema::hasColumn('bs_rps_detail', 'creation_method')) {
            Schema::table('bs_rps_detail', function (Blueprint $table) {
                $table->dropColumn('creation_method');
            });
        }
    }
};
