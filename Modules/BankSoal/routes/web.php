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
use Modules\BankSoal\Http\Controllers\BS\Dosen\ArsipSoalController;
use Modules\BankSoal\Http\Controllers\BS\GPM\ValidasiBankSoalController;
use Modules\BankSoal\Http\Controllers\BS\GPM\RiwayatValidasiController;
use Modules\BankSoal\Http\Controllers\RPS\Gpm\PeriodeRpsController;
use Modules\BankSoal\Http\Controllers\Komprehensif\AdminCbtController;
use Modules\BankSoal\Http\Controllers\Komprehensif\AktivasiSesiController;
use Modules\BankSoal\Http\Controllers\Komprehensif\AlokasiSesiController;
use Modules\BankSoal\Http\Controllers\Komprehensif\CbtEngineController;
use Modules\BankSoal\Http\Controllers\Komprehensif\JadwalController;
use Modules\BankSoal\Http\Controllers\Komprehensif\MahasiswaController;
use Modules\BankSoal\Http\Controllers\Komprehensif\PendaftarAdminController;
use Modules\BankSoal\Http\Controllers\Komprehensif\PeriodeController;

Route::middleware(['auth', 'module.active:bank_soal'])->prefix('bank-soal')->group(function () {

    // -------------------------------------------------------------------------
    // PERMISSION: VIEW (Dashboard & List)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.view'])->group(function () {
        
        # Dashboard Utama
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('role:admin_banksoal|superadmin|dosen|gpm')
            ->name('banksoal.dashboard');

        # Admin Routes - Kontrol Umum
        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/kontrol-umum')->name('banksoal.admin.kontrol-umum.')->group(function () {
            Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah');
            Route::get('/cpl-cpmk', [CplCpmkController::class, 'index'])->name('cpl-cpmk');
            Route::get('/pemetaan', [PemetaanController::class, 'index'])->name('pemetaan');
        });

        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
            Route::get('/cpl', [CplCpmkController::class, 'listCpl'])->name('cpl.index');
            Route::get('/cpl/next-code', [CplCpmkController::class, 'nextCplCode'])->name('cpl.next-code');
            Route::get('/cpl/export-template', [CplCpmkController::class, 'exportCplTemplate'])->name('cpl.export-template');
            Route::post('/cpl/import', [CplCpmkController::class, 'importCpl'])->name('cpl.import');
            Route::get('/cpl/{id}', [CplCpmkController::class, 'showCpl'])->name('cpl.show');

            Route::get('/rps/approved', [AdminRpsController::class, 'listApproved'])->name('rps.approved.index');

            Route::get('/cpmk', [CplCpmkController::class, 'listCpmk'])->name('cpmk.index');
            Route::get('/cpmk/next-code', [CplCpmkController::class, 'nextCpmkCode'])->name('cpmk.next-code');
            Route::get('/cpmk/export-template', [CplCpmkController::class, 'exportCpmkTemplate'])->name('cpmk.export-template');
            Route::post('/cpmk/import', [CplCpmkController::class, 'importCpmk'])->name('cpmk.import');
            Route::get('/cpmk/{id}', [CplCpmkController::class, 'showCpmk'])->name('cpmk.show');

            Route::post('/mata-kuliah/import', [MataKuliahController::class, 'import'])->name('mata-kuliah.import');
            Route::get('/mata-kuliah/export-template', [MataKuliahController::class, 'exportTemplate'])->name('mata-kuliah.export-template');

            Route::get('/pemetaan/options', [PemetaanController::class, 'options'])->name('pemetaan.options');
            Route::get('/pemetaan/cpmk-cpl', [PemetaanController::class, 'listCpmkCpl'])->name('pemetaan.cpmk-cpl.index');
            Route::get('/pemetaan/mk-cpl', [PemetaanController::class, 'listMkCpl'])->name('pemetaan.mk-cpl.index');
            Route::get('/pemetaan/dosen-mk', [PemetaanController::class, 'listDosenMk'])->name('pemetaan.dosen-mk.index');
        });

        # Admin Routes - Kontrol BankSoal
        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/kontrol-banksoal')->name('banksoal.admin.kontrol-banksoal.')->group(function () {
            Route::get('/rps', [AdminRpsController::class, 'index'])->name('rps');
            Route::get('/rps/{rpsId}/preview', [AdminRpsController::class, 'previewDokumen'])->name('rps.preview');
            Route::get('/rps/{rpsId}/download', [AdminRpsController::class, 'downloadDokumen'])->name('rps.download');
            Route::get('/soal', [\Modules\BankSoal\Http\Controllers\BS\Admin\ManajemenSoalController::class, 'index'])->name('soal');
            Route::get('/soal/{id}/cetak', [\Modules\BankSoal\Http\Controllers\BS\Admin\ManajemenSoalController::class, 'cetakDokumen'])->name('soal.cetak');
            Route::post('/soal/{id}/tandai-selesai', [\Modules\BankSoal\Http\Controllers\BS\Admin\ManajemenSoalController::class, 'tandaiSelesai'])->name('soal.tandai-selesai');
        });

        # RPS Routes - View Mode
        Route::prefix('rps')->name('banksoal.rps.')->group(function () {
            // RPS - Dosen
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::get('/', [DosenRpsController::class, 'index'])->name('index');
                Route::get('/preview/{rpsId}', [DosenRpsController::class, 'previewDokumen'])->name('preview');
                Route::get('/download/{rpsId}', [DosenRpsController::class, 'downloadDokumen'])->name('download');
                Route::get('/{rpsId}/edit', [DosenRpsController::class, 'edit'])->name('edit');
                Route::get('/{rpsId}/edit-modal', [DosenRpsController::class, 'editModal'])->name('edit-modal');
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
            Route::middleware('role:admin_banksoal|superadmin')->prefix('admin')->name('admin.')->group(function () {
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
            Route::get('/admin', fn() => view('banksoal::pages.bank-soal.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal|superadmin');
        });

        # Arsip Routes - View Mode
        Route::prefix('arsip')->name('banksoal.arsip.')->group(function () {
            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::get('/', [ArsipSoalController::class, 'index'])->name('index');
                Route::get('/{id}', [ArsipSoalController::class, 'show'])->name('show');
                Route::get('/penarikan/{id}', [ArsipSoalController::class, 'showPenarikan'])->name('penarikan.show');
                Route::get('/penarikan/{id}/edit', [ArsipSoalController::class, 'editPenarikan'])->name('penarikan.edit');
                Route::put('/penarikan/{id}', [ArsipSoalController::class, 'convertPenarikan'])->name('penarikan.update');
                Route::delete('/penarikan/{id}', [ArsipSoalController::class, 'destroyPenarikan'])->name('penarikan.destroy');
                Route::delete('/{id}', [ArsipSoalController::class, 'destroy'])->name('destroy');
                Route::post('/upload/pdf', [ArsipSoalController::class, 'uploadPdf'])->name('upload-pdf');
                Route::post('/upload/csv', [ArsipSoalController::class, 'uploadCsv'])->name('upload-csv');
            });
            Route::get('/gpm', fn() => view('banksoal::pages.arsip.Gpm.index'))->name('gpm.index')->middleware('role:gpm');
            Route::get('/admin', fn() => view('banksoal::pages.arsip.Admin.index'))->name('admin.index')->middleware('role:admin_banksoal|superadmin');
        });
    });

    // -------------------------------------------------------------------------
    // PERMISSION: EDIT (Store, Update, Submit)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.edit'])->group(function () {
        
        // 0. Admin Mata Kuliah CRUD Routes
        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/api/mata-kuliah')->name('banksoal.api.v1.admin.mata-kuliah.')->group(function () {
            Route::get('/', [MataKuliahController::class, 'index'])->name('index');
            Route::post('/', [MataKuliahController::class, 'store'])->name('store');
            Route::get('/{id}', [MataKuliahController::class, 'show'])->name('show');
            Route::put('/{id}', [MataKuliahController::class, 'update'])->name('update');
        });

        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
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

            Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
                Route::post('/arsip-soal', [ArsipSoalController::class, 'storeFromEkstraksi'])->name('arsip-soal.store');
            });
        });
    });
    // -------------------------------------------------------------------------
    // PERMISSION: DELETE
    // -------------------------------------------------------------------------
    Route::middleware(['permission:banksoal.delete'])->group(function () {
        Route::delete('/destroy/{id}', [BankSoalController::class, 'destroy'])->name('banksoal.destroy');
        
        // Admin Mata Kuliah Delete Routes
        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/api/mata-kuliah')->name('banksoal.api.v1.admin.mata-kuliah.')->group(function () {
            Route::delete('/{id}', [MataKuliahController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [MataKuliahController::class, 'bulkDelete'])->name('bulk-delete');
        });

        Route::middleware('role:admin_banksoal|superadmin')->prefix('admin/api')->name('banksoal.api.v1.admin.')->group(function () {
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
        Route::middleware('role:admin_banksoal|admin|superadmin')->group(function () {
            Route::get('/setup',                         [PeriodeController::class, 'index'])->name('setup');
            Route::post('/setup',                        [PeriodeController::class, 'store'])->name('store');
            Route::put('/setup/{id}',                    [PeriodeController::class, 'update'])->name('update');
            Route::delete('/setup/{id}',                 [PeriodeController::class, 'destroy'])->name('destroy');
            Route::patch('/setup/{id}/close-pendaftaran',[PeriodeController::class, 'closePendaftaran'])->name('close-pendaftaran');

            Route::get('/jadwal',    [JadwalController::class, 'index'])->name('jadwal');
            Route::post('/jadwal',   [JadwalController::class, 'store'])->name('jadwal.store');
            Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
        });
    });

    # Manajemen Peserta Routes
    Route::prefix('admin/pendaftar')->name('banksoal.pendaftaran.')->group(function () {
        Route::middleware('role:admin_banksoal|admin|superadmin')->group(function () {
            Route::get('/',               [PendaftarAdminController::class, 'index'])->name('index');
            Route::get('/lookup-nim',     [PendaftarAdminController::class, 'lookupNIM'])->name('lookupNIM');
            Route::post('/',              [PendaftarAdminController::class, 'store'])->name('store');
            Route::patch('/{id}/status',  [PendaftarAdminController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/{id}',        [PendaftarAdminController::class, 'destroy'])->name('destroy');
        });
    });

    # Alokasi Sesi Routes
    Route::prefix('admin/alokasi-sesi')->name('banksoal.pendaftaran.alokasi-sesi.')->group(function () {
        Route::middleware('role:admin_banksoal|admin|superadmin')->group(function () {
            Route::get('/',        [AlokasiSesiController::class, 'index'])->name('index');
            Route::post('/',       [AlokasiSesiController::class, 'store'])->name('store');
            Route::post('/remove', [AlokasiSesiController::class, 'remove'])->name('remove');
        });
    });


    # Aktivasi Sesi Routes
    Route::prefix('admin/aktivasi-sesi')->name('banksoal.aktivasi.')->group(function () {
        Route::middleware('role:admin_banksoal|admin|superadmin')->group(function () {
            Route::get('/',            [AktivasiSesiController::class, 'index'])->name('index');
            Route::patch('/{id}/toggle',[AktivasiSesiController::class, 'toggle'])->name('toggle');
        });
    });

    # Manajemen Ujian (Live Proctoring & Riwayat)
    Route::prefix('admin/cbt')->name('banksoal.admin.cbt.')->group(function () {
        Route::middleware('role:admin_banksoal|admin|superadmin')->group(function () {
            Route::get('/live-proctoring',              [AdminCbtController::class, 'liveProctoring'])->name('live-proctoring');
            Route::post('/live-proctoring/{id}/force-submit', [AdminCbtController::class, 'forceSubmit'])->name('force-submit');
            Route::get('/riwayat',                      [AdminCbtController::class, 'riwayat'])->name('riwayat');
            Route::get('/analitik',                     [AdminCbtController::class, 'analytics'])->name('analitik');
            Route::get('/riwayat/{id}',                 [AdminCbtController::class, 'detailHasil'])->name('detail');
            Route::post('/reset-semua',                 [AdminCbtController::class, 'resetSemua'])->name('reset-semua');
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
        Route::get('/dashboard',                      [MahasiswaController::class, 'dashboard'])->name('dashboard');

        // Route lama di-redirect langsung ke form (landing page tidak diperlukan)
        Route::get('/pengajuan-pendaftaran', function () {
            return redirect()->route('komprehensif.mahasiswa.pendaftaran.form');
        })->name('pendaftaran');

        Route::get('/pengajuan-pendaftaran/form',  [MahasiswaController::class, 'createPendaftaran'])->name('pendaftaran.form');
        Route::post('/pengajuan-pendaftaran/form', [MahasiswaController::class, 'storePendaftaran'])->name('pendaftaran.store');

        Route::get('/riwayat-ujian', [MahasiswaController::class, 'riwayat'])->name('riwayat');

        // CBT Engine Routes
        // Flow: validate-token (generate soal + start sesi) → engine/run
        Route::post('/engine/validate-token', [CbtEngineController::class, 'validateToken'])
            ->middleware('throttle:cbt-token-validation')  // Max 5 percobaan/menit per user
            ->name('engine.validate');
        Route::get('/engine/run',             [CbtEngineController::class, 'run'])->name('engine.run');

        // Waiting room dipertahankan sebagai fallback (tidak digunakan dalam flow normal)
        Route::get('/engine/waiting-room',    [CbtEngineController::class, 'waitingRoom'])->name('engine.waiting');

        // CBT Engine API Routes
        Route::post('/engine/save-answer',  [CbtEngineController::class, 'saveAnswer'])->name('engine.save-answer');
        Route::post('/engine/toggle-ragu',  [CbtEngineController::class, 'toggleRagu'])->name('engine.toggle-ragu');
        Route::post('/engine/log-violation',[CbtEngineController::class, 'logViolation'])->name('engine.log-violation');
        Route::get('/engine/finish',        [CbtEngineController::class, 'finish'])->name('engine.finish');
    });
