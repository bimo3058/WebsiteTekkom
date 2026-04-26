<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->boolean('pendaftaran_ditutup_paksa')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->dropColumn('pendaftaran_ditutup_paksa');
        });
    }
};
