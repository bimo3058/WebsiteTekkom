<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use App\Services\AcademicRoleSynchronizer;
use App\Services\AuditLogger;
use App\Services\SsoAccountResolver;
use App\Services\SupabaseStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function __construct(
        private readonly AcademicRoleSynchronizer $academicRoles,
        private readonly SsoAccountResolver $ssoAccounts,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    public function redirect()
    {
        return Socialite::driver('azure')
            ->scopes(['User.Read'])
            ->redirect();
    }

    public function switchAccount()
    {
        return Socialite::driver('azure')
            ->scopes(['User.Read'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // ─────────────────────────────────────────────────────────────────────────
    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();

            $email = strtolower(trim((string) $microsoftUser->getEmail()));
            $allowedDomains = [
                '@students.undip.ac.id',
                '@lecturer.undip.ac.id',
                '@undip.ac.id',
            ];

            $isAllowed = collect($allowedDomains)
                ->contains(fn ($domain) => str_ends_with($email, $domain));

            if (! $isAllowed) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akses hanya untuk civitas akademika UNDIP.',
                ]);
            }

            // 1. Create or refresh the SSO identity only when needed.
            $ssoData = [
                'id' => (string) $microsoftUser->getId(),
                'email' => $email,
                'name' => (string) $microsoftUser->getName(),
            ];
            $raw = (array) $microsoftUser->getRaw();
            $resolution = $this->ssoAccounts->resolve(
                $ssoData['id'],
                $ssoData['name'],
                $email,
                $raw,
            );

            if ($resolution['ambiguous']) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Ada lebih dari satu akun impor dengan identitas yang sama. Hubungi Administrator agar akun dapat ditautkan dengan aman.',
                ]);
            }

            $user = $resolution['user'];
            $wasCreated = $resolution['created'];
            $wasClaimed = $resolution['claimed'];

            $syncTtl = max(1, (int) config('auth.sso.sync_ttl_seconds', 21_600));
            $identityChanged = (string) $user->external_id !== $ssoData['id']
                || (string) $user->name !== $ssoData['name']
                || $user->sso_data !== $ssoData;
            $identityStale = ! $user->last_synced_from_sso
                || $user->last_synced_from_sso->lt(now()->subSeconds($syncTtl));
            $refreshIdentity = $wasCreated || $wasClaimed || $identityChanged || $identityStale;

            if ($this->academicRoles->shouldSync($user, $refreshIdentity)) {
                $this->syncAcademicRole($user, $email, $microsoftUser);
                $this->academicRoles->markSynced($user);
            }

            if ($refreshIdentity) {
                $user->forceFill([
                    'external_id' => $ssoData['id'],
                    'name' => $ssoData['name'],
                    'sso_data' => $ssoData,
                    'last_synced_from_sso' => now(),
                ])->save();
            }

            // 2. A Redis marker avoids repeated profile/role/permission repairs.

            // 3. Set the fallback password only once.
            if ($wasCreated || ! $user->password) {
                $user->update([
                    'password' => Hash::make(
                        explode(' ', $microsoftUser->getName())[0].
                        ($raw['surname'] ?? '')
                    ),
                ]);
            }

            // 4. Reject suspended accounts before scheduling optional work.
            if ($user->isSuspended()) {
                $message = 'Akun Anda telah ditangguhkan.';
                if ($user->suspension_reason) {
                    $message .= ' Alasan: '.$user->suspension_reason;
                }

                return redirect()->route('login')->withErrors(['email' => $message]);
            }

            if (! $user->getRawOriginal('avatar_url')) {
                defer(
                    fn () => $this->syncAvatar($user, $microsoftUser),
                    "sso-avatar:{$user->id}"
                );
            }

            // 5. Read roles from Redis, falling back to one database query.
            // unsetRelation() wajib dipanggil agar Spatie tidak pakai cache
            // in-memory yang sudah di-load sebelum syncAcademicRole dipanggil.
            // Tanpa ini, user yang baru saja di-assign role bisa tetap terdeteksi
            // "roles kosong" karena object $user masih menyimpan snapshot lama.
            $user->unsetRelation('roles');
            $userRoles = $user->getCachedRoleData();
            $roleNames = $userRoles->pluck('name')->map(fn ($role) => strtolower($role));

            if ($roleNames->isEmpty()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun belum memiliki akses (Role). Silakan hubungi Administrator.',
                ]);
            }

            // 6. Create the Redis-backed Laravel session.
            Auth::login($user, remember: false);
            request()->session()->regenerate();

            $user->cacheUserData();

            request()->session()->put('session_version', $user->session_version);

            AuditLogger::log(
                module: 'auth',
                action: 'LOGIN',
                description: "Login via Microsoft SSO sebagai {$roleNames->implode(', ')}",
                userId: $user->id,
            );

            // 7. Redirect according to the cached role list.
            if ($roleNames->contains('superadmin')) {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            $adminRedirects = [
                'admin_banksoal' => 'banksoal.dashboard',
                'admin_capstone' => 'capstone.dashboard',
                'admin_eoffice' => 'eoffice.dashboard',
                'admin_kemahasiswaan' => 'manajemenmahasiswa.mahasiswa.dashboard',
            ];

            foreach ($adminRedirects as $role => $routeName) {
                if ($roleNames->contains($role)) {
                    return redirect()->intended(route($routeName));
                }
            }

            if ($roleNames->intersect(['mahasiswa', 'dosen', 'gpm', 'pengurus_himpunan', 'alumni', 'dosen_koor'])->isNotEmpty()) {
                return redirect()->intended(route('dashboard'));
            }

            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Role tidak dikenali. Hubungi Administrator.',
            ]);

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
    /** Fetch and store the avatar after the login redirect has been sent. */
    private function syncAvatar(User $user, $microsoftUser): void
    {
        $tmpPath = null;

        try {
            $response = Http::withToken($microsoftUser->token)
                ->accept('image/jpeg')
                ->connectTimeout(max(1, (int) config('auth.sso.avatar_connect_timeout_seconds', 2)))
                ->timeout(max(1, (int) config('auth.sso.avatar_timeout_seconds', 3)))
                ->get('https://graph.microsoft.com/v1.0/me/photo/$value');

            if (! $response->successful()) {
                return;
            }

            $tmpPath = tempnam(sys_get_temp_dir(), 'ms_avatar_');
            if ($tmpPath === false || file_put_contents($tmpPath, $response->body()) === false) {
                return;
            }

            $tmpFile = new UploadedFile(
                $tmpPath,
                $user->id.'_avatar.jpg',
                'image/jpeg',
                null,
                true
            );
            $storage = app(SupabaseStorage::class);
            $path = $storage->upload($tmpFile, 'avatars', 'user_avatar');
            $pathOriginal = $storage->upload($tmpFile, 'avatars_format', 'user_avatar');
            $updateData = [];

            if ($path) {
                $updateData['avatar_url'] = $storage->publicUrl($path, 'user_avatar');
            }
            if ($pathOriginal) {
                $updateData['avatar_url_format'] = $storage->publicUrl($pathOriginal, 'user_avatar');
            }
            if ($updateData !== []) {
                $user->update($updateData);
                cache()->forget("user:{$user->id}:data");
            }
        } catch (\Throwable $exception) {
            Log::warning('Gagal ambil avatar Microsoft', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        } finally {
            if (is_string($tmpPath) && is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    /**
     * Repair the academic profile and global role when the Redis marker expires.
     * Additional roles assigned by an administrator are preserved.
     */
    private function syncAcademicRole(User $user, string $email, $microsoftUser): void
    {
        if (str_ends_with($email, '@lecturer.undip.ac.id') || str_ends_with($email, '@undip.ac.id')) {
            $this->ensureLecturerProfile($user, $email, $microsoftUser);
        } elseif (str_ends_with($email, '@students.undip.ac.id')) {
            $this->ensureStudentProfile($user, $email, $microsoftUser);
        }

        // Profil akademik adalah sumber kebenaran role SSO:
        // lecturers -> dosen, students -> mahasiswa.
        $this->academicRoles->sync($user);
    }

    // ── Dosen ─────────────────────────────────────────────────────────────────
    private function ensureLecturerProfile(User $user, string $email, $microsoftUser): void
    {
        // Pastikan record lecturers ada
        $raw = $microsoftUser->getRaw();
        $employeeNumber = $raw['onPremisesSamAccountName']   // NIP dari AD
            ?? $raw['employeeId']                            // fallback field lain
            ?? explode('@', $email)[0];                      // fallback: bagian sebelum @

        Lecturer::updateOrCreate(
            ['user_id' => $user->id],
            ['employee_number' => $employeeNumber]
        );

        Log::info("SSO: Lecturer record ensured untuk {$email}", [
            'employee_number' => $employeeNumber,
        ]);
    }

    // ── Mahasiswa ─────────────────────────────────────────────────────────────
    private function ensureStudentProfile(User $user, string $email, $microsoftUser): void
    {
        // Ekstrak NIM dan tahun angkatan
        // Microsoft AD untuk UNDIP menyimpan NIM di field 'surname' atau 'onPremisesSamAccountName'
        $raw = $microsoftUser->getRaw();
        $nim = $raw['onPremisesSamAccountName']  // NIM dari AD (paling akurat)
            ?? $raw['surname']                   // fallback lama
            ?? explode('@', $email)[0];          // fallback: prefix email

        // Hitung cohort year dari digit ke-7 & ke-8 NIM (format UNDIP: XXXXXYYZZZ)
        // Contoh: NIM 24060122140008 → angkatan 2024 (digit[0..1] = "24")
        // Atau ambil dari karakter pertama jika format berbeda
        $cohortYear = $this->extractCohortYear($nim);

        Student::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => $nim,
                'cohort_year' => $cohortYear,
            ]
        );

        Log::info("SSO: Student record ensured untuk {$email}", [
            'nim' => $nim,
            'cohort_year' => $cohortYear,
        ]);
    }

    /**
     * Ekstrak tahun angkatan dari NIM mahasiswa UNDIP.
     *
     * Format NIM UNDIP: XXXXXYYZZZ (14 digit)
     * Contoh: 21120123120029
     *   - digit ke-7 & ke-8 (index 6–7) = "23" → angkatan 2023
     *
     * Strategi: utamakan posisi index 6–7, fallback ke prefix 0–1
     * jika NIM terlalu pendek.
     */
    private function extractCohortYear(string $nim): int
    {
        $currentYear = (int) date('Y');

        // Primary: digit ke-7 & ke-8 (index 6–7) — format standar NIM UNDIP
        // Contoh: "21120123120029" → substr(6,2) = "23" → 2023
        if (strlen($nim) >= 8) {
            $fromMiddle = (int) ('20'.substr($nim, 6, 2));
            if ($fromMiddle >= 2000 && $fromMiddle <= $currentYear) {
                return $fromMiddle;
            }
        }

        // Fallback: 2 digit pertama (untuk format NIM non-standar / pendek)
        if (strlen($nim) >= 2) {
            $fromPrefix = (int) ('20'.substr($nim, 0, 2));
            if ($fromPrefix >= 2000 && $fromPrefix <= $currentYear) {
                return $fromPrefix;
            }
        }

        // Last resort: tahun sekarang
        return $currentYear;
    }
}
