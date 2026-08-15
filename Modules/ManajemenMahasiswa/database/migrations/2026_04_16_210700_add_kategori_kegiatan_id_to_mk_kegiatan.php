<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mk_kegiatan', 'kategori_kegiatan_id')) {
            Schema::table('mk_kegiatan', function (Blueprint $table) {
                $table->unsignedBigInteger('kategori_kegiatan_id')->nullable()->change();
            });

            return;
        }

        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_kegiatan_id')->nullable()->after('user_id');
            $table->foreign('kategori_kegiatan_id')->references('id')->on('mk_kategori_kegiatan')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Kolom dapat berasal dari migration create_mk_kegiatan_table.
    }
};
