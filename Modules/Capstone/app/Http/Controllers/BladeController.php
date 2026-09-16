<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Capstone\Support\CapstoneActor;

class BladeController extends Controller
{
    public function page(Request $request)
    {
        $page = $request->route()->defaults['capstone_page'];
        $file = module_path('Capstone', 'resources/views/pages/'.$page.'.blade.php');
        $actor = $request->user() ? CapstoneActor::payload($request->user(), $request->attributes->get('capstone_role')) : null;
        $data = [
            'actor' => $actor,
            'activeRole' => $actor['active_role'] ?? null,
            'pagePath' => '/'.preg_replace('#^capstone/?#', '', $request->path()),
            'pageParams' => $request->route()->parameters(),
            'featureAccess' => $request->attributes->get('capstone_feature_access', []),
        ];
        if (!is_file($file)) {
            return response()->view('capstone::pages.unavailable', $data, 503)->header('Cache-Control', 'no-store');
        }
        return View::file($file, $data);
    }

    public function dashboard(Request $request)
    {
        $roles = CapstoneActor::roles($request->user());
        $role = $request->session()->get('capstone.blade_role');
        $role = in_array($role, $roles, true) ? $role : ($roles[0] ?? null);
        abort_unless($role, 403);
        return redirect('/capstone/'.$role.'/dashboard');
    }

    public function asset(string $path)
    {
        $root = realpath(module_path('Capstone', 'public'));
        $file = realpath($root.DIRECTORY_SEPARATOR.$path);
        abort_unless($file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file), 404);
        $type = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml',
            'png' => 'image/png', 'webp' => 'image/webp', 'jpg', 'jpeg' => 'image/jpeg',
            'woff2' => 'font/woff2', 'ico' => 'image/x-icon', default => null,
        };
        abort_unless($type, 404);
        return response()->file($file, ['Content-Type' => $type, 'Cache-Control' => 'public, max-age=3600', 'X-Content-Type-Options' => 'nosniff']);
    }
}
