<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_alumni', function (Blueprint $table) {
            $table->string('bidang_industri')->nullable()->after('jabatan');
            $table->integer('tahun_mulai_bekerja')->nullable()->after('bidang_industri');
            $table->string('status_karir')->nullable()->after('tahun_mulai_bekerja');
        });
    }

    public function down(): void
    {
        Schema::table('mk_alumni', function (Blueprint $table) {
            $table->dropColumn(['bidang_industri', 'tahun_mulai_bekerja', 'status_karir']);
        });
    }
};
