<?php

use Illuminate\Support\Facades\Route;
use Modules\EOffice\Http\Controllers\EOfficeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('eoffices', EOfficeController::class)->names('eoffice');
});
