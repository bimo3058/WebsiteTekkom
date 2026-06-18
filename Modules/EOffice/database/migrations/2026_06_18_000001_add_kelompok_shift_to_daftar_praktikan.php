<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_praktikan', function (Blueprint $table) {
            $table->string('kelompok', 50)->nullable()->after('status');
            $table->string('shift', 50)->nullable()->after('kelompok');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_praktikan', function (Blueprint $table) {
            $table->dropColumn(['kelompok', 'shift']);
        });
    }
};
