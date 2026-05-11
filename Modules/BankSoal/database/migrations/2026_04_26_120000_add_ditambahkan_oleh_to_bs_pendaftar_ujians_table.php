<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_pendaftar_ujians', function (Blueprint $table) {
            $table->foreignId('ditambahkan_oleh')
                ->nullable()
                ->after('catatan_admin')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bs_pendaftar_ujians', function (Blueprint $table) {
            $table->dropForeign(['ditambahkan_oleh']);
            $table->dropColumn('ditambahkan_oleh');
        });
    }
};
