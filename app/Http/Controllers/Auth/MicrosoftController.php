<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuditLog;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MicrosoftController extends Controller
{
    /**
     * Redirect ke Microsoft login
     */
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Handle Microsoft callback
     */
    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();

            // 1. Determine role dari email domain
            $role = $this->determineRoleFromEmail($microsoftUser->getEmail());

            // 2. Sync atau create user
            $user = User::updateOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'name' => $microsoftUser->getName(),
                    'microsoft_id' => $microsoftUser->getId(), // ← Track Microsoft ID
                    'role' => $role, // ← Assign role otomatis
                    // Jangan simpan password untuk SSO users
                    'last_synced_from_sso' => now(),
                    'sso_data' => json_encode([
                        'id' => $microsoftUser->getId(),
                        'email' => $microsoftUser->getEmail(),
                        'name' => $microsoftUser->getName(),
                        'raw' => $microsoftUser->getRaw(), // Simpan raw data untuk reference
                    ]),
                ]
            );

            // 3. Update last login
            $user->update(['last_login' => now()]);

            // 4. Log activity
            UserAuditLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'source' => 'microsoft_sso',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 5. Login user
            Auth::login($user, remember: true);

            // 6. Redirect berdasarkan role
            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            Log::error('Microsoft SSO Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Tentukan role dari email domain
     */
    private function determineRoleFromEmail($email)
    {
        if (str_ends_with($email, '@lecturer.undip.ac.id')) {
            return 'LECTURER';
        } elseif (str_ends_with($email, '@students.undip.ac.id')) {
            return 'STUDENT';
        }

        // Default ke STUDENT jika domain tidak dikenali
        return 'STUDENT';
    }

    /**
     * Redirect berdasarkan role user
     */
    private function redirectByRole($user)
    {
        return match ($user->role) {
            'SUPERADMIN' => redirect()->route('superadmin.dashboard'),
            'ADMIN' => redirect()->route('admin.dashboard'),
            'LECTURER' => redirect()->route('lecturer.dashboard'),
            'STUDENT' => redirect()->route('student.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}