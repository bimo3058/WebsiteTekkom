<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            // PDF asli sebelum ada TTD tertanam — tidak pernah diubah setelah upload pertama.
            // Digunakan untuk regenerasi PDF saat TTD dibatalkan / diulang.
            $table->string('surat_proker_original')->nullable()->after('surat_proker');
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropColumn('surat_proker_original');
        });
    }
};
