<?php

use Illuminate\Support\Facades\Route;
use Modules\BankSoal\Http\Controllers\BankSoalController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('banksoals', BankSoalController::class)->names('banksoal');
});
