<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mk_kegiatan', 'dosen_pendamping_id')) {
            Schema::table('mk_kegiatan', function (Blueprint $table) {
                $table->foreignId('dosen_pendamping_id')
                    ->nullable()
                    ->constrained('lecturers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // This migration repairs schema drift. Do not remove a column that may
        // have been created by the original 2026_04_16_204800 migration.
    }
};
