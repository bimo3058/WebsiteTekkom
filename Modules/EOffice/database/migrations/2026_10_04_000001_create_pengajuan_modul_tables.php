<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->string('status_pelaksanaan', 30)->default('persiapan')->after('status');
            $table->timestamp('started_at')->nullable()->after('status_pelaksanaan');
        });

        // Modul lama dianggap sudah sah. Jika sudah ada absensi, praktikum juga
        // dianggap telah berjalan agar struktur historis tidak dapat direvisi.
        DB::table('eo_praktikum')
            ->whereIn('id', DB::table('modul_praktikum')->select('praktikum_id')->distinct())
            ->update(['status_pelaksanaan' => 'siap_berjalan']);

        $praktikumBerjalan = DB::table('modul_praktikum')
                ->join('absensi_praktikum', 'absensi_praktikum.modul_id', '=', 'modul_praktikum.id')
                ->select('modul_praktikum.praktikum_id', DB::raw('MIN(absensi_praktikum.created_at) as mulai'))
                ->groupBy('modul_praktikum.praktikum_id')
                ->get();

        foreach ($praktikumBerjalan as $riwayat) {
            DB::table('eo_praktikum')->where('id', $riwayat->praktikum_id)->update([
                'status_pelaksanaan' => 'berjalan',
                'started_at' => $riwayat->mulai ?? now(),
            ]);
        }

        Schema::create('pengajuan_modul', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->uuid('praktikum_sumber_id')->nullable();
            $table->unsignedInteger('versi');
            $table->string('status', 30)->default('draft');
            $table->foreignId('diajukan_oleh')->constrained('users')->cascadeOnDelete();
            $table->foreignId('direview_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_pengajuan')->nullable();
            $table->text('catatan_reviewer')->nullable();
            $table->timestamp('diajukan_at')->nullable();
            $table->timestamp('direview_at')->nullable();
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
            $table->foreign('praktikum_sumber_id')->references('id')->on('eo_praktikum')->nullOnDelete();
            $table->unique(['praktikum_id', 'versi']);
            $table->index(['praktikum_id', 'status']);
        });

        Schema::create('pengajuan_modul_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_modul_id')->constrained('pengajuan_modul')->cascadeOnDelete();
            $table->foreignId('modul_sumber_id')->nullable()->constrained('modul_praktikum')->nullOnDelete();
            $table->string('aksi', 20)->default('tambah');
            $table->string('nama_lama')->nullable();
            $table->unsignedInteger('urutan_lama')->nullable();
            $table->string('nama_baru')->nullable();
            $table->unsignedInteger('urutan_baru')->nullable();
            $table->timestamps();

            $table->index(['pengajuan_modul_id', 'aksi']);
        });

        Schema::table('modul_praktikum', function (Blueprint $table) {
            $table->foreignId('modul_sumber_id')->nullable()->after('praktikum_id')
                ->constrained('modul_praktikum')->nullOnDelete();
            $table->foreignId('pengajuan_modul_id')->nullable()->after('modul_sumber_id')
                ->constrained('pengajuan_modul')->nullOnDelete();
        });

        Schema::table('materi_modul', function (Blueprint $table) {
            $table->foreignId('materi_sumber_id')->nullable()->after('modul_id')
                ->constrained('materi_modul')->nullOnDelete();
            $table->unique(['modul_id', 'materi_sumber_id'], 'materi_modul_sumber_unique');
        });
    }

    public function down(): void
    {
        Schema::table('materi_modul', function (Blueprint $table) {
            $table->dropUnique('materi_modul_sumber_unique');
            $table->dropConstrainedForeignId('materi_sumber_id');
        });

        Schema::table('modul_praktikum', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengajuan_modul_id');
            $table->dropConstrainedForeignId('modul_sumber_id');
        });

        Schema::dropIfExists('pengajuan_modul_item');
        Schema::dropIfExists('pengajuan_modul');

        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->dropColumn(['status_pelaksanaan', 'started_at']);
        });
    }
};
