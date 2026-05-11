<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class AuthBridgeController extends Controller
{
    public function exchange(Request $request)
    {
        $request->validate([
            'ott' => 'required|string|size:64',
        ]);

        // Rate limit per IP â€” cegah brute force
        $key = 'capstone-auth-exchange:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['message' => 'Too many attempts.'], 429);
        }
        RateLimiter::hit($key, 60);

        // pull = ambil + hapus (atomic) â†’ mencegah replay attack
        $payload = Cache::pull("capstone:ott:{$request->ott}");

        if (!$payload) {
            return response()->json(['message' => 'Token kadaluarsa atau invalid.'], 401);
        }

        $user = User::with(['student', 'lecturer', 'roles'])->findOrFail($payload['user_id']);

        $primaryRole = $this->resolvePrimaryRole($user);
        if (!$primaryRole) {
            return response()->json(['message' => 'User tidak punya role Capstone.'], 403);
        }

        // Generate Sanctum token (TTL 8 jam)
        $token = $user->createToken('capstone-fe', ['*'], now()->addHours(8));

        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $token->accessToken->expires_at,
            'user'         => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $primaryRole,
                'lecturer_id' => $user->lecturer?->id,
                'student_id'  => $user->student?->id,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out.']);
    }

    private function resolvePrimaryRole(User $user): ?string
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin_capstone')) {
            return 'admin';
        }
        if ($user->hasRole('dosen')) return 'dosen';
        if ($user->hasRole('mahasiswa')) return 'mahasiswa';
        return null;
    }
}
