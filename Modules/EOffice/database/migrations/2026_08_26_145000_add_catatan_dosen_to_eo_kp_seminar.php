<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eo_kp_seminar', function (Blueprint $table) {
            if (!Schema::hasColumn('eo_kp_seminar', 'catatan_dosen')) {
                $table->text('catatan_dosen')->nullable()->after('status_validasi_dosen');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eo_kp_seminar', function (Blueprint $table) {
            if (Schema::hasColumn('eo_kp_seminar', 'catatan_dosen')) {
                $table->dropColumn('catatan_dosen');
            }
        });
    }
};
