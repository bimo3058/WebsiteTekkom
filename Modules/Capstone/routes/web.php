<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\CapstoneController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('capstones', CapstoneController::class)->names('capstone');
});
