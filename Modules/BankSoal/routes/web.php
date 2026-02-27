<?php

use Illuminate\Support\Facades\Route;
use Modules\BankSoal\Http\Controllers\BankSoalController;
use Modules\BankSoal\Http\Controllers\DashboardController;

Route::middleware(['auth'])->prefix('bank-soal')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('banksoal.dashboard');
});
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('banksoal', BankSoalController::class)->names('banksoal');
// });

