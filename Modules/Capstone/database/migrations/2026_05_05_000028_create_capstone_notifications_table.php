<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Notifications (notifikasi internal modul Capstone)
 *
 * Source: 2026_02_24_000003_create_notifications_table
 *
 * NOTE:
 * - user_id tetap FK ke `users` (siapapun bisa terima notif)
 * - Tabel ini terpisah dari sistem notif modul lain (eo_notifikasi, dll)
 *   biar gak campur — FE Next.js akses ini aja.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');          // e.g. GROUP_INVITATION, SCHEDULE_APPROVED
            $table->string('title');
            $table->text('message');
            $table->string('related_type')->nullable();  // e.g. Group, SeminarSchedule
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Indexes for unread count + inbox ordering
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_notifications');
    }
};
