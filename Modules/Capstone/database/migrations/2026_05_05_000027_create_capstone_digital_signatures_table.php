<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Digital Signatures (TTD digital untuk dokumen Capstone)
 *
 * Source: 2026_02_27_000007_create_digital_signatures_table
 *
 * NOTE:
 * - user_id tetap FK ke `users` karena yang TTD bisa siapa aja
 *   (admin, dosen, dekan, dll)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('document_reference');   // reference to signed document
            $table->string('document_type');         // SURAT_TUGAS, BERITA_ACARA, etc.
            $table->text('signature_data');          // base64 signature image atau hash
            $table->string('hash')->unique();        // SHA-256 hash for verification
            $table->timestamp('signed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_digital_signatures');
    }
};
