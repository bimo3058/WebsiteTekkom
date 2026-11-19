<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->string('metode_pembagian', 30)->nullable();
            $table->json('draft_pembagian')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->dropColumn(['metode_pembagian', 'draft_pembagian']);
        });
    }
};
