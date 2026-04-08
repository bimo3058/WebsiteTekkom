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

    # Periode Ujian Routes
    Route::prefix('admin/periode')->name('banksoal.periode.')->group(function () {
        Route::middleware('role:superadmin|admin')->group(function () {
            Route::get('/setup', [\Modules\BankSoal\Http\Controllers\PeriodeController::class, 'index'])->name('setup');
            Route::post('/setup', [\Modules\BankSoal\Http\Controllers\PeriodeController::class, 'store'])->name('store');
            Route::put('/setup/{id}', [\Modules\BankSoal\Http\Controllers\PeriodeController::class, 'update'])->name('update');
            Route::delete('/setup/{id}', [\Modules\BankSoal\Http\Controllers\PeriodeController::class, 'destroy'])->name('destroy');

            Route::get('/jadwal', [\Modules\BankSoal\Http\Controllers\JadwalController::class, 'index'])->name('jadwal');
            Route::post('/jadwal', [\Modules\BankSoal\Http\Controllers\JadwalController::class, 'store'])->name('jadwal.store');
            Route::delete('/jadwal/{id}', [\Modules\BankSoal\Http\Controllers\JadwalController::class, 'destroy'])->name('jadwal.destroy');
        });
    });

    # Manajemen Peserta Routes
    Route::prefix('admin/pendaftar')->name('banksoal.pendaftaran.')->group(function () {
        Route::middleware('role:superadmin|admin')->group(function () {
            Route::get('/', [\Modules\BankSoal\Http\Controllers\PendaftarAdminController::class, 'index'])->name('index');
            Route::post('/', [\Modules\BankSoal\Http\Controllers\PendaftarAdminController::class, 'store'])->name('store');
            Route::patch('/{id}/status', [\Modules\BankSoal\Http\Controllers\PendaftarAdminController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{id}', [\Modules\BankSoal\Http\Controllers\PendaftarAdminController::class, 'destroy'])->name('destroy');
        });
    });

});

// Route khusus ujian komprehensif untuk mahasiswa (Module Bank Soal)
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('ujian-komprehensif')
    ->name('komprehensif.mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', [\Modules\BankSoal\Http\Controllers\MahasiswaController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/pengajuan-pendaftaran', [\Modules\BankSoal\Http\Controllers\MahasiswaController::class, 'pendaftaran'])->name('pendaftaran');
        
        Route::get('/pengajuan-pendaftaran/form', [\Modules\BankSoal\Http\Controllers\MahasiswaController::class, 'createPendaftaran'])->name('pendaftaran.form');
        Route::post('/pengajuan-pendaftaran/form', [\Modules\BankSoal\Http\Controllers\MahasiswaController::class, 'storePendaftaran'])->name('pendaftaran.store');
        
        Route::get('/riwayat-ujian', function () {
            return view('banksoal::mahasiswa.riwayat');
        })->name('riwayat');
});

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('banksoal', BankSoalController::class)->names('banksoal');
// });

