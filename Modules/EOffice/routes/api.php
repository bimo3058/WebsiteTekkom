<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DosenController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\PraktikumController;

// ─── Routes EOffice yang sudah ada ────────────────────────────────────────────
Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::apiResource('eoffices', EOfficeController::class)->names('eoffice');
});

// ─── Manajemen Praktikum ───────────────────────────────────────────────────────
// Auth pakai session/sanctum global superapp — TIDAK ada AuthController sendiri.
// Middleware 'auth' = sudah login via SSO/Microsoft global.
// Middleware 'role:...' = RoleMiddleware global (app/Http/Middleware/RoleMiddleware.php).
Route::middleware(['auth:sanctum', 'module.active:eoffice'])
    ->prefix('v1/eoffice/manprak')
    ->name('api.eoffice.manprak.')
    ->group(function () {

        // ── Admin routes (role: superadmin atau admin_eoffice) ────────────────
        Route::middleware(['role:superadmin,admin_eoffice'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {

                // Daftar dosen
                Route::get('dosen', [DosenController::class, 'index'])
                    ->name('dosen.index');

                // CRUD Praktikum
                Route::apiResource('praktikum', PraktikumController::class)
                    ->names('praktikum');

                // Assign koordinator ke praktikum
                Route::put('praktikum/{id}/assign-koor', [PraktikumController::class, 'assignKoor'])
                    ->name('praktikum.assign-koor');
            });

        // ── Dosen routes (role: dosen) ────────────────────────────────────────
        Route::middleware(['role:dosen'])
            ->prefix('dosen')
            ->name('dosen.')
            ->group(function () {
                // Tambahkan routes dosen di sini (Step berikutnya)
            });

        // ── Koor routes (role: koor_prak) ─────────────────────────────────────
        Route::middleware(['role:koor_prak'])
            ->prefix('koor')
            ->name('koor.')
            ->group(function () {
                // Tambahkan routes koor di sini (Step berikutnya)
            });

        // ── Asprak routes (role: asprak) ───────────────────────────────────────
        Route::middleware(['role:asprak'])
            ->prefix('asprak')
            ->name('asprak.')
            ->group(function () {
                // Tambahkan routes asprak di sini (Step berikutnya)
            });

        // ── Mahasiswa routes ───────────────────────────────────────────────────
        Route::middleware(['role:mahasiswa'])
            ->prefix('mahasiswa')
            ->name('mahasiswa.')
            ->group(function () {
                // Tambahkan routes mahasiswa di sini (Step berikutnya)
            });
    });
