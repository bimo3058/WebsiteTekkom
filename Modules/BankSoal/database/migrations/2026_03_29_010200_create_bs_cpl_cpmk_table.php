<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_cpl_cpmk', function (Blueprint $table) {
            $table->unsignedBigInteger('cpl_id');
            $table->unsignedBigInteger('cpmk_id')->unique();

            $table->primary(['cpl_id', 'cpmk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_cpl_cpmk');
    }
};
