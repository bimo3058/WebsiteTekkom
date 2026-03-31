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
        // Pastikan hasAnyRole sudah kamu tambahkan di Model User sebelumnya
        $isAdmin      = $user->hasAnyRole(['admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan']);

        // 1. Logika Nama (Hanya Admin/Superadmin)
        if ($isSuperadmin || $isAdmin) {
            $user->name = $validated['name'];
        }

        // 2. Field Umum: Email Pribadi
        if (isset($validated['personal_email'])) {
            $user->personal_email = $validated['personal_email'] ?: null;
        }

        // 3. LOGIKA WHATSAPP (Simpan langsung ke tabel users)
        if ($request->filled('whatsapp')) {
            $wa = $request->whatsapp;
            
            // Bersihkan semua karakter kecuali angka
            $wa = preg_replace('/[^0-9]/', '', $wa); 
            
            // Hapus prefix 62 atau 0 di depan agar tidak double saat ditambah +62
            if (str_starts_with($wa, '62')) {
                $wa = substr($wa, 2);
            } elseif (str_starts_with($wa, '0')) {
                $wa = substr($wa, 1);
            }
            
            // Simpan ke kolom whatsapp di tabel users
            $user->whatsapp = '+62' . $wa;
        } else {
            $user->whatsapp = null;
        }

        // 4. Final Save & Cache Cleaning
        $user->save();
        $user->clearUserCache();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();
        
        // Bersihkan cache sebelum delete agar data tidak nyangkut di memori
        $user->clearUserCache();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}