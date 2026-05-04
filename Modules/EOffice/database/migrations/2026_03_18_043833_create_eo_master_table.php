<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── SHARED ──────────────────────────────────────────────
        // Roles khusus modul EO (terpisah dari global roles)
        Schema::create('eo_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('enum: mahasiswa, dosen, admin, kadiv, super_admin');
            $table->integer('prioritas')->comment('1=rendah, 3=tinggi');
            $table->timestamps();
        });

        // ─── MANAJEMEN SURAT ─────────────────────────────────────
        Schema::create('eo_jenis_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_jenis');
            $table->string('kode_surat');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('eo_penomoran_counter', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bulan_tahun');
            $table->integer('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('eo_template_surat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jenis_id');
            $table->text('isi_template');
            $table->timestamps();

            $table->foreign('jenis_id')->references('id')->on('eo_jenis_surat')->onDelete('cascade');
        });

        Schema::create('eo_field_template', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('template_id');
            $table->string('nama_field');
            $table->string('tipe_input')->comment('text, textarea, select, date, dll');
            $table->boolean('is_required')->default(false);
            $table->integer('urutan');
            $table->text('options')->nullable()->comment('untuk tipe select');
            $table->string('default_value')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('eo_template_surat')->onDelete('cascade');
        });

        // ─── PEMINJAMAN RUANGAN ───────────────────────────────────
        Schema::create('eo_ruangan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode')->unique()->comment('contoh: R101');
            $table->string('nama')->comment('contoh: Lab Komputer 1');
            $table->string('gedung');
            $table->integer('lantai');
            $table->integer('kapasitas');
            $table->text('fasilitas')->nullable()->comment('proyektor, AC, dll');
            $table->string('status')->default('aktif')->comment('enum: aktif, nonaktif');
            $table->timestamps();
        });

        Schema::create('eo_aturan_peminjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_aturan')->unique()->comment('contoh: MPA_DURASI');
            $table->string('nama')->comment('contoh: Maksimal Durasi Peminjaman');
            $table->integer('nilai')->comment('contoh: 4');
            $table->string('satuan')->comment('contoh: jam');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable()->comment('FK: super_admin id');
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_aturan_peminjaman');
        Schema::dropIfExists('eo_ruangan');
        Schema::dropIfExists('eo_field_template');
        Schema::dropIfExists('eo_template_surat');
        Schema::dropIfExists('eo_penomoran_counter');
        Schema::dropIfExists('eo_jenis_surat');
        Schema::dropIfExists('eo_roles');
    }
};