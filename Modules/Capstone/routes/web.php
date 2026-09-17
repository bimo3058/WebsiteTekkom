<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\CapstoneLogoutController;
use Modules\Capstone\Http\Controllers\LaunchController;
use Modules\Capstone\Http\Controllers\BladeController;
use Modules\Capstone\Http\Middleware\BladeAccessMiddleware;

Route::get('/capstone/assets/{path}', [BladeController::class, 'asset'])->where('path', '.+')->name('capstone.asset');

// Reuse the existing API policies and controllers with the web session + CSRF.
// Sanctum's web guard provides its transient first-party token; no bearer token
// is stored in HTML, localStorage, or a new authentication bridge.
Route::middleware(['auth', 'module.active:capstone', \Modules\Capstone\Http\Middleware\BladeSessionFeatureMiddleware::class])
    ->prefix('capstone/session')->name('capstone.session.')
    ->group(__DIR__.'/api.php');

Route::middleware(['auth', 'module.active:capstone'])->group(function () {
    // Existing SICATA links now enter the Laravel Blade frontend.
    Route::get('/capstone/launch', [LaunchController::class, 'launch'])
        ->name('capstone.dashboard');

    Route::get('/capstone/dashboard', [BladeController::class, 'dashboard'])
        ->middleware(BladeAccessMiddleware::class)->name('capstone.blade.dashboard');

    Route::post('/capstone/logout', CapstoneLogoutController::class)
        ->name('capstone.logout');
});

$pages = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/pages.json')), true);
usort($pages, fn ($a, $b) => substr_count($a['route'], '[') <=> substr_count($b['route'], '['));
foreach ($pages as $page) {
    $path = $page['route'];
    if ($path === '/') {
        Route::get('/capstone', fn () => redirect()->route('capstone.blade.dashboard'))
            ->middleware(['auth', 'module.active:capstone'])->name('capstone.page.home');
        continue;
    }
    if (in_array($path, ['/login', '/auth/exchange'], true)) {
        Route::get('/capstone'.$path, fn () => redirect()->route('capstone.blade.dashboard'))->middleware('auth');
        continue;
    }
    if ($path === '/mahasiswa/ta-defense') {
        Route::get('/capstone'.$path, fn () => redirect('/capstone/mahasiswa/ta-submission'))
            ->middleware(['auth', 'module.active:capstone', BladeAccessMiddleware::class.':mahasiswa'])
            ->name('capstone.page.mahasiswa.ta-defense');
        continue;
    }
    $routePath = preg_replace('/\[([^\]]+)\]/', '{$1}', $path);
    $viewPath = trim(preg_replace('/\[([^\]]+)\]/', '_$1_', $path), '/') ?: 'home';
    $route = Route::get('/capstone'.($routePath === '/' ? '' : $routePath), [BladeController::class, 'page'])
        ->defaults('capstone_page', $viewPath)
        ->name('capstone.page.'.str_replace('/', '.', $viewPath));
    if ($path !== '/' && $path !== '/unauthorized') {
        $role = explode('/', trim($path, '/'))[0];
        $route->middleware(['auth', 'module.active:capstone', BladeAccessMiddleware::class.(in_array($role, ['admin','dosen','mahasiswa'], true) ? ':'.$role : '')]);
    }
}
