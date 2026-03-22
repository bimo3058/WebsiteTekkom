<?php

use Illuminate\Support\Facades\Route;
use app\Http\Middleware\RoleMiddleware;
use Modules\BankSoal\Http\Controllers\DashboardController;
use Modules\BankSoal\Http\Controllers\RPS\Dosen\RpsController as DosenRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\RpsController as GpmRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Admin\RpsController as AdminRpsController;

Route::middleware(['auth'])->prefix('bank-soal')->group(function () {
    ##Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('banksoal.dashboard');
    
    ##RPS Routes
    Route::prefix('rps')->name('banksoal.rps.')->group(function () {
        // RPS - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', [DosenRpsController::class, 'index'])->name('index');
            Route::post('/submit', [DosenRpsController::class, 'store'])->name('store');
            Route::get('/cpl/{mkId}', [DosenRpsController::class, 'getCplByMk'])->name('cpl');
            Route::get('/cpmk/{mkId}', [DosenRpsController::class, 'getCpmkByCpl'])->name('cpmk');
            Route::get('/dosen/{mkId}', [DosenRpsController::class, 'getDosenByMk'])->name('dosen');
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
    
    ##Bank Soal Routes
    Route::prefix('bank-soal')->name('banksoal.banksoal.')->group(function () {
        // Bank Soal - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.index');
            })->name('index');
        });
        
        // Bank Soal - GPM
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.index');
            })->name('index');
        });
        
        // Bank Soal - Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.bank-soal.index');
            })->name('index');
        });
        
    });

    ##Arsip Routes
    Route::prefix('arsip')->name('banksoal.arsip.')->group(function () {
        // Arsip - Dosen
        Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.index');
            })->name('index');
        });
        
        // Arsip - GPM
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.index');
            })->name('index');
        });
        
        // Arsip - Admin
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/', function () {
                return view('banksoal::pages.arsip.index');
            })->name('index');
        });
 
    });
});
