<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LocalPasswordSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        abort_unless(LocalPasswordSession::isActive($request), 403, LocalPasswordSession::CHANGE_LOCKED);

        $user = User::findOrFail($request->user()->id);

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Cek password baru tidak boleh sama dengan password lama
        if (Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password saat ini.'
            ], 'updatePassword');
        }

        // Cek password tidak boleh mengandung bagian dari nama user
        $nameParts = array_filter(explode(' ', strtolower($user->name)));
        $newPassword = strtolower($validated['password']);

        foreach ($nameParts as $part) {
            // Hanya cek bagian nama yang lebih dari 2 karakter agar tidak terlalu ketat
            if (strlen($part) > 2 && str_contains($newPassword, $part)) {
                return back()->withErrors([
                    'password' => 'Password tidak boleh mengandung bagian dari nama Anda.'
                ], 'updatePassword');
            }
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'local_password_initialized_at' => $user->local_password_initialized_at ?? now(),
        ])->save();

        $user->clearUserCache();

        return back()->with('status', 'password-updated');
    }
}
