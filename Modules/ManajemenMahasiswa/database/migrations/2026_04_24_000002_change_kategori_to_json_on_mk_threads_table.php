<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya jalankan syntax PostgreSQL ini jika driver yang digunakan adalah 'pgsql'
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE mk_threads ALTER COLUMN kategori TYPE jsonb USING (
                CASE 
                    WHEN kategori IS NULL THEN '[]'::jsonb 
                    ELSE jsonb_build_array(kategori) 
                END
            )");
        }
        // Untuk SQLite, kita biarkan saja karena SQLite tidak mendukung 
        // perubahan tipe kolom secara langsung seperti ini.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE mk_threads ALTER COLUMN kategori TYPE varchar(50) USING (
                CASE 
                    WHEN jsonb_array_length(kategori) > 0 THEN kategori->>0
                    ELSE ''
                END
            )");
        }
    }
};