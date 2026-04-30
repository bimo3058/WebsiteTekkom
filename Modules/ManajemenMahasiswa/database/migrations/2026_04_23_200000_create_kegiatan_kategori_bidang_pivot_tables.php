<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Pivot table: kegiatan <-> kategori (many-to-many) ──
        Schema::create('mk_kegiatan_kategori', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('kategori_kegiatan_id');
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('cascade');
            $table->foreign('kategori_kegiatan_id')->references('id')->on('mk_kategori_kegiatan')->onDelete('cascade');
            $table->unique(['kegiatan_id', 'kategori_kegiatan_id'], 'kegiatan_kategori_unique');
        });

        // ── Pivot table: kegiatan <-> bidang (many-to-many) ──
        Schema::create('mk_kegiatan_bidang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('bidang_id');
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('mk_kegiatan')->onDelete('cascade');
            $table->foreign('bidang_id')->references('id')->on('mk_bidang')->onDelete('cascade');
            $table->unique(['kegiatan_id', 'bidang_id'], 'kegiatan_bidang_unique');
        });

        // ── Migrate existing data from FK columns to pivot tables ──
        $kegiatan = DB::table('mk_kegiatan')->get();

        foreach ($kegiatan as $k) {
            // Migrate kategori
            if ($k->kategori_kegiatan_id) {
                DB::table('mk_kegiatan_kategori')->insert([
                    'kegiatan_id' => $k->id,
                    'kategori_kegiatan_id' => $k->kategori_kegiatan_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Migrate bidang
            if (isset($k->bidang_id) && $k->bidang_id) {
                DB::table('mk_kegiatan_bidang')->insert([
                    'kegiatan_id' => $k->id,
                    'bidang_id' => $k->bidang_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_kegiatan_bidang');
        Schema::dropIfExists('mk_kegiatan_kategori');
    }
};
