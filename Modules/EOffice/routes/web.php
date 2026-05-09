<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\DashboardController;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DosenController;
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

        // Dashboard manprak (sementara redirect ke dashboard utama)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Admin ──────────────────────────────────────────────────────────────
        Route::middleware(['role:superadmin,admin_eoffice'])
            ->prefix('admin')->name('admin.')
            ->group(function () {

                // Daftar dosen
                Route::get('dosen', [DosenController::class, 'index'])
                    ->name('dosen.index');

                // CRUD Praktikum
                Route::resource('praktikum', PraktikumController::class)
                    ->names('praktikum');

                // Assign koordinator
                Route::put('praktikum/{id}/assign-koor', [PraktikumController::class, 'assignKoor'])
                    ->name('praktikum.assign-koor');
            });

        // ── Dosen ──────────────────────────────────────────────────────────────
        Route::middleware(['role:dosen'])
            ->prefix('dosen')->name('dosen.')
            ->group(function () {
                // TODO: routes dosen manprak
            });

        // ── Koor ───────────────────────────────────────────────────────────────
        Route::middleware(['role:koor_prak,admin_eoffice,superadmin'])
            ->prefix('koor')->name('koor.')
            ->group(function () {
                // TODO: routes koor
            });

        // ── Asprak ─────────────────────────────────────────────────────────────
        Route::middleware(['role:asprak,koor_prak,admin_eoffice,superadmin'])
            ->prefix('asprak')->name('asprak.')
            ->group(function () {
                // TODO: routes asprak
            });
    });

    // ══════════════════════════════════════════════════════════════════════════
    // KERJA PRAKTIK (KP) — stub, diisi saat modul KP selesai
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/kp')->name('eoffice.kp.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PEMINJAMAN — stub, diisi saat modul Peminjaman selesai
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('eoffice/peminjaman')->name('eoffice.peminjaman.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });
});