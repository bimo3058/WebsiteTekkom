<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\MicrosoftController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');


/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard redirect berdasarkan role
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'SUPERADMIN' => redirect()->route('superadmin.dashboard'),
            'ADMIN'      => redirect()->route('admin.dashboard'),
            'LECTURER'   => redirect()->route('lecturer.dashboard'),
            'STUDENT'    => redirect()->route('student.dashboard'),
            default      => view('dashboard'),
        };
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
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


/*
|--------------------------------------------------------------------------
| AUTH SCAFFOLDING
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';