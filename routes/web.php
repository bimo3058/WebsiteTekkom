<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {

    // Login page (pilih metode login)
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Microsoft SSO
    Route::get('/auth/microsoft/redirect', [MicrosoftController::class, 'redirect'])
        ->name('microsoft.redirect');

    Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback'])
        ->name('microsoft.callback');
});

Route::middleware('auth')->group(function () {

    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        
        // Dashboard - Superadmin only
        Route::get('/dashboard', [SuperAdminController::class, 'index'])
            ->name('dashboard');

        // User Management
        Route::get('/users', [SuperAdminController::class, 'users'])
            ->name('users.index');
        Route::post('/users/{user}/update-role', [SuperAdminController::class, 'updateRole'])
            ->name('users.update-role');

        // Module Management
        Route::get('/modules', [SuperAdminController::class, 'modules'])
            ->name('modules');

        // Audit Logs
        Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])
            ->name('audit-logs');
    });

    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // HANYA superadmin yang redirect langsung
        if ($user->roles()->where('name', 'superadmin')->exists()) {
            return redirect()->route('superadmin.dashboard');
        }
        
        // Sisanya (dosen, mahasiswa, admin, gpm) ke global dashboard
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', function () {

        \App\Models\UserAuditLog::create([
            'user_id' => auth()->user()->id,
            'action'  => 'logout',
            'source'  => 'manual',
        ]);

        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login')->with('status', 'You have been logged out.');
    })->name('logout');
});


require __DIR__.'/auth.php';