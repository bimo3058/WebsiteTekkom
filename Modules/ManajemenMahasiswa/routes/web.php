<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\GamificationController;
use Modules\ManajemenMahasiswa\Http\Controllers\CvController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ForumController;

Route::middleware(['auth'])->prefix('manajemen-mahasiswa')->name('manajemenmahasiswa.')->group(function () {

    // Dashboard

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('mahasiswa.dashboard');

    // Pengumuman

    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/', [PengumumanController::class, 'index'])->name('index');
        Route::get('/create', [PengumumanController::class, 'create'])->name('create');
        Route::post('/', [PengumumanController::class, 'store'])->name('store');
        Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
        Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
        Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
        Route::delete('/{pengumuman}', [PengumumanController::class, 'remove'])->name('remove');

        // Custom actions

        Route::patch('/{pengumuman}/publish', [PengumumanController::class, 'publish'])->name('publish');
        Route::delete('/{pengumuman}/lampiran/{lampiran}', [PengumumanController::class, 'removeLampiran'])->name('lampiran.remove');
    });


    // Forum Mahasiswa

    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::post('/', [ForumController::class, 'storeForum'])->name('store');
        Route::put('/{forum}', [ForumController::class, 'updateForum'])->name('update');
        Route::delete('/{forum}', [ForumController::class, 'removeForum'])->name('remove');

        Route::prefix('{forum}/diskusi')->name('discussion.')->group(function () {
            Route::get('/', [ForumController::class, 'showForum'])->name('index');
            Route::post('/', [ForumController::class, 'storeDiscussion'])->name('store');
            Route::get('/{discussion}', [ForumController::class, 'showDiscussion'])->name('show');
            Route::put('/{discussion}', [ForumController::class, 'updateDiscussion'])->name('update');
            Route::delete('/{discussion}', [ForumController::class, 'removeDiscussion'])->name('remove');
            Route::patch('/{discussion}/pin', [ForumController::class, 'pinDiscussion'])->name('pin');
            Route::patch('/{discussion}/close', [ForumController::class, 'closeDiscussion'])->name('close');

            Route::post('/{discussion}/komentar', [ForumController::class, 'storeComment'])->name('comment.store');
        });

        Route::put('/komentar/{comment}', [ForumController::class, 'updateComment'])->name('comment.update');
        Route::delete('/komentar/{comment}', [ForumController::class, 'removeComment'])->name('comment.remove');
    });

    // Gamifikasi & Badges

    Route::prefix('gamifikasi')->name('gamification.')->group(function () {
        Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/profil-saya', [GamificationController::class, 'myProfile'])->name('profile');
    });

});
