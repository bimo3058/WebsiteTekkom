<?php

use Illuminate\Support\Facades\Route;
use Modules\ManajemenMahasiswa\Http\Controllers\DashboardController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengumumanController;
use Modules\ManajemenMahasiswa\Http\Controllers\KemahasiswaanController;
use Modules\ManajemenMahasiswa\Http\Controllers\ForumController;
use Modules\ManajemenMahasiswa\Http\Controllers\GamificationController;
use Modules\ManajemenMahasiswa\Http\Controllers\AnonPengaduanController;
use Modules\ManajemenMahasiswa\Http\Controllers\PengaduanController;

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
        // Route ini sengaja tanpa 'auth' (magic link dibuka tanpa login), jadi
        // throttle adalah satu-satunya rem terhadap brute force token & spam.
        Route::prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::post('/track/{token}/confirm', [AnonPengaduanController::class, 'confirm'])
                ->middleware('throttle:20,1')->name('anon.confirm');
            Route::post('/track/{token}/store', [AnonPengaduanController::class, 'store'])
                ->middleware('throttle:5,1')->name('anon.store');
            Route::get('/track/{token}', [AnonPengaduanController::class, 'track'])
                ->middleware('throttle:30,1')->name('track');
            Route::get('/track/{token}/bukti/{index}', [AnonPengaduanController::class, 'bukti'])
                ->whereNumber('index')->middleware('throttle:30,1')->name('anon.bukti');
            // Pratinjau bukti yang baru diunggah (draft) — hanya bisa dibuka oleh sesi pengunggah.
            Route::get('/track/{token}/bukti-pending/{index}', [AnonPengaduanController::class, 'buktiPending'])
                ->whereNumber('index')->middleware('throttle:60,1')->name('anon.bukti.pending');
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
            Route::middleware('role:pengurus_himpunan|staff_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|dosen|gpm|admin|admin_kemahasiswaan|superadmin|dpm|ketua_departemen')->group(function () {
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
            Route::middleware('role:ketua_unit|ketua_bidang|ketua_himpunan|admin|admin_kemahasiswaan|superadmin|dpm')
                ->group(function () {
                Route::get('/verifikasi', [PengumumanController::class, 'verifikasiIndex'])
                    ->name('verifikasi.index');
                Route::patch('/verifikasi/{requestId}/approve', [PengumumanController::class, 'approveVerifikasi'])
                    ->name('verifikasi.approve')->whereNumber('requestId');
                Route::patch('/verifikasi/{requestId}/reject', [PengumumanController::class, 'rejectVerifikasi'])
                    ->name('verifikasi.reject')->whereNumber('requestId');
            });

            // Pin Global — hanya admin, superadmin, admin_kemahasiswaan
            Route::patch('/{pengumuman}/pin', [PengumumanController::class, 'pin'])
                ->name('pin')
                ->whereNumber('pengumuman')
                ->middleware('role:superadmin|admin|admin_kemahasiswaan');

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
            // Mahasiswa membuat pengaduan
            // NOTE: HARUS didefinisikan sebelum /{pengaduan} agar tidak konflik dengan path seperti /create
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan')->group(function () {
                Route::get('/jalur', [PengaduanController::class, 'jalur'])->name('jalur');
                Route::get('/create', [PengaduanController::class, 'create'])->name('create');
                Route::get('/bukti-pending/{index}', [PengaduanController::class, 'buktiPending'])
                    ->whereNumber('index')->middleware('throttle:60,1')->name('bukti.pending');
                Route::post('/confirm', [PengaduanController::class, 'confirm'])
                    ->middleware('throttle:20,1')->name('confirm');
                Route::post('/', [PengaduanController::class, 'store'])
                    ->middleware('throttle:5,1')->name('store');

                // Jalur Konfidensial (pembuatan Magic Link).
                // POST, bukan GET: method ini menulis ke database, dan sebagai GET
                // setiap refresh/prefetch membuat satu baris draft baru.
                Route::post('/anon/generate', [AnonPengaduanController::class, 'generate'])
                    ->middleware('throttle:10,1')->name('anon.generate');
            });

            // Akses pengaduan: mahasiswa, pengurus himpunan, dan staff (gpm/admin)
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|dpm|gpm|kaprodi|ketua_departemen|admin|superadmin|admin_kemahasiswaan')->group(function () {
                Route::get('/', [PengaduanController::class, 'index'])->name('index');
                Route::get('/{pengaduan}', [PengaduanController::class, 'show'])
                    ->whereNumber('pengaduan')
                    ->name('show');
                Route::get('/{pengaduan}/bukti/{index}', [PengaduanController::class, 'bukti'])
                    ->whereNumber(['pengaduan', 'index'])
                    ->middleware('throttle:60,1')
                    ->name('bukti');
            });

            // Admin: toggle tercatat
            Route::middleware('role:admin|superadmin|admin_kemahasiswaan|gpm|kaprodi|dpm|ketua_departemen')->group(function () {
                Route::post('/{pengaduan}/toggle-tercatat', [PengaduanController::class, 'toggleTercatat'])
                    ->name('toggle.tercatat')->whereNumber('pengaduan');
            });

            // Hapus pengaduan — hanya Admin & Superadmin
            Route::delete('/{pengaduan}', [PengaduanController::class, 'destroy'])
                ->name('destroy')
                ->whereNumber('pengaduan')
                ->middleware('role:admin|superadmin');
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
            Route::get('/saya', [ForumController::class, 'myThreads'])->name('my');
            Route::get('/leaderboard', [ForumController::class, 'leaderboard'])->name('leaderboard');
            Route::get('/create', [ForumController::class, 'create'])->name('create');
            Route::post('/drafts', [ForumController::class, 'saveDraft'])->name('drafts.store');
            Route::delete('/drafts/{id}', [ForumController::class, 'deleteDraft'])->name('drafts.destroy');
            Route::post('/', [ForumController::class, 'store'])->name('store');

            // Report Management (admin only) — MUST be before /{id} wildcard
            Route::middleware('role:superadmin|admin|admin_kemahasiswaan|gpm|dpm|ketua_departemen')->group(function () {
                Route::get('/laporan', [ForumController::class, 'forumReports'])->name('reports');
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

        // Keputusan pemilik modul (20 Sep 2026): role `admin` biasa TIDAK dipakai di bab Manajemen Kegiatan
        // (Proker, Pelaksanaan, Arsip), Direktori Mahasiswa, maupun Verifikasi Data — begitu pula
        // `dosen_koordinator`. Jangan ditambahkan kembali ke daftar `role:` di tiga bab ini.
        // ── Rencana Proker (Subbab 1 Manajemen Kegiatan) ──────────────────
        // Akses: superadmin, admin_kemahasiswaan, dpm, gpm, ketua_departemen,
        //        ketua_himpunan, ketua_bidang, ketua_unit, staff_himpunan
        Route::prefix('proker')->name('proker.')
            ->middleware('role:superadmin|admin_kemahasiswaan|dpm|gpm|ketua_departemen|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan')
            ->group(function () {
            Route::get('/', [ProkerController::class, 'index'])->name('index');
            Route::get('/{id}', [ProkerController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // GET ke endpoint aksi (PATCH-only) — mis. user mengetik URL .../ajukan
            // langsung di browser — kembalikan 403 ramah, bukan halaman debug 405.
            Route::get('/{id}/ajukan', fn () => abort(403))->where('id', '[0-9]+');

            // Buat proker — hanya ketua hanya ketua & admin. admin kemahasiswaan. staff_himpunan TIDAK boleh membuat,
            // hanya melengkapi/mengedit proker yang sudah dibuat ketua.
            Route::middleware('role:ketua_himpunan|ketua_bidang|ketua_unit|admin_kemahasiswaan|superadmin')
                ->group(function () {
                Route::get('/create', [ProkerController::class, 'create'])->name('create');
                Route::post('/', [ProkerController::class, 'store'])->name('store');
            });

            // Edit proker — ketua, admin kemahasiswaan, dan staff_himpunan (pengurus himpunan
            // melengkapi rencana yang dibuat ketua). GPM, Kadep & DPM view-only.
            Route::middleware('role:ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|admin_kemahasiswaan|superadmin')
                ->group(function () {
                Route::get('/{id}/edit', [ProkerController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [ProkerController::class, 'update'])->name('update')->where('id', '[0-9]+');
            });

            // Ajukan ke tahap Pelaksanaan — kewenangan ketua saja, sinkron dengan
            // whitelist di ProkerController::ajukan(). DPM = pembina himpunan: memantau saja.
            Route::middleware('role:superadmin|ketua_himpunan|ketua_bidang|ketua_unit')
                ->group(function () {
                Route::patch('/{id}/ajukan', [ProkerController::class, 'ajukan'])->name('ajukan')->where('id', '[0-9]+');
            });

            // Hapus — admin kemahasiswaan & pengurus inti himpunan (GPM, Kadep & DPM view-only)
            Route::middleware('role:admin_kemahasiswaan|superadmin|ketua_himpunan|ketua_bidang|ketua_unit')
                ->group(function () {
                Route::delete('/{id}', [ProkerController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });

        });



        // ── Pelaksanaan Kegiatan (Subbab 2 Manajemen Kegiatan) ────────────
        // Akses: superadmin, admin_kemahasiswaan, dpm, gpm, ketua_departemen,
        //        ketua_himpunan, ketua_bidang, ketua_unit, staff_himpunan
        Route::prefix('pelaksanaan')->name('pelaksanaan.')
            ->middleware('role:superadmin|admin_kemahasiswaan|dpm|gpm|ketua_departemen|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan')
            ->group(function () {
            Route::get('/', [PelaksanaanController::class, 'index'])->name('index');
            Route::get('/{id}', [PelaksanaanController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // GET ke endpoint aksi (POST-only) — mis. user mengetik URL .../publish
            // langsung di browser — kembalikan 403 ramah, bukan halaman debug 405.
            Route::get('/{id}/publish', fn () => abort(403))->where('id', '[0-9]+');

            // Edit & publish — GPM, Kadep & DPM hanya lihat (view-only)
            Route::middleware('role:ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|admin_kemahasiswaan|superadmin')
                ->group(function () {
                Route::get('/{id}/edit', [PelaksanaanController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [PelaksanaanController::class, 'update'])->name('update')->where('id', '[0-9]+');
                Route::post('/{id}/publish', [PelaksanaanController::class, 'publishToArsip'])->name('publish')->where('id', '[0-9]+');
            });

            // Hapus — admin kemahasiswaan, superadmin, dan ketua-ketua himpunan (GPM, Kadep & DPM view-only)
            Route::middleware('role:admin_kemahasiswaan|superadmin|ketua_himpunan|ketua_bidang|ketua_unit')
                ->group(function () {
                Route::delete('/{id}', [PelaksanaanController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });
        });

        // ── Kegiatan / Laporan & Arsip (Subbab 3 Manajemen Kegiatan) ──────
        Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
            // View — semua role boleh
            Route::get('/', [KegiatanController::class, 'index'])->name('index');
            Route::get('/{id}', [KegiatanController::class, 'show'])->name('show')->where('id', '[0-9]+');

            // Tambah Kegiatan — hanya role kurasi admin kemahasiswaan di luar alur himpunan.
            // Role himpunan wajib lewat alur Proker → Pelaksanaan → publish; DPM = pembina view-only.
            Route::middleware('role:admin_kemahasiswaan|superadmin')->group(function () {
                Route::get('/create', [KegiatanController::class, 'create'])->name('create');
                Route::post('/', [KegiatanController::class, 'store'])->name('store');
            });

            // Edit & Hapus — ketua + admin kemahasiswaan (GPM, Kadep, DPM & staff_himpunan TIDAK termasuk)
            // Batasi {id} ke numerik (selaras grup Proker & Pelaksanaan) agar segmen non-numerik
            // tidak jatuh ke route PUT/DELETE ini dan memunculkan 405.
            Route::middleware('role:ketua_himpunan|ketua_bidang|ketua_unit|admin_kemahasiswaan|superadmin')->group(function () {
                Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [KegiatanController::class, 'update'])->name('update')->where('id', '[0-9]+');
                Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });

            // Segmen non-numerik di /kegiatan/{...} — mis. user UAT mengetik .../kegiatan/proker.
            // Tanpa ini, segmen non-numerik jatuh ke route PUT/DELETE /kegiatan/{id} dan memunculkan
            // halaman debug 405 (MethodNotAllowed tidak ditangani handler global). Kembalikan 403 agar
            // diteruskan ke halaman "Akses Ditolak". Harus diletakkan paling akhir agar tidak menutup
            // route /create (alpha) yang sudah terdaftar lebih dulu.
            Route::get('/{slug}', fn () => abort(403))->where('slug', '[A-Za-z].*');
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
                //
                // /profil tidak lagi punya halaman sendiri: isinya sama persis dengan
                // halaman detail, jadi sekarang ia hanya mendaftarkan pemiliknya dari
                // SSO bila perlu lalu melempar ke /{id}. Route-nya dipertahankan supaya
                // tautan & bookmark lama tidak mati.
                Route::middleware('role:mahasiswa|alumni')->group(function () {
                    Route::get('/profil', [DirektoriMahasiswaController::class, 'profil'])
                        ->name('profil');
                    Route::get('/profil/cv', [DirektoriMahasiswaController::class, 'generateCvSelf'])
                        ->name('profil.cv');
                });

                // Daftar semua mahasiswa — semua role boleh lihat index dan profil
                Route::middleware('role:superadmin|admin_kemahasiswaan|gpm|ketua_departemen|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|dosen|dpm|mahasiswa|alumni')
                    ->group(function () {
                    Route::get('/', [DirektoriMahasiswaController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [DirektoriMahasiswaController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                });

                // Unduh CV mahasiswa — pengelola & pembina (bukan sesama mahasiswa).
                // Harus didaftarkan sebelum /{id}/edit tidak masalah karena segmen
                // keduanya berbeda; /profil/cv sudah terdaftar lebih dulu di atas.
                //
                // Daftar role sengaja tidak ditulis ulang di sini: CvProfilePolicy
                // adalah sumber kebenarannya, dipakai juga oleh Policy check di
                // controller dan oleh flag $canDownloadCv di halaman show — supaya
                // ketiganya mustahil berbeda seperti sebelumnya.
                Route::middleware('role:' . implode('|', \App\Policies\CvProfilePolicy::PENGELOLA_CV))
                    ->group(function () {
                    Route::get('/{id}/cv', [DirektoriMahasiswaController::class, 'generateCv'])
                        ->name('cv')->where('id', '[0-9]+');
                });

                // Edit biodata — admin kemahasiswaan (semua mahasiswa) + mahasiswa/alumni (dirinya sendiri).
                //
                // Gerbang role di sini sengaja longgar; yang menentukan BARIS MILIK SIAPA
                // adalah KemahasiswaanPolicy::update() yang dipanggil di edit() dan
                // update(). Tanpa Policy itu, setiap mahasiswa yang lolos gerbang ini bisa
                // mengetik /{id}/edit milik orang lain. Kolom Status juga hanya ditulis
                // untuk pengelola — lihat update() di controller.
                Route::middleware('role:superadmin|admin_kemahasiswaan|mahasiswa|alumni')->group(function () {
                    Route::get('/{id}/edit', [DirektoriMahasiswaController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [DirektoriMahasiswaController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });

                // Riwayat kegiatan manual DIHAPUS dari direktori.
                // Alur resmi penambahan riwayat kegiatan sekarang ada di modul
                // Verifikasi Data (mahasiswa mengajukan → pengurus/admin kemahasiswaan memverifikasi).

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

                // Daftar semua alumni — admin kemahasiswaan, gpm, pengurus, dosen, mahasiswa, alumni
                Route::middleware('role:superadmin|admin_kemahasiswaan|gpm|ketua_departemen|dosen|dpm|pengurus_himpunan|mahasiswa|alumni')
                    ->group(function () {
                    Route::get('/', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'index'])
                        ->name('index');
                    Route::get('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'show'])
                        ->name('show')->where('id', '[0-9]+');
                });

                // Unduh CV alumni — pengelola & pembina, gerbang yang sama persis
                // dengan CV mahasiswa. Method generateCv() sudah lama ada tapi tidak
                // punya route sama sekali (dead code); di sini baru dihidupkan.
                Route::middleware('role:' . implode('|', \App\Policies\CvProfilePolicy::PENGELOLA_CV))
                    ->group(function () {
                    Route::get('/{id}/cv', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'generateCv'])
                        ->name('cv')->where('id', '[0-9]+');
                });

                // Edit data alumni — admin kemahasiswaan only
                Route::middleware('role:superadmin|admin_kemahasiswaan')->group(function () {
                    Route::get('/{id}/edit', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'edit'])
                        ->name('edit')->where('id', '[0-9]+');
                    Route::put('/{id}', [\Modules\ManajemenMahasiswa\Http\Controllers\DirektoriAlumniController::class, 'update'])
                        ->name('update')->where('id', '[0-9]+');
                });

                // Riwayat kegiatan & prestasi alumni — admin kemahasiswaan only (tambahkan role di sini jika diperlukan)
                Route::middleware('role:superadmin|admin_kemahasiswaan')
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

            // Index — mahasiswa, alumni (read-only), pengurus himpunan, admin kemahasiswaan
            // (verifikator), serta Ketua Departemen & GPM (read-only, tanpa approve/reject).
            //
            // GPM sempat dikecualikan total (2 Sep 2026), lalu dikembalikan read-only
            // disamakan dengan Ketua Departemen. DPM sendiri sempat singgah di sini
            // juga (read-only) setelah sebelumnya verifikator penuh, tapi pemilik
            // modul langsung mengoreksi: DPM tidak perlu bisa melihat bab Verifikasi
            // Data mahasiswa SAMA SEKALI — dicabut total, bukan dijadikan read-only.
            // Lihat VerifikasiController::isPengawas().
            Route::middleware('role:mahasiswa|alumni|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin_kemahasiswaan|ketua_departemen|gpm')
                ->get('/', [VerifikasiController::class, 'index'])->name('index');

            // Berkas bukti — satu-satunya pintu menuju sertifikat mahasiswa.
            //
            // Sebelumnya berkas ditempel langsung memakai URL publik Supabase,
            // yang tidak pernah menanyakan siapa yang membukanya: sekali
            // tautannya tersalin keluar, sertifikat beserta nama & NIM di
            // dalamnya terbuka untuk siapa pun tanpa akun, selamanya. Daftar
            // rolenya disalin dari route index di atas (DPM ikut dikecualikan
            // dengan alasan yang sama) — dan kepemilikan berkasnya dicek lagi
            // di controller.
            Route::middleware('role:mahasiswa|alumni|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin_kemahasiswaan|ketua_departemen|gpm')
                ->get('/bukti/{id}', [VerifikasiController::class, 'bukti'])
                ->name('bukti.show')->where('id', '[0-9]+');

            // Submit pengajuan — mahasiswa, semua pengurus himpunan, admin kemahasiswaan (alumni TIDAK diizinkan)
            Route::middleware('role:mahasiswa|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin_kemahasiswaan')
                ->group(function () {
                Route::post('/riwayat', [VerifikasiController::class, 'storeRiwayat'])->name('riwayat.store');
                Route::post('/prestasi', [VerifikasiController::class, 'storePrestasi'])->name('prestasi.store');

                // Tarik pengajuan sendiri selama masih menunggu — kepemilikan &
                // status dicek di controller. Tanpa ini salah ketik hanya bisa
                // dibetulkan dengan menunggu admin menolak lebih dulu.
                Route::delete('/riwayat/{id}', [VerifikasiController::class, 'destroyRiwayat'])
                    ->name('riwayat.destroy')->where('id', '[0-9]+');
                Route::delete('/prestasi/{id}', [VerifikasiController::class, 'destroyPrestasi'])
                    ->name('prestasi.destroy')->where('id', '[0-9]+');

                // Pengajuan reward prestasi — dilakukan mahasiswa pemilik (Request Bu Bellia / B.2)
                Route::patch('/prestasi/{id}/reward/ajukan', [VerifikasiController::class, 'ajukanReward'])
                    ->name('prestasi.reward.ajukan')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/reward/batal', [VerifikasiController::class, 'batalkanReward'])
                    ->name('prestasi.reward.batal')->where('id', '[0-9]+');
            });

            // Klaim Prestasi — subbab ke-3 Verifikasi Data (dulu cuma tombol
            // "Klaim Reward" di header Verifikasi Prestasi, sekarang halaman
            // sendiri). Daftar rolenya disamakan persis dengan route index di
            // atas: audiens subbab ini sama dengan 2 subbab lain di bab yang
            // sama — mahasiswa/pengurus/alumni melihat & mengajukan reward
            // miliknya sendiri, admin kemahasiswaan mengelola, Ketua Departemen
            // & GPM memantau read-only (lihat VerifikasiController::rewardIndex()).
            // DPM TIDAK termasuk — sama dengan route index/bukti di atas.
            Route::middleware('role:mahasiswa|alumni|pengurus_himpunan|ketua_himpunan|ketua_bidang|ketua_unit|staff_himpunan|superadmin|admin_kemahasiswaan|ketua_departemen|gpm')
                ->get('/klaim-reward', [VerifikasiController::class, 'rewardIndex'])
                ->name('reward.index');

            // Verifikasi prestasi & riwayat kegiatan — verifikator (admin kemahasiswaan).
            //
            // DPM sebelumnya verifikator penuh di sini, lalu sempat diturunkan
            // jadi read-only — tapi keputusan akhirnya DPM dicabut total dari
            // seluruh bab Verifikasi Data (lihat route index di atas &
            // VerifikasiController::isPengawas()), jadi otomatis tidak pernah
            // sampai ke grup route ini juga.
            Route::middleware('role:superadmin|admin_kemahasiswaan')
                ->group(function () {
                Route::patch('/riwayat/{id}/approve', [VerifikasiController::class, 'approveRiwayat'])
                    ->name('riwayat.approve')->where('id', '[0-9]+');
                Route::patch('/riwayat/{id}/reject', [VerifikasiController::class, 'rejectRiwayat'])
                    ->name('riwayat.reject')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/approve', [VerifikasiController::class, 'approvePrestasi'])
                    ->name('prestasi.approve')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/reject', [VerifikasiController::class, 'rejectPrestasi'])
                    ->name('prestasi.reject')->where('id', '[0-9]+');

                // Kembalikan keputusan ke "menunggu". Persetujuan/penolakan
                // sebelumnya final tanpa jalan pulang, sehingga satu klik keliru
                // tidak bisa dibetulkan lewat aplikasi.
                Route::patch('/riwayat/{id}/batal-verifikasi', [VerifikasiController::class, 'batalkanVerifikasiRiwayat'])
                    ->name('riwayat.batal')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/batal-verifikasi', [VerifikasiController::class, 'batalkanVerifikasiPrestasi'])
                    ->name('prestasi.batal')->where('id', '[0-9]+');
            });

            // Keputusan reward prestasi — admin kemahasiswaan saja.
            //
            // Lebih sempit dari verifikasi di atas: yang diputus di sini bukan
            // benar-tidaknya sebuah prestasi, melainkan konversi nilai mata
            // kuliah (SK FT 774). Ketua Departemen & GPM tetap boleh membuka
            // halaman Klaim Reward untuk memantau, tetapi tidak menyetujui,
            // menolak, maupun membatalkan persetujuannya. DPM tidak bisa
            // membuka halaman itu sama sekali (lihat route reward.index).
            Route::middleware('role:superadmin|admin_kemahasiswaan')
                ->group(function () {
                Route::patch('/prestasi/{id}/reward/setujui', [VerifikasiController::class, 'setujuiReward'])
                    ->name('prestasi.reward.setujui')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/reward/tolak', [VerifikasiController::class, 'tolakReward'])
                    ->name('prestasi.reward.tolak')->where('id', '[0-9]+');
                Route::patch('/prestasi/{id}/reward/batalkan-persetujuan', [VerifikasiController::class, 'batalkanPersetujuanReward'])
                    ->name('prestasi.reward.batalkan')->where('id', '[0-9]+');
            });

        });

    });
