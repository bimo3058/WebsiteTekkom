<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bs_jadwal_ujians', 'tanggal_ujian')) {
            Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
            $table->date('tanggal_ujian')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bs_jadwal_ujians', 'tanggal_ujian')) {
            Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
                $table->dropColumn('tanggal_ujian');
            });
        }
    }
};
