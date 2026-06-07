<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KemahasiswaanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ForumController;
use Modules\ManajemenMahasiswa\Http\Controllers\GamificationController;
use Modules\ManajemenMahasiswa\Http\Controllers\AnonPengaduanController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengaduanController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengaduanDelegasiController;
use Modules\ManajemenMahasiswa\Http\Controllers\KegiatanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ProkerController;
use Modules\ManajemenMahasiswa\Http\Controllers\PelaksanaanController;
use Modules\ManajemenMahasiswa\Http\Controllers\DirektoriMahasiswaController;
use Modules\ManajemenMahasiswa\Http\Controllers\ManajemenPenggunaController;
use Modules\ManajemenMahasiswa\Http\Controllers\VerifikasiController;

Route::middleware(['module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {
        // ── Layanan Pengaduan (Publik / Magic Link) ──────────────────────
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::post('/track/{token}/confirm', [AnonPengaduanController::class, 'confirm'])->name('anon.confirm');
            Route::post('/track/{token}/store', [AnonPengaduanController::class, 'store'])->name('anon.store');
            Route::get('/track/{token}', [AnonPengaduanController::class, 'track'])->name('track');
            Route::post('/track/{token}/close', [AnonPengaduanController::class, 'close'])->name('track.close');
            Route::post('/track/{token}/reopen', [AnonPengaduanController::class, 'reopen'])->name('track.reopen');
        });
    });

Route::middleware(['auth', 'module.active:manajemen_mahasiswa'])
    ->prefix('manajemen-mahasiswa')
    ->name('manajemenmahasiswa.')
    ->group(function () {

        // Dashboard Utama Modul — semua role boleh akses, renderDashboard() menentukan view sesuai role
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Modal data endpoint untuk dashboard analitik (AJAX)
        Route::get('/dashboard/modal-data', [DashboardController::class, 'modalData'])
            ->name('dashboard.modal')
            ->middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dpm|ketua_departemen');

        // Switch tampilan dashboard antar-role (untuk user multi-role)
        Route::post('/dashboard/switch-mode', [DashboardController::class, 'switchMode'])
            ->name('switch.mode');

        // ── Pengurus Himpunan ─────────────────────────────────────────────
        Route::middleware('role:pengurus_himpunan|gpm|admin|admin_kemahasiswaan|superadmin|dpm|ketua_departemen')
            ->prefix('pengurus')
            ->name('pengurus.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.pengurus');
            })->name('dashboard');
        });

        // ── Alumni ────────────────────────────────────────────────────────
        Route::middleware('role:alumni|gpm|admin|admin_kemahasiswaan|superadmin|dpm|ketua_departemen')
            ->prefix('alumni')
            ->name('alumni.')
            ->group(function () {
            Route::get('/dashboard', function () {
                return view('manajemenmahasiswa::dashboard.alumni');
            })->name('dashboard');
        });

        // ── Dosen ─────────────────────────────────────────────────────────
        Route::middleware('role:dosen|dosen_koordinator|dpm|gpm|ketua_departemen|admin|admin_kemahasiswaan|superadmin')
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

            // Create/Edit/Delete — pengurus + admin + staff_himpunan
            Route::middleware('role:pengurus_himpunan|staff_himpunan|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|dosen|gpm|admin|admin_kemahasiswaan|superadmin|dpm|ketua_departemen')->group(function () {
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

            // Verification Request — staff himpunan yang ingin publish
            Route::middleware('role:staff_himpunan|superadmin')->group(function () {
                Route::get('/{pengumuman}/verification-request', [PengumumanController::class, 'verificationRequest'])
                    ->name('verification.request')->whereNumber('pengumuman');
                Route::post('/{pengumuman}/verification-request', [PengumumanController::class, 'submitVerificationRequest'])
                    ->name('verification.submit')->whereNumber('pengumuman');
                Route::delete('/{pengumuman}/verification-request', [PengumumanController::class, 'cancelVerificationRequest'])
                    ->name('verification.cancel')->whereNumber('pengumuman');
            });

            // Riwayat & Status Verifikasi — untuk staff himpunan melihat request yang pernah diajukan
            Route::middleware('role:staff_himpunan|superadmin')
                ->get('/riwayat-verifikasi', [PengumumanController::class, 'riwayatVerifikasiStaff'])
                ->name('riwayat.verifikasi');

            // Verifikasi Dashboard — ketua himpunan + admin kemahasiswaan
            Route::middleware('role:ketua_unit|ketua_bidang|ketua_himpunan|wakil_ketua_himpunan|admin|admin_kemahasiswaan|superadmin')
                ->group(function () {
                Route::get('/verifikasi', [PengumumanController::class, 'verifikasiIndex'])
                    ->name('verifikasi.index');
                Route::patch('/verifikasi/{requestId}/approve', [PengumumanController::class, 'approveVerifikasi'])
                    ->name('verifikasi.approve')->whereNumber('requestId');
                Route::patch('/verifikasi/{requestId}/reject', [PengumumanController::class, 'rejectVerifikasi'])
                    ->name('verifikasi.reject')->whereNumber('requestId');
            });

            // Pin Global — hanya admin, superadmin, admin_kemahasiswaan, gpm
            Route::patch('/{pengumuman}/pin', [PengumumanController::class, 'pin'])
                ->name('pin')
                ->whereNumber('pengumuman')
                ->middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dpm|ketua_departemen');

            // Pin Pribadi — semua user terautentikasi (dilindungi auth di level parent)
            Route::post('/{pengumuman}/personal-pin', [PengumumanController::class, 'personalPin'])
                ->name('personal_pin')
                ->whereNumber('pengumuman');

            // Download lampiran — semua role boleh
            Route::get('/lampiran/{lampiran}/download', [PengumumanController::class, 'downloadLampiran'])->name('lampiran.download');

            // Show — semua role boleh (HARUS setelah /create agar tidak konflik)
            Route::get('/{pengumuman}', [PengumumanController::class, 'show'])->name('show');
        });

        // ── Layanan Pengaduan ─────────────────────────────────────────────
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            // Jalur Anonim / Konfidensial (Pembuatan Magic Link)
            Route::get('/anon/generate', [AnonPengaduanController::class, 'generate'])->name('anon.generate');

            // Mahasiswa membuat pengaduan
            // NOTE: HARUS didefinisikan sebelum /{pengaduan} agar tidak konflik dengan path seperti /create
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan')->group(function () {
                Route::get('/jalur', [PengaduanController::class, 'jalur'])->name('jalur');
                Route::get('/create', [PengaduanController::class, 'create'])->name('create');
                Route::post('/confirm', [PengaduanController::class, 'confirm'])->name('confirm');
                Route::post('/', [PengaduanController::class, 'store'])->name('store');

                // Mahasiswa: tandai selesai & ajukan ulang
                Route::post('/{pengaduan}/close', [PengaduanController::class, 'closeByMahasiswa'])
                    ->name('close')->whereNumber('pengaduan');
                Route::post('/{pengaduan}/reopen', [PengaduanController::class, 'reopen'])
                    ->name('reopen')->whereNumber('pengaduan');
            });

            // Akses pengaduan: mahasiswa, pengurus himpunan, dan staff (dosen/gpm/admin)
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|dosen|dosen_koordinator|dpm|gpm|ketua_departemen|admin|superadmin|admin_kemahasiswaan')->group(function () {
                Route::get('/', [PengaduanController::class, 'index'])->name('index');
                Route::get('/{pengaduan}', [PengaduanController::class, 'show'])
                    ->whereNumber('pengaduan')
                    ->name('show');
            });

            // Admin: jawab langsung, delegasi, forward, tutup paksa
            Route::middleware('role:admin|superadmin|admin_kemahasiswaan|gpm|dpm|ketua_departemen')->group(function () {
                Route::post('/{pengaduan}/reply', [PengaduanController::class, 'reply'])
                    ->name('reply')->whereNumber('pengaduan');
                Route::post('/{pengaduan}/delegate', [PengaduanController::class, 'delegate'])
                    ->name('delegate')->whereNumber('pengaduan');
                Route::post('/{pengaduan}/forward', [PengaduanController::class, 'forwardAnswer'])
                    ->name('forward')->whereNumber('pengaduan');
                Route::post('/{pengaduan}/close-admin', [PengaduanController::class, 'closeByAdmin'])
                    ->name('close.admin')->whereNumber('pengaduan');
            });

            // Hapus pengaduan — hanya Admin & GPM
            Route::delete('/{pengaduan}', [PengaduanController::class, 'destroy'])
                ->name('destroy')
                ->whereNumber('pengaduan')
                ->middleware('role:admin|superadmin|admin_kemahasiswaan|gpm|dpm|ketua_departemen');

            // ── Delegasi (khusus Dosen) ────────────────────────────────────
            Route::prefix('delegasi')->name('delegasi.')->middleware('role:dosen|dosen_koordinator')->group(function () {
                Route::post('/{delegasi}/respond', [PengaduanDelegasiController::class, 'respond'])
                    ->name('respond')->whereNumber('delegasi');
                Route::post('/{delegasi}/reject', [PengaduanDelegasiController::class, 'reject'])
                    ->name('reject')->whereNumber('delegasi');
            });
        });

        // ── Forum Notifications (AJAX) ────────────────────────────────────
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [ForumController::class, 'getNotifications'])->name('index');
            Route::post('/read/{id}', [ForumController::class, 'markNotificationRead'])->name('read');
            Route::post('/read-all', [ForumController::class, 'markAllNotificationsRead'])->name('read_all');
        });

        // ── Forum Diskusi ──────────────────────────────────────────────────
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/', [ForumController::class, 'index'])->name('index');
            Route::get('/create', [ForumController::class, 'create'])->name('create');
            Route::post('/drafts', [ForumController::class, 'saveDraft'])->name('drafts.store');
            Route::delete('/drafts/{id}', [ForumController::class, 'deleteDraft'])->name('drafts.destroy');
            Route::post('/', [ForumController::class, 'store'])->name('store');

            // Report Management (admin only) — MUST be before /{id} wildcard
            Route::middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dpm|ketua_departemen')->group(function () {
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

            // Poll Vote
            Route::post('/{threadId}/poll/vote', [ForumController::class, 'votePoll'])->name('poll.vote');

            // Poll Close/Open (thread owner only)
            Route::patch('/{threadId}/poll/close', [ForumController::class, 'closePoll'])->name('poll.close');

            // Best Answer
            Route::post('/{threadId}/best-answer/{commentId}', [ForumController::class, 'markBestAnswer'])->name('best_answer');

            // Comments
            Route::post('/{threadId}/comments', [ForumController::class, 'storeComment'])->name('comments.store');
            Route::post('/comments/{commentId}/vote', [ForumController::class, 'voteComment'])->name('comments.vote');
            Route::put('/comments/{commentId}', [ForumController::class, 'updateComment'])->name('comments.update');
            Route::delete('/comments/{commentId}', [ForumController::class, 'destroyComment'])->name('comments.destroy');
        });

        // ── Rencana Proker (Subbab 1 Manajemen Kegiatan) ──────────────────
        // Akses: superadmin, admin_kemahasiswaan, dpm, gpm, wakil_ketua_himpunan,
        //        ketua_himpunan, ketua_bidang, ketua_unit
        Route::prefix('proker')->name('proker.')
            ->middleware('role:superadmin|admin_kemahasiswaan|dpm|gpm|wakil_ketua_himpunan|ketua_himpunan|ketua_bidang|ketua_unit')
            ->group(function () {
            Route::get('/', [ProkerController::class, 'index'])->name('index');
            Route::get('/{id}', [ProkerController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // Pengurus: buat, edit, ajukan
            Route::middleware('role:ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|admin_kemahasiswaan|superadmin|gpm|dpm')
                ->group(function () {
                Route::get('/create', [ProkerController::class, 'create'])->name('create');
                Route::post('/', [ProkerController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [ProkerController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [ProkerController::class, 'update'])->name('update')->where('id', '[0-9]+');
                Route::patch('/{id}/ajukan', [ProkerController::class, 'ajukan'])->name('ajukan')->where('id', '[0-9]+');
            });

            // Hapus — admin, gpm, dpm, pengurus inti himpunan
            Route::middleware('role:admin_kemahasiswaan|superadmin|gpm|dpm|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit')
                ->group(function () {
                Route::delete('/{id}', [ProkerController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });

        });



        // ── Pelaksanaan Kegiatan (Subbab 2 Manajemen Kegiatan) ────────────
        // Akses: superadmin, admin_kemahasiswaan, dpm, gpm, wakil_ketua_himpunan,
        //        ketua_himpunan, ketua_bidang, ketua_unit, staff_himpunan
        Route::prefix('pelaksanaan')->name('pelaksanaan.')
            ->middleware('role:superadmin|admin_kemahasiswaan|dpm|gpm|wakil_ketua_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan')
            ->group(function () {
            Route::get('/', [PelaksanaanController::class, 'index'])->name('index');
            Route::get('/{id}', [PelaksanaanController::class, 'show'])->name('show')->where('id', '[0-9]+');

            Route::middleware('role:ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|admin_kemahasiswaan|superadmin|gpm|dpm')
                ->group(function () {
                Route::get('/{id}/edit', [PelaksanaanController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [PelaksanaanController::class, 'update'])->name('update')->where('id', '[0-9]+');
                Route::post('/{id}/publish', [PelaksanaanController::class, 'publishToArsip'])->name('publish')->where('id', '[0-9]+');
            });

            // Hapus — admin kemahasiswaan, superadmin, gpm, dpm, dan ketua-ketua himpunan
            Route::middleware('role:admin_kemahasiswaan|superadmin|gpm|dpm|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit')
                ->group(function () {
                Route::delete('/{id}', [PelaksanaanController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });
        });

        // ── Kegiatan / Laporan & Arsip (Subbab 3 Manajemen Kegiatan) ──────
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            // View — semua role boleh
            Route::get('/', [KegiatanController::class, 'index'])->name('index');
            Route::get('/{id}', [KegiatanController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // Tambah Kegiatan — pengurus semua jenjang (termasuk staff) + admin + dosen pengawas
            Route::middleware('role:pengurus_himpunan|admin_kemahasiswaan|superadmin|gpm|dpm')->group(function () {
                Route::get('/create', [KegiatanController::class, 'create'])->name('create');
                Route::post('/', [KegiatanController::class, 'store'])->name('store');
            });

            // Edit & Hapus — hanya ketua + admin + dosen pengawas (staff_himpunan TIDAK termasuk)
            Route::middleware('role:ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|admin_kemahasiswaan|superadmin|gpm|dpm')->group(function () {
                Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
                Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
                Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
            });
        });

        // ── Manajemen Pengguna (Role Assignment) ─────────────────────────
        Route::middleware('role:admin_kemahasiswaan|admin|superadmin|ketua_himpunan|ketua_bidang|ketua_unit')
            ->prefix('pengguna')
            ->name('pengguna.')
            ->group(function () {
            Route::get('/', [ManajemenPenggunaController::class, 'index'])->name('index');
            Route::get('/kategori/{category}', [ManajemenPenggunaController::class, 'category'])->name('category');
            Route::post('/users/{user}/update-role', [ManajemenPenggunaController::class, 'updateRole'])->name('update-role');
            Route::post('/check-alumni', [ManajemenPenggunaController::class, 'checkAlumni'])
                ->name('check-alumni')
                ->middleware('role:admin_kemahasiswaan|admin|superadmin');
            Route::post('/reset-pengurus', [ManajemenPenggunaController::class, 'resetPengurusRoles'])
                ->name('reset-pengurus')
                ->middleware('role:admin_kemahasiswaan|admin|superadmin');
        });

        // ── Direktori Mahasiswa ───────────────────────────────────────────
        Route::prefix('direktori')->name('direktori.')->group(function () {

            // Subbab: Mahasiswa
            Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

                // Profil sendiri (role mahasiswa dan alumni)
                Route::middleware('role:mahasiswa|alumni')->group(function () {
                    Route::get('/profil', [DirektoriMahasiswaController::class, 'profil'])
                        ->name('profil');
                    Route::get('/profil/cv', [DirektoriMahasiswaController::class, 'generateCvSelf'])
                        ->name('profil.cv');
                });

                // Daftar semua mahasiswa — semua role boleh lihat index dan profil
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|pengurus_himpunan|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|dosen|dosen_koordinator|dpm|mahasiswa|alumni')
                    ->group(function () {
                    Route::get('/', [DirektoriMahasiswaController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [DirektoriMahasiswaController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                });

                // Edit biodata — admin only
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan')->group(function () {
                    Route::get('/{id}/edit', [DirektoriMahasiswaController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [DirektoriMahasiswaController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });

                // Riwayat kegiatan — pengurus + admin
                Route::middleware('role:pengurus_himpunan|superadmin|admin|admin_kemahasiswaan')
                    ->group(function () {
                    Route::post('/{id}/riwayat', [DirektoriMahasiswaController::class, 'storeRiwayat'])
                        ->name('riwayat.store')->where('id', '[0-9]+');
                    Route::put('/riwayat/{riwayatId}', [DirektoriMahasiswaController::class, 'updateRiwayat'])
                        ->name('riwayat.update')->where('riwayatId', '[0-9]+');
                    Route::delete('/riwayat/{riwayatId}', [DirektoriMahasiswaController::class, 'destroyRiwayat'])
                        ->name('riwayat.destroy')->where('riwayatId', '[0-9]+');
                });

                // Generate CV — admin group, GPM, DPM, dan Dosen
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dpm|dosen|dosen_koordinator')
                    ->group(function () {
                    Route::get('/{id}/cv', [DirektoriMahasiswaController::class, 'generateCv'])
                        ->name('cv')->where('id', '[0-9]+');
                });
            });

            // Subbab: Alumni
            Route::prefix('alumni')->name('alumni.')->group(function () {
                // Profil karir sendiri (role mahasiswa dan alumni)
                Route::middleware('role:mahasiswa|alumni')->group(function () {
                    Route::get('/profil', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'profil'])
                        ->name('profil');
                    Route::get('/profil/cv', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'generateCvSelf'])
                        ->name('profil.cv');
                    Route::put('/profil', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'updateProfil'])
                        ->name('profil.update');
                });

                // Daftar semua alumni — admin, gpm, pengurus, dosen, mahasiswa, alumni
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dosen|dosen_koordinator|dpm|pengurus_himpunan|mahasiswa|alumni')
                    ->group(function () {
                    Route::get('/', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                    Route::get('/{id}/cv', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'generateCv'])
                        ->name('cv')->where('id', '[0-9]+');
                });

                // Edit data alumni — admin only
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan')->group(function () {
                    Route::get('/{id}/edit', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });

                // Riwayat kegiatan & prestasi alumni — admin only (tambahkan role di sini jika diperlukan)
                Route::middleware('role:superadmin|admin|admin_kemahasiswaan')
                    ->group(function () {
                    // Riwayat kegiatan
                    Route::post('/{id}/riwayat', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'storeRiwayat'])
                        ->name('riwayat.store')->where('id', '[0-9]+');
                    Route::put('/riwayat/{riwayatId}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'updateRiwayat'])
                        ->name('riwayat.update')->where('riwayatId', '[0-9]+');
                    Route::delete('/riwayat/{riwayatId}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'destroyRiwayat'])
                        ->name('riwayat.destroy')->where('riwayatId', '[0-9]+');
                    // Prestasi
                    Route::post('/{id}/prestasi', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'storePrestasi'])
                        ->name('prestasi.store')->where('id', '[0-9]+');
                    Route::delete('/prestasi/{prestasiId}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'destroyPrestasi'])
                        ->name('prestasi.destroy')->where('prestasiId', '[0-9]+');
                });
            });
        });

        // ── Verifikasi Data ─────────────────────────────────────────────
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {

            // Index — mahasiswa, alumni (read-only), pengurus himpunan, dan admin
            Route::middleware('role:mahasiswa|alumni|pengurus_himpunan|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin|admin_kemahasiswaan')
                ->get('/', [VerifikasiController::class, 'index'])->name('index');

            // Submit pengajuan — mahasiswa, semua pengurus himpunan, admin (alumni TIDAK diizinkan)
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|wakil_ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin|admin_kemahasiswaan')
                ->group(function () {
                Route::post('/riwayat', [VerifikasiController::class, 'storeRiwayat'])->name('riwayat.store');
                Route::post('/prestasi', [VerifikasiController::class, 'storePrestasi'])->name('prestasi.store');
            });

            // Approve/Reject — admin only
            Route::middleware('role:superadmin|admin|admin_kemahasiswaan')
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