<?php

namespace Modules\Capstone\Support;

final class BladePages
{
    public const UNAVAILABLE = 'Halaman ini belum tersedia.';

    /** Cache only within this request, so newly added views become available immediately. */
    public static function catalog(): array
    {
        $request = request();
        if ($request->attributes->has('capstone_blade_pages')) return $request->attributes->get('capstone_blade_pages');
        $pages = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/pages.json')), true);
        $result = [];
        foreach ($pages as $page) {
            $path = $page['route'];
            $view = trim(preg_replace('/\[([^\]]+)\]/', '_$1_', $path), '/') ?: 'home';
            $result[$path] = in_array($path, ['/', '/login', '/auth/exchange', '/mahasiswa/ta-defense'], true)
                || is_file(module_path('Capstone', 'resources/views/pages/'.$view.'.blade.php'));
        }
        $result['/dashboard'] = $result['/launch'] = true;
        $request->attributes->set('capstone_blade_pages', $result);
        return $result;
    }

    public static function available(string $href): bool
    {
        $path = parse_url($href, PHP_URL_PATH) ?: '/';
        $base = parse_url(url('/capstone'), PHP_URL_PATH);
        if ($path === $base || str_starts_with($path, $base.'/')) $path = substr($path, strlen($base)) ?: '/';
        $path = rtrim($path, '/') ?: '/';
        $catalog = self::catalog();
        if (array_key_exists($path, $catalog)) return $catalog[$path];
        foreach ($catalog as $route => $available) {
            $pattern = implode('[^/]+', array_map(fn($part) => preg_quote($part, '#'), preg_split('/\[[^\]]+\]/', $route)));
            if (preg_match('#^'.$pattern.'$#', $path)) return $available;
        }
        return false;
    }

    public static function reason(string $href): ?string
    {
        return self::available($href) ? null : self::UNAVAILABLE;
    }
}
