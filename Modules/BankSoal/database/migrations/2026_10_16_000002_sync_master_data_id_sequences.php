<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronisasi sequence primary key tabel data master.
 *
 * Data master pernah diisi ulang memakai id eksplisit (import/seeder) sehingga
 * sequence-nya tertinggal dan insert baru gagal dengan error
 * "duplicate key value violates unique constraint ..._pkey".
 * Migrasi ini mengembalikan sequence ke posisi yang benar dan aman dijalankan ulang.
 */
return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = ['bs_mata_kuliah', 'bs_cpl', 'bs_cpmk', 'bs_parameter'];

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->tables as $table) {
            $sequence = DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", [$table])->seq ?? null;

            if (!$sequence) {
                continue;
            }

            $maxId = (int) (DB::table($table)->max('id') ?? 0);

            // is_called = false -> nextval mengembalikan maxId + 1
            DB::statement('SELECT setval(?, ?, false)', [$sequence, $maxId + 1]);
        }
    }

    public function down(): void
    {
        // Perbaikan sequence tidak perlu dibalik.
    }
};
