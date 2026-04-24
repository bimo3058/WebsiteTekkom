<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Http\Controllers\BS\DashboardController;
use Modules\BankSoal\Http\Controllers\BS\Admin\CplCpmkController;
use Modules\BankSoal\Http\Controllers\BS\Admin\MataKuliahController;
use Modules\BankSoal\Http\Controllers\BS\Admin\PemetaanController;
use Modules\BankSoal\Http\Controllers\RPS\Dosen\RpsController as DosenRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\RpsController as GpmRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Admin\RpsController as AdminRpsController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\TemplateRpsController;
use Modules\BankSoal\Http\Controllers\BS\BankSoalController;
use Modules\BankSoal\Http\Controllers\BS\GPM\ValidasiBankSoalController;
use Modules\BankSoal\Http\Controllers\BS\GPM\RiwayatValidasiController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\PeriodeRpsController;

Route::middleware(['auth', 'module.active:bank_soal'])->prefix('bank-soal')->group(function () {

    // -------------------------------------------------------------------------
    // PERMISSION: VIEW (Dashboard & List)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.view'])->group(function () {
        
        # Dashboard Utama
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('role:admin_banksoal,dosen,gpm')
            ->name('banksoal.dashboard');

        # Admin Routes - Kontrol Umum
        Route::middleware('role:admin_banksoal')->prefix('admin/kontrol-umum')->name('banksoal.admin.kontrol-umum.')->group(function () {
            Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
            Route::get('/cpl-cpmk', [CplCpmkController::class, 'index'])->name('cpl-cpmk');
            Route::get('/pemetaan', [PemetaanController::class, 'index'])->name('pemetaan');
        });

        Route::middleware('role:admin_banksoal')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
            Route::get('/cpl', [CplCpmkController::class, 'listCpl'])->name('cpl.index');
            Route::get('/cpl/next-code', [CplCpmkController::class, 'nextCplCode'])->name('cpl.next-code');
            Route::get('/cpl/{id}', [CplCpmkController::class, 'showCpl'])->name('cpl.show');

            Route::get('/cpmk', [CplCpmkController::class, 'listCpmk'])->name('cpmk.index');
            Route::get('/cpmk/next-code', [CplCpmkController::class, 'nextCpmkCode'])->name('cpmk.next-code');
            Route::get('/cpmk/{id}', [CplCpmkController::class, 'showCpmk'])->name('cpmk.show');

            Route::get('/pemetaan/options', [PemetaanController::class, 'options'])->name('pemetaan.options');
            Route::get('/pemetaan/cpmk-cpl', [PemetaanController::class, 'listCpmkCpl'])->name('pemetaan.cpmk-cpl.index');
            Route::get('/pemetaan/mk-cpl', [PemetaanController::class, 'listMkCpl'])->name('pemetaan.mk-cpl.index');
            Route::get('/pemetaan/dosen-mk', [PemetaanController::class, 'listDosenMk'])->name('pemetaan.dosen-mk.index');
        });

        # Admin Routes - Kontrol BankSoal
        Route::middleware('role:admin_banksoal')->prefix('admin/kontrol-banksoal')->name('banksoal.admin.kontrol-banksoal.')->group(function () {
            Route::get('/rps', fn() => view('banksoal::pages.admin.kontrol-banksoal.rps'))->name('rps');
            Route::get('/soal', fn() => view('banksoal::pages.admin.kontrol-banksoal.soal'))->name('soal');
        });

        # RPS Routes - View Mode
        Route::prefix('rps')->name('banksoal.rps.')->group(function () {
            // RPS - Dosen
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::get('/', [DosenRpsController::class, 'index'])->name('index');
                Route::get('/preview/{rpsId}', [DosenRpsController::class, 'previewDokumen'])->name('preview');
                Route::get('/{rpsId}/edit', [DosenRpsController::class, 'edit'])->name('edit');
                Route::get('/mk', [DosenRpsController::class, 'getMkByDosen'])->name('mk');
                Route::get('/cpl/{mkId?}', [DosenRpsController::class, 'getCplByMk'])->name('cpl');
                Route::get('/cpmk', [DosenRpsController::class, 'getCpmkByCpl'])->name('cpmk');
                Route::get('/cpmk-by-rps/{rpsId}', [DosenRpsController::class, 'getCpmkByRps'])->name('cpmk-by-rps');
                Route::get('/dosen', [DosenRpsController::class, 'getDosenByMk'])->name('dosen');
            });
            // RPS - GPM
            Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
                Route::get('/', [RiwayatValidasiController::class, 'index'])->name('index');
                Route::get('/validasi-rps', [GpmRpsController::class, 'validasiRps'])->name('validasi-rps');
                Route::get('/validasi-rps/review/{rpsId}', [GpmRpsController::class, 'validasiRpsReview'])->name('validasi-rps.review');
                Route::get('/validasi-rps/preview/{rpsId}', [GpmRpsController::class, 'previewDokumen'])->name('validasi-rps.preview');
                Route::get('/riwayat-validasi/rps', [RiwayatValidasiController::class, 'rps'])->name('riwayat-validasi.rps');
                Route::get('/periode-rps', [PeriodeRpsController::class, 'index'])->name('periode-rps.index');
                
                // Delete routes for GPM
                Route::middleware('permission:banksoal.delete')->group(function () {
                    Route::delete('/periode-rps/{id}', [PeriodeRpsController::class, 'destroy'])->name('periode-rps.destroy');
                    Route::delete('/template-inactive', [TemplateRpsController::class, 'deleteInactive'])->name('template.delete-inactive');
                });
            });
            // RPS - Admin
            Route::middleware('role:admin_banksoal')->prefix('admin')->name('admin.')->group(function () {
                Route::get('/', [AdminRpsController::class, 'index'])->name('index');
            });
        });

        # Bank Soal Pages - View Mode
        Route::prefix('soal')->name('banksoal.soal.')->group(function () {
            // Banksoal - Dosen
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::post('/tarik-soal', [BankSoalController::class, 'ekstrak'])->name('ekstrak');
                Route::post('/cetak-ujian', [BankSoalController::class, 'cetakUjian'])->name('cetak-ujian');
                Route::get('/get-by-mk/{mk_id}', [BankSoalController::class, 'getAvailableSoals'])->name('get-available-soals');
                Route::get('/export-csv', [BankSoalController::class, 'exportCsv'])->name('export-csv');
                Route::post('/import-csv', [BankSoalController::class, 'importCsv'])->name('import-csv');
                Route::post('/ajukan-semua', [BankSoalController::class, 'ajukanSemuaDraf'])->name('ajukan-semua');
                Route::get('/', [BankSoalController::class, 'index'])->name('index');
                Route::get('/create', [BankSoalController::class, 'create'])->name('create');
                Route::post('/store', [BankSoalController::class, 'store'])->name('store');
                Route::get('/{id}', [BankSoalController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [BankSoalController::class, 'edit'])->name('edit');
                Route::put('/{id}', [BankSoalController::class, 'update'])->name('update');
            });
            
           // Banksoal - GPM
            Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
                Route::get('/riwayat-validasi', [RiwayatValidasiController::class, 'index'])->name('riwayat-validasi');
                Route::get('/validasi-bank-soal', [ValidasiBankSoalController::class, 'index'])->name('validasi-bank-soal');
                Route::get('/validasi-bank-soal/review', [ValidasiBankSoalController::class, 'review'])->name('validasi-bank-soal.review');
                Route::get('/riwayat-validasi/bank-soal', [RiwayatValidasiController::class, 'bankSoal'])->name('riwayat-validasi.bank-soal');
                Route::get('/riwayat-validasi/bank-soal/{id}/detail', [RiwayatValidasiController::class, 'detailBankSoal'])->name('riwayat-validasi.bank-soal.detail');
            });

            // Banksoal - Admin
            Route::get('/admin', fn() => view('banksoal::pages.bank-soal.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal');
        });

        # Arsip Routes - View Mode
        Route::prefix('arsip')->name('banksoal.arsip.')->group(function () {
            Route::get('/dosen', fn() => view('banksoal::pages.arsip.Dosen.index'))->name('dosen.index')->middleware('role:dosen');
            Route::get('/gpm', fn() => view('banksoal::pages.arsip.Gpm.index'))->name('gpm.index')->middleware('role:gpm');
            Route::get('/admin', fn() => view('banksoal::pages.arsip.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal');
        });
    });

    // -------------------------------------------------------------------------
    // PERMISSION: EDIT (Store, Update, Submit)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.edit'])->group(function () {
        
        // 0. Admin Mata Kuliah CRUD Routes
        Route::middleware('role:admin_banksoal')->prefix('admin/api/mata-kuliah')->name('banksoal.api.v1.admin.mata-kuliah.')->group(function () {
            Route::get('/', [MataKuliahController::class, 'index'])->name('index');
            Route::post('/', [MataKuliahController::class, 'store'])->name('store');
            Route::get('/{id}', [MataKuliahController::class, 'show'])->name('show');
            Route::put('/{id}', [MataKuliahController::class, 'update'])->name('update');
        });

        Route::middleware('role:admin_banksoal')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
            Route::post('/cpl', [CplCpmkController::class, 'storeCpl'])->name('cpl.store');
            Route::put('/cpl/{id}', [CplCpmkController::class, 'updateCpl'])->name('cpl.update');

            Route::post('/cpmk', [CplCpmkController::class, 'storeCpmk'])->name('cpmk.store');
            Route::put('/cpmk/{id}', [CplCpmkController::class, 'updateCpmk'])->name('cpmk.update');

            Route::post('/pemetaan/cpmk-cpl', [PemetaanController::class, 'storeCpmkCpl'])->name('pemetaan.cpmk-cpl.store');
            Route::post('/pemetaan/mk-cpl', [PemetaanController::class, 'storeMkCpl'])->name('pemetaan.mk-cpl.store');
            Route::post('/pemetaan/dosen-mk', [PemetaanController::class, 'storeDosenMk'])->name('pemetaan.dosen-mk.store');
        });

        // 1. Blok RPS
        Route::prefix('rps')->name('banksoal.rps.')->group(function () {
            // RPS - Dosen
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::post('/submit', [DosenRpsController::class, 'store'])->name('store');
                Route::put('/{rpsId}', [DosenRpsController::class, 'update'])->name('update');
            });
            // RPS - GPM
            Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
                Route::post('/validasi-rps/store', [GpmRpsController::class, 'storeValidasi'])->name('validasi-rps.store');                
                Route::post('/periode-rps', [PeriodeRpsController::class, 'store'])->name('periode-rps.store');
                Route::put('/periode-rps/{id}', [PeriodeRpsController::class, 'update'])->name('periode-rps.update');
                Route::post('/periode-rps/open-session', [PeriodeRpsController::class, 'openSession'])->name('periode-rps.open-session');
                Route::post('/periode-rps/close-session', [PeriodeRpsController::class, 'closeSession'])->name('periode-rps.close-session');
                Route::post('/template', [TemplateRpsController::class, 'store'])->name('template.store');
            });
        });

        // 2. Blok Bank Soal
        Route::prefix('soal')->name('banksoal.soal.')->group(function () {
            // Bank Soal - GPM
            Route::middleware('role:gpm')->prefix('gpm')->name('gpm.')->group(function () {
                Route::post('/validasi-bank-soal/store', [ValidasiBankSoalController::class, 'store'])->name('validasi-bank-soal.store');            
                Route::put('/validasi-bank-soal/update/{id}', [ValidasiBankSoalController::class, 'update'])->name('validasi-bank-soal.update');
            });
        });
    });
    // -------------------------------------------------------------------------
    // PERMISSION: DELETE
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.delete'])->group(function () {
        Route::delete('/destroy/{id}', [BankSoalController::class, 'destroy'])->name('banksoal.destroy');
        
        // Admin Mata Kuliah Delete Routes
        Route::middleware('role:admin_banksoal')->prefix('admin/api/mata-kuliah')->name('banksoal.api.v1.admin.mata-kuliah.')->group(function () {
            Route::delete('/{id}', [MataKuliahController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [MataKuliahController::class, 'bulkDelete'])->name('bulk-delete');
        });

        Route::middleware('role:admin_banksoal')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
            Route::delete('/cpl/{id}', [CplCpmkController::class, 'destroyCpl'])->name('cpl.destroy');
            Route::delete('/cpmk/{id}', [CplCpmkController::class, 'destroyCpmk'])->name('cpmk.destroy');

            Route::delete('/pemetaan/cpmk-cpl', [PemetaanController::class, 'destroyCpmkCpl'])->name('pemetaan.cpmk-cpl.destroy');
            Route::delete('/pemetaan/mk-cpl', [PemetaanController::class, 'destroyMkCpl'])->name('pemetaan.mk-cpl.destroy');
            Route::delete('/pemetaan/dosen-mk/{id}', [PemetaanController::class, 'destroyDosenMk'])->name('pemetaan.dosen-mk.destroy');
        });
        
        // RPS Dosen Delete
        Route::prefix('rps')->name('banksoal.rps.')->group(function () {
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::delete('/{rpsId}', [DosenRpsController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // -------------------------------------------------------------------------
    // PUBLIC ROUTES (No permission check)
    // -------------------------------------------------------------------------
    // Download template RPS (untuk Dosen dan umum)
    Route::get('/rps/template/download', [TemplateRpsController::class, 'download'])
        ->middleware('role:dosen')
        ->name('rps.template.download');
    
    

    # Periode Ujian Routes
    Route::prefix('admin/periode')->name('banksoal.periode.')->group(function () {
        Route::middleware('role:admin_banksoal,admin')->group(function () {
            Route::get('/setup', [\Modules\BankSoal\Http\Controllers\Komprehensif\PeriodeController::class, 'index'])->name('setup');
            Route::post('/setup', [\Modules\BankSoal\Http\Controllers\Komprehensif\PeriodeController::class, 'store'])->name('store');
            Route::put('/setup/{id}', [\Modules\BankSoal\Http\Controllers\Komprehensif\PeriodeController::class, 'update'])->name('update');
            Route::delete('/setup/{id}', [\Modules\BankSoal\Http\Controllers\Komprehensif\PeriodeController::class, 'destroy'])->name('destroy');

            Route::get('/jadwal', [\Modules\BankSoal\Http\Controllers\Komprehensif\JadwalController::class, 'index'])->name('jadwal');
            Route::post('/jadwal', [\Modules\BankSoal\Http\Controllers\Komprehensif\JadwalController::class, 'store'])->name('jadwal.store');
            Route::delete('/jadwal/{id}', [\Modules\BankSoal\Http\Controllers\Komprehensif\JadwalController::class, 'destroy'])->name('jadwal.destroy');
        });
    });

    # Manajemen Peserta Routes
    Route::prefix('admin/pendaftar')->name('banksoal.pendaftaran.')->group(function () {
        Route::middleware('role:admin_banksoal,admin')->group(function () {
            Route::get('/', [\Modules\BankSoal\Http\Controllers\Komprehensif\PendaftarAdminController::class, 'index'])->name('index');
            Route::post('/', [\Modules\BankSoal\Http\Controllers\Komprehensif\PendaftarAdminController::class, 'store'])->name('store');
            Route::patch('/{id}/status', [\Modules\BankSoal\Http\Controllers\Komprehensif\PendaftarAdminController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{id}', [\Modules\BankSoal\Http\Controllers\Komprehensif\PendaftarAdminController::class, 'destroy'])->name('destroy');
        });
    });

    # Alokasi Sesi Routes
    Route::prefix('admin/alokasi-sesi')->name('banksoal.pendaftaran.alokasi-sesi.')->group(function () {
        Route::middleware('role:admin_banksoal,admin')->group(function () {
            Route::get('/', [\Modules\BankSoal\Http\Controllers\Komprehensif\AlokasiSesiController::class, 'index'])->name('index');
            Route::post('/', [\Modules\BankSoal\Http\Controllers\Komprehensif\AlokasiSesiController::class, 'store'])->name('store');
            Route::post('/remove', [\Modules\BankSoal\Http\Controllers\Komprehensif\AlokasiSesiController::class, 'remove'])->name('remove');
        });
    });


    # Aktivasi Sesi Routes
    Route::prefix('admin/aktivasi-sesi')->name('banksoal.aktivasi.')->group(function () {
        Route::middleware('role:admin_banksoal,admin')->group(function () {
            Route::get('/', [\Modules\BankSoal\Http\Controllers\Komprehensif\AktivasiSesiController::class, 'index'])->name('index');
            Route::patch('/{id}/toggle', [\Modules\BankSoal\Http\Controllers\Komprehensif\AktivasiSesiController::class, 'toggle'])->name('toggle');
        });
    });

});

// -------------------------------------------------------------------------
// MAHASISWA SECTION (Komprehensif)
// -------------------------------------------------------------------------
Route::middleware(['auth', 'role:mahasiswa', 'module.active:bank_soal'])
    ->prefix('ujian-komprehensif')
    ->name('komprehensif.mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', [\Modules\BankSoal\Http\Controllers\Komprehensif\MahasiswaController::class, 'dashboard'])->name('dashboard');
        
        Route::get('/pengajuan-pendaftaran', [\Modules\BankSoal\Http\Controllers\Komprehensif\MahasiswaController::class, 'pendaftaran'])->name('pendaftaran');
        
        Route::get('/pengajuan-pendaftaran/form', [\Modules\BankSoal\Http\Controllers\Komprehensif\MahasiswaController::class, 'createPendaftaran'])->name('pendaftaran.form');
        Route::post('/pengajuan-pendaftaran/form', [\Modules\BankSoal\Http\Controllers\Komprehensif\MahasiswaController::class, 'storePendaftaran'])->name('pendaftaran.store');
        
        Route::get('/riwayat-ujian', function () {
            return view('banksoal::mahasiswa.riwayat');
        })->name('riwayat');
    });

