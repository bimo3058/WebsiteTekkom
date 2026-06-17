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
        Schema::table('eo_kp_periode', function (Blueprint $table) {
            $table->date('pra_kp_mulai')->nullable();
            $table->date('pra_kp_akhir')->nullable();
            $table->date('pra_kp_pengingat')->nullable();
            $table->date('saat_kp_mulai')->nullable();
            $table->date('saat_kp_akhir')->nullable();
            $table->date('saat_kp_pengingat')->nullable();
            $table->date('pasca_kp_mulai')->nullable();
            $table->date('pasca_kp_akhir')->nullable();
            $table->date('pasca_kp_pengingat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_periode', function (Blueprint $table) {
            $table->dropColumn([
                'pra_kp_mulai',
                'pra_kp_akhir',
                'pra_kp_pengingat',
                'saat_kp_mulai',
                'saat_kp_akhir',
                'saat_kp_pengingat',
                'pasca_kp_mulai',
                'pasca_kp_akhir',
                'pasca_kp_pengingat'
            ]);
        });
    }
};
