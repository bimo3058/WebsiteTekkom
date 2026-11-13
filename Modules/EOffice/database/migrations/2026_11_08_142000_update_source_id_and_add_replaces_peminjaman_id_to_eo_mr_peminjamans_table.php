<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eo_mr_peminjamans', function (Blueprint $table) {
            // 1. Ubah source_id menjadi string(255) agar mendukung UUID Praktikum dan ID integer KP/Capstone
            $table->string('source_id', 255)->nullable()->change();

            // 2. Tambahkan replaces_peminjaman_id untuk alur Two-Phase Swap reschedule
            $table->unsignedBigInteger('replaces_peminjaman_id')->nullable()->after('source_id')->index();
            $table->foreign('replaces_peminjaman_id')->references('id')->on('eo_mr_peminjamans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_mr_peminjamans', function (Blueprint $table) {
            $table->dropForeign(['replaces_peminjaman_id']);
            $table->dropIndex(['replaces_peminjaman_id']);
            $table->dropColumn(['replaces_peminjaman_id']);
            $table->unsignedBigInteger('source_id')->nullable()->change();
        });
    }
};

