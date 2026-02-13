<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\ManajemenMahasiswaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('manajemenmahasiswas', ManajemenMahasiswaController::class)->names('manajemenmahasiswa');
});
