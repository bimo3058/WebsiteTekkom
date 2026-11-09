<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dosen pendamping kegiatan: dari satu slot (kolom mk_kegiatan.dosen_pendamping_id)
 * menjadi banyak (pivot mk_kegiatan_dosen_pendamping), selaras dengan pola panitia.
 *
 * Data lama disalin ke pivot lebih dulu, baru kolom FK-nya dibuang supaya tidak ada
 * dua sumber data untuk hal yang sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_kegiatan_dosen_pendamping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('mk_kegiatan')->onDelete('cascade');
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->timestamps();

            // Satu dosen hanya boleh tercatat sekali per kegiatan
            $table->unique(['kegiatan_id', 'lecturer_id']);
        });

        if (! Schema::hasColumn('mk_kegiatan', 'dosen_pendamping_id')) {
            return;
        }

        // Pindahkan data lama ke pivot
        $now = now();
        DB::table('mk_kegiatan')
            ->whereNotNull('dosen_pendamping_id')
            ->orderBy('id')
            ->select('id', 'dosen_pendamping_id')
            ->chunk(200, function ($rows) use ($now) {
                $payload = $rows->map(fn ($row) => [
                    'kegiatan_id' => $row->id,
                    'lecturer_id' => $row->dosen_pendamping_id,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ])->all();

                if ($payload) {
                    DB::table('mk_kegiatan_dosen_pendamping')->insert($payload);
                }
            });

        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['dosen_pendamping_id']);
            }
            $table->dropColumn('dosen_pendamping_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('mk_kegiatan', 'dosen_pendamping_id')) {
            Schema::table('mk_kegiatan', function (Blueprint $table) {
                $table->unsignedBigInteger('dosen_pendamping_id')->nullable()->after('ketua_pelaksana_id');
                $table->foreign('dosen_pendamping_id')->references('id')->on('lecturers')->onDelete('set null');
            });
        }

        // Kembalikan satu dosen pertama per kegiatan (sisanya tidak muat di kolom tunggal)
        if (Schema::hasTable('mk_kegiatan_dosen_pendamping')) {
            DB::table('mk_kegiatan_dosen_pendamping')
                ->orderBy('kegiatan_id')
                ->orderBy('id')
                ->get(['kegiatan_id', 'lecturer_id'])
                ->groupBy('kegiatan_id')
                ->each(function ($rows, $kegiatanId) {
                    DB::table('mk_kegiatan')
                        ->where('id', $kegiatanId)
                        ->update(['dosen_pendamping_id' => $rows->first()->lecturer_id]);
                });
        }

        Schema::dropIfExists('mk_kegiatan_dosen_pendamping');
    }
};
