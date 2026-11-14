<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soft delete untuk data master (Mata Kuliah, CPL) dan parameter GPM.
 *
 * Tombol "Hapus" pada halaman master tidak lagi menghapus baris secara permanen,
 * melainkan mengisi deleted_at sehingga data tersebut hilang dari frontend
 * (daftar & dropdown) tanpa merusak relasi/histori yang sudah ada.
 */
return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = ['bs_mata_kuliah', 'bs_cpl', 'bs_parameter'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->timestamp('deleted_at')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('deleted_at');
                });
            }
        }
    }
};
