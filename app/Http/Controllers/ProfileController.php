<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;

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

        $canEditName = $user->hasRole('superadmin')
            || $user->hasAnyRole(['admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan']);

        // Susun nomor WA lengkap (kode negara + nomor lokal)
        $phoneCode = $validated['phone_code'] ?? '+62';
        $localNum  = $validated['whatsapp'] ?? null;
        
        if ($localNum) {
            // Hilangkan 0 di depan jika kode negaranya +62 (Indonesia)
            if ($phoneCode === '+62' && str_starts_with($localNum, '0')) {
                $localNum = ltrim($localNum, '0');
            }
            $fullWa = $phoneCode . $localNum;
        } else {
            $fullWa = null;
        }

        // updateQuietly agar tidak trigger observers yang tidak relevan
        // (perubahan nama/email tidak ada hubungannya dengan roles/permissions)
        $user->updateQuietly([
            'name'           => $canEditName ? $validated['name'] : $user->name,
            'personal_email' => $validated['personal_email'] ?? null,
            'whatsapp'       => $fullWa,
        ]);

        // Sinkronisasi: perbarui mk_kemahasiswaan.kontak agar direktori mahasiswa ikut update
        try {
            Kemahasiswaan::where('user_id', $user->id)
                ->update(['kontak' => $fullWa]);
        } catch (\Throwable $e) {
            Log::warning('Gagal sinkronisasi kontak ke mk_kemahasiswaan', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        // Sinkronisasi: perbarui cv_profiles agar CV Builder selalu memiliki kontak terbaru
        try {
            \App\Models\CvProfile::where('user_id', $user->id)->update([
                'cv_whatsapp' => $fullWa,
                'cv_email'    => $validated['personal_email'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal sinkronisasi kontak ke cv_profiles', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        $user->clearUserCache();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request, SupabaseStorage $supabase)
    {
        $request->validate([
            'avatar'          => 'required|image|max:2048',
            'avatar_original' => 'nullable|image|max:2048',
        ]);

        $user      = auth()->user();
        $oldAvatar = $user->getOriginal('avatar_url');

        // 1. Upload webp (hasil crop) ke folder avatars — untuk tampilan UI
        $path = $supabase->upload($request->file('avatar'), 'avatars', 'user_avatar');

        if (! $path) {
            return response()->json(['status' => 'error', 'message' => 'Gagal upload'], 422);
        }

        $url = $supabase->publicUrl($path, 'user_avatar');

        // 2. Upload format asli ke avatars_format — untuk CV/print
        $urlOriginal = null;
        if ($request->hasFile('avatar_original')) {
            $pathOriginal = $supabase->upload(
                $request->file('avatar_original'),
                'avatars_format',
                'user_avatar'
            );
            $urlOriginal = $pathOriginal
                ? $supabase->publicUrl($pathOriginal, 'user_avatar')
                : null;
        }

        $user->update([
            'avatar_url'        => $url,
            'avatar_url_format' => $urlOriginal,
        ]);

        if ($oldAvatar) {
            cache()->forget("user_avatar_{$user->id}_" . md5($oldAvatar));
        }

        $user->clearUserCache();
        session()->flash('success', 'Foto profil berhasil diperbarui.');

        return response()->json([
            'status' => 'success',
            'url'    => $url . '?width=100&height=100&resize=contain',
        ]);
    }

    public function destroyAvatar(Request $request, SupabaseStorage $supabase)
    {
        $user = $request->user();

        if (! $user->avatar_url) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada foto untuk dihapus'], 404);
        }

        // Hapus webp dari avatars
        $path = str_replace($supabase->publicUrl('', 'user_avatar'), '', $user->avatar_url);
        $deleted = $supabase->delete($path, 'user_avatar');

        if (! $deleted) {
            Log::warning('Gagal menghapus avatar dari Supabase Storage', [
                'user_id' => $user->id,
                'path'    => $path,
            ]);
        }

        // Hapus format asli dari avatars_format juga
        if ($user->avatar_url_format) {
            $pathOriginal = str_replace($supabase->publicUrl('', 'user_avatar'), '', $user->avatar_url_format);
            $supabase->delete($pathOriginal, 'user_avatar');
        }

        $user->update([
            'avatar_url'        => null,
            'avatar_url_format' => null,
        ]);

        $user->clearUserCache();

        session()->flash('success', 'Foto profil berhasil dihapus.');

        return response()->json(['status' => 'success']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // FIX: bersihkan cache dan hapus user SEBELUM logout
        // agar $user masih valid saat clearUserCache() dan delete() dipanggil
        $user->clearUserCache();
        $user->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}