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
        Schema::table('eo_kp_komponen_nilai', function (Blueprint $table) {
            $table->foreignId('master_rubrik_id')->nullable()->after('periode_id')->constrained('eo_kp_master_rubrik')->nullOnDelete();
            $table->string('kode')->nullable()->after('master_rubrik_id'); // Snapshot of kode
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_komponen_nilai', function (Blueprint $table) {
            $table->dropForeign(['master_rubrik_id']);
            $table->dropColumn(['master_rubrik_id', 'kode']);
        });
    }
};
