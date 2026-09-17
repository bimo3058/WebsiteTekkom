<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\BladeFeatureAccess;

class BladeAccessMiddleware
{
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        $user = $request->user();
        abort_unless($user && $user->can('capstone.view'), 403);
        $roles = CapstoneActor::roles($user);
        $storedRole = $request->session()->get('capstone.blade_role');
        $activeRole = $role ?? (in_array($storedRole, $roles, true) ? $storedRole : ($roles[0] ?? null));
        abort_unless(in_array($activeRole, $roles, true), 403);
        if ($activeRole === 'mahasiswa') {
            CapstoneActor::student($user);
            $state = BladeFeatureAccess::snapshot($user);
            $request->attributes->set('capstone_feature_access', $state);
            $path = '/'.preg_replace('#^capstone/?#', '', $request->path());
            $reason = BladeFeatureAccess::reason($path, $state);
            if ($reason) {
                return response()->view('capstone::pages.locked', [
                    'actor'=>CapstoneActor::payload($user, $activeRole), 'activeRole'=>$activeRole,
                    'pagePath'=>$path, 'pageParams'=>[], 'featureAccess'=>$state, 'lockedReason'=>$reason,
                ], 403);
            }
        } elseif ($activeRole === 'dosen') {
            CapstoneActor::lecturer($user);
        }
        $request->attributes->set('capstone_role', $activeRole);
        $request->session()->put('capstone.blade_role', $activeRole);

        return $next($request);
    }
}
