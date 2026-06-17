<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom reward (Request Bu Bellia / B.2 — dasar SK FT 774/2025).
        // Mengembangkan claim toggle B.3 menjadi workflow pengajuan reward.
        if (!Schema::hasColumn('mk_prestasi', 'reward_penyelenggara')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->string('reward_penyelenggara', 20)->nullable()->after('claimed_by');
                $table->string('reward_capaian', 40)->nullable()->after('reward_penyelenggara');
                $table->boolean('reward_is_invention')->default(false)->after('reward_capaian');
                $table->unsignedTinyInteger('reward_jml_mk_max')->nullable()->after('reward_is_invention');
                $table->unsignedTinyInteger('reward_sks_max')->nullable()->after('reward_jml_mk_max');
                $table->text('reward_mk_disetujui')->nullable()->after('reward_sks_max');
                $table->unsignedBigInteger('reward_reviewed_by')->nullable()->after('reward_mk_disetujui');
                $table->timestamp('reward_reviewed_at')->nullable()->after('reward_reviewed_by');
                $table->text('reward_note')->nullable()->after('reward_reviewed_at');

                $table->foreign('reward_reviewed_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Migrasi nilai claim_status lama (B.3) -> status workflow reward baru.
        // Self-claim B.3 BUKAN persetujuan departemen & tidak membawa data SK 774,
        // jadi keduanya direset ke 'belum_ajukan' agar diajukan ulang lewat alur baru.
        DB::table('mk_prestasi')->whereIn('claim_status', ['belum_claim', 'sudah_claim'])->update([
            'claim_status' => 'belum_ajukan',
            'claimed_by'   => null,
            'claimed_at'   => null,
        ]);
    }

    public function down(): void
    {
        // Kembalikan nilai status ke skema B.3 sebelum hapus kolom reward.
        DB::table('mk_prestasi')->whereIn('claim_status', ['belum_ajukan', 'diajukan', 'ditolak'])->update(['claim_status' => 'belum_claim']);
        DB::table('mk_prestasi')->where('claim_status', 'disetujui')->update(['claim_status' => 'sudah_claim']);

        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropForeign(['reward_reviewed_by']);
            $table->dropColumn([
                'reward_penyelenggara',
                'reward_capaian',
                'reward_is_invention',
                'reward_jml_mk_max',
                'reward_sks_max',
                'reward_mk_disetujui',
                'reward_reviewed_by',
                'reward_reviewed_at',
                'reward_note',
            ]);
        });
    }
};
