<?php

use Illuminate\Support\Facades\Route;
use Modules\BankSoal\Http\Controllers\DashboardController;
use Modules\BankSoal\Http\Controllers\RPS\Dosen\RpsController as DosenRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\RpsController as GpmRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Admin\RpsController as AdminRpsController;

Route::middleware(['auth'])->prefix('bank-soal')->group(function () {
    #Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('banksoal.dashboard');
    
    #RPS Routes
    Route::prefix('rps')->name('banksoal.rps.')->group(function () {
        // RPS - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', [DosenRpsController::class, 'index'])->name('index');
            Route::post('/submit', [DosenRpsController::class, 'store'])->name('store');
            Route::get('/cpl/{mkId?}', [DosenRpsController::class, 'getCplByMk'])->name('cpl');
            Route::get('/cpmk', [DosenRpsController::class, 'getCpmkByCpl'])->name('cpmk');
            Route::get('/dosen', [DosenRpsController::class, 'getDosenByMk'])->name('dosen');
            Route::get('/preview/{rpsId}', [DosenRpsController::class, 'previewDokumen'])->name('preview');
        });
        
        // RPS - GPM
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/', [GpmRpsController::class, 'index'])->name('index');
        });
        
        // RPS - Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminRpsController::class, 'index'])->name('index');
        });
        
    });
    
    #Bank Soal Routes
    Route::prefix('bank-soal')->name('banksoal.banksoal.')->group(function () {
        // Bank Soal - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.Dosen.index');
            })->name('index');
        });
        
        // Bank Soal - GPM
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.Gpm.index');
            })->name('index');
        });
        
        // Bank Soal - Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.Admin.index');
            })->name('index');
        });
        
    });

    #Arsip Routes
    Route::prefix('arsip')->name('banksoal.arsip.')->group(function () {
        // Arsip - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.Dosen.index');
            })->name('index');
        });
        
        // Arsip - GPM
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.Gpm.index');
            })->name('index');
        });
        
        // Arsip - Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.Admin.index');
            })->name('index');
        });
 
    });
});
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('banksoal', BankSoalController::class)->names('banksoal');
// });

