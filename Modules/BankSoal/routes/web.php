<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

// Route khusus ujian komprehensif untuk mahasiswa (Module Bank Soal)
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('ujian-komprehensif')
    ->name('komprehensif.mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('banksoal::mahasiswa.beranda'); 
        })->name('dashboard');
        
        Route::get('/pengajuan-pendaftaran', function () {
            return view('banksoal::mahasiswa.pendaftaran');
        })->name('pendaftaran');
        
        Route::get('/riwayat-ujian', function () {
            return view('banksoal::mahasiswa.riwayat');
        })->name('riwayat');
});

Route::middleware(['auth'])->prefix('bank-soal')->group(function () {
    // Route khusus halaman Validasi RPS (GPM)
    Route::get('/gpm/validasi-rps', function () {
        return view('banksoal::gpm.validasi-rps');
    })->name('gpm.validasi-rps');

    Route::get('/gpm/validasi-rps/review', function () {
        return view('banksoal::gpm.validasi-rps-review');
    })->name('gpm.validasi-rps.review');

    // Route khusus halaman Validasi Bank Soal (GPM)
    Route::get('/gpm/validasi-bank-soal', function () {
        return view('banksoal::gpm.validasi-bank-soal');
    })->name('gpm.validasi-bank-soal');

    // 1. GET: Menampilkan halaman review soal
    Route::get('/gpm/validasi-bank-soal/review', function () {
        // Ambil soal pertama yang BELUM ADA di tabel bs_review
        $soal = DB::table('bs_pertanyaan')
            ->join('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->join('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            ->whereNotIn('bs_pertanyaan.id', function($query) {
                $query->select('pertanyaan_id')->from('bs_review');
            })
            ->select(
                'bs_pertanyaan.*', 
                'bs_cpl.kode as cpl_kode', 'bs_cpl.deskripsi as cpl_deskripsi',
                'bs_mata_kuliah.nama as mk_nama', 'bs_mata_kuliah.kode as mk_kode'
            )
            ->orderBy('bs_pertanyaan.id', 'asc')
            ->first();

        // Kalau $soal kosong, berarti semua soal sudah beres divalidasi!
        if (!$soal) {
            return redirect()->route('gpm.validasi-bank-soal')->with('success', 'Mantap! Semua soal telah selesai divalidasi.');
        }

        // Ambil opsi jawaban sesuai ID soal yang lagi tampil (bukan hardcode 1 lagi)
        $opsi_jawaban = DB::table('bs_jawaban')
            ->where('soal_id', $soal->id)
            ->orderBy('opsi', 'asc')
            ->get();

        return view('banksoal::gpm.validasi-bank-soal-review', compact('soal', 'opsi_jawaban'));
    })->name('gpm.validasi-bank-soal.review');
    
    // 2. POST: Menyimpan hasil review form
    Route::post('/gpm/validasi-bank-soal/store', function (Request $request) {
        // Validasi input form
        $request->validate([
            'pertanyaan_id' => 'required|integer',
            'status_review' => 'required|string',
            'catatan'       => 'required|string'
        ]);

        // Insert data ke tabel review
        DB::table('bs_review')->insert([
            'pertanyaan_id' => $request->pertanyaan_id,
            'gpm_user_id'   => auth()->id() ?? 1,
            'status_review' => $request->status_review,
            'catatan'       => $request->catatan,
            'created_at'    => now(),
            'updated_at'    => now()
        ]);

        // Alihkan ulang ke rute review (yang sekarang otomatis nampilin soal ID berikutnya!)
        return redirect()->route('gpm.validasi-bank-soal.review')->with('success', 'Hasil review berhasil disimpan!');
    })->name('gpm.validasi-bank-soal.store');

    // Route khusus halaman Riwayat Validasi (GPM)
    Route::get('/gpm/riwayat-validasi', function () {
        return view('banksoal::gpm.riwayat-validasi.index');
    })->name('gpm.riwayat-validasi');

    Route::get('/gpm/riwayat-validasi/rps', function () {
        return view('banksoal::gpm.riwayat-validasi.rps');
    })->name('gpm.riwayat-validasi.rps');

    Route::get('/gpm/riwayat-validasi/bank-soal', function () {
        return view('banksoal::gpm.riwayat-validasi.bank-soal');
    })->name('gpm.riwayat-validasi.bank-soal');
});