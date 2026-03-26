<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemModule;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActive
{
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        // Cache status modul selamanya agar tidak query DB terus-menerus
        $isActive = Cache::rememberForever("module_active_{$moduleSlug}", function () use ($moduleSlug) {
            $module = SystemModule::where('slug', $moduleSlug)->first();
            return $module ? $module->is_active : false;
        });

        // Jika modul mati (Superadmin tetap bisa tembus untuk testing)
        if (!$isActive && !auth()->user()?->hasRole('superadmin')) {
            abort(503, "Modul sedang dalam perbaikan (Maintenance). Silakan kembali beberapa saat lagi.");
        }

        return $next($request);
    }
}
