<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('rencana_tempat', 'instansi_kp');
            $table->renameColumn('rencana_judul', 'judul_kp');

            // Drop unused fix columns
            $table->dropColumn(['tempat_fix', 'judul_fix']);
        });
    }

    public function down()
    {
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            // Restore drop columns
            $table->string('tempat_fix')->nullable()->comment('Tempat KP yang sudah dikonfirmasi');
            $table->string('judul_fix')->nullable()->comment('Judul laporan KP final');

            // Revert rename
            $table->renameColumn('instansi_kp', 'rencana_tempat');
            $table->renameColumn('judul_kp', 'rencana_judul');
        });
    }
};
