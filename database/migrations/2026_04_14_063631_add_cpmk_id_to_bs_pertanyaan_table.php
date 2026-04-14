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
        Schema::table('bs_pertanyaan', function (Blueprint $table) {
            $table->foreignId('cpmk_id')->nullable()->after('cpl_id')->constrained('bs_cpmk')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_pertanyaan', function (Blueprint $table) {
            $table->dropForeign(['cpmk_id']);
            $table->dropColumn('cpmk_id');
        });
    }
};
