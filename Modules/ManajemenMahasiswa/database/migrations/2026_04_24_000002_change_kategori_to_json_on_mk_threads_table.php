<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom kategori dari varchar(50) ke jsonb dan bungkus data lama ke dalam array
        DB::statement("ALTER TABLE mk_threads ALTER COLUMN kategori TYPE jsonb USING (
            CASE 
                WHEN kategori IS NULL THEN '[]'::jsonb 
                ELSE jsonb_build_array(kategori) 
            END
        )");
    }

    public function down(): void
    {
        // Kembalikan ke format text/varchar (mengambil elemen pertama dari array JSON)
        DB::statement("ALTER TABLE mk_threads ALTER COLUMN kategori TYPE varchar(50) USING (
            CASE 
                WHEN jsonb_array_length(kategori) > 0 THEN kategori->>0
                ELSE ''
            END
        )");
    }
};
