<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadUserRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        auth()->user()?->loadMissing('roles');
        return $next($request);
    }
}