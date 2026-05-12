<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\KerjaPraktikController;
use Modules\EOffice\Http\Controllers\DosenController;
use Modules\EOffice\Http\Controllers\KoordinatorController;

Route::middleware(['web'])->prefix('kp')->name('kp.')->group(function () {
    // Route Mahasiswa
    Route::get('/daftar', [KerjaPraktikController::class, 'create'])->name('register');
    Route::post('/daftar', [KerjaPraktikController::class, 'store'])->name('store');
    
    // Route Dosen Pembimbing
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/bimbingan/{id}', [DosenController::class, 'show'])->name('bimbingan.show');
        Route::post('/bimbingan/{id}/approve-pra-kp', [DosenController::class, 'approvePraKp'])->name('bimbingan.approve_pra_kp');
        
        // Route untuk Approval Berkas Laporan/Makalah
        Route::post('/bimbingan/{id}/dokumen/{dokumenId}/approve', [DosenController::class, 'approveDokumen'])->name('bimbingan.dokumen.approve');
        Route::post('/bimbingan/{id}/dokumen/{dokumenId}/reject', [DosenController::class, 'rejectDokumen'])->name('bimbingan.dokumen.reject');

        // Route untuk Form Penilaian KP
        Route::get('/bimbingan/{id}/penilaian', [DosenController::class, 'showPenilaian'])->name('bimbingan.penilaian');
        Route::post('/bimbingan/{id}/penilaian', [DosenController::class, 'storePenilaian'])->name('bimbingan.penilaian.store');

        // Route untuk halaman Validasi & Approval Berkas (semua mahasiswa bimbingan)
        Route::get('/validasi-berkas', [DosenController::class, 'validasiBerkas'])->name('validasi_berkas');
    });

    // Route Koordinator KP
    Route::prefix('koordinator')->name('koordinator.')->group(function () {
        Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/balancing', [KoordinatorController::class, 'balancingDosen'])->name('balancing');
        Route::post('/balancing', [KoordinatorController::class, 'storeBalancing'])->name('balancing.store');
        
        Route::get('/pengumuman', [KoordinatorController::class, 'pengumuman'])->name('pengumuman');
        Route::post('/pengumuman', [KoordinatorController::class, 'storePengumuman'])->name('pengumuman.store');
        Route::delete('/pengumuman/{id}', [KoordinatorController::class, 'destroyPengumuman'])->name('pengumuman.destroy');
        
        Route::get('/validasi-berkas', [KoordinatorController::class, 'validasiBerkas'])->name('validasi_berkas');
    });
});
