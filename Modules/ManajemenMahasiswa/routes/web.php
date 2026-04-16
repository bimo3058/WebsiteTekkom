<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KemahasiswaanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ForumController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengaduanController;

Route::middleware(['auth', 'module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {

        // Dashboard Utama Modul — semua role boleh akses, renderDashboard() menentukan view sesuai role
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

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

        // ── Layanan Pengaduan ─────────────────────────────────────────────
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::get('/', [PengaduanController::class, 'index'])->name('index');
            Route::get('/create', [PengaduanController::class, 'create'])->name('create');
            Route::post('/', [PengaduanController::class, 'store'])->name('store');
            Route::get('/{pengaduan}', [PengaduanController::class, 'show'])->name('show');

            // Jawab pengaduan — admin
            Route::post('/{pengaduan}/reply', [PengaduanController::class, 'reply'])
                ->name('reply')
                ->middleware('role:superadmin,admin,admin_kemahasiswaan,gpm');
        });

        // ── Forum Diskusi ──────────────────────────────────────────────────
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/create', [ForumController::class, 'create'])->name('create');
            Route::post('/', [ForumController::class, 'store'])->name('store');
            Route::get('/{id}', [ForumController::class, 'show'])->name('show');
            Route::post('/{id}/vote', [ForumController::class, 'vote'])->name('vote');
            Route::delete('/{id}', [ForumController::class, 'destroy'])->name('destroy');

            // Comments
            Route::post('/{threadId}/comments', [ForumController::class, 'storeComment'])->name('comments.store');
            Route::post('/comments/{commentId}/vote', [ForumController::class, 'voteComment'])->name('comments.vote');
            Route::delete('/comments/{commentId}', [ForumController::class, 'destroyComment'])->name('comments.destroy');
        });

        // ── Gamification API ──────────────────────────────────────────────
        Route::prefix('gamification')->name('gamification.')->group(function () {
            Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
            Route::get('/stats', [GamificationController::class, 'userStats'])->name('stats');
        });
    });