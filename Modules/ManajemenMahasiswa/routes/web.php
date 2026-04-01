<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KemahasiswaanController;

Route::middleware(['auth', 'module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {

        // Dashboard Utama Modul
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'index'])
            ->name('mahasiswa.dashboard');

        // -------------------------------------------------------------------------
        // Pengumuman
        // -------------------------------------------------------------------------
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/', [PengumumanController::class , 'index'])->name('index');
            Route::get('/create', [PengumumanController::class , 'create'])->name('create');
            Route::post('/', [PengumumanController::class , 'store'])->name('store');
            Route::get('/{pengumuman}', [PengumumanController::class , 'show'])->name('show');
            Route::get('/{pengumuman}/edit', [PengumumanController::class , 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class , 'update'])->name('update');
            Route::delete('/{pengumuman}', [PengumumanController::class , 'destroy'])->name('destroy');

            // Custom actions
            Route::patch('/{pengumuman}/publish', [PengumumanController::class , 'publish'])->name('publish');
            Route::delete('/{pengumuman}/lampiran/{lampiran}', [PengumumanController::class , 'destroyLampiran'])->name('lampiran.destroy');
        });
    });