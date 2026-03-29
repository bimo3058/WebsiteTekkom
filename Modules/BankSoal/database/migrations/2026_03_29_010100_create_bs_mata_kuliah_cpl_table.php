<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_mata_kuliah_cpl', function (Blueprint $table) {
            $table->unsignedBigInteger('mk_id');
            $table->unsignedBigInteger('cpl_id');

            $table->primary(['mk_id', 'cpl_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_mata_kuliah_cpl');
    }
};
