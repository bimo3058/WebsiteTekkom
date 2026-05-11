<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Documents (dokumen yg di-upload mahasiswa per fase)
 *
 * Konsolidasi dari CTMS migrations:
 * - 2026_02_16_140427_create_documents_table
 * - 2026_02_22_000008_update_documents_table_add_document_type
 *
 * NOTE:
 * - student_id FK ke `students`
 * - reviewed_by tetap FK ke `users` (yg review bisa admin/dosen)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('capstone_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()
                ->constrained('students')->nullOnDelete();
            $table->string('phase'); // PDC1, PDC2, TA, etc.
            $table->string('document_type')->default('GENERAL');
            // C100, C200, C300 (PDC1), C400, C500 (PDC2), GENERAL
            $table->string('file_path');
            $table->integer('version')->default(1);
            $table->string('status')->default('DRAFT'); // DRAFT, SUBMITTED, APPROVED, REJECTED
            $table->text('feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_documents');
    }
};
