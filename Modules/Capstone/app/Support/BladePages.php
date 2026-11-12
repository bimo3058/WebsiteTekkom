<?php

namespace Modules\Capstone\Support;

final class BladePages
{
    public const UNAVAILABLE = 'Halaman ini belum tersedia.';

    /** @var array<string, mixed>|null */
    private static ?array $manifest = null;

    /**
     * Aliased routes share one view instead of a one-line `@extends` wrapper.
     *
     * @return array{view: string, peer?: bool}|null
     */
    public static function aliasForRoute(string $route): ?array
    {
        return match ($route) {
            '/admin/period-assessment-config' => ['view' => 'admin/configuration/index', 'peer' => false],
            '/admin/peer-review' => ['view' => 'admin/configuration/index', 'peer' => true],
            '/admin/period-assessment-config/[id]/edit' => ['view' => 'admin/configuration/edit', 'peer' => false],
            '/admin/peer-review/[id]/edit' => ['view' => 'admin/configuration/edit', 'peer' => true],
            '/mahasiswa/expo/[expoId]' => ['view' => 'mahasiswa/expo-detail/index'],
            '/mahasiswa/titles/[id]' => ['view' => 'mahasiswa/title-detail/index'],
            default => null,
        };
    }

    public static function viewForRoute(string $route): string
    {
        return trim((string) preg_replace('/\[([^\]]+)\]/', '_$1_', $route), '/') ?: 'home';
    }

    public static function routeNameForView(string $view): string
    {
        return 'capstone.page.'.str_replace('/', '.', $view);
    }

    public static function routePathForRoute(string $route): string
    {
        return (string) preg_replace('/\[([^\]]+)\]/', '{$1}', $route);
    }

    /** @return array<int, array{route: string}> */
    public static function manifest(): array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $path = module_path('Capstone', 'resources/reference/pages.json');
        $raw = is_file($path) ? file_get_contents($path) : '[]';
        $pages = json_decode($raw ?: '[]', true);

        self::$manifest = is_array($pages) ? $pages : [];

        return self::$manifest;
    }

    /** Cache only within this request, so newly added views become available immediately. */
    public static function catalog(): array
    {
        $request = request();
        if ($request->attributes->has('capstone_blade_pages')) {
            return $request->attributes->get('capstone_blade_pages');
        }
        $result = [];
        foreach (self::manifest() as $page) {
            $path = $page['route'];
            $result[$path] = self::viewFile($path) !== null
                || in_array($path, ['/', '/login', '/auth/exchange', '/mahasiswa/ta-defense'], true);
        }
        $result['/dashboard'] = $result['/launch'] = true;
        $request->attributes->set('capstone_blade_pages', $result);

        return $result;
    }

    public static function viewFile(string $route): ?string
    {
        if ($alias = self::aliasForRoute($route)) {
            $file = module_path('Capstone', 'resources/views/pages/'.$alias['view'].'.blade.php');

            return is_file($file) ? $file : null;
        }

        $view = self::viewForRoute($route);
        $file = module_path('Capstone', 'resources/views/pages/'.$view.'.blade.php');

        if (is_file($file)) {
            return $file;
        }

        $index = module_path('Capstone', 'resources/views/pages/'.$view.'/index.blade.php');

        return is_file($index) ? $index : null;
    }

    public static function available(string $href): bool
    {
        $path = parse_url($href, PHP_URL_PATH) ?: '/';
        $base = parse_url(url('/capstone'), PHP_URL_PATH);
        if ($path === $base || str_starts_with($path, $base.'/')) {
            $path = substr($path, strlen($base)) ?: '/';
        }
        $path = rtrim($path, '/') ?: '/';
        $catalog = self::catalog();
        if (array_key_exists($path, $catalog)) {
            return $catalog[$path];
        }
        foreach ($catalog as $route => $available) {
            $pattern = implode('[^/]+', array_map(fn ($part) => preg_quote($part, '#'), preg_split('/\[[^\]]+\]/', $route)));
            if (preg_match('#^'.$pattern.'$#', $path)) {
                return $available;
            }
        }

        return false;
    }

    public static function reason(string $href): ?string
    {
        return self::available($href) ? null : self::UNAVAILABLE;
    }
}
