<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\CapstoneLogoutController;
use Modules\Capstone\Http\Controllers\LaunchController;

Route::middleware(['auth', 'module.active:capstone'])->group(function () {
    // Klik card "Capstone & TA" â†’ generate OTT â†’ redirect ke FE Next.js
    Route::get('/capstone/launch', [LaunchController::class, 'launch'])
        ->name('capstone.dashboard');

    Route::post('/capstone/logout', CapstoneLogoutController::class)
        ->name('capstone.logout');
});
