<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_alumni', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_alumni', 'status_karir')) {
                $table->string('status_karir')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('mk_alumni', 'bidang_industri')) {
                $table->string('bidang_industri')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('mk_alumni', 'tahun_mulai_bekerja')) {
                $table->integer('tahun_mulai_bekerja')->nullable()->after('bidang_industri');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_alumni', function (Blueprint $table) {
            $table->dropColumn('status_karir');
        });
    }
};