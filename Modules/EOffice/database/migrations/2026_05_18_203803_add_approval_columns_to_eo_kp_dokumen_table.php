<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('eo_kp_dokumen', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('file_path');
            $table->enum('phase', ['pra_kp', 'saat_kp', 'pasca_kp'])->nullable()->after('jenis_dokumen');
            
            // Approval fields
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'revision'])->default('pending')->after('status_validasi');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('revision_note')->nullable()->after('approved_at');
            
            // Nilai Lapangan fields
            $table->float('nilai_input_mahasiswa')->nullable();
            $table->float('nilai_validasi_koordinator')->nullable();
            $table->enum('nilai_status', ['pending', 'valid', 'rejected'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_dokumen', function (Blueprint $table) {
            $table->dropColumn([
                'file_name',
                'phase',
                'approval_status',
                'approved_by',
                'approved_at',
                'revision_note',
                'nilai_input_mahasiswa',
                'nilai_validasi_koordinator',
                'nilai_status',
            ]);
        });
    }
};
