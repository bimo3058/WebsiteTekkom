<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: Expo Registrations (pendaftaran group ke expo event)
 *
 * Source: 2026_02_24_000002_create_expo_registrations_table
 *
 * NOTE:
 * - status pakai string biasa (bukan enum) supaya kompatibel cross-DB
 *   (CTMS asli pakai enum yg tricky di Postgres)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_expo_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expo_event_id')
                ->constrained('capstone_expo_events')->cascadeOnDelete();
            $table->foreignId('group_id')
                ->constrained('capstone_groups')->cascadeOnDelete();
            $table->timestamp('registered_at')->useCurrent();
            $table->string('status')->default('REGISTERED'); // REGISTERED, SCHEDULED, DONE
            $table->timestamps();

            // Cegah double registration
            $table->unique(['expo_event_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_expo_registrations');
    }
};
