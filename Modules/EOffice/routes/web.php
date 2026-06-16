<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\DashboardController;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\KerjaPraktikController;
use Modules\EOffice\Http\Controllers\DosenController;
use Modules\EOffice\Http\Controllers\KoordinatorController;
use Modules\EOffice\Http\Controllers\MahasiswaKpController;

// ── ManajemenPraktikum Admin ─────────────────────────────────────────────────
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\AsprakController as AdminAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DaftarPraktikanController as AdminDaftarPraktikanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DashboardController as AdminManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DosenController as AdminDosenPrakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\KelolaPendaftaranController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PendaftaranAsprakController as AdminPendaftaranAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PendaftaranKoorController as AdminPendaftaranKoorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\KelolRoleController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\MatkulPraktikumController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PeriodePendaftaranController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumDetailController;


// ── ManajemenPraktikum Dosen ─────────────────────────────────────────────────
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\AsprakController as DosenAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\DaftarPraktikanController as DosenDaftarPraktikanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\DashboardController as DosenManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\ModulController as DosenModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\NilaiController as DosenNilaiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\PendaftaranKoorController as DosenPendaftaranKoorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\PengumumanController as DosenPengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\TugasController as DosenTugasController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\PeriodePendaftaranController as DosenPeriodePendaftaranController;

// ── ManajemenPraktikum Koordinator ───────────────────────────────────────────
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\BagiModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController as KoorManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\ModulController as KoorModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\NilaiController as KoorNilaiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\PendaftaranAsprakController as KoorPendaftaranAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\PendaftaranPraktikanController as KoorPendaftaranPraktikanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\PengumumanController as KoorPengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\PeriodePendaftaranController as KoorPeriodePendaftaranController;

// ── ManajemenPraktikum Asprak ────────────────────────────────────────────────
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\AbsensiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\DashboardController as AsprakManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\MateriController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\ModulController as AsprakModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\PengumumanController as AsprakPengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\DaftarPraktikanController as AsprakDaftarPraktikanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController as AsprakTugasController;

// ── ManajemenPraktikum Mahasiswa ─────────────────────────────────────────────
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DaftarAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\PendaftaranPraktikanController as MhsPendaftaranPraktikanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DashboardController as MhsManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\ModulController as MhsModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\NilaiController as MhsNilaiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\PengumumanController as MhsPengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\TugasController as MhsTugasController;

Route::middleware(['auth', 'module.active:eoffice'])->group(function () {

    // ── Dashboard utama ──────────────────────────────────────────────────────
    Route::get('/eoffice/dashboard', [DashboardController::class, 'index'])
        ->name('eoffice.dashboard');

    // ── Dokumen / EOffice umum ───────────────────────────────────────────────
    Route::resource('eoffice/documents', EOfficeController::class)->except(['index']);

    // ════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PRAKTIKUM
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/manprak')->name('eoffice.manprak.')->group(function () {

        // ── ADMIN ────────────────────────────────────────────────────────────
        Route::middleware(['role:superadmin|admin_eoffice'])
            ->prefix('admin')->name('admin.')
            ->group(function () {

                Route::get('/dashboard', [AdminManprakDashboard::class, 'index'])
                    ->name('dashboard');

                Route::get('praktikum/{id}/detail', [PraktikumDetailController::class, 'show'])  // ← TAMBAH INI DULU
                    ->name('praktikum.detail');

                // CRUD Praktikum
                Route::resource('praktikum', PraktikumController::class)
                    ->names('praktikum');
                Route::put('praktikum/{id}/assign-koor', [PraktikumController::class, 'assignKoor'])
                    ->name('praktikum.assign-koor');
                Route::post('praktikum/generate-kode', [PraktikumController::class, 'generateKode'])
                    ->name('praktikum.generate-kode');

                // Dosen (list)
                Route::get('dosen', [AdminDosenPrakController::class, 'index'])
                    ->name('dosen.index');

                // Asisten Praktikum (lihat per praktikum)
                Route::get('asprak', [AdminAsprakController::class, 'index'])
                    ->name('asprak.index');

                // Daftar Praktikan (tambah/import/hapus)
                Route::get('daftar-praktikan', [AdminDaftarPraktikanController::class, 'index'])
                    ->name('daftar-praktikan.index');
                Route::post('daftar-praktikan', [AdminDaftarPraktikanController::class, 'store'])
                    ->name('daftar-praktikan.store');
                Route::delete('daftar-praktikan/{id}', [AdminDaftarPraktikanController::class, 'destroy'])
                    ->name('daftar-praktikan.destroy');

                // Pendaftaran Asprak
                Route::get('pendaftaran-asprak', [AdminPendaftaranAsprakController::class, 'index'])
                    ->name('pendaftaran-asprak.index');
                Route::post('pendaftaran-asprak/{id}/approve', [AdminPendaftaranAsprakController::class, 'approve'])
                    ->name('pendaftaran-asprak.approve');
                Route::post('pendaftaran-asprak/{id}/reject', [AdminPendaftaranAsprakController::class, 'reject'])
                    ->name('pendaftaran-asprak.reject');

                // Pendaftaran Koordinator
                Route::get('pendaftaran-koor', [AdminPendaftaranKoorController::class, 'index'])
                    ->name('pendaftaran-koor.index');
                Route::post('pendaftaran-koor/{id}/approve', [AdminPendaftaranKoorController::class, 'approve'])
                    ->name('pendaftaran-koor.approve');
                Route::post('pendaftaran-koor/{id}/reject', [AdminPendaftaranKoorController::class, 'reject'])
                    ->name('pendaftaran-koor.reject');


                // Mata Kuliah Praktikum (CRUD)
                Route::get('matkul-praktikum', [MatkulPraktikumController::class, 'index'])
                    ->name('matkul-praktikum.index');
                Route::post('matkul-praktikum', [MatkulPraktikumController::class, 'store'])
                    ->name('matkul-praktikum.store');
                Route::put('matkul-praktikum/{id}', [MatkulPraktikumController::class, 'update'])
                    ->name('matkul-praktikum.update');
                Route::delete('matkul-praktikum/{id}', [MatkulPraktikumController::class, 'destroy'])
                    ->name('matkul-praktikum.destroy');

                // Periode Pendaftaran (koor & asprak)
                Route::get('periode-pendaftaran', [PeriodePendaftaranController::class, 'index'])
                    ->name('periode-pendaftaran.index');
                Route::post('periode-pendaftaran', [PeriodePendaftaranController::class, 'store'])
                    ->name('periode-pendaftaran.store');
                Route::get('periode-pendaftaran/{id}/edit', [PeriodePendaftaranController::class, 'edit'])
                    ->name('periode-pendaftaran.edit');
                Route::put('periode-pendaftaran/{id}', [PeriodePendaftaranController::class, 'update'])
                    ->name('periode-pendaftaran.update');
                Route::post('periode-pendaftaran/assign-matkul', [PeriodePendaftaranController::class, 'assignMatkul'])
                    ->name('periode-pendaftaran.assign-matkul');
                Route::post('periode-pendaftaran/{id}/tutup', [PeriodePendaftaranController::class, 'tutup'])
                    ->name('periode-pendaftaran.tutup');
                Route::delete('periode-pendaftaran/{id}', [PeriodePendaftaranController::class, 'destroy'])
                    ->name('periode-pendaftaran.destroy');

                // Bagi Asprak ke Modul
                Route::get('bagi-asprak', [KelolaPendaftaranController::class, 'index'])
                    ->name('bagi-asprak.index');
                Route::post('bagi-asprak', [KelolaPendaftaranController::class, 'store'])
                    ->name('bagi-asprak.store');

                // Kelola Role (assign/revoke asprak & koor per praktikum)
                Route::get('kelola-role', [KelolRoleController::class, 'index'])
                    ->name('kelola-role.index');
                Route::post('kelola-role/assign', [KelolRoleController::class, 'assignRole'])
                    ->name('kelola-role.assign');
                Route::delete('kelola-role/{id}', [KelolRoleController::class, 'revokeRole'])
                    ->name('kelola-role.revoke');
            });

        // ── DOSEN ────────────────────────────────────────────────────────────
        Route::middleware(['role:dosen'])
            ->prefix('dosen')->name('dosen.')
            ->group(function () {

                Route::get('/dashboard', [DosenManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Tunjuk koordinator dari NIM
                Route::post('tunjuk-koor', [DosenManprakDashboard::class, 'tunjukKoor'])
                    ->name('tunjuk-koor');

                // Kelola modul (read-only)
                Route::get('modul/{praktikumId}', [DosenModulController::class, 'index'])
                    ->name('modul.index');

                // Asisten Praktikum (lihat per praktikum)
                Route::get('asprak', [DosenAsprakController::class, 'index'])
                    ->name('asprak.index');

                // Daftar Praktikan (lihat per praktikum)
                Route::get('daftar-praktikan', [DosenDaftarPraktikanController::class, 'index'])
                    ->name('daftar-praktikan.index');

                // Pengumuman (lihat)
                Route::get('pengumuman', [DosenPengumumanController::class, 'index'])
                    ->name('pengumuman.index');

                // Daftar Tugas (lihat + lihat pengumpulan)
                Route::get('tugas', [DosenTugasController::class, 'index'])
                    ->name('tugas.index');
                Route::get('tugas/{id}/pengumpulan', [DosenTugasController::class, 'pengumpulan'])
                    ->name('tugas.pengumpulan');

                // Pendaftaran Koordinator (CRUD + seleksi)
                Route::get('pendaftaran-koor', [DosenPendaftaranKoorController::class, 'index'])
                    ->name('pendaftaran-koor.index');
                Route::post('pendaftaran-koor/{id}/approve', [DosenPendaftaranKoorController::class, 'approve'])
                    ->name('pendaftaran-koor.approve');
                Route::post('pendaftaran-koor/{id}/reject', [DosenPendaftaranKoorController::class, 'reject'])
                    ->name('pendaftaran-koor.reject');
                Route::delete('pendaftaran-koor/{id}', [DosenPendaftaranKoorController::class, 'destroy'])
                    ->name('pendaftaran-koor.destroy');

                // Nilai (lihat + approve publikasi)
                Route::get('nilai/{praktikumId}', [DosenNilaiController::class, 'index'])
                    ->name('nilai.index');
                Route::post('nilai/{praktikumId}/approve', [DosenNilaiController::class, 'approve'])
                    ->name('nilai.approve');

                Route::get('periode-pendaftaran', [DosenPeriodePendaftaranController::class, 'index'])
                    ->name('periode-pendaftaran.index');
                Route::post('periode-pendaftaran', [DosenPeriodePendaftaranController::class, 'store'])
                    ->name('periode-pendaftaran.store');
                Route::post('periode-pendaftaran/{id}/tutup', [DosenPeriodePendaftaranController::class, 'tutup'])
                    ->name('periode-pendaftaran.tutup');
                Route::delete('periode-pendaftaran/{id}', [DosenPeriodePendaftaranController::class, 'destroy'])
                    ->name('periode-pendaftaran.destroy');
            });

        // ── KOORDINATOR ──────────────────────────────────────────────────────
        // koor.owns: pastikan koor hanya bisa akses praktikum miliknya sendiri
        // (eo_praktikum.koor_id = auth()->id()). Admin & superadmin di-bypass.
        Route::middleware(['role:koor_prak|admin_eoffice|superadmin', 'koor.owns'])
            ->prefix('koor')->name('koor.')
            ->group(function () {

                Route::get('/dashboard', [KoorManprakDashboard::class, 'index'])
                    ->name('dashboard');
                Route::post('/switch-praktikum', [KoorManprakDashboard::class, 'switchPraktikum'])
                    ->name('switch-praktikum');
                Route::post('/praktikum/generate-kode', [KoorManprakDashboard::class, 'generateKodePraktikum'])
                    ->name('praktikum.generate-kode');

                // Daftar Praktikan (lihat + import)
                Route::get('praktikan', [KoorManprakDashboard::class, 'praktikan'])
                    ->name('praktikan.index');
                Route::post('praktikan/import', [KoorManprakDashboard::class, 'importPraktikan'])
                    ->name('praktikan.import');

                // Modul (CRUD + generate kode + detail)
                Route::get('modul', [KoorModulController::class, 'index'])
                    ->name('modul.index');
                Route::post('modul', [KoorModulController::class, 'store'])
                    ->name('modul.store');
                Route::get('modul/{id}', [KoorModulController::class, 'show'])
                    ->name('modul.show');
                Route::put('modul/{id}', [KoorModulController::class, 'update'])
                    ->name('modul.update');
                Route::delete('modul/{id}', [KoorModulController::class, 'destroy'])
                    ->name('modul.destroy');
                Route::post('modul/{id}/generate-kode', [KoorModulController::class, 'generateKode'])
                    ->name('modul.generate-kode');

                // Bagi Modul ke Asprak
                Route::get('bagi-modul', [BagiModulController::class, 'index'])
                    ->name('bagi-modul.index');
                Route::post('bagi-modul', [BagiModulController::class, 'store'])
                    ->name('bagi-modul.store');
                Route::delete('bagi-modul/{id}', [BagiModulController::class, 'destroy'])
                    ->name('bagi-modul.destroy');

                // Pendaftaran Asprak (seleksi)
                Route::get('pendaftaran-asprak', [KoorPendaftaranAsprakController::class, 'index'])
                    ->name('pendaftaran-asprak.index');
                Route::post('pendaftaran-asprak/{id}/approve', [KoorPendaftaranAsprakController::class, 'approve'])
                    ->name('pendaftaran-asprak.approve');
                Route::post('pendaftaran-asprak/{id}/reject', [KoorPendaftaranAsprakController::class, 'reject'])
                    ->name('pendaftaran-asprak.reject');
                Route::delete('pendaftaran-asprak/{id}', [KoorPendaftaranAsprakController::class, 'destroy'])
                    ->name('pendaftaran-asprak.destroy');

                // Pendaftaran Praktikan (verifikasi IRS)
                Route::get('pendaftaran-praktikan', [KoorPendaftaranPraktikanController::class, 'index'])
                    ->name('pendaftaran-praktikan.index');
                Route::post('pendaftaran-praktikan/{id}/approve', [KoorPendaftaranPraktikanController::class, 'approve'])
                    ->name('pendaftaran-praktikan.approve');
                Route::post('pendaftaran-praktikan/{id}/reject', [KoorPendaftaranPraktikanController::class, 'reject'])
                    ->name('pendaftaran-praktikan.reject');
                Route::post('pendaftaran-praktikan/{id}/reject-irs-default', [KoorPendaftaranPraktikanController::class, 'rejectIrsDefault'])
                    ->name('pendaftaran-praktikan.reject-irs-default');
                Route::get('periode-pendaftaran', [KoorPeriodePendaftaranController::class, 'index'])
                    ->name('periode-pendaftaran.index');
                Route::post('periode-pendaftaran', [KoorPeriodePendaftaranController::class, 'store'])
                    ->name('periode-pendaftaran.store');
                Route::post('periode-pendaftaran/{id}/tutup', [KoorPeriodePendaftaranController::class, 'tutup'])
                    ->name('periode-pendaftaran.tutup');
                Route::delete('periode-pendaftaran/{id}', [KoorPeriodePendaftaranController::class, 'destroy'])
                    ->name('periode-pendaftaran.destroy');

                // Pengumuman
                Route::get('pengumuman', [KoorPengumumanController::class, 'index'])
                    ->name('pengumuman.index');
                Route::post('pengumuman', [KoorPengumumanController::class, 'store'])
                    ->name('pengumuman.store');
                Route::delete('pengumuman/{id}', [KoorPengumumanController::class, 'destroy'])
                    ->name('pengumuman.destroy');

                // Nilai (approve koor → kirim ke dosen untuk publikasi)
                Route::get('nilai', [KoorNilaiController::class, 'index'])
                    ->name('nilai.index');
                Route::post('nilai/approve', [KoorNilaiController::class, 'approve'])
                    ->name('nilai.approve');
            });

        // ── KOORDINATOR — Periode Pendaftaran ────────────────────────────────
        Route::middleware(['role:koor_prak'])
            ->prefix('koordinator')->name('koordinator.')
            ->group(function () {
                Route::get('periode-pendaftaran', [KoorPeriodePendaftaranController::class, 'index'])
                    ->name('periode-pendaftaran.index');
                Route::post('periode-pendaftaran', [KoorPeriodePendaftaranController::class, 'store'])
                    ->name('periode-pendaftaran.store');
                Route::post('periode-pendaftaran/{id}/tutup', [KoorPeriodePendaftaranController::class, 'tutup'])
                    ->name('periode-pendaftaran.tutup');
                Route::delete('periode-pendaftaran/{id}', [KoorPeriodePendaftaranController::class, 'destroy'])
                    ->name('periode-pendaftaran.destroy');
            });

        // ── ASISTEN PRAKTIKUM ────────────────────────────────────────────────
        // asprak.owns: pastikan asprak hanya bisa akses modul yang di-assign
        // kepadanya (modul_asprak.asprak_id). Koor & admin di-bypass.
        Route::middleware(['role:asprak|koor_prak|admin_eoffice|superadmin', 'asprak.owns'])
            ->prefix('asprak')->name('asprak.')
            ->group(function () {

                Route::get('/dashboard', [AsprakManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Absensi (CRUD)
                Route::get('absensi', [AbsensiController::class, 'index'])
                    ->name('absensi.index');
                Route::get('absensi/{modulId}', [AbsensiController::class, 'show'])
                    ->name('absensi.show');
                Route::post('absensi/{modulId}', [AbsensiController::class, 'store'])
                    ->name('absensi.store');
                Route::put('absensi/{id}', [AbsensiController::class, 'update'])
                    ->name('absensi.update');
                Route::delete('absensi/{id}', [AbsensiController::class, 'destroy'])
                    ->name('absensi.destroy');

                // Tugas (CRUD + nilai + revisi + status)
                Route::get('tugas', [AsprakTugasController::class, 'index'])
                    ->name('tugas.index');
                Route::get('tugas/create', [AsprakTugasController::class, 'create'])
                    ->name('tugas.create');
                Route::post('tugas', [AsprakTugasController::class, 'store'])
                    ->name('tugas.store');
                Route::put('tugas/{id}', [AsprakTugasController::class, 'update'])
                    ->name('tugas.update');
                Route::delete('tugas/{id}', [AsprakTugasController::class, 'destroy'])
                    ->name('tugas.destroy');
                Route::get('tugas/{id}/pengumpulan', [AsprakTugasController::class, 'pengumpulan'])
                    ->name('tugas.pengumpulan');
                Route::post('tugas/{id}/nilai', [AsprakTugasController::class, 'beriNilai'])
                    ->name('tugas.nilai');
                Route::post('tugas/{id}/revisi', [AsprakTugasController::class, 'beriRevisi'])
                    ->name('tugas.revisi');

                // Modul (CRUD — asprak bisa membuat modul baru)
                Route::get('modul', [AsprakModulController::class, 'index'])
                    ->name('modul.index');
                Route::post('modul', [AsprakModulController::class, 'store'])
                    ->name('modul.store');
                Route::get('modul/{id}', [AsprakModulController::class, 'show'])
                    ->name('modul.show');
                Route::put('modul/{id}', [AsprakModulController::class, 'update'])
                    ->name('modul.update');
                Route::delete('modul/{id}', [AsprakModulController::class, 'destroy'])
                    ->name('modul.destroy');

                // Materi Modul (upload/hapus)
                Route::get('materi', [MateriController::class, 'index'])
                    ->name('materi.index');
                Route::post('materi', [MateriController::class, 'store'])
                    ->name('materi.store');
                Route::delete('materi/{id}', [MateriController::class, 'destroy'])
                    ->name('materi.destroy');

                // Pengumuman (upload/hapus — semua asprak bisa)
                Route::get('pengumuman', [AsprakPengumumanController::class, 'index'])
                    ->name('pengumuman.index');
                Route::post('pengumuman', [AsprakPengumumanController::class, 'store'])
                    ->name('pengumuman.store');
                Route::delete('pengumuman/{id}', [AsprakPengumumanController::class, 'destroy'])
                    ->name('pengumuman.destroy');

                // Daftar Praktikan (lihat per praktikum)
                Route::get('daftar-praktikan', [AsprakDaftarPraktikanController::class, 'index'])
                    ->name('daftar-praktikan.index');
            });

        // ── MAHASISWA ────────────────────────────────────────────────────────
        Route::middleware(['role:mahasiswa|asprak|koor_prak|praktikan|admin_eoffice|superadmin'])
            ->prefix('mahasiswa')->name('mahasiswa.')
            ->group(function () {

                Route::get('/dashboard', [MhsManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Input kode praktikum
                Route::post('masuk', [MhsManprakDashboard::class, 'masukkanKode'])
                    ->name('masuk');

                // Pengumuman (lihat)
                Route::get('pengumuman', [MhsPengumumanController::class, 'index'])
                    ->name('pengumuman.index');

                // Tugas (lihat + kumpul + kirim ulang setelah revisi)
                Route::get('tugas', [MhsTugasController::class, 'index'])
                    ->name('tugas.index');
                Route::post('tugas/{id}/kumpul', [MhsTugasController::class, 'kumpul'])
                    ->name('tugas.kumpul');
                Route::post('tugas/{id}/kirim-ulang', [MhsTugasController::class, 'kirimUlang'])
                    ->name('tugas.kirim-ulang');

                // Nilai (lihat jika sudah dipublikasikan)
                Route::get('nilai', [MhsNilaiController::class, 'index'])
                    ->name('nilai.index');

                // Modul (lihat + download materi)
                Route::get('modul', [MhsModulController::class, 'index'])
                    ->name('modul.index');

                // Pendaftaran Asprak & Koor
                Route::get('daftar-asprak', [DaftarAsprakController::class, 'index'])
                    ->name('daftar-asprak.index');
                Route::post('daftar-asprak', [DaftarAsprakController::class, 'store'])
                    ->name('daftar-asprak.store');
                Route::post('daftar-koor', [DaftarAsprakController::class, 'daftarKoor'])
                    ->name('daftar-koor.store');

                // Pendaftaran Praktikan (IRS)
                Route::get('pendaftaran-praktikan', [MhsPendaftaranPraktikanController::class, 'index'])
                    ->name('pendaftaran-praktikan.index');
                Route::post('pendaftaran-praktikan', [MhsPendaftaranPraktikanController::class, 'store'])
                    ->name('pendaftaran-praktikan.store');
            });

        // ── Redirect root manprak ke dashboard sesuai role ───────────────────
        Route::get('/', function () {
            $user = auth()->user();
            $email = strtolower($user->email ?? '');

            if ($email === 'ike.pertiwi@undip.ac.id') {
                return redirect()->route('eoffice.kp.koordinator.dashboard');
            }
            if ($user->hasRole('superadmin') || $user->hasRole('admin_eoffice', 'eoffice')) {
                return redirect()->route('eoffice.manprak.admin.dashboard');
            }
            if ($user->hasRole('dosen', 'eoffice') || (str_ends_with($email, '@undip.ac.id') && !str_ends_with($email, '@students.undip.ac.id'))) {
                return redirect()->route('eoffice.manprak.dosen.dashboard');
            }
            if ($user->hasRole('koor_prak', 'eoffice')) {
                return redirect()->route('eoffice.manprak.koor.dashboard');
            }
            if ($user->hasRole('asprak', 'eoffice')) {
                return redirect()->route('eoffice.manprak.asprak.dashboard');
            }
            return redirect()->route('eoffice.manprak.mahasiswa.dashboard');
        })->name('dashboard');

    });

    // ════════════════════════════════════════════════════════════════════════
    // ADMIN E-OFFICE
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/admin')->name('eoffice.admin.')->middleware(['role:superadmin|admin_eoffice'])->group(function () {
        Route::get('/template-proposal', [EOfficeController::class, 'templateProposal'])->name('template_proposal');
        Route::post('/template-proposal', [EOfficeController::class, 'storeTemplateProposal'])->name('template_proposal.store');

        // Fitur Kelola Role dan Validasi Timeline Admin
        Route::get('/kelola-role', [EOfficeController::class, 'kelolaRole'])->name('kelola_role');
        Route::get('/validasi-timeline', [EOfficeController::class, 'validasiTimeline'])->name('validasi_timeline');
    });

    // ════════════════════════════════════════════════════════════════════════
    // KERJA PRAKTIK (KP)
    // KERJA PRAKTIK (KP)
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/kp')->name('eoffice.kp.')->group(function () {
        Route::get('/daftar', [KerjaPraktikController::class, 'create'])->name('register');
        Route::post('/daftar', [KerjaPraktikController::class, 'store'])->name('store');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Route Mahasiswa KP (baru) ──────────────────────────────────────
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/dashboard', [MahasiswaKpController::class, 'dashboard'])->name('dashboard');
            Route::get('/proposal', [MahasiswaKpController::class, 'proposal'])->name('proposal');
            Route::get('/surat', [MahasiswaKpController::class, 'surat'])->name('surat');
            Route::get('/pengumuman', [MahasiswaKpController::class, 'pengumuman'])->name('pengumuman');
            Route::get('/pengumuman/{id}/lampiran', [MahasiswaKpController::class, 'serveLampiran'])->name('pengumuman.lampiran');
            Route::get('/faq', [MahasiswaKpController::class, 'faq'])->name('faq');

            // Keperluan Perusahaan
            Route::post('/surat-pengantar/export', [MahasiswaKpController::class, 'exportSuratPengantar'])->name('surat_pengantar.export');
            Route::post('/proposal/export', [MahasiswaKpController::class, 'exportProposal'])->name('proposal.export');

            Route::get('/pendaftaran', [MahasiswaKpController::class, 'pendaftaran'])->name('pendaftaran');
            Route::post('/pendaftaran', [MahasiswaKpController::class, 'storePendaftaran'])->name('pendaftaran.store');
            Route::get('/dokumen', [MahasiswaKpController::class, 'dokumen'])->name('dokumen');
            Route::post('/dokumen', [MahasiswaKpController::class, 'storeDokumen'])->name('dokumen.store');
            Route::put('/dokumen/update-data', [MahasiswaKpController::class, 'updateDataKp'])->name('dokumen.update_data');
            Route::get('/dokumen/template/{type}', [MahasiswaKpController::class, 'downloadTemplate'])->name('dokumen.template');
            Route::post('/dokumen/export-a2', [MahasiswaKpController::class, 'exportA2'])->name('dokumen.export_a2');
            Route::post('/dokumen/generate-a2', [MahasiswaKpController::class, 'generateA2'])->name('dokumen.generate_a2');
            Route::get('/seminar', [MahasiswaKpController::class, 'seminar'])->name('seminar');
            Route::post('/seminar', [MahasiswaKpController::class, 'storeSeminar'])->name('seminar.store');
        });

        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
            Route::get('/bimbingan', [DosenController::class, 'bimbingan'])->name('bimbingan.index');
            Route::get('/bimbingan/{id}', [DosenController::class, 'show'])->name('bimbingan.show');
            Route::post('/bimbingan/{id}/approve-pra-kp', [DosenController::class, 'approvePraKp'])->name('bimbingan.approve_pra_kp');
            Route::post('/bimbingan/{id}/dokumen/{dokumenId}/approve', [DosenController::class, 'approveDokumen'])->name('bimbingan.dokumen.approve');
            Route::post('/bimbingan/{id}/dokumen/{dokumenId}/reject', [DosenController::class, 'rejectDokumen'])->name('bimbingan.dokumen.reject');
            Route::get('/bimbingan/{id}/penilaian', [DosenController::class, 'showPenilaian'])->name('bimbingan.penilaian');
            Route::post('/bimbingan/{id}/penilaian', [DosenController::class, 'storePenilaian'])->name('bimbingan.penilaian.store');
            Route::get('/validasi-berkas', [DosenController::class, 'validasiBerkas'])->name('validasi_berkas');
            Route::get('/penilaian-seminar', [DosenController::class, 'penilaianSeminar'])->name('penilaian_seminar');
            Route::post('/penilaian-seminar/{id}/approve', [DosenController::class, 'approveSeminar'])->name('penilaian_seminar.approve');
            Route::post('/penilaian-seminar/{id}/reject', [DosenController::class, 'rejectSeminar'])->name('penilaian_seminar.reject');
        });

        Route::prefix('koordinator')->name('koordinator.')->group(function () {
            Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('dashboard');
            Route::get('/pengaturan', [KoordinatorController::class, 'pengaturan'])->name('pengaturan');
            Route::post('/pengaturan', [KoordinatorController::class, 'storePengaturan'])->name('pengaturan.store');
            Route::get('/balancing', [KoordinatorController::class, 'balancingDosen'])->name('balancing');
            Route::post('/balancing', [KoordinatorController::class, 'storeBalancing'])->name('balancing.store');
            Route::get('/pengumuman', [KoordinatorController::class, 'pengumuman'])->name('pengumuman');
            Route::post('/pengumuman', [KoordinatorController::class, 'storePengumuman'])->name('pengumuman.store');
            Route::put('/pengumuman/{id}', [KoordinatorController::class, 'updatePengumuman'])->name('pengumuman.update');
            Route::delete('/pengumuman/{id}', [KoordinatorController::class, 'destroyPengumuman'])->name('pengumuman.destroy');

            Route::get('/template', [KoordinatorController::class, 'template'])->name('template');
            Route::post('/template', [KoordinatorController::class, 'storeTemplate'])->name('template.store');
            Route::put('/template/{id}', [KoordinatorController::class, 'updateTemplate'])->name('template.update');
            Route::delete('/template/{id}', [KoordinatorController::class, 'destroyTemplate'])->name('template.destroy');
            Route::get('/faq', [KoordinatorController::class, 'faq'])->name('faq');
            Route::post('/faq/dokumen', [KoordinatorController::class, 'storeDokumenPanduan'])->name('faq.dokumen.store');
            Route::delete('/faq/dokumen/{id}', [KoordinatorController::class, 'destroyDokumenPanduan'])->name('faq.dokumen.destroy');
            Route::post('/faq', [KoordinatorController::class, 'storeFaq'])->name('faq.store');
            Route::delete('/faq/{id}', [KoordinatorController::class, 'destroyFaq'])->name('faq.destroy');
            Route::get('/upload-berkas', [KoordinatorController::class, 'uploadBerkas'])->name('upload_berkas');
            Route::post('/upload-berkas/template-a2', [KoordinatorController::class, 'storeTemplateA2'])->name('upload_berkas.template_a2');
            Route::get('/validasi-berkas', [KoordinatorController::class, 'validasiBerkas'])->name('validasi_berkas');
            Route::post('/validasi-berkas/{id}/approve', [KoordinatorController::class, 'approveDokumen'])->name('validasi_berkas.approve');
            Route::post('/validasi-berkas/{id}/reject', [KoordinatorController::class, 'rejectDokumen'])->name('validasi_berkas.reject');
            Route::get('/kelola-role', [KoordinatorController::class, 'kelolaRole'])->name('kelola_role');
            Route::get('/nilai-lapangan', [KoordinatorController::class, 'nilaiLapangan'])->name('nilai_lapangan');
            Route::post('/nilai-lapangan/{id}/update', [KoordinatorController::class, 'updateNilaiLapangan'])->name('nilai_lapangan.update');

            Route::get('/data-mahasiswa', [KoordinatorController::class, 'dataMahasiswa'])->name('data_mahasiswa');
            Route::get('/data-mahasiswa/export', [KoordinatorController::class, 'exportDataMahasiswa'])->name('data_mahasiswa.export');

            Route::get('/periode', [KoordinatorController::class, 'periode'])->name('periode');
            Route::get('/periode/create', [KoordinatorController::class, 'createPeriode'])->name('periode.create');
            Route::post('/periode', [KoordinatorController::class, 'storePeriode'])->name('periode.store');
            Route::get('/periode/{id}/edit', [KoordinatorController::class, 'editPeriode'])->name('periode.edit');
            Route::put('/periode/{id}', [KoordinatorController::class, 'updatePeriode'])->name('periode.update');
            Route::delete('/periode/{id}', [KoordinatorController::class, 'destroyPeriode'])->name('periode.destroy');

            Route::get('/pendaftar', [KoordinatorController::class, 'pendaftarKp'])->name('pendaftar');
            Route::delete('/pendaftar/{id}', [KoordinatorController::class, 'resetPendaftar'])->name('pendaftar.destroy');
        });
    });

    // ════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PEMINJAMAN
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/peminjaman')->name('eoffice.peminjaman.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});