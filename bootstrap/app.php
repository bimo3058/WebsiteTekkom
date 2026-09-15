<?php

// bootstrap/app.php

use App\Http\Middleware\ApplyPageMetadata;
use App\Http\Middleware\CheckModuleActive;
use App\Http\Middleware\CheckSessionVersion;
use App\Http\Middleware\CheckSuspended;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RedirectBasedOnRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Middleware\CapstoneAccessMiddleware;
use Modules\Capstone\Http\Middleware\CapstoneRoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // ─── Web routes per modul ────────────────────────────────────────
            $webModules = [
                'Capstone',
                'BankSoal',
                'EOffice',
                'ManajemenMahasiswa',
            ];

            foreach ($webModules as $module) {
                $path = base_path("Modules/{$module}/routes/web.php");
                if (file_exists($path)) {
                    Route::middleware('web')->group($path);
                }
            }

            // ─── API routes per modul ────────────────────────────────────────
            $apiModules = [
                'Capstone',
                'BankSoal',
                'EOffice',
                'ManajemenMahasiswa',
            ];

            Route::middleware(['api'])
                ->prefix('api')
                ->group(function () use ($apiModules) {
                    foreach ($apiModules as $module) {
                        $path = base_path("Modules/{$module}/routes/api.php");
                        if (file_exists($path)) {
                            require $path;
                        }
                    }
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/capstone/auth/exchange',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EnsureMicrosoftSsoSession::class,
            CheckSuspended::class,
            CheckSessionVersion::class,
            PreventBackHistory::class,
            RedirectBasedOnRole::class,
            ApplyPageMetadata::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'module.active' => CheckModuleActive::class,
            'capstone.access' => CapstoneAccessMiddleware::class,
            'capstone.role' => CapstoneRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->renderable(function (HttpException $e, $request) {
            $code = $e->getStatusCode();
            $supported = [400, 401, 403, 404, 429, 500, 503];

            if (in_array($code, $supported) && ! $request->expectsJson()) {
                return redirect()->route('error.page', ['code' => $code])
                    ->with('from_exception', true);
            }
        });

        $exceptions->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.',
                    ], 419);
                }

                return redirect()->route('login')
                    ->with('status', 'Sesi kedaluwarsa, silakan login kembali.');
            }
        });

        $exceptions->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            return redirect()->route('login')
                ->with('status', 'Sesi kedaluwarsa, silakan login kembali.');
        });
    })->create();
