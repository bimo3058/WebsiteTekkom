<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom realisasi dan status baru ke mk_kegiatan
 * untuk mendukung alur: Subbab 1 (Rencana Proker) → Subbab 2 (Pelaksanaan) → Subbab 3 (Arsip)
 *
 * Status baru: draft, diajukan, disetujui, ditolak
 * (sebelumnya hanya: akan_datang, berlangsung, selesai)
 *
 * Kolom realisasi disimpan TERPISAH dari kolom rencana agar bisa
 * dibandingkan rencana vs realisasi di laporan LPJ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            // ── Kolom realisasi (disimpan terpisah dari rencana) ──────────
            $table->date('realisasi_tanggal_mulai')->nullable()->after('jam_selesai')
                  ->comment('tanggal aktual pelaksanaan (bisa beda dari rencana)');
            $table->date('realisasi_tanggal_selesai')->nullable()->after('realisasi_tanggal_mulai')
                  ->comment('tanggal aktual selesai');
            $table->string('realisasi_lokasi', 255)->nullable()->after('realisasi_tanggal_selesai')
                  ->comment('lokasi aktual pelaksanaan');
            $table->integer('realisasi_peserta')->nullable()->after('realisasi_lokasi')
                  ->comment('jumlah peserta hadir aktual');
            $table->decimal('realisasi_anggaran', 15, 2)->nullable()->after('realisasi_peserta')
                  ->comment('pengeluaran aktual (beda dari estimasi anggaran)');
            $table->text('catatan_pelaksanaan')->nullable()->after('realisasi_anggaran')
                  ->comment('laporan/catatan singkat pelaksanaan hari H');

            // ── Kolom approval ──────────────────────────────────────────────
            $table->unsignedBigInteger('disetujui_oleh')->nullable()->after('catatan_pelaksanaan')
                  ->comment('FK users — admin yang approve proker');
            $table->timestamp('disetujui_at')->nullable()->after('disetujui_oleh');
            $table->text('catatan_penolakan')->nullable()->after('disetujui_at')
                  ->comment('alasan jika proker ditolak');

            $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn([
                'realisasi_tanggal_mulai',
                'realisasi_tanggal_selesai',
                'realisasi_lokasi',
                'realisasi_peserta',
                'realisasi_anggaran',
                'catatan_pelaksanaan',
                'disetujui_oleh',
                'disetujui_at',
                'catatan_penolakan',
            ]);
        });
    }
};
