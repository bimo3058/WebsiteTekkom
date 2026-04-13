<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_SECONDS = 60;

    // ── Helper: throttle key per user ─────────────────────────────────────────
    private function throttleKey(int $userId): string
    {
        return 'sso_verify:' . $userId;
    }

    // ── Helper: hitung default password untuk user ────────────────────────────
    private function getDefaultPassword(User $user): string
    {
        return explode(' ', $user->name)[0] .
               ($user->student?->student_number ?? '');
    }

    // ── Helper: cek apakah password masih default ─────────────────────────────
    private function isDefaultPassword(User $user): bool
    {
        if (!$user->password) return false;
        return Hash::check($this->getDefaultPassword($user), $user->password);
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function redirect()
    {
        return Socialite::driver('azure')
            ->scopes(['User.Read'])
            ->redirect();
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();

            $email          = $microsoftUser->getEmail();
            $allowedDomains = [
                '@students.undip.ac.id',
                '@lecturer.undip.ac.id',
                '@undip.ac.id',
            ];

            $isAllowed = collect($allowedDomains)
                ->contains(fn($domain) => str_ends_with($email, $domain));

            if (!$isAllowed) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses hanya untuk civitas akademika UNDIP.'
                ]);
            }

            // ── 1. Sync atau create user ──────────────────────────────────────
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'external_id'          => $microsoftUser->getId(),
                    'name'                 => $microsoftUser->getName(),
                    'last_synced_from_sso' => now(),
                    'sso_data'             => [
                        'id'    => $microsoftUser->getId(),
                        'email' => $email,
                        'name'  => $microsoftUser->getName(),
                    ],
                ]
            );

            // ── 2. Assign role & student/lecturer record untuk user baru ──────
            if ($user->wasRecentlyCreated) {
                $this->assignRoleFromEmail($user, $email, $microsoftUser);
                $user->load('student');
            }

            // ── 3. Set default password HANYA untuk user baru / belum punya pw
            if ($user->wasRecentlyCreated || !$user->password) {
                $defaultPassword = explode(' ', $microsoftUser->getName())[0] .
                                   ($microsoftUser->getRaw()['surname'] ?? '');
                $user->update([
                    'password' => Hash::make($defaultPassword)
                ]);
            }

            // ── 4. Ambil avatar dari Microsoft Graph ──────────────────────────
            try {
                $response = \Illuminate\Support\Facades\Http::withToken($microsoftUser->token)
                    ->withHeaders(['Accept' => 'image/jpeg'])
                    ->get('https://graph.microsoft.com/v1.0/me/photo/$value');

                Log::info('Avatar SSO response', [
                    'status'  => $response->status(),
                    'success' => $response->successful(),
                    'has_avatar' => (bool) $user->avatar_url,
                ]);

                if ($response->successful() && !$user->avatar_url) {
                    $storage = new \App\Services\SupabaseStorage();
                    $tmpPath = tempnam(sys_get_temp_dir(), 'ms_avatar_') . '.jpg';
                    file_put_contents($tmpPath, $response->body());

                    $tmpFile = new \Illuminate\Http\UploadedFile(
                        $tmpPath,
                        $user->id . '_avatar.jpg',
                        'image/jpeg',
                        null,
                        true
                    );

                    // Upload webp untuk UI — folder avatars
                    $path = $storage->upload($tmpFile, 'avatars', 'user_avatar');

                    // Upload format asli (jpg) untuk CV — folder avatars_format
                    $pathOriginal = $storage->upload($tmpFile, 'avatars_format', 'user_avatar');

                    $updateData = [];

                    if ($path) {
                        $updateData['avatar_url'] = $storage->publicUrl($path, 'user_avatar');
                    }

                    if ($pathOriginal) {
                        $updateData['avatar_url_format'] = $storage->publicUrl($pathOriginal, 'user_avatar');
                    }

                    if (!empty($updateData)) {
                        $user->update($updateData);
                    }

                    @unlink($tmpPath);
                }
            } catch (\Exception $e) {
                Log::warning('Gagal ambil avatar Microsoft', ['error' => $e->getMessage()]);
            }

            // ── 5. Cek suspended ──────────────────────────────────────────────
            if ($user->isSuspended()) {
                $message = 'Akun Anda telah ditangguhkan.';
                if ($user->suspension_reason) {
                    $message .= ' Alasan: ' . $user->suspension_reason;
                }
                return redirect()->route('login')->withErrors(['email' => $message]);
            }

            // ── 6. Sync permissions ───────────────────────────────────────────
            $user->syncPermissionsFromRoles();

            // ── 7. Cek role tidak kosong ──────────────────────────────────────
            $roleNames = $user->roles()->pluck('name')->map(fn($r) => strtolower($r));

            if ($roleNames->isEmpty()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun belum memiliki akses (Role). Silakan hubungi Administrator.'
                ]);
            }

            // ── 8. Cek throttle — tidak bisa bypass dengan SSO ulang ──────────
            $throttleKey = $this->throttleKey($user->id);
            if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return redirect()->route('login')->withErrors([
                    'email' => "Akun sementara dikunci karena terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
                ]);
            }

            // ── 9. Simpan ke session sementara — belum login ──────────────────
            session([
                'sso_pending_user_id' => $user->id,
                'sso_verified'        => true,
            ]);

            return redirect()->route('sso.password');

        } catch (\Exception $e) {
            Log::error('Microsoft SSO Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Authentication gagal. Silakan coba lagi.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function showPasswordForm()
    {
        if (!session('sso_pending_user_id')) {
            return redirect()->route('login');
        }

        $user      = User::with('student')->findOrFail(session('sso_pending_user_id'));
        $isDefault = $this->isDefaultPassword($user);

        // Kirim sisa percobaan ke view
        $throttleKey  = $this->throttleKey($user->id);
        $attempts     = RateLimiter::attempts($throttleKey);
        $remaining    = max(0, self::MAX_ATTEMPTS - $attempts);

        return view('auth.sso-password', compact('user', 'isDefault', 'remaining'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function verifyPassword(Request $request)
    {
        $userId = session('sso_pending_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        // ── Rate limiting berdasarkan user_id ─────────────────────────────────
        $throttleKey = $this->throttleKey($userId);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'password' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ]);
        }

        $user = User::with('student')->findOrFail($userId);

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            // Tambah hitungan gagal
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            $remaining = max(0, self::MAX_ATTEMPTS - RateLimiter::attempts($throttleKey));

            return back()->withErrors([
                'password' => $remaining > 0
                    ? "Password salah. Sisa percobaan: {$remaining}x."
                    : "Password salah. Akun dikunci selama " . self::DECAY_SECONDS . " detik."
            ]);
        }

        // ── Password benar — reset throttle & hapus session sementara ─────────
        RateLimiter::clear($throttleKey);
        session()->forget(['sso_pending_user_id', 'sso_verified']);

        Auth::login($user, remember: true);

        $userRoles = $user->roles()->get();
        $roleNames = $userRoles->pluck('name')->map(fn($r) => strtolower($r));

        $user->cacheUserData();
        Cache::put(
            "user:{$user->id}:roles",
            $userRoles->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'module'      => $r->module,
                'is_academic' => (bool) $r->is_academic,
            ])->toArray(),
            now()->addHours(8)
        );

        request()->session()->put('session_version', $user->session_version);
        $user->recordLogin();

        \App\Services\AuditLogger::log(
            module:      'auth',
            action:      'LOGIN',
            description: "Login via Microsoft SSO sebagai {$roleNames->implode(', ')}",
            userId:      $user->id,
        );

        // ── Cek password masih default — redirect ke profile ──────────────────
        if ($this->isDefaultPassword($user)) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Harap ubah password Anda. Password saat ini masih menggunakan password default.');
        }

        // ── Redirect sesuai role ──────────────────────────────────────────────
        if ($roleNames->contains('superadmin')) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        $adminRedirects = [
            'admin_banksoal'      => 'banksoal.dashboard',
            'admin_capstone'      => 'capstone.dashboard',
            'admin_eoffice'       => 'eoffice.dashboard',
            'admin_kemahasiswaan' => 'manajemenmahasiswa.mahasiswa.dashboard',
        ];

        foreach ($adminRedirects as $role => $routeName) {
            if ($roleNames->contains($role)) {
                return redirect()->intended(route($routeName));
            }
        }

        if ($roleNames->intersect(['mahasiswa', 'dosen', 'gpm', 'pengurus_himpunan', 'alumni'])->isNotEmpty()) {
            return redirect()->intended(route('dashboard'));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Role tidak dikenali. Hubungi Administrator.'
        ]);
    }

    // ── Assign role berdasarkan domain email ──────────────────────────────────
    private function assignRoleFromEmail(User $user, string $email, $microsoftUser): void
    {
        if (str_ends_with($email, '@lecturer.undip.ac.id')) {
            $role = Role::where('name', 'dosen')->where('module', 'global')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            Lecturer::firstOrCreate(
                ['user_id' => $user->id],
                ['employee_number' => 'NIP-' . $user->id]
            );

        } elseif (str_ends_with($email, '@students.undip.ac.id')) {
            $role = Role::where('name', 'mahasiswa')->where('module', 'global')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            $nim          = $microsoftUser->getRaw()['surname'] ?? explode('@', $email)[0];
            $cohortSuffix = substr($nim, 6, 2);
            $cohortYear   = (int) ('20' . $cohortSuffix);

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => $nim,
                    'cohort_year'    => $cohortYear > 2000 ? $cohortYear : date('Y'),
                ]
            );
        }
    }
}