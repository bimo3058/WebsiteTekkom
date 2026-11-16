<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eo_notifikasi', function (Blueprint $table) {
            $table->string('practicum_role', 20)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('eo_notifikasi', fn (Blueprint $table) => $table->dropColumn('practicum_role'));
    }
};
