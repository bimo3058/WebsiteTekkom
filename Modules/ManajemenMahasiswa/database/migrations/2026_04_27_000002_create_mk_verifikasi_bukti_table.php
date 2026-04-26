<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_verifikasi_bukti', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bukti_type', 50);  // 'riwayat' atau 'prestasi'
            $table->unsignedBigInteger('bukti_id'); // ID dari riwayat/prestasi
            $table->string('nama_file', 255);
            $table->string('path_file', 500);
            $table->string('tipe_file', 50);   // image, document
            $table->timestamps();

            $table->index(['bukti_type', 'bukti_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_verifikasi_bukti');
    }
};
