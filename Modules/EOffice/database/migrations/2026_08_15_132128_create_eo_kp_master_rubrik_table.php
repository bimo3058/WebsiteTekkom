<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eo_kp_master_rubrik', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->text('deskripsi')->nullable();
            $table->integer('bobot')->default(0);
            $table->enum('role_penilai', ['dosen_pembimbing', 'koordinator'])->default('dosen_pembimbing');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_master_rubrik');
    }
};
