<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('role')) {
            Schema::table('role', function (Blueprint $table) {
                // Menambahkan kolom module jika belum ada
                if (!Schema::hasColumn('role', 'module')) {
                    $table->string('module')->nullable()->after('nama');
                }

                // Menambahkan kolom is_academic jika belum ada
                if (!Schema::hasColumn('role', 'is_academic')) {
                    $table->boolean('is_academic')->default(false)->after('deskripsi');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->dropColumn(['module', 'is_academic']);
        });
    }
};