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
        Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
            $table->string('status')->default('menunggu_jadwal')->after('ruangan');
            $table->string('token', 10)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_jadwal_ujians', function (Blueprint $table) {
            $table->dropColumn(['status', 'token']);
        });
    }
};
