<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Capstone\Support\CapstoneActor;

class AuthBridgeController extends Controller
{
    public function exchange(Request $request)
    {
        $request->validate([
            'ott' => 'required|string|size:64',
        ]);

        // Rate limit per IP â€” cegah brute force
        $key = 'capstone-auth-exchange:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['message' => 'Too many attempts.'], 429);
        }
        RateLimiter::hit($key, 60);

        // pull = ambil + hapus (atomic) â†’ mencegah replay attack
        $payload = Cache::pull("capstone:ott:{$request->ott}");

        if (! $payload) {
            return response()->json(['message' => 'Token kadaluarsa atau invalid.'], 401);
        }

        if (($payload['ip'] ?? null) !== $request->ip()
            || ! hash_equals(
                (string) ($payload['user_agent'] ?? ''),
                hash('sha256', (string) $request->userAgent())
            )) {
            return response()->json(['message' => 'Token tidak cocok dengan sesi browser.'], 401);
        }

        $user = User::with(['student', 'lecturer', 'roles'])->findOrFail($payload['user_id']);

        $primaryRole = CapstoneActor::role($user);
        if (! $primaryRole) {
            return response()->json(['message' => 'User tidak punya role Capstone.'], 403);
        }

        if (! $user->can('capstone.view')) {
            return response()->json(['message' => 'User tidak memiliki akses ke modul Capstone.'], 403);
        }

        if ($primaryRole === 'mahasiswa') {
            if (! $user->student) {
                $user->tokens()->where('name', 'capstone-fe')->delete();

                return response()->json([
                    'message' => 'Data mahasiswa belum terdaftar di SICATA.',
                    'code' => 'SICATA_STUDENT_NOT_REGISTERED',
                    'redirect_url' => route('dashboard'),
                ], 403);
            }
        } elseif ($primaryRole === 'dosen') {
            CapstoneActor::lecturer($user);
        }

        $user->tokens()->where('name', 'capstone-fe')->delete();
        $token = $user->createToken(
            'capstone-fe',
            ['capstone:access'],
            now()->addHours((int) config('capstone.token_ttl_hours', 8))
        );

        return response()->json(['data' => [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at,
            'user' => CapstoneActor::payload($user, $primaryRole),
            'logout' => [
                'url' => route('capstone.logout'),
                'csrf_token' => $payload['csrf_token'] ?? null,
            ],
        ]]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->loadMissing(['student', 'lecturer', 'roles']);

        return response()->json([
            'data' => CapstoneActor::payload(
                $user,
                $request->header('X-Capstone-Role')
            ),
        ]);
    }

    public function setActiveRole(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:admin,dosen,mahasiswa'],
        ]);

        $user = $request->user()->loadMissing(['student', 'lecturer', 'roles']);
        $role = CapstoneActor::role($user, $validated['role']);

        if ($role !== $validated['role']) {
            return response()->json(['message' => 'Role tidak dimiliki pengguna.'], 403);
        }

        if ($role === 'mahasiswa') {
            if (! $user->student) {
                $user->tokens()->where('name', 'capstone-fe')->delete();

                return response()->json([
                    'message' => 'Data mahasiswa belum terdaftar di SICATA.',
                    'code' => 'SICATA_STUDENT_NOT_REGISTERED',
                    'redirect_url' => route('dashboard'),
                ], 403);
            }
        } elseif ($role === 'dosen') {
            CapstoneActor::lecturer($user);
        }

        return response()->json(['data' => CapstoneActor::payload($user, $role)]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
