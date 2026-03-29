<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bs_rps_assign', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mk_id');
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->timestamp('tenggat')->nullable();
            $table->string('semester_berlaku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_rps_assign');
    }
};
