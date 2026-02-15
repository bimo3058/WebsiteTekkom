<?php

use Illuminate\Support\Facades\Route;
use Modules\BankSoal\Http\Controllers\BankSoalController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('banksoal', BankSoalController::class)->names('banksoal');
});
