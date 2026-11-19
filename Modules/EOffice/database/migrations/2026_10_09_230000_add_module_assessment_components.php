<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->json('komponen_modul')->nullable();
            $table->timestamp('komponen_modul_locked_at')->nullable();
            $table->unsignedInteger('komponen_modul_version')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('eo_praktikum', fn (Blueprint $table) => $table->dropColumn(['komponen_modul','komponen_modul_locked_at','komponen_modul_version']));
    }
};
