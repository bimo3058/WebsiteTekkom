<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\ManajemenMahasiswaController;

Route::middleware('auth')->group(function () {
    Route::get('/mahasiswa/dashboard', [ManajemenMahasiswaController::class, 'dashboard'])
        ->name('mahasiswa.dashboard');
});
