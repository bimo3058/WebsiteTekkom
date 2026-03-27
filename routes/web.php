<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])
        ->name('microsoft.redirect');

    Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])
        ->name('microsoft.callback');
});

Route::middleware('auth')->group(function () {

    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [SuperAdminController::class, 'users'])
            ->name('users.index');
        Route::get('/import-status/{id}', function($id) {
            $status = \App\Models\ImportStatus::findOrFail($id);
            return response()->json([
                'processed' => $status->processed_rows,
                'total' => $status->total_rows,
                'status' => $status->status
            ]);
        })->name('superadmin.import.status');
        Route::get('/modules', [SuperAdminController::class, 'modules'])->name('modules');
        Route::get('/permissions', [SuperAdminController::class, 'permissions'])
            ->name('permissions');
        Route::post('/modules/{slug}/settings', [SuperAdminController::class, 'updateModuleSettings'])->name('modules.settings');
        Route::post('/modules/{slug}/purge-cache', [SuperAdminController::class, 'purgeModuleCache'])->name('modules.purge-cache');
        Route::post('/modules/{slug}/maintenance', [SuperAdminController::class, 'toggleMaintenance'])->name('modules.maintenance');
        Route::post('/modules/{slug}/toggle', [SuperAdminController::class, 'toggleModule'])->name('modules.toggle');
        Route::post('/modules/{slug}/update-config', [SuperAdminController::class, 'updateConfig'])->name('modules.update-config');
        Route::post('/users/{user}/update-permissions', [SuperAdminController::class, 'updatePermissions'])
            ->name('users.update-permissions');
        Route::patch('/users/{user}/update',       [SuperAdminController::class, 'updateUser'])
            ->name('users.update');
        Route::post('/users/{user}/force-logout',  [SuperAdminController::class, 'forceLogout'])
            ->name('users.force-logout');
        Route::post('/users/{user}/suspend',       [SuperAdminController::class, 'suspend'])
            ->name('users.suspend');
        Route::post('/users/{user}/unsuspend',     [SuperAdminController::class, 'unsuspend'])
            ->name('users.unsuspend');
        // Tambah user baru
        Route::post('/users/bulk-import', [SuperAdminController::class, 'bulkImport'])->name('users.bulkImport');
        Route::post('/import-status/{id}/cancel', [SuperAdminController::class, 'cancelImport'])->name('import.cancel');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])
            ->name('users.store');

        Route::post('/storage/upload', [SuperAdminController::class, 'testUpload'])
            ->name('storage.upload');

        Route::delete('/storage/delete', [SuperAdminController::class, 'testDelete'])
            ->name('storage.delete');

        // Update role user (ganti dari POST ke PATCH, nama route tetap bisa dipakai keduanya)
        Route::post('/users/{user}/update-role', [SuperAdminController::class, 'updateRole'])
            ->name('users.update-role');
        Route::patch('/users/{user}/roles', [SuperAdminController::class, 'updateRole'])
            ->name('users.update-roles');       

        // Hapus user (soft-delete)
        Route::delete('/users/bulk-destroy', [SuperAdminController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])
            ->name('users.destroy');
        Route::delete('/users/{user}/destroy', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/modules', [SuperAdminController::class, 'modules'])
            ->name('modules');

        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])
            ->name('audit-logs');

        Route::get('/audit-logs/table', [SuperAdminController::class, 'auditLogsTable'])
            ->name('audit-logs.table');
    });

    // Global dashboard — pakai DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', function () {
        $user = auth()->user();

        \App\Models\UserAuditLog::create([
            'user_id' => $user->id,
            'action'  => 'logout',
            'source'  => 'manual',
        ]);

        $user->clearUserCache();

        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login')->with('status', 'You have been logged out.');
    })->name('logout');
});

require __DIR__.'/auth.php';