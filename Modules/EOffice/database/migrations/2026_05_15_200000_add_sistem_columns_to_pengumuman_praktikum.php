<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumuman_praktikum', function (Blueprint $table) {
            // null = dibuat manual oleh koor/asprak
            // 'buka' = otomatis saat periode pendaftaran dibuka
            // 'tutup' = otomatis saat periode pendaftaran ditutup
            $table->string('tipe_sistem')->nullable()->after('is_published');

            // ID periode yang men-trigger pengumuman ini (untuk traceability)
            $table->unsignedBigInteger('periode_id')->nullable()->after('tipe_sistem');

            // Soft delete — pengumuman "buka" di-soft-delete saat periode tutup,
            // tapi tetap tersimpan di DB untuk riwayat admin
            $table->softDeletes()->after('updated_at');

            $table->foreign('periode_id')
                ->references('id')
                ->on('manprak_periode_pendaftaran')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengumuman_praktikum', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn(['tipe_sistem', 'periode_id', 'deleted_at']);
        });
    }
};
