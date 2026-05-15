<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\DashboardController;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\KerjaPraktikController;
use Modules\EOffice\Http\Controllers\DosenController;
use Modules\EOffice\Http\Controllers\KoordinatorController;

// ManajemenPraktikum Controllers
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DosenController as AdminDosenPrakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DashboardController as AdminManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PendaftaranAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PendaftaranKoorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\KelolaPendaftaranController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\DashboardController as DosenManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\NilaiController as DosenNilaiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen\ModulController as DosenModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController as KoorManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\BagiModulController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\PengumumanController as KoorPengumumanController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\DashboardController as AsprakManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\AbsensiController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\TugasController as AsprakTugasController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Asprak\MateriController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DashboardController as MhsManprakDashboard;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\TugasController as MhsTugasController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DaftarAsprakController;

Route::middleware(['auth', 'module.active:eoffice'])->group(function () {

    // ── Dashboard utama ──────────────────────────────────────────────────────
    Route::get('/eoffice/dashboard', [DashboardController::class, 'index'])
        ->name('eoffice.dashboard');

    // ── Dokumen / EOffice umum ───────────────────────────────────────────────
    Route::resource('eoffice/documents', EOfficeController::class)->except(['index']);

    // ════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PRAKTIKUM — semua role diakses di bawah /eoffice/manprak
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/manprak')->name('eoffice.manprak.')->group(function () {

        // ── ADMIN ────────────────────────────────────────────────────────────
        Route::middleware(['role:superadmin|admin_eoffice'])
            ->prefix('admin')->name('admin.')
            ->group(function () {

                // Dashboard Admin Manprak
                Route::get('/dashboard', [AdminManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // CRUD Praktikum
                Route::resource('praktikum', PraktikumController::class)
                    ->names('praktikum');
                Route::put('praktikum/{id}/assign-koor', [PraktikumController::class, 'assignKoor'])
                    ->name('praktikum.assign-koor');
                Route::post('praktikum/generate-kode', [PraktikumController::class, 'generateKode'])
                    ->name('praktikum.generate-kode');

                // Dosen
                Route::get('dosen', [AdminDosenPrakController::class, 'index'])
                    ->name('dosen.index');

                // Pendaftaran Asprak
                Route::get('pendaftaran-asprak', [PendaftaranAsprakController::class, 'index'])
                    ->name('pendaftaran-asprak.index');
                Route::post('pendaftaran-asprak/{id}/approve', [PendaftaranAsprakController::class, 'approve'])
                    ->name('pendaftaran-asprak.approve');
                Route::post('pendaftaran-asprak/{id}/reject', [PendaftaranAsprakController::class, 'reject'])
                    ->name('pendaftaran-asprak.reject');

                // Pendaftaran Koordinator
                Route::get('pendaftaran-koor', [PendaftaranKoorController::class, 'index'])
                    ->name('pendaftaran-koor.index');
                Route::post('pendaftaran-koor/{id}/approve', [PendaftaranKoorController::class, 'approve'])
                    ->name('pendaftaran-koor.approve');
                Route::post('pendaftaran-koor/{id}/reject', [PendaftaranKoorController::class, 'reject'])
                    ->name('pendaftaran-koor.reject');

                // Bagi Asprak per Modul (Admin juga bisa)
                Route::get('bagi-asprak', [KelolaPendaftaranController::class, 'index'])
                    ->name('bagi-asprak.index');
                Route::post('bagi-asprak', [KelolaPendaftaranController::class, 'store'])
                    ->name('bagi-asprak.store');
            });

        // ── DOSEN ────────────────────────────────────────────────────────────
        Route::middleware(['role:dosen'])
            ->prefix('dosen')->name('dosen.')
            ->group(function () {

                Route::get('/dashboard', [DosenManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Tunjuk Koordinator dari Mahasiswa via NIM
                Route::post('tunjuk-koor', [DosenManprakDashboard::class, 'tunjukKoor'])
                    ->name('tunjuk-koor');

                // Izinkan publikasi materi/tugas
                Route::post('praktikum/{id}/publish', [DosenManprakDashboard::class, 'publish'])
                    ->name('praktikum.publish');

                // Nilai (dosen approve)
                Route::get('nilai/{praktikumId}', [DosenNilaiController::class, 'index'])
                    ->name('nilai.index');
                Route::post('nilai/{praktikumId}/approve', [DosenNilaiController::class, 'approve'])
                    ->name('nilai.approve');

                // Modul (dosen lihat)
                Route::get('modul/{praktikumId}', [DosenModulController::class, 'index'])
                    ->name('modul.index');
            });

        // ── KOORDINATOR ──────────────────────────────────────────────────────
        Route::middleware(['role:koor_prak|admin_eoffice|superadmin'])
            ->prefix('koor')->name('koor.')
            ->group(function () {

                Route::get('/dashboard', [KoorManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Bagi Modul ke Asprak
                Route::get('bagi-modul', [BagiModulController::class, 'index'])
                    ->name('bagi-modul.index');
                Route::post('bagi-modul', [BagiModulController::class, 'store'])
                    ->name('bagi-modul.store');
                Route::delete('bagi-modul/{id}', [BagiModulController::class, 'destroy'])
                    ->name('bagi-modul.destroy');

                // Pengumuman
                Route::get('pengumuman', [KoorPengumumanController::class, 'index'])
                    ->name('pengumuman.index');
                Route::post('pengumuman', [KoorPengumumanController::class, 'store'])
                    ->name('pengumuman.store');
                Route::delete('pengumuman/{id}', [KoorPengumumanController::class, 'destroy'])
                    ->name('pengumuman.destroy');

                // Update daftar praktikan
                Route::get('praktikan', [KoorManprakDashboard::class, 'praktikan'])
                    ->name('praktikan.index');
                Route::post('praktikan/import', [KoorManprakDashboard::class, 'importPraktikan'])
                    ->name('praktikan.import');
            });

        // ── ASISTEN PRAKTIKUM ────────────────────────────────────────────────
        Route::middleware(['role:asprak|koor_prak|admin_eoffice|superadmin'])
            ->prefix('asprak')->name('asprak.')
            ->group(function () {

                Route::get('/dashboard', [AsprakManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Absensi
                Route::get('absensi', [AbsensiController::class, 'index'])
                    ->name('absensi.index');
                Route::get('absensi/{modulId}', [AbsensiController::class, 'show'])
                    ->name('absensi.show');
                Route::post('absensi/{modulId}', [AbsensiController::class, 'store'])
                    ->name('absensi.store');

                // Tugas & Penilaian
                Route::get('tugas', [AsprakTugasController::class, 'index'])
                    ->name('tugas.index');
                Route::get('tugas/create', [AsprakTugasController::class, 'create'])
                    ->name('tugas.create');
                Route::post('tugas', [AsprakTugasController::class, 'store'])
                    ->name('tugas.store');
                Route::get('tugas/{id}/pengumpulan', [AsprakTugasController::class, 'pengumpulan'])
                    ->name('tugas.pengumpulan');
                Route::post('tugas/{id}/nilai', [AsprakTugasController::class, 'beriNilai'])
                    ->name('tugas.nilai');
                Route::post('tugas/{id}/revisi', [AsprakTugasController::class, 'beriRevisi'])
                    ->name('tugas.revisi');

                // Materi Modul
                Route::get('materi', [MateriController::class, 'index'])
                    ->name('materi.index');
                Route::post('materi', [MateriController::class, 'store'])
                    ->name('materi.store');
                Route::delete('materi/{id}', [MateriController::class, 'destroy'])
                    ->name('materi.destroy');

                // Pengumuman (asprak)
                Route::post('pengumuman', [AsprakManprakDashboard::class, 'buatPengumuman'])
                    ->name('pengumuman.store');
            });

        // ── MAHASISWA ────────────────────────────────────────────────────────
        Route::middleware(['role:mahasiswa|asprak|koor_prak|admin_eoffice|superadmin'])
            ->prefix('mahasiswa')->name('mahasiswa.')
            ->group(function () {

                Route::get('/dashboard', [MhsManprakDashboard::class, 'index'])
                    ->name('dashboard');

                // Input kode praktikum
                Route::post('masuk', [MhsManprakDashboard::class, 'masukkanKode'])
                    ->name('masuk');

                // Tugas (mahasiswa)
                Route::get('tugas', [MhsTugasController::class, 'index'])
                    ->name('tugas.index');
                Route::post('tugas/{id}/kumpul', [MhsTugasController::class, 'kumpul'])
                    ->name('tugas.kumpul');

                // Daftar Asprak
                Route::get('daftar-asprak', [DaftarAsprakController::class, 'index'])
                    ->name('daftar-asprak.index');
                Route::post('daftar-asprak', [DaftarAsprakController::class, 'store'])
                    ->name('daftar-asprak.store');

                // Daftar Koor
                Route::post('daftar-koor', [DaftarAsprakController::class, 'daftarKoor'])
                    ->name('daftar-koor.store');
            });

        // ── Redirect root manprak ke dashboard sesuai role ───────────────────
        Route::get('/', function () {
            $user = auth()->user();
            if ($user->hasRole('superadmin') || $user->hasRole('admin_eoffice', 'eoffice')) {
                return redirect()->route('eoffice.manprak.admin.dashboard');
            }
            if ($user->hasRole('dosen', 'eoffice')) {
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
    // KERJA PRAKTIK (KP) — tidak berubah
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/kp')->name('eoffice.kp.')->group(function () {
        Route::get('/daftar', [KerjaPraktikController::class, 'create'])->name('register');
        Route::post('/daftar', [KerjaPraktikController::class, 'store'])->name('store');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
            Route::get('/bimbingan/{id}', [DosenController::class, 'show'])->name('bimbingan.show');
            Route::post('/bimbingan/{id}/approve-pra-kp', [DosenController::class, 'approvePraKp'])->name('bimbingan.approve_pra_kp');
            Route::post('/bimbingan/{id}/dokumen/{dokumenId}/approve', [DosenController::class, 'approveDokumen'])->name('bimbingan.dokumen.approve');
            Route::post('/bimbingan/{id}/dokumen/{dokumenId}/reject', [DosenController::class, 'rejectDokumen'])->name('bimbingan.dokumen.reject');
            Route::get('/bimbingan/{id}/penilaian', [DosenController::class, 'showPenilaian'])->name('bimbingan.penilaian');
            Route::post('/bimbingan/{id}/penilaian', [DosenController::class, 'storePenilaian'])->name('bimbingan.penilaian.store');
            Route::get('/validasi-berkas', [DosenController::class, 'validasiBerkas'])->name('validasi_berkas');
        });

        Route::prefix('koordinator')->name('koordinator.')->group(function () {
            Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('dashboard');
            Route::get('/balancing', [KoordinatorController::class, 'balancingDosen'])->name('balancing');
            Route::post('/balancing', [KoordinatorController::class, 'storeBalancing'])->name('balancing.store');
            Route::get('/pengumuman', [KoordinatorController::class, 'pengumuman'])->name('pengumuman');
            Route::post('/pengumuman', [KoordinatorController::class, 'storePengumuman'])->name('pengumuman.store');
            Route::delete('/pengumuman/{id}', [KoordinatorController::class, 'destroyPengumuman'])->name('pengumuman.destroy');
            Route::get('/faq', [KoordinatorController::class, 'faq'])->name('faq');
            Route::post('/faq/dokumen', [KoordinatorController::class, 'storeDokumenPanduan'])->name('faq.dokumen.store');
            Route::delete('/faq/dokumen/{id}', [KoordinatorController::class, 'destroyDokumenPanduan'])->name('faq.dokumen.destroy');
            Route::post('/faq', [KoordinatorController::class, 'storeFaq'])->name('faq.store');
            Route::delete('/faq/{id}', [KoordinatorController::class, 'destroyFaq'])->name('faq.destroy');
            Route::get('/validasi-berkas', [KoordinatorController::class, 'validasiBerkas'])->name('validasi_berkas');
            Route::get('/data-mahasiswa', [KoordinatorController::class, 'dataMahasiswa'])->name('data_mahasiswa');
        });
    });

    // ════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PEMINJAMAN
    // ════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/peminjaman')->name('eoffice.peminjaman.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});
