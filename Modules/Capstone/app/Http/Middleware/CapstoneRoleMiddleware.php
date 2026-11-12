<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CapstoneRoleMiddleware extends CapstoneAccessMiddleware
{
    public function handle(Request $request, Closure $next, ?string $expected = null)
    {
        return parent::handle($request, $next, $expected === '' ? null : $expected);
    }
}
