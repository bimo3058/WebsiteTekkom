<?php

namespace Modules\Capstone\Support;

use Illuminate\Support\Facades\Route;

/**
 * Domain Widget helper: central URL generation for Capstone Blade views.
 *
 * Primitive components must not hardcode url('/capstone...').
 * Gating Wrappers (sb-link, feature-link) and shell should use this helper
 * so route caching keeps working and renames stay in one place.
 */
final class CapstoneUrl
{
    /**
     * Resolve a Capstone feature path (e.g. /admin/users) to a named route URL.
     *
     * Falls back to url('/capstone'.$path) when no named route exists
     * (e.g. dynamic segments without parameters).
     *
     * @param  array<string, mixed>  $parameters
     */
    public static function to(string $path, array $parameters = []): string
    {
        $normalized = '/'.ltrim($path, '/');

        // Allow callers to pass full /capstone/... paths.
        if (str_starts_with($normalized, '/capstone/')) {
            $normalized = substr($normalized, strlen('/capstone'));
            $normalized = '/'.ltrim($normalized, '/');
        }

        if ($normalized === '/' || $normalized === '') {
            return Route::has('capstone.page.home')
                ? route('capstone.page.home')
                : url('/capstone');
        }

        // Legacy alias handled by redirect route.
        if ($normalized === '/mahasiswa/ta-defense') {
            return Route::has('capstone.page.mahasiswa.ta-submission')
                ? route('capstone.page.mahasiswa.ta-submission', $parameters)
                : url('/capstone/mahasiswa/ta-submission');
        }

        $viewPath = BladePages::viewForRoute($normalized);
        $name = BladePages::routeNameForView($viewPath);

        if (Route::has($name)) {
            try {
                return route($name, $parameters);
            } catch (\Throwable) {
                // Missing route parameters — fall through to plain URL.
            }
        }

        return url('/capstone'.$normalized);
    }
}
