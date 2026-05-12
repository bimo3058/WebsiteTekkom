<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asprak_praktikum', function (Blueprint $table) {
            // Add foreign key untuk praktikum_id (pointing to praktikum table)
            $table->foreign('praktikum_id')
                ->references('id')
                ->on('praktikum')
                ->onDelete('cascade');

            // Add foreign key untuk user_id
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Add unique constraint: 1 user tidak bisa 2x asprak di praktikum sama
            $table->unique(['praktikum_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asprak_praktikum', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique(['praktikum_id', 'user_id']);
        });
    }
};