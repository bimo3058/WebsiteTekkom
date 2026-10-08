<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengaduan tidak boleh hilang permanen.
 *
 * destroy() sebelumnya melakukan hard delete, dan FK pada mk_pengaduan_delegasi
 * serta mk_pengaduan_log memakai ON DELETE CASCADE — sehingga menghapus satu
 * tiket ikut memusnahkan seluruh jejak auditnya tanpa catatan apa pun.
 * Dengan SoftDeletes, baris tetap ada dan cascade tidak pernah terpicu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
