<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom status claim reward ke mk_prestasi (Request Pak Delphi / B.3)
        if (!Schema::hasColumn('mk_prestasi', 'claim_status')) {
            Schema::table('mk_prestasi', function (Blueprint $table) {
                $table->string('claim_status', 20)->default('belum_claim')->after('verification_note');
                $table->timestamp('claimed_at')->nullable()->after('claim_status');
                $table->unsignedBigInteger('claimed_by')->nullable()->after('claimed_at');

                $table->foreign('claimed_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropForeign(['claimed_by']);
            $table->dropColumn(['claim_status', 'claimed_at', 'claimed_by']);
        });
    }
};
