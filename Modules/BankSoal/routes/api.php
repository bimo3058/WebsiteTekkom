<?php

use Illuminate\Support\Facades\Route;
use Modules\BankSoal\Http\Controllers\BS\BankSoalController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::apiResource('banksoal', BankSoalController::class)->names('banksoal');
});
