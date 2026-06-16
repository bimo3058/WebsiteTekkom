<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
      /**
       * Run the migrations.
       *
       * Membuat tabel-tabel untuk sub-modul Kerja Praktik (KP)
       * di dalam Modules/EOffice dengan prefix eo_.
       *
       * Entitas utama berdasarkan ERD:
       *  - eo_kp_mahasiswa       : profil mahasiswa dalam konteks KP
       *  - eo_kp_dosen           : profil dosen pembimbing KP
       *  - eo_kp_pengumuman      : timeline pengumuman/info KP
       *  - eo_kerja_praktik      : data utama pengajuan & pelaksanaan KP
       *  - eo_kp_dokumen         : dokumen persyaratan & hasil KP
       *  - eo_kp_seminar         : jadwal & detail seminar KP
       *  - eo_kp_penilaian       : nilai akhir KP per mahasiswa
       */
      public function up(): void
      {
            // ─── PROFIL MAHASISWA KP ──────────────────────────────────────────────
            // Ekstensi data mahasiswa yang relevan untuk konteks KP.
            // Berelasi ke tabel users global (bukan duplikasi).
            Schema::create('eo_kp_mahasiswa', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('user_id')->unique()->comment('FK ke tabel users global');
                  $table->string('nim')->comment('Nomor Induk Mahasiswa');
                  $table->string('nama_lengkap');
                  $table->string('prodi')->comment('Program studi, misal: Teknik Informatika');
                  $table->timestamps();

                  $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->onDelete('cascade');

                  $table->index('nim');
            });

            // ─── PROFIL DOSEN KP ─────────────────────────────────────────────────
            // Ekstensi data dosen yang bertindak sebagai pembimbing KP.
            Schema::create('eo_kp_dosen', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('user_id')->unique()->comment('FK ke tabel users global');
                  $table->string('nip')->comment('Nomor Induk Pegawai');
                  $table->string('nama_lengkap');
                  $table->timestamps();

                  $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->onDelete('cascade');

                  $table->index('nip');
            });

            // ─── PENGUMUMAN / TIMELINE KP ─────────────────────────────────────────
            // Dikelola oleh Admin atau Koordinator KP untuk menginformasikan
            // jadwal, persyaratan, dan pengumuman lain seputar KP.
            Schema::create('eo_kp_pengumuman', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('user_id')->comment('Admin/Koor yang membuat info (FK users)');
                  $table->string('judul');
                  $table->text('deskripsi')->nullable();
                  $table->date('tanggal_mulai')->nullable()->comment('Tanggal mulai periode yang diumumkan');
                  $table->date('tanggal_selesai')->nullable()->comment('Tanggal akhir periode yang diumumkan');
                  $table->boolean('is_published')->default(false);
                  $table->timestamps();

                  $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->onDelete('cascade');
            });

            // ─── KERJA PRAKTIK (TABEL UTAMA) ─────────────────────────────────────
            // Menyimpan seluruh data satu siklus KP seorang mahasiswa:
            // mulai dari pengajuan awal hingga selesai.
            // Satu baris = satu pengajuan KP oleh satu mahasiswa.
            Schema::create('eo_kerja_praktik', function (Blueprint $table) {
                  $table->bigIncrements('id');

                  // Identitas pengaju
                  $table->string('nim')->comment('Denormalisasi NIM agar mudah query tanpa JOIN');
                  $table->unsignedBigInteger('mahasiswa_id')->comment('FK ke eo_kp_mahasiswa');

                  // Pembimbing (nullable: belum tentu langsung ditugaskan)
                  $table->unsignedBigInteger('dosen_pembimbing_id')
                        ->nullable()
                        ->comment('FK ke eo_kp_dosen; null = belum ada pembimbing');

                  // Data rencana (diisi saat pengajuan awal / pra-KP)
                  $table->string('rencana_judul')->nullable()->comment('Judul rencana / usulan KP');
                  $table->string('rencana_tempat')->nullable()->comment('Nama instansi/perusahaan yang dituju');

                  // Data realisasi (diisi setelah KP berjalan / disetujui)
                  $table->string('tempat_fix')->nullable()->comment('Tempat KP yang sudah dikonfirmasi');
                  $table->string('judul_fix')->nullable()->comment('Judul laporan KP final');
                  $table->date('tanggal_mulai')->nullable();
                  $table->date('tanggal_selesai')->nullable();

                  // Status alur KP
                  $table->string('status_kp')
                        ->default('Pra-KP')
                        ->comment('Enum: Pra-KP, Saat KP, Pasca KP, Selesai');

                  // Flag khusus admin (misal: untuk proses validasi manual)
                  $table->boolean('is_acc_admin')->default(false)
                        ->comment('true = sudah divalidasi/acc oleh admin');

                  $table->timestamps();
                  $table->softDeletes();

                  // Foreign keys
                  $table->foreign('mahasiswa_id')
                        ->references('id')->on('eo_kp_mahasiswa')
                        ->onDelete('cascade');

                  $table->foreign('dosen_pembimbing_id')
                        ->references('id')->on('eo_kp_dosen')
                        ->onDelete('set null');

                  // Index untuk query umum
                  $table->index('nim');
                  $table->index('status_kp');
                  $table->index(['mahasiswa_id', 'status_kp']);
            });

            // ─── DOKUMEN KP ───────────────────────────────────────────────────────
            // Setiap KP memiliki banyak dokumen yang diunggah mahasiswa
            // pada berbagai tahap (pra, saat, pasca KP).
            Schema::create('eo_kp_dokumen', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('kp_id')->comment('FK ke eo_kerja_praktik');
                  $table->string('jenis_dokumen')
                        ->comment('Enum: Transkrip, Proposal, Bukti Terima, Laporan, Makalah, Kartu Hijau, A2, dll');
                  $table->string('file_path')->comment('Path file yang disimpan di storage');
                  $table->string('status_validasi')
                        ->default('menunggu')
                        ->comment('Enum: menunggu, disetujui, ditolak');
                  $table->timestamp('tanggal_upload')->useCurrent();
                  $table->timestamps();

                  $table->foreign('kp_id')
                        ->references('id')->on('eo_kerja_praktik')
                        ->onDelete('cascade');

                  $table->index(['kp_id', 'jenis_dokumen']);
                  $table->index('status_validasi');
            });

            // ─── SEMINAR KP ───────────────────────────────────────────────────────
            // Satu KP menghasilkan tepat satu seminar.
            // Menyimpan jadwal, ruangan, dan berkas undangan seminar.
            Schema::create('eo_kp_seminar', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('kp_id')->comment('FK ke eo_kerja_praktik');
                  $table->date('tanggal_seminar');
                  $table->time('waktu_seminar');
                  $table->string('ruangan')->nullable();
                  $table->string('status_validasi_syarat')
                        ->default('belum')
                        ->comment('Enum: belum, proses, valid, ditolak — validasi syarat seminar');
                  $table->string('path_undangan')
                        ->nullable()
                        ->comment('Path file surat undangan seminar (generated oleh sistem)');
                  $table->timestamps();

                  $table->foreign('kp_id')
                        ->references('id')->on('eo_kerja_praktik')
                        ->onDelete('cascade');

                  $table->index('tanggal_seminar');
            });

            // ─── PENILAIAN KP ────────────────────────────────────────────────────
            // Nilai akhir KP per mahasiswa, dikompilasi dari berbagai aspek.
            // Satu KP memiliki tepat satu record penilaian.
            Schema::create('eo_kp_penilaian', function (Blueprint $table) {
                  $table->bigIncrements('id');
                  $table->unsignedBigInteger('kp_id')->comment('FK ke eo_kerja_praktik');
                  $table->float('nilai_lapangan')
                        ->nullable()
                        ->comment('Nilai dari pembimbing lapangan (instansi)');
                  $table->float('nilai_seminar_pembimbing')
                        ->nullable()
                        ->comment('Nilai dari dosen pembimbing saat seminar');
                  $table->float('nilai_akhir')
                        ->nullable()
                        ->comment('Nilai akhir yang dikompilasi / dikomputasi');
                  $table->timestamps();

                  $table->foreign('kp_id')
                        ->references('id')->on('eo_kerja_praktik')
                        ->onDelete('cascade');
            });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
            // Drop dalam urutan terbalik untuk menghormati foreign key constraints
            Schema::dropIfExists('eo_kp_penilaian');
            Schema::dropIfExists('eo_kp_seminar');
            Schema::dropIfExists('eo_kp_dokumen');
            Schema::dropIfExists('eo_kerja_praktik');
            Schema::dropIfExists('eo_kp_pengumuman');
            Schema::dropIfExists('eo_kp_dosen');
            Schema::dropIfExists('eo_kp_mahasiswa');
      }
};
