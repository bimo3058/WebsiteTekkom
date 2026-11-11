<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the direct password change form for administrators.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Change an administrator's password after verifying the current password.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ], [
            'current_password.required' => 'Masukkan password lama Anda.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru harus berbeda dari password lama.',
            'password.min' => 'Password baru minimal :min karakter.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! $user->hasAnyRole(['admin', 'superadmin'])
            || $user->isSuspended() || ! $user->password
            || ! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password lama tidak sesuai, atau akun tidak memiliki akses admin/superadmin.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        $user->forceLogout();

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
