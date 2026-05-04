<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna_role', function (Blueprint $table) {
            // Gunakan auto-increment id + composite unique (bukan UUID PK)
            // karena Eloquent BelongsToMany attach/sync tidak support custom UUID PK
            $table->id(); // auto-increment bigint
            $table->uuid('pengguna_id');
            $table->uuid('role_id');
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamps(); // created_at, updated_at

            $table->foreign('pengguna_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');

            // Satu user tidak boleh punya role yang sama dua kali
            $table->unique(['pengguna_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna_role');
    }
};
