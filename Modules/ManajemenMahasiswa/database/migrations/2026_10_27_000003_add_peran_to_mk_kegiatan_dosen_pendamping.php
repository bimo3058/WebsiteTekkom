<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Peran tiap dosen di sebuah kegiatan (Pendamping, Narasumber, Moderator, Juri, Lainnya).
 * Baris lama otomatis menjadi "Pendamping", sesuai arti kolom sebelumnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mk_kegiatan_dosen_pendamping', 'peran')) {
            return;
        }

        Schema::table('mk_kegiatan_dosen_pendamping', function (Blueprint $table) {
            $table->string('peran', 30)->default('Pendamping')->after('lecturer_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_kegiatan_dosen_pendamping', 'peran')) {
            Schema::table('mk_kegiatan_dosen_pendamping', function (Blueprint $table) {
                $table->dropColumn('peran');
            });
        }
    }
};
