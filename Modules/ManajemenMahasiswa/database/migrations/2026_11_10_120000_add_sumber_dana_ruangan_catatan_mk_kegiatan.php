<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Usulan stakeholder Pak Maman (L-41, 10 Nov 2026):
 *  - S-79 catatan penolakan KABID → tabel mk_kegiatan_catatan
 *  - S-80 anggaran per sumber dana → 3 kolom nominal (Dana Departemen, Kas Himpunan, Kas Bidang)
 *  - S-81 ruangan kampus dari master EOffice → ruangan_id
 *
 * Hanya menambah; data lama tidak diubah. `anggaran` tetap ada dan kini berisi
 * jumlah ketiga sumber dana (kegiatan lama tanpa rincian tetap memakai angkanya).
 * `ruangan_id` sengaja tanpa foreign key: tabelnya milik modul EOffice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->decimal('dana_departemen', 15, 2)->nullable()->after('anggaran');
            $table->decimal('kas_himpunan', 15, 2)->nullable()->after('dana_departemen');
            $table->decimal('kas_bidang', 15, 2)->nullable()->after('kas_himpunan');
            $table->unsignedBigInteger('ruangan_id')->nullable()->after('lokasi')->index();
        });

        Schema::create('mk_kegiatan_catatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('mk_kegiatan')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('isi');
            $table->timestamp('diperbaiki_at')->nullable();
            $table->foreignId('diperbaiki_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kegiatan_id', 'diperbaiki_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_kegiatan_catatan');

        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropIndex(['ruangan_id']);
            $table->dropColumn([
                'dana_departemen', 'kas_himpunan', 'kas_bidang', 'ruangan_id',
            ]);
        });
    }
};
