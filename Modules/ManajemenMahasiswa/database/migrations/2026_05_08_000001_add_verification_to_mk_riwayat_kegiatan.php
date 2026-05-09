<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_riwayat_kegiatan', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('tanggal_kegiatan');
            }
            if (!Schema::hasColumn('mk_riwayat_kegiatan', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('mk_riwayat_kegiatan', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('mk_riwayat_kegiatan', 'verification_note')) {
                $table->text('verification_note')->nullable()->after('verified_at');
            }
        });

        Schema::table('mk_prestasi', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_prestasi', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('tahun');
            }
            if (!Schema::hasColumn('mk_prestasi', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('mk_prestasi', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('mk_prestasi', 'verification_note')) {
                $table->text('verification_note')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('mk_prestasi', 'tanggal')) {
                $table->date('tanggal')->nullable()->after('verification_note');
            }
        });

        Schema::table('mk_kegiatan_panitia', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kegiatan_panitia', 'peran')) {
                $table->string('peran')->nullable()->after('student_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'verification_note']);
        });

        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'verification_note', 'tanggal']);
        });

        Schema::table('mk_kegiatan_panitia', function (Blueprint $table) {
            $table->dropColumn('peran');
        });
    }
};
