<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_rps_cpl', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rps_id');
            $table->unsignedBigInteger('cpl_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_rps_cpl');
    }
};
