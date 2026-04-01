<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_academic')->default(false)->after('module');
        });

        // 2. Update data dengan explicit casting untuk PostgreSQL
        // Menggunakan DB::raw('CAST(? AS boolean)') atau sintaks '::boolean'
        DB::table('roles')
            ->whereIn('name', ['dosen', 'mahasiswa', 'gpm'])
            ->update([
                'is_academic' => DB::raw("true") 
            ]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_academic');
        });
    }
};