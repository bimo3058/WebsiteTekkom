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
        $module = Cache::remember("module_data_{$moduleSlug}", 3600, function () use ($moduleSlug) {
            return SystemModule::where('slug', $moduleSlug)->first();
        });

        // Superadmin bypass semua
        if (auth()->user()?->hasRole('superadmin')) {
            return $next($request);
        }

        if (!$module || !$module->is_active) {
            abort(503, "Modul sedang maintenance. Silakan kembali beberapa saat lagi.");
        }

        // Enforce max upload per modul
        foreach ($request->allFiles() as $file) {
            $files = is_array($file) ? $file : [$file];
            foreach ($files as $f) {
                $maxMB = $module->setting('max_upload', 10);
                if ($f->getSize() > ($maxMB * 1024 * 1024)) {
                    return back()->with('error', "Ukuran file melebihi batas maksimal {$maxMB} MB untuk modul ini.");
                }
            }
        }

        return $next($request);
    }
}