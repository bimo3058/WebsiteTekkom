<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user      = $request->user();
        $validated = $request->validated();

        $isSuperadmin = $user->hasRole('superadmin');
        $isAdmin      = $user->hasAnyRole(['admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan']);

        // Nama hanya boleh diubah oleh admin & superadmin
        if ($isSuperadmin || $isAdmin) {
            $user->name = $validated['name'];
        }
        // User biasa (mahasiswa, dosen): nama diabaikan meskipun ada di request

        // Field umum yang bisa diubah semua role
        if (isset($validated['personal_email'])) {
            $user->personal_email = $validated['personal_email'] ?: null;
        }

        $user->save();
        $user->clearUserCache();

        // Simpan nomor WA ke tabel eo_user_profiles (jika ada)
        if (isset($validated['whatsapp'])) {
            $user->eoUserProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['no_wa'   => '+62' . ltrim($validated['whatsapp'] ?? '', '0')]
            );
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();
        $user->clearUserCache();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}