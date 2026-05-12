<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');

// Halaman Error Kustom
Route::get('/error/{code}', function ($code) {
    if (!session('from_exception')) {
        return redirect('/');
    }
    return view("errors.{$code}");
})->name('error.page')->where('code', '400|401|403|404|419|429|500|503');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // SSO Microsoft
    Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])->name('microsoft.redirect');
    Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])->name('microsoft.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // SSO & Session Management
    Route::middleware('web')->group(function () {
        Route::get('/sso/password', [MicrosoftController::class, 'showPasswordForm'])->name('sso.password');
        Route::post('/sso/password', [MicrosoftController::class, 'verifyPassword'])->name('sso.verify');
        Route::get('/auth/microsoft/switch', [MicrosoftController::class, 'switchAccount'])->name('microsoft.switch');
        Route::post('/logout-and-switch', [MicrosoftController::class, 'logoutAndSwitch'])->name('logout.switch');
    });

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
        Route::post('/profile/avatar', 'updateAvatar')->name('profile.avatar.update');
        Route::delete('/profile/avatar', 'destroyAvatar')->name('profile.avatar.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | SuperAdmin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');

        // User Management
        Route::controller(SuperAdminController::class)->group(function () {
            Route::get('/users', 'users')->name('users.index');
            Route::post('/users', 'storeUser')->name('users.store');
            Route::patch('/users/{user}/update', 'updateUser')->name('users.update');
            Route::delete('/users/{user}', 'destroyUser')->name('users.destroy');
            Route::post('/users/{user}/update-role', 'updateRole')->name('users.update-role');
            Route::post('/users/{user}/force-logout', 'forceLogout')->name('users.force-logout');
            Route::post('/users/{user}/suspend', 'suspend')->name('users.suspend');
            Route::post('/users/{user}/unsuspend', 'unsuspend')->name('users.unsuspend');

            // Online & Suspended Users
            Route::get('/users/online', 'onlineUsers')->name('users.online');
            Route::get('/users/suspended', 'suspendedUsers')->name('users.suspended');
        });

        // Module & Permission Management
        Route::get('/modules', [SuperAdminController::class, 'modules'])->name('modules');
        Route::get('/permissions', [SuperAdminController::class, 'permissions'])->name('permissions');
        Route::post('/permissions/repair-all', [SuperAdminController::class, 'repairAllPermissions'])->name('permissions.repair-all');

        // Audit Logs
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])->name('audit-logs');
        Route::post('/audit-logs/bulk-delete', [SuperAdminController::class, 'bulkDeleteAuditLogs'])->name('audit-logs.bulk-delete');
        Route::post('/audit-logs/bulk-destroy', [SuperAdminController::class, 'bulkDeleteAuditLogs'])->name('audit-logs.bulk-destroy');

        // Module Toggle
        Route::post('/modules/{slug}/toggle', [SuperAdminController::class, 'toggleModule'])->name('modules.toggle');

        // Users — Bulk Operations
        Route::post('/users/bulk-destroy', [SuperAdminController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::post('/users/bulk-import', [SuperAdminController::class, 'bulkImport'])->name('users.bulkImport');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Praktikum (Tugas Anda)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:asprak')->prefix('praktikum')->name('praktikum.')->group(function () {
        // Tuliskan route pengerjaan Manajemen Praktikum Anda di sini
        // Contoh: Route::get('/jadwal', [PraktikumController::class, 'jadwal'])->name('jadwal');
    });

    // Logout Manual
    Route::post('/logout', function () {
        $user = auth()->user();
        \App\Models\UserAuditLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'source' => 'manual',
        ]);
        $user->clearUserCache();
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login')->with('status', 'You have been logged out.');
    })->name('logout');
});

require __DIR__ . '/auth.php';