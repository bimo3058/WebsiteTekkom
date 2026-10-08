<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_proker_ttd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('mk_kegiatan')->cascadeOnDelete();
            
            // Role yang menandatangani: ketua_himpunan, dpm, ketua_departemen
            $table->string('role');
            
            // User yang menandatangani
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Path gambar tanda tangan di Supabase Storage
            $table->string('signature_image_path')->nullable();
            
            // Posisi penempatan TTD pada PDF (disimpan sebagai persen dari ukuran halaman)
            $table->unsignedSmallInteger('page_number')->default(1);
            $table->decimal('pos_x_percent', 6, 2)->default(0);   // % dari kiri halaman
            $table->decimal('pos_y_percent', 6, 2)->default(0);   // % dari atas halaman
            $table->decimal('width_percent', 6, 2)->default(15);  // % lebar halaman
            $table->decimal('height_percent', 6, 2)->default(8);  // % tinggi halaman
            
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
            
            // Satu proker hanya bisa punya satu TTD per role
            $table->unique(['kegiatan_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_proker_ttd');
    }
};
