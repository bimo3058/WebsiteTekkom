<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->makeNullable('bs_pendaftar_ujians', 'mahasiswa_id');
        $this->makeNullable('bs_kompre_session', 'user_id');

        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->foreignId('pendaftar_ujian_id')
                ->nullable()
                ->constrained('bs_pendaftar_ujians')
                ->nullOnDelete();
            $table->index('pendaftar_ujian_id');
        });
    }

    public function down(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->dropForeign(['pendaftar_ujian_id']);
            $table->dropIndex(['pendaftar_ujian_id']);
            $table->dropColumn('pendaftar_ujian_id');
        });

        $this->makeNotNull('bs_kompre_session', 'user_id');
        $this->makeNotNull('bs_pendaftar_ujians', 'mahasiswa_id');
    }

    private function makeNullable(string $table, string $column): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} DROP NOT NULL");

            return;
        }

        Schema::table($table, function (Blueprint $table) use ($column) {
            $table->unsignedBigInteger($column)->nullable()->change();
        });
    }

    private function makeNotNull(string $table, string $column): void
    {
        if (DB::table($table)->whereNull($column)->exists()) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE {$table} ALTER COLUMN {$column} SET NOT NULL");

            return;
        }

        Schema::table($table, function (Blueprint $table) use ($column) {
            $table->unsignedBigInteger($column)->nullable(false)->change();
        });
    }
};
