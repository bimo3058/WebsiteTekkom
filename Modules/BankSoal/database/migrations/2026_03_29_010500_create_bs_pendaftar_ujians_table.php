<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Struktur final sudah dibuat oleh migration 2026_03_27_092220.
        // File ini dipertahankan sebagai migration kompatibilitas agar seluruh
        // riwayat project tetap tercatat tanpa membuat tabel yang sama dua kali.
    }

    public function down(): void
    {
        // Tidak menghapus tabel yang dimiliki migration sebelumnya.
    }
};
