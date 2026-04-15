<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Models\KemahasiswaanController;

Route::middleware(['auth', 'module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {

        // Dashboard Utama Modul — semua role boleh akses, renderDashboard() menentukan view sesuai role
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('mahasiswa.dashboard');

        // Switch tampilan dashboard antar-role (untuk user multi-role)
        Route::post('/dashboard/switch-mode', [DashboardController::class, 'switchMode'])
            ->name('dashboard.switch-mode');

        // ── Pengurus Himpunan ─────────────────────────────────────────────
        Route::middleware('role:pengurus_himpunan,admin_kemahasiswaan,superadmin')
            ->prefix('pengurus')
            ->name('pengurus.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.pengurus');
            })->name('dashboard');
        });

        // ── Alumni ────────────────────────────────────────────────────────
        Route::middleware('role:alumni,admin_kemahasiswaan,superadmin')
            ->prefix('alumni')
            ->name('alumni.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.alumni');
            })->name('dashboard');
        });

        // ── Pengumuman ────────────────────────────────────────────────────
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
            Route::get('/create', [PengumumanController::class, 'create'])->name('create');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
            Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
            Route::delete('/{pengumuman}', [PengumumanController::class, 'remove'])->name('remove');

            // View only — semua role boleh
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');

            // Create/Edit/Delete — hanya pengurus + admin
            Route::middleware('role:pengurus_himpunan,admin_kemahasiswaan,superadmin')->group(function () {
                Route::get('/create', [PengumumanController::class, 'create'])->name('create');
                Route::post('/', [PengumumanController::class, 'store'])->name('store');
                Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
                Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
                Route::delete('/{pengumuman}', [PengumumanController::class, 'remove'])->name('remove');
                Route::patch('/{pengumuman}/publish', [PengumumanController::class, 'publish'])->name('publish');
                Route::delete('/{pengumuman}/lampiran/{lampiran}', [PengumumanController::class, 'removeLampiran'])->name('lampiran.remove');
            });
        });
    });