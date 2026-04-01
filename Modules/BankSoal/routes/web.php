<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Http\Controllers\DashboardController;
use Modules\BankSoal\Http\Controllers\RPS\Dosen\RpsController as DosenRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\RpsController as GpmRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Admin\RpsController as AdminRpsController;
use Modules\BankSoal\Http\Controllers\BankSoal\RiwayatValidasiController;

Route::middleware(['auth'])->prefix('bank-soal')->group(function () {
    #Dashboard Dosen
    Route::middleware('role:dosen')->group(function(){
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('banksoal.dashboard');
    });
    #Dashboard GPM
    Route::middleware('role:gpm')->group(function () {
        Route::get('/gpm/riwayat-validasi', [RiwayatValidasiController::class, 'index'])
            ->name('banksoal.riwayat-validasi');

        Route::get('/gpm/riwayat-validasi/bank-soal', [RiwayatValidasiController::class, 'bankSoal'])
            ->name('banksoal.riwayat-validasi.bank-soal');
    });
    
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
            Route::get('/validasi-rps', [GpmRpsController::class, 'validasiRps'])->name('validasi-rps');
            Route::get('/validasi-rps/review/{rpsId}', [GpmRpsController::class, 'validasiRpsReview'])->name('validasi-rps.review');
            Route::get('/riwayat-validasi/rps', function () {
                return view('banksoal::gpm.riwayat-validasi.rps');
            })->name('riwayat-validasi.rps');
        });

        # Bank Soal Pages - View Mode
        Route::prefix('bank-soal')->name('banksoal.banksoal.')->group(function () {
            Route::get('/dosen', fn() => view('banksoal::pages.bank-soal.Dosen.index'))->name('dosen.index')->middleware('role:dosen');
            Route::get('/gpm', fn() => view('banksoal::pages.bank-soal.Gpm.index'))->name('gpm.index')->middleware('role:gpm');
            Route::get('/admin', fn() => view('banksoal::pages.bank-soal.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal');
        });

        # Arsip Routes - View Mode
        Route::prefix('arsip')->name('banksoal.arsip.')->group(function () {
            Route::get('/dosen', fn() => view('banksoal::pages.arsip.Dosen.index'))->name('dosen.index')->middleware('role:dosen');
            Route::get('/gpm', fn() => view('banksoal::pages.arsip.Gpm.index'))->name('gpm.index')->middleware('role:gpm');
            Route::get('/admin', fn() => view('banksoal::pages.arsip.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal');

        });

        # GPM Specific: Validasi & Riwayat
        Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
            Route::get('/validasi-rps', fn() => view('banksoal::gpm.validasi-rps'))->name('validasi-rps');
            Route::get('/validasi-rps/review', fn() => view('banksoal::gpm.validasi-rps-review'))->name('validasi-rps.review');
            Route::get('/validasi-bank-soal', fn() => view('banksoal::gpm.validasi-bank-soal'))->name('validasi-bank-soal');
            Route::get('/validasi-bank-soal/review', fn() => view('banksoal::gpm.validasi-bank-soal-review'))->name('validasi-bank-soal.review');
            Route::get('/riwayat-validasi', fn() => view('banksoal::gpm.riwayat-validasi.index'))->name('riwayat-validasi');
            Route::get('/riwayat-validasi/rps', fn() => view('banksoal::gpm.riwayat-validasi.rps'))->name('riwayat-validasi.rps');
            Route::get('/riwayat-validasi/bank-soal', fn() => view('banksoal::gpm.riwayat-validasi.bank-soal'))->name('riwayat-validasi.bank-soal');
        });
    });

    // -------------------------------------------------------------------------
    // PERMISSION: EDIT (Store, Update, Submit)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.edit'])->group(function () {
        
        // Submit RPS - Dosen
        Route::prefix('rps/dosen')->name('banksoal.rps.dosen.')->middleware('role:dosen')->group(function () {
            Route::post('/submit', [DosenRpsController::class, 'store'])->name('store');
        });
        
        Route::prefix('bank-soal/gpm')->name('banksoal.soal.gpm.')->middleware('role:gpm')->group(function () {
            Route::post('/validasi-bank-soal/store', function (Request $request) {
                $request->validate([
                    'pertanyaan_id' => 'required|integer',
                    'status_review' => 'required|string',
                    'catatan'       => 'required|string'
                ]);

                DB::table('bs_review')->insert([
                    'pertanyaan_id' => $request->pertanyaan_id,
                    'gpm_user_id'   => auth()->id() ?? 1,
                    'status_review' => $request->status_review,
                    'catatan'       => $request->catatan,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);

                return redirect()->route('banksoal.soal.gpm.validasi-bank-soal.review')->with('success', 'Hasil review berhasil disimpan!');
            })->name('validasi-bank-soal.store');
        });
    });

    // -------------------------------------------------------------------------
    // PERMISSION: DELETE
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.delete'])->group(function () {
        Route::delete('/destroy/{id}', [BankSoalController::class, 'destroy'])->name('banksoal.destroy');
    });
});

// -------------------------------------------------------------------------
// MAHASISWA SECTION (Komprehensif)
// -------------------------------------------------------------------------
Route::middleware(['auth', 'role:mahasiswa', 'module.active:bank_soal'])
    ->prefix('ujian-komprehensif')
    ->name('komprehensif.mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('banksoal::mahasiswa.beranda'))->name('dashboard');
        Route::get('/pengajuan-pendaftaran', fn() => view('banksoal::mahasiswa.pendaftaran'))->name('pendaftaran');
        Route::get('/riwayat-ujian', fn() => view('banksoal::mahasiswa.riwayat'))->name('riwayat');
    });

