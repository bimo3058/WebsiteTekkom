<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\BladeController;
use Modules\Capstone\Http\Controllers\CapstoneLogoutController;
use Modules\Capstone\Http\Controllers\LaunchController;
use Modules\Capstone\Http\Middleware\BladeAccessMiddleware;
use Modules\Capstone\Http\Middleware\BladeSessionFeatureMiddleware;
use Modules\Capstone\Support\BladePages;

Route::get('/capstone/assets/{path}', [BladeController::class, 'asset'])->where('path', '.+')->name('capstone.asset');

// Reuse the existing API policies and controllers with the web session + CSRF.
// Sanctum's web guard provides its transient first-party token; no bearer token
// is stored in HTML, localStorage, or a new authentication bridge.
Route::middleware(['auth', 'module.active:capstone', BladeSessionFeatureMiddleware::class])
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

$pages = BladePages::manifest();
usort($pages, fn ($a, $b) => substr_count($a['route'], '[') <=> substr_count($b['route'], '['));
foreach ($pages as $page) {
    $path = $page['route'];
    if ($path === '/') {
        Route::redirect('/capstone', '/capstone/dashboard', 302)
            ->middleware(['auth', 'module.active:capstone'])->name('capstone.page.home');

        continue;
    }
    if (in_array($path, ['/login', '/auth/exchange'], true)) {
        Route::redirect('/capstone'.$path, '/capstone/dashboard', 302)->middleware('auth');

        continue;
    }
    if ($path === '/mahasiswa/ta-defense') {
        Route::redirect('/capstone'.$path, '/capstone/mahasiswa/ta-submission', 302)
            ->middleware(['auth', 'module.active:capstone', BladeAccessMiddleware::class.':mahasiswa'])
            ->name('capstone.page.mahasiswa.ta-defense');

        continue;
    }
    $routePath = BladePages::routePathForRoute($path);
    $viewPath = BladePages::viewForRoute($path);
    $defaults = ['capstone_page' => $viewPath];
    if ($alias = BladePages::aliasForRoute($path)) {
        $defaults = ['capstone_page' => $alias['view']];
        if (array_key_exists('peer', $alias)) {
            $defaults['peer'] = $alias['peer'];
        }
    }
    $route = Route::get('/capstone'.($routePath === '/' ? '' : $routePath), [BladeController::class, 'page'])
        ->defaults('capstone_page', $defaults['capstone_page'])
        ->name(BladePages::routeNameForView($viewPath));
    if (array_key_exists('peer', $defaults)) {
        $route->defaults('peer', $defaults['peer']);
    }
    if ($path !== '/' && $path !== '/unauthorized') {
        $role = explode('/', trim($path, '/'))[0];
        $route->middleware(['auth', 'module.active:capstone', BladeAccessMiddleware::class.(in_array($role, ['admin', 'dosen', 'mahasiswa'], true) ? ':'.$role : '')]);
    }
}
