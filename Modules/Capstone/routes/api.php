<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\CapstoneController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('capstones', CapstoneController::class)->names('capstone');
});
