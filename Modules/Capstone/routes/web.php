<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\CapstoneController;

Route::middleware('auth', 'module.active:capstone')->group(function () {
    Route::get('/capstone/dashboard', [CapstoneController::class, 'dashboard'])
        ->name('capstone.dashboard');
});
