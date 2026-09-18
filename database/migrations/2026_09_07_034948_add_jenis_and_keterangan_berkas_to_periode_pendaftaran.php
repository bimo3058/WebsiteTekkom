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
        Schema::table('manprak_periode_pendaftaran', function (Blueprint $table) {
            $table->string('jenis_berkas_tambahan')->nullable()->after('nama_berkas_tambahan');
            $table->text('keterangan_berkas_tambahan')->nullable()->after('jenis_berkas_tambahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manprak_periode_pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['jenis_berkas_tambahan', 'keterangan_berkas_tambahan']);
        });
    }
};
