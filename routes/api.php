<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes — Sistem Manajemen Praktikum
|--------------------------------------------------------------------------
*/

// ─── Auth (Public) ────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// ─── Auth (Protected — perlu token) ──────────────────────────────────────────
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::get('/me',           [AuthController::class, 'me']);
    Route::post('/switch-role', [AuthController::class, 'switchRole']);
    Route::post('/logout',      [AuthController::class, 'logout']);
});

// ─── Admin Routes (is_admin — strict, harus tepat admin) ──────────────────────
Route::prefix('admin')->middleware(['auth:sanctum', 'is_admin'])->group(function () {
    // Test endpoint untuk middleware verification
    Route::get('/test', function () {
        return response()->json(['success' => true, 'message' => 'Admin access OK']);
    });

    // Praktikum routes dengan PraktikumAccess
    Route::prefix('praktikum/{praktikum_id}')->middleware('praktikum_access')->group(function () {
        Route::get('/test', function ($praktikum_id) {
            return response()->json([
                'success' => true,
                'message' => 'Admin praktikum access OK',
                'praktikum_id' => $praktikum_id,
            ]);
        });
    });

    // Step 4 — List Dosen
    Route::get('/dosen', [\App\Http\Controllers\Api\Admin\DosenController::class, 'index']);

    // Step 5 — CRUD /admin/praktikum
    Route::apiResource('praktikum', \App\Http\Controllers\Api\Admin\PraktikumController::class);
    Route::put('/praktikum/{id}/assign-koor', [\App\Http\Controllers\Api\Admin\PraktikumController::class, 'assignKoor']);

    // Step 6 — POST /admin/praktikum/{id}/praktikan/upload
    // Step 7 — GET /admin/praktikum/{id}/asprak
    // Step 8 — PUT /admin/pengguna/{id}/role-status
    // Step 9 — GET /admin/pendaftaran/koor|asprak
});

// ─── Dosen Routes (role:dosen + praktikum_access) ─────────────────────────────
Route::prefix('dosen')->middleware(['auth:sanctum', 'role:dosen'])->group(function () {
    // Test endpoint
    Route::get('/test', function () {
        return response()->json(['success' => true, 'message' => 'Dosen access OK']);
    });

    // Praktikum routes — PraktikumAccess validates dosen mengampu ini
    Route::prefix('praktikum/{praktikum_id}')->middleware('praktikum_access')->group(function () {
        Route::get('/test', function ($praktikum_id) {
            return response()->json([
                'success' => true,
                'message' => 'Dosen praktikum access OK',
                'praktikum_id' => $praktikum_id,
            ]);
        });
    });

    // Step 8 — PUT /dosen/pengguna/{id}/role-status
});

// ─── Koor Routes (role:koor_prak — hierarchy: koor + dosen + admin bisa) ──────
Route::prefix('koor')->middleware(['auth:sanctum', 'role:koor_prak'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['success' => true, 'message' => 'Koor access OK']);
    });
});

// ─── Asprak Routes (role:asprak — hierarchy: asprak + koor + dosen + admin) ───
Route::prefix('asprak')->middleware(['auth:sanctum', 'role:asprak'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['success' => true, 'message' => 'Asprak access OK']);
    });
});

// ─── Mahasiswa Routes ─────────────────────────────────────────────────────────
Route::prefix('mahasiswa')->middleware(['auth:sanctum', 'role:mahasiswa'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['success' => true, 'message' => 'Mahasiswa access OK']);
    });
});
