<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\DashboardController; // Import DashboardController
use Modules\Capstone\Http\Controllers\CapstoneController;

Route::middleware(['auth', 'module.active:capstone'])->group(function () {
    // Arahkan ke DashboardController@index, bukan CapstoneController@dashboard
    Route::get('/capstone/dashboard', [DashboardController::class, 'index'])
        ->name('capstone.dashboard');

    // Contoh rute lain tetap menggunakan CapstoneController untuk CRUD
    Route::get('/capstone/create', [CapstoneController::class, 'create'])->name('capstone.create');
});