<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KemahasiswaanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ForumController;
use Modules\ManajemenMahasiswa\Http\Controllers\GamificationController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengaduanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KegiatanController;
use Modules\ManajemenMahasiswa\Http\Controllers\DirektoriMahasiswaController;
use Modules\ManajemenMahasiswa\Http\Controllers\ManajemenPenggunaController;
use Modules\ManajemenMahasiswa\Http\Controllers\VerifikasiController;

Route::middleware(['auth', 'module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {

        // Dashboard Utama Modul — semua role boleh akses, renderDashboard() menentukan view sesuai role
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Switch tampilan dashboard antar-role (untuk user multi-role)
        Route::post('/dashboard/switch-mode', [DashboardController::class, 'switchMode'])
            ->name('switch.mode');

        // ── Pengurus Himpunan ─────────────────────────────────────────────
        Route::middleware('role:pengurus_himpunan,gpm,admin,admin_kemahasiswaan,superadmin')
            ->prefix('pengurus')
            ->name('pengurus.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.pengurus');
            })->name('dashboard');
        });

        // ── Alumni ────────────────────────────────────────────────────────
        Route::middleware('role:alumni,gpm,admin,admin_kemahasiswaan,superadmin')
            ->prefix('alumni')
            ->name('alumni.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.alumni');
            })->name('dashboard');
        });

        // ── Dosen ─────────────────────────────────────────────────────────
        Route::middleware('role:dosen,dosen_koordinator,gpm,admin,admin_kemahasiswaan,superadmin')
            ->prefix('dosen')
            ->name('dosen.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return redirect()->route('manajemenmahasiswa.pengumuman.index');
            })->name('dashboard');
        });

        // ── Pengumuman ────────────────────────────────────────────────────
        Route::prefix('pengumuman')->name('pengumuman.')->group(function () {

            // Index — semua role boleh
            Route::get('/', [PengumumanController::class, 'index'])->name('index');

            // Create/Edit/Delete — hanya pengurus + admin
            Route::middleware('role:pengurus_himpunan,gpm,admin,admin_kemahasiswaan,superadmin')->group(function () {
                Route::get('/create', [PengumumanController::class, 'create'])->name('create');
                Route::post('/drafts', [PengumumanController::class, 'saveDraft'])->name('drafts.store');
                Route::delete('/drafts/{id}', [PengumumanController::class, 'deleteDraft'])->name('drafts.destroy');
                Route::post('/inline-image', [PengumumanController::class, 'uploadInlineImage'])->name('inline.image');
                Route::post('/', [PengumumanController::class, 'store'])->name('store');
                Route::get('/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('edit');
                Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
                Route::delete('/{pengumuman}', [PengumumanController::class, 'remove'])->name('remove');
                Route::patch('/{pengumuman}/publish', [PengumumanController::class, 'publish'])->name('publish');
                Route::delete('/{pengumuman}/lampiran/{lampiran}', [PengumumanController::class, 'removeLampiran'])->name('lampiran.remove');
            });

            // Download lampiran — semua role boleh
            Route::get('/lampiran/{lampiran}/download', [PengumumanController::class, 'downloadLampiran'])->name('lampiran.download');

            // Show — semua role boleh (HARUS setelah /create agar tidak konflik)
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
        });

        // ── Layanan Pengaduan ─────────────────────────────────────────────
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            // Mahasiswa & pengurus himpunan membuat pengaduan
            // NOTE: HARUS didefinisikan sebelum /{pengaduan} agar tidak konflik dengan path seperti /create
            Route::middleware('role:mahasiswa,pengurus_himpunan,ketua_himpunan,wakil_ketua_himpunan,ketua_bidang,ketua_unit,staff_himpunan')->group(function () {
                Route::get('/create', [PengaduanController::class, 'create'])->name('create');
                Route::post('/confirm', [PengaduanController::class, 'confirm'])->name('confirm');
                Route::post('/', [PengaduanController::class, 'store'])->name('store');
            });

            // Akses pengaduan: mahasiswa, pengurus himpunan, dan staff (dosen/gpm/admin)
            Route::middleware('role:mahasiswa,pengurus_himpunan,ketua_himpunan,wakil_ketua_himpunan,ketua_bidang,ketua_unit,staff_himpunan,dosen,gpm,admin,superadmin,admin_kemahasiswaan')->group(function () {
                Route::get('/', [PengaduanController::class, 'index'])->name('index');
                Route::get('/{pengaduan}', [PengaduanController::class, 'show'])
                    ->whereNumber('pengaduan')
                    ->name('show');
            });

            // Jawab pengaduan — hanya Admin & GPM
            Route::post('/{pengaduan}/reply', [PengaduanController::class, 'reply'])
                ->name('reply')
                ->whereNumber('pengaduan')
                ->middleware('role:admin,superadmin,admin_kemahasiswaan,gpm');

            // Hapus pengaduan — hanya Admin & GPM
            Route::delete('/{pengaduan}', [PengaduanController::class, 'destroy'])
                ->name('destroy')
                ->whereNumber('pengaduan')
                ->middleware('role:admin,superadmin,admin_kemahasiswaan,gpm');
        });

        // ── Forum Diskusi ──────────────────────────────────────────────────
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/create', [ForumController::class, 'create'])->name('create');
            Route::post('/drafts', [ForumController::class, 'saveDraft'])->name('drafts.store');
            Route::delete('/drafts/{id}', [ForumController::class, 'deleteDraft'])->name('drafts.destroy');
            Route::post('/', [ForumController::class, 'store'])->name('store');

            // Report Management (admin only) — MUST be before /{id} wildcard
            Route::middleware('role:superadmin,admin,admin_kemahasiswaan,gpm')->group(function () {
                Route::delete('/reports/{reportId}/dismiss', [ForumController::class, 'dismissReport'])->name('reports.dismiss');
                Route::delete('/reports/{reportId}/delete-thread', [ForumController::class, 'deleteReportedThread'])->name('reports.delete_thread');
                Route::patch('/reports/{reportId}/lock-thread', [ForumController::class, 'lockReportedThread'])->name('reports.lock_thread');
            });

            Route::get('/{id}', [ForumController::class, 'show'])->name('show');
            Route::post('/{id}/vote', [ForumController::class, 'vote'])->name('vote');
            Route::post('/{id}/report', [ForumController::class, 'reportThread'])->name('report');
            Route::patch('/{id}/pin', [ForumController::class, 'pin'])->name('pin');
            Route::patch('/{id}/lock', [ForumController::class, 'lockThread'])->name('lock');
            Route::post('/{id}/personal-pin', [ForumController::class, 'personalPin'])->name('personal_pin');
            Route::get('/{id}/edit', [ForumController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ForumController::class, 'update'])->name('update');
            Route::delete('/{id}', [ForumController::class, 'destroy'])->name('destroy');

            // Best Answer
            Route::post('/{threadId}/best-answer/{commentId}', [ForumController::class, 'markBestAnswer'])->name('best_answer');

            // Comments
            Route::post('/{threadId}/comments', [ForumController::class, 'storeComment'])->name('comments.store');
            Route::post('/comments/{commentId}/vote', [ForumController::class, 'voteComment'])->name('comments.vote');
            Route::put('/comments/{commentId}', [ForumController::class, 'updateComment'])->name('comments.update');
            Route::delete('/comments/{commentId}', [ForumController::class, 'destroyComment'])->name('comments.destroy');
        });

        // ── Kegiatan ──────────────────────────────────────────────────────
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            // View — semua role boleh
            Route::get('/', [KegiatanController::class, 'index'])->name('index');
            Route::get('/{id}', [KegiatanController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // Create/Edit/Delete — hanya pengurus + admin
            Route::middleware('role:pengurus_himpunan,admin_kemahasiswaan,superadmin')->group(function () {
                Route::get('/create', [KegiatanController::class, 'create'])->name('create');
                Route::post('/', [KegiatanController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
                Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
            });
        });

        // ── Manajemen Pengguna (Role Assignment) ─────────────────────────
        Route::middleware('role:admin_kemahasiswaan,admin,superadmin,ketua_himpunan,wakil_ketua_himpunan,ketua_bidang,ketua_unit')
            ->prefix('pengguna')
            ->name('pengguna.')
            ->group(function () {
            Route::get('/', [ManajemenPenggunaController::class, 'index'])->name('index');
            Route::get('/kategori/{category}', [ManajemenPenggunaController::class, 'category'])->name('category');
            Route::post('/users/{user}/update-role', [ManajemenPenggunaController::class, 'updateRole'])->name('update-role');
            Route::post('/check-alumni', [ManajemenPenggunaController::class, 'checkAlumni'])
                ->name('check-alumni')
                ->middleware('role:admin_kemahasiswaan,admin,superadmin');
        });

        // ── Direktori Mahasiswa ───────────────────────────────────────────
        Route::prefix('direktori')->name('direktori.')->group(function () {

            // Subbab: Mahasiswa
            Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

                // Profil sendiri (role mahasiswa dan alumni)
                Route::middleware('role:mahasiswa,alumni')->group(function () {
                    Route::get('/profil', [DirektoriMahasiswaController::class, 'profil'])
                        ->name('profil');
                    Route::get('/profil/cv', [DirektoriMahasiswaController::class, 'generateCvSelf'])
                        ->name('profil.cv');
                });

                // Daftar semua mahasiswa — admin, gpm, pengurus, mahasiswa, alumni
                Route::middleware('role:superadmin,admin,admin_kemahasiswaan,gpm,pengurus_himpunan,mahasiswa,alumni')
                    ->group(function () {
                    Route::get('/', [DirektoriMahasiswaController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [DirektoriMahasiswaController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                });

                // Edit biodata — admin only
                Route::middleware('role:superadmin,admin,admin_kemahasiswaan')->group(function () {
                    Route::get('/{id}/edit', [DirektoriMahasiswaController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [DirektoriMahasiswaController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });

                // Riwayat kegiatan — pengurus + admin
                Route::middleware('role:pengurus_himpunan,superadmin,admin,admin_kemahasiswaan')
                    ->group(function () {
                    Route::post('/{id}/riwayat', [DirektoriMahasiswaController::class, 'storeRiwayat'])
                        ->name('riwayat.store')->where('id', '[0-9]+');
                    Route::put('/riwayat/{riwayatId}', [DirektoriMahasiswaController::class, 'updateRiwayat'])
                        ->name('riwayat.update')->where('riwayatId', '[0-9]+');
                    Route::delete('/riwayat/{riwayatId}', [DirektoriMahasiswaController::class, 'destroyRiwayat'])
                        ->name('riwayat.destroy')->where('riwayatId', '[0-9]+');
                });

                // Generate CV — pengurus + admin
                Route::middleware('role:pengurus_himpunan,superadmin,admin,admin_kemahasiswaan,gpm')
                    ->group(function () {
                    Route::get('/{id}/cv', [DirektoriMahasiswaController::class, 'generateCv'])
                        ->name('cv')->where('id', '[0-9]+');
                    Route::get('/{id}/cv-builder-preview', [DirektoriMahasiswaController::class, 'previewCvBuilder'])
                        ->name('cv-builder-preview')->where('id', '[0-9]+');
                });
            });

            // Subbab: Alumni
            Route::prefix('alumni')->name('alumni.')->group(function () {
                // Profil karir sendiri (role mahasiswa dan alumni)
                Route::middleware('role:mahasiswa,alumni')->group(function () {
                    Route::get('/profil', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'profil'])
                        ->name('profil');
                    Route::put('/profil', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'updateProfil'])
                        ->name('profil.update');
                });

                // Daftar semua alumni — admin, gpm, pengurus, dosen, mahasiswa, alumni
                Route::middleware('role:superadmin,admin,admin_kemahasiswaan,gpm,pengurus_himpunan,mahasiswa,alumni')
                    ->group(function () {
                    Route::get('/', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                });

                // Edit data alumni — admin only
                Route::middleware('role:superadmin,admin,admin_kemahasiswaan')->group(function () {
                    Route::get('/{id}/edit', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });
            });
        });

        // ── Verifikasi Data ─────────────────────────────────────────────
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {

            // Index — semua role boleh akses (view berbeda per role)
            Route::get('/', [VerifikasiController::class, 'index'])->name('index');

            // Submit pengajuan — mahasiswa, alumni, pengurus himpunan
            Route::middleware('role:mahasiswa,alumni,pengurus_himpunan,superadmin,admin,admin_kemahasiswaan')
                ->group(function () {
                    Route::post('/riwayat', [VerifikasiController::class, 'storeRiwayat'])->name('riwayat.store');
                    Route::post('/prestasi', [VerifikasiController::class, 'storePrestasi'])->name('prestasi.store');
                });

            // Approve/Reject — admin & GPM only
            Route::middleware('role:superadmin,admin,admin_kemahasiswaan,gpm')
                ->group(function () {
                    Route::patch('/riwayat/{id}/approve', [VerifikasiController::class, 'approveRiwayat'])
                        ->name('riwayat.approve')->where('id', '[0-9]+');
                    Route::patch('/riwayat/{id}/reject', [VerifikasiController::class, 'rejectRiwayat'])
                        ->name('riwayat.reject')->where('id', '[0-9]+');
                    Route::patch('/prestasi/{id}/approve', [VerifikasiController::class, 'approvePrestasi'])
                        ->name('prestasi.approve')->where('id', '[0-9]+');
                    Route::patch('/prestasi/{id}/reject', [VerifikasiController::class, 'rejectPrestasi'])
                        ->name('prestasi.reject')->where('id', '[0-9]+');
                });
        });

    });