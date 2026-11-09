<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Multi-file attachments are stored as a JSON string. A varchar(255)
     * cannot reliably hold the paths and original names of up to three files.
     */
    public function up(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->text('file_path')->nullable()->change();
        });

        Schema::table('riwayat_pengumpulan_tugas', function (Blueprint $table) {
            $table->text('file_path')->change();
        });

        Schema::table('tugas_praktikum', function (Blueprint $table) {
            $table->text('file_path')->nullable()->change();
        });
    }

    /**
     * Keep these columns as text when rolling back. Once multi-file JSON has
     * been saved, narrowing them to varchar(255) could truncate user data.
     */
    public function down(): void
    {
        // Intentionally left unchanged to prevent attachment data loss.
    }
};
