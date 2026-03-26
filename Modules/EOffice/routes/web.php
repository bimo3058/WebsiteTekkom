<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\EOfficeController;

Route::middleware('auth', 'module.active:eoffice')->group(function () {
    Route::get('/eoffice/dashboard', [EOfficeController::class, 'dashboard'])
        ->name('eoffice.dashboard');
});
