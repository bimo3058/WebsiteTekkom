<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Audit Logs (audit trail per modul)
 *
 * Source: 2026_02_22_000005_create_audit_logs_table
 *
 * NOTE:
 * - Webtekkom punya tabel `audit_logs` global di luar modul ini.
 *   Kamu bisa pilih:
 *   (a) Pakai tabel ini buat audit khusus Capstone, atau
 *   (b) Skip migration ini & pakai global audit_logs aja.
 *   Saran saya: pakai keduanya (dual-write) — global buat overview superadmin,
 *   modul buat detail action di Capstone yg gak relevan ke modul lain.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');           // e.g. FINALIZATION_ALLOCATE, BIDDING_LOCK
            $table->string('target_type');      // e.g. Group, Title, TaSubmission
            $table->unsignedBigInteger('target_id');
            $table->json('payload')->nullable(); // extra context
            $table->timestamps();

            $table->index('user_id');
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_audit_logs');
    }
};
