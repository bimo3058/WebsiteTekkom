<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckSuspended;        
use App\Http\Middleware\RedirectBasedOnRole;
use App\Http\Middleware\LockToModule;           
use App\Http\Middleware\CheckModuleActive;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckSessionVersion;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            CheckSuspended::class,
            CheckSessionVersion::class,
            PreventBackHistory::class,    
            RedirectBasedOnRole::class,
        ]);

        $middleware->alias([
            'role'          => CheckRole::class,
            'permission'    => CheckPermission::class,
            'module.active' => CheckModuleActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            $code = $e->getStatusCode();
            $supported = [400, 401, 403, 404, 429, 500, 503];

            if (in_array($code, $supported) && !$request->expectsJson()) {
                return redirect()->route('error.page', ['code' => $code])
                    ->with('from_exception', true);
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.'
                    ], 419);
                }

                return redirect()->route('login')
                    ->with('status', 'Sesi kedaluwarsa, silakan login kembali.');
            }
        });

        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman.'
                ], 419);
            }

            return redirect()->route('login')
                ->with('status', 'Sesi kedaluwarsa, silakan login kembali.');
        });
    })->create();