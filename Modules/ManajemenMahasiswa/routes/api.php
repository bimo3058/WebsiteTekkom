<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\ManajemenMahasiswaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manajemenmahasiswas', ManajemenMahasiswaController::class)->names('manajemenmahasiswa');
});
