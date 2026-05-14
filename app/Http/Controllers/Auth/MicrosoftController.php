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
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
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

            Log::info('SSO Raw Data', [
                'id'       => $microsoftUser->getId(),
                'name'     => $microsoftUser->getName(),
                'email'    => $microsoftUser->getEmail(),
                'nickname' => $microsoftUser->getNickname(),
                'raw'      => $microsoftUser->getRaw(),
            ]);

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
                    'email' => 'Akses hanya untuk civitas akademika UNDIP.',
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

            // ── 2. SELALU sync role & profil akademik setiap login SSO ───────
            // Tidak hanya wasRecentlyCreated — ini memastikan:
            // a) User lama yang rolenya terhapus akan di-repair
            // b) Record students/lecturers yang hilang akan dibuat ulang
            // c) Role baru di modul (misal: dosen di eoffice) ikut di-assign
            $this->syncAcademicRole($user, $email, $microsoftUser);

            // ── 3. Set default password untuk user baru / belum punya pw ─────
            if ($user->wasRecentlyCreated || !$user->password) {
                $raw = $microsoftUser->getRaw();
                $user->update([
                    'password' => Hash::make(
                        explode(' ', $microsoftUser->getName())[0] .
                        ($raw['surname'] ?? '')
                    ),
                ]);
            }

            // ── 4. Ambil avatar dari Microsoft Graph ──────────────────────────
            try {
                $response = \Illuminate\Support\Facades\Http::withToken($microsoftUser->token)
                    ->withHeaders(['Accept' => 'image/jpeg'])
                    ->get('https://graph.microsoft.com/v1.0/me/photo/$value');

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

                    $path         = $storage->upload($tmpFile, 'avatars', 'user_avatar');
                    $pathOriginal = $storage->upload($tmpFile, 'avatars_format', 'user_avatar');

                    $updateData = [];
                    if ($path)         $updateData['avatar_url']        = $storage->publicUrl($path, 'user_avatar');
                    if ($pathOriginal) $updateData['avatar_url_format'] = $storage->publicUrl($pathOriginal, 'user_avatar');
                    if (!empty($updateData)) $user->update($updateData);

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

            // ── 6. Sync permissions dari roles ────────────────────────────────
            $user->syncPermissionsFromRoles();

            // ── 7. Cek role tidak kosong ──────────────────────────────────────
            $userRoles = $user->roles()->get();
            $roleNames = $userRoles->pluck('name')->map(fn($r) => strtolower($r));

            if ($roleNames->isEmpty()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun belum memiliki akses (Role). Silakan hubungi Administrator.',
                ]);
            }

            // ── 8. Login ──────────────────────────────────────────────────────
            $user->refresh();
            Auth::login($user, remember: false);
            request()->session()->regenerate();

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

            // ── 9. Redirect sesuai role ───────────────────────────────────────
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
    /**
     * Sync role akademik dan record students/lecturers berdasarkan domain email.
     *
     * Dipanggil SETIAP LOGIN (bukan hanya wasRecentlyCreated) agar:
     * - Role yang terhapus secara tidak sengaja langsung di-repair
     * - Record students/lecturers yang hilang dibuat ulang
     * - User yang baru dipindah domain (jarang terjadi) tetap konsisten
     *
     * Pakai syncWithoutDetaching agar role lain (misal: asprak, koor_prak)
     * yang sudah di-assign manual oleh Admin tidak ikut terhapus.
     */
    private function syncAcademicRole(User $user, string $email, $microsoftUser): void
    {
        if (str_ends_with($email, '@lecturer.undip.ac.id') || str_ends_with($email, '@undip.ac.id')) {
            $this->assignDosenRole($user, $email, $microsoftUser);
        } elseif (str_ends_with($email, '@students.undip.ac.id')) {
            $this->assignMahasiswaRole($user, $email, $microsoftUser);
        }
    }

    // ── Dosen ─────────────────────────────────────────────────────────────────
    private function assignDosenRole(User $user, string $email, $microsoftUser): void
    {
        // Assign role global 'dosen' jika belum punya
        $roleGlobal = Role::where('name', 'dosen')
            ->where('module', 'global')
            ->first();

        if ($roleGlobal && !$user->hasRole('dosen')) {
            $user->roles()->syncWithoutDetaching([$roleGlobal->id]);
            Log::info("SSO: Assigned role 'dosen' (global) ke {$email}");
        }

        // Assign role 'dosen' di modul eoffice jika belum punya
        // (dibutuhkan agar $user->hasRole('dosen', 'eoffice') di ManprakController benar)
        $roleEoffice = Role::where('name', 'dosen')
            ->where('module', 'eoffice')
            ->first();

        if ($roleEoffice && !$user->hasRole('dosen', 'eoffice')) {
            $user->roles()->syncWithoutDetaching([$roleEoffice->id]);
            Log::info("SSO: Assigned role 'dosen' (eoffice) ke {$email}");
        }

        // Pastikan record lecturers ada
        $raw           = $microsoftUser->getRaw();
        $employeeNumber = $raw['onPremisesSamAccountName']   // NIP dari AD
            ?? $raw['employeeId']                            // fallback field lain
            ?? explode('@', $email)[0];                      // fallback: bagian sebelum @

        Lecturer::firstOrCreate(
            ['user_id' => $user->id],
            ['employee_number' => $employeeNumber]
        );

        Log::info("SSO: Lecturer record ensured untuk {$email}", [
            'employee_number' => $employeeNumber,
        ]);
    }

    // ── Mahasiswa ─────────────────────────────────────────────────────────────
    private function assignMahasiswaRole(User $user, string $email, $microsoftUser): void
    {
        // Assign role global 'mahasiswa' jika belum punya
        $roleGlobal = Role::where('name', 'mahasiswa')
            ->where('module', 'global')
            ->first();

        if ($roleGlobal && !$user->hasRole('mahasiswa')) {
            $user->roles()->syncWithoutDetaching([$roleGlobal->id]);
            Log::info("SSO: Assigned role 'mahasiswa' (global) ke {$email}");
        }

        // Assign role 'mahasiswa' di modul eoffice jika ada
        $roleEoffice = Role::where('name', 'mahasiswa')
            ->where('module', 'eoffice')
            ->first();

        if ($roleEoffice && !$user->hasRole('mahasiswa', 'eoffice')) {
            $user->roles()->syncWithoutDetaching([$roleEoffice->id]);
            Log::info("SSO: Assigned role 'mahasiswa' (eoffice) ke {$email}");
        }

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
                'cohort_year'    => $cohortYear,
            ]
        );

        Log::info("SSO: Student record ensured untuk {$email}", [
            'nim'         => $nim,
            'cohort_year' => $cohortYear,
        ]);
    }

    /**
     * Ekstrak tahun angkatan dari NIM mahasiswa UNDIP.
     *
     * Format NIM UNDIP: XXXXXYYZZZ
     *   - XX = 2 digit terakhir tahun angkatan
     *   - Posisi bisa bervariasi tergantung prodi
     *
     * Strategi: coba posisi 0-1 dulu (misal NIM dimulai dgn tahun),
     * jika tidak masuk akal (< 2000 atau > tahun sekarang), coba posisi 6-7
     * seperti yang ada di kode lama.
     */
    private function extractCohortYear(string $nim): int
    {
        $currentYear = (int) date('Y');

        // Coba 2 digit pertama: format "24060122140008" → "24" → 2024
        if (strlen($nim) >= 2) {
            $fromPrefix = (int) ('20' . substr($nim, 0, 2));
            if ($fromPrefix >= 2000 && $fromPrefix <= $currentYear) {
                return $fromPrefix;
            }
        }

        // Coba digit ke-7 & ke-8: format lama yang dipakai sebelumnya
        if (strlen($nim) >= 8) {
            $fromMiddle = (int) ('20' . substr($nim, 6, 2));
            if ($fromMiddle >= 2000 && $fromMiddle <= $currentYear) {
                return $fromMiddle;
            }
        }

        // Fallback: tahun sekarang
        return $currentYear;
    }
}