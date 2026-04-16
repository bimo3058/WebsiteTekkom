<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_kegiatan_id')->nullable()->after('user_id');
            $table->foreign('kategori_kegiatan_id')->references('id')->on('mk_kategori_kegiatan')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['kategori_kegiatan_id']);
            $table->dropColumn('kategori_kegiatan_id');
        });
    }
};
