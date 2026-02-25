<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('external_id', 100)->unique(); // NIM / NIP / SSO unique ID
            $table->string('name');
            $table->string('email')->unique();

            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_synced_from_sso')->nullable();
            $table->json('sso_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};