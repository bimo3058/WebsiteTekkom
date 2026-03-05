<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\UserAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();

            // 1. Sync atau create user — tanpa kolom 'role' karena pakai pivot
            $user = User::updateOrCreate(
                ['email' => $microsoftUser->getEmail()],
                [
                    'external_id'          => $microsoftUser->getId(),
                    'name'                 => $microsoftUser->getName(),
                    'last_synced_from_sso' => now(),
                    'sso_data'             => [
                        'id'    => $microsoftUser->getId(),
                        'email' => $microsoftUser->getEmail(),
                        'name'  => $microsoftUser->getName(),
                        'raw'   => $microsoftUser->getRaw(),
                    ],
                ]
            );

            // 2. Assign role berdasarkan domain email (hanya untuk user baru)
            if ($user->wasRecentlyCreated) {
                $this->assignRoleFromEmail($user, $microsoftUser->getEmail());
            }

            // 3. Load roles ke memory
            $user->loadMissing('roles');

            // 4. Update last login
            $user->update(['last_login' => now()]);

            // 5. Log activity
            UserAuditLog::create([
                'user_id' => $user->id,
                'action'  => 'login',
                'source'  => 'microsoft_sso',
                'details' => [
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
            ]);

            // 6. Login user
            Auth::login($user, remember: true);

            // 7. Redirect berdasarkan role
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
     * Assign role ke user baru berdasarkan domain email.
     * Juga buat record lecturer/student jika perlu.
     */
    private function assignRoleFromEmail(User $user, string $email): void
    {
        if (str_ends_with($email, '@lecturer.undip.ac.id')) {
            $role = Role::where('name', 'dosen')->where('module', 'global')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            // Buat lecturer record
            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['employee_number' => 'NIP-' . $user->id] // placeholder, update manual
            );

        } elseif (str_ends_with($email, '@students.undip.ac.id')) {
            $role = Role::where('name', 'mahasiswa')->where('module', 'global')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            // Buat student record
            // Ambil NIM dari email (format: nim@students.undip.ac.id)
            $nim = explode('@', $email)[0];
            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => $nim,
                    'cohort_year'    => (int) substr($nim, 0, 4), // 4 digit pertama NIM
                ]
            );
        }
    }

    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->hasRole('dosen')) {
            return redirect()->route('dashboard');
        }

        // mahasiswa dan role lainnya
        return redirect()->route('dashboard');
    }
}