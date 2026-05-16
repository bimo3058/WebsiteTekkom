<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\DashboardController;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\KerjaPraktikController;
use Modules\EOffice\Http\Controllers\DosenController;
use Modules\EOffice\Http\Controllers\KoordinatorController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DosenController as AdminDosenPrakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumController;

Route::middleware(['auth', 'module.active:eoffice'])->group(function () {

    // ── Dashboard utama ────────────────────────────────────────────────────────
    Route::get('/eoffice/dashboard', [DashboardController::class, 'index'])
        ->name('eoffice.dashboard');

    // ── Dokumen / EOffice umum ─────────────────────────────────────────────────
    Route::resource('eoffice/documents', EOfficeController::class)->except(['index']);

    // ══════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PRAKTIKUM
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/manprak')->name('eoffice.manprak.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Admin Manprak
        Route::middleware(['role:superadmin|admin_eoffice'])
            ->prefix('admin')->name('admin.')
            ->group(function () {
                Route::get('dosen', [AdminDosenPrakController::class, 'index'])->name('dosen.index');
                Route::resource('praktikum', PraktikumController::class)->names('praktikum');
                Route::put('praktikum/{id}/assign-koor', [PraktikumController::class, 'assignKoor'])->name('praktikum.assign-koor');
            });

        // Placeholder Dosen, Koor, Asprak Manprak
        Route::middleware(['role:dosen'])->prefix('dosen')->name('dosen.')->group(function () { /* TODO */});
        Route::middleware(['role:koor_prak|admin_eoffice|superadmin'])->prefix('koor')->name('koor.')->group(function () { /* TODO */});
        Route::middleware(['role:asprak|koor_prak|admin_eoffice|superadmin'])->prefix('asprak')->name('asprak.')->group(function () { /* TODO */});
    });

    // ══════════════════════════════════════════════════════════════════════════
    // KERJA PRAKTIK (KP)
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/kp')->name('eoffice.kp.')->group(function () {

        // Route Mahasiswa
        Route::get('/daftar', [KerjaPraktikController::class, 'create'])->name('register');
        Route::post('/daftar', [KerjaPraktikController::class, 'store'])->name('store');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Route Dosen Pembimbing KP
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
        });

        // Route Koordinator KP
        Route::prefix('koordinator')->name('koordinator.')->group(function () {
            Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('dashboard');
            Route::get('/balancing', [KoordinatorController::class, 'balancingDosen'])->name('balancing');
            Route::post('/balancing', [KoordinatorController::class, 'storeBalancing'])->name('balancing.store');
            Route::get('/pengumuman', [KoordinatorController::class, 'pengumuman'])->name('pengumuman');
            Route::post('/pengumuman', [KoordinatorController::class, 'storePengumuman'])->name('pengumuman.store');
            Route::put('/pengumuman/{id}', [KoordinatorController::class, 'updatePengumuman'])->name('pengumuman.update');
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

    // ══════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PEMINJAMAN
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/peminjaman')->name('eoffice.peminjaman.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});