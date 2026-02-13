<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\EOfficeController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('eoffices', EOfficeController::class)->names('eoffice');
});
