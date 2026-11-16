<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PracticumAdminLoginRequest;

class PracticumAdminLoginController extends Controller
{
    public function create()
    {
        return view('auth.practicum-admin-login');
    }

    public function store(PracticumAdminLoginRequest $request)
    {
        return app(AuthenticatedSessionController::class)->store($request);
    }
}
