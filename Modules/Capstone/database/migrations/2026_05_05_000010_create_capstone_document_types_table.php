<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Document Types (master tipe dokumen — HKI, Hak Cipta, dll)
 *
 * Source: 2026_02_27_000006_create_document_types_table
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // e.g. "HKI", "Hak Cipta", "Surat Keterangan"
            $table->text('description')->nullable();
            $table->string('phase')->nullable(); // PDC1, PDC2, TA, or null (all phases)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_document_types');
    }
};
