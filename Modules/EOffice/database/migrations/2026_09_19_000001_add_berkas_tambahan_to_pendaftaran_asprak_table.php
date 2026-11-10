<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pendaftaran_asprak', 'berkas_tambahan_path')) {
            Schema::table('pendaftaran_asprak', function (Blueprint $table) {
                $table->string('berkas_tambahan_path')->nullable()->after('berkas_cerc_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pendaftaran_asprak', 'berkas_tambahan_path')) {
            Schema::table('pendaftaran_asprak', function (Blueprint $table) {
                $table->dropColumn('berkas_tambahan_path');
            });
        }
    }
};
