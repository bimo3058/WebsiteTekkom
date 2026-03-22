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
        Schema::create('bs_rps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mk_id');
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->timestamp('tenggat');
            $table->string('semester_berlaku');
            $table->timestamps();

            $table->foreign('mk_id')
                ->references('id')->on('bs_mata_kuliah')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('dosen_id')
                ->references('id')->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_rps');
    }
};
