<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\EOfficeController;
use Modules\EOffice\Http\Controllers\DashboardController;

Route::middleware(['auth', 'module.active:eoffice'])->group(function () {
    // Arahkan ke DashboardController@index
    Route::get('/eoffice/dashboard', [DashboardController::class, 'index'])
        ->name('eoffice.dashboard');
        
    // Rute lainnya tetap ke EOfficeController
    Route::resource('eoffice/documents', EOfficeController::class)->except(['index']);
});