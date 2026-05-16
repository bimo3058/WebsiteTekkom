<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_pengumuman_personal_pins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pengumuman_id');
            $table->timestamps();

            $table->unique(['user_id', 'pengumuman_id'], 'mk_pengumuman_personal_pins_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pengumuman_id')->references('id')->on('mk_pengumuman')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_pengumuman_personal_pins');
    }
};
