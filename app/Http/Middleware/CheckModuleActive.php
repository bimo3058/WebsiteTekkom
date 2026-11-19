<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemModule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $maxMB = $module->setting('max_upload', 10);
        foreach (Arr::flatten($request->allFiles()) as $file) {
            // Inputs such as tahap[0][lampiran][] can be nested more than one level.
            // Empty optional inputs are not uploaded files; validation handles invalid uploads.
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }
            if ($file->getSize() > ($maxMB * 1024 * 1024)) {
                return back()->with('error', "Ukuran file melebihi batas maksimal {$maxMB} MB untuk modul ini.");
            }
        }

        return $next($request);
    }
}
