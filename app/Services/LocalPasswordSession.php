<?php

namespace App\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocalPasswordSession
{
    public const KEY = 'auth.local_password';

    public const CHANGE_REQUIRED = 'Anda perlu mengganti kata sandi saat pertama kali masuk lewat form.';

    public const CHANGE_LOCKED = 'Kata sandi hanya dapat diubah setelah masuk lewat form login.';

    public static function isActive(Request $request): bool
    {
        return $request->session()->get(self::KEY) === true
            && ! $request->session()->has(MicrosoftSsoSession::KEY);
    }

    public static function redirectToPasswordChange(): RedirectResponse
    {
        return redirect()->route('profile.edit', ['tab' => 'password'])
            ->with('password_change_required', self::CHANGE_REQUIRED);
    }
}
