<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\{Auth,RateLimiter};
use Illuminate\Validation\ValidationException;

class PracticumAdminLoginRequest extends LoginRequest
{
    public function authenticate(): void
    {
        parent::authenticate();
        if (!Auth::user()?->hasRole('admin_praktikum')) {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages(['email'=>'Email atau password salah.']);
        }
    }
}
