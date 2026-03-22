<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userId = auth()->id();
        $cached = Cache::get("user:{$userId}:roles");

        if ($cached) {
            // Cache menyimpan array of arrays [{id, name, module}, ...]
            // pluck 'name' supaya jadi flat Collection of strings
            $userRoles = collect($cached)->pluck('name');
        } else {
            // Cache miss — query DB, simpan format konsisten
            $rolesCollection = auth()->user()->roles()->get();
            Cache::put("user:{$userId}:roles", $rolesCollection->toArray(), now()->addHours(8));
            $userRoles = $rolesCollection->pluck('name');
        }

        $roles   = collect(explode('|', $role))->map(fn($r) => strtolower($r));
        $hasRole = $roles->some(fn($r) => $userRoles->contains($r));

        if (!$hasRole) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}