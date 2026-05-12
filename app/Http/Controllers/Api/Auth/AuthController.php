<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SwitchRoleRequest;
use App\Http\Resources\Auth\PenggunaResource;
use App\Models\Pengguna;
use App\Models\SystemRole;
use App\Models\SesiLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // ─── POST /api/auth/login ─────────────────────────────────────────────────

    /**
     * Login pengguna dan generate Sanctum token.
     *
     * - Validasi email + password
     * - Default role aktif = role tertinggi user
     * - Simpan ke SESI_LOGIN
     * - Return token + data user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // 1. Cari user berdasarkan email
        $pengguna = Pengguna::with('roles')
                            ->where('email', $request->email)
                            ->first();

        // 2. Verifikasi password
        if (! $pengguna || ! Hash::check($request->password, $pengguna->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak valid.',
            ], 401);
        }

        // 3. Cek status user
        if ($pengguna->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ], 403);
        }

        // 4. Cek user punya role
        $roles = $pengguna->roles()->where('pengguna_role.status', 'aktif')->get();
        if ($roles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki role yang aktif.',
            ], 403);
        }

        // 5. Tentukan role aktif default = role tertinggi
        $roleTertinggi  = $pengguna->roleTertinggi();
        $roleAktif      = SystemRole::where('nama', $roleTertinggi)->first();

        // 6. Generate Sanctum token
        $tokenName  = "login_{$pengguna->id}_{$roleAktif->nama}";
        $tokenResult = $pengguna->createToken($tokenName);
        $tokenPlain  = $tokenResult->plainTextToken;

        // 7. Simpan ke SESI_LOGIN
        SesiLogin::create([
            'pengguna_id'      => $pengguna->id,
            'role_aktif_id'    => $roleAktif->id,
            'token'            => $tokenResult->accessToken->token, // hashed token
            'kedaluwarsa_pada' => now()->addDays(7),
        ]);

        // 8. File log (non-critical)
        Log::info('User login', [
            'pengguna_id' => $pengguna->id,
            'email'       => $pengguna->email,
            'role_aktif'  => $roleAktif->nama,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'       => $tokenPlain,
                'token_type'  => 'Bearer',
                'user'        => new PenggunaResource($pengguna->load('roles')),
                'role_aktif'  => $roleAktif->nama,
            ],
        ]);
    }

    // ─── POST /api/auth/switch-role ───────────────────────────────────────────

    /**
     * Switch role aktif pengguna yang sudah login.
     *
     * - User hanya bisa switch ke role yang ia MILIKI
     * - Update SESI_LOGIN terbaru dengan role baru
     * - Token tidak di-regenerate (sama token, beda role aktif)
     */
    public function switchRole(SwitchRoleRequest $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna   = $request->user();
        $roleDiminta = $request->input('role');

        // 1. Cek apakah user punya role ini dan aktif
        $role = $pengguna->roles()
                         ->where('nama', $roleDiminta)
                         ->where('pengguna_role.status', 'aktif')
                         ->first();

        if (! $role) {
            return response()->json([
                'success' => false,
                'message' => "Anda tidak memiliki role '{$roleDiminta}' atau role ini tidak aktif.",
            ], 403);
        }

        // 2. Update SESI_LOGIN terbaru
        $sesiAktif = SesiLogin::where('pengguna_id', $pengguna->id)
                               ->whereNull('logout_pada')
                               ->latest('login_pada')
                               ->first();

        if ($sesiAktif) {
            $sesiAktif->update(['role_aktif_id' => $role->id]);
        }

        // 3. File log
        Log::info('User switch role', [
            'pengguna_id' => $pengguna->id,
            'role_baru'   => $roleDiminta,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Role aktif berhasil diubah ke '{$roleDiminta}'.",
            'data'    => [
                'user'       => new PenggunaResource($pengguna->load('roles')),
                'role_aktif' => $roleDiminta,
            ],
        ]);
    }

    // ─── POST /api/auth/logout ────────────────────────────────────────────────

    /**
     * Logout — revoke token Sanctum + mark SESI_LOGIN sebagai logout.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user();

        // 1. Update SESI_LOGIN: set logout_pada
        SesiLogin::where('pengguna_id', $pengguna->id)
                  ->whereNull('logout_pada')
                  ->latest('login_pada')
                  ->first()
                  ?->update(['logout_pada' => now()]);

        // 2. Revoke token Sanctum saat ini
        $request->user()->currentAccessToken()->delete();

        Log::info('User logout', ['pengguna_id' => $pengguna->id]);

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    // ─── GET /api/auth/me ─────────────────────────────────────────────────────

    /**
     * Ambil data user yang sedang login + role aktifnya.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = $request->user()->load('roles');

        // Ambil role aktif dari SESI_LOGIN terbaru
        $sesiAktif  = SesiLogin::with('roleAktif')
                                ->where('pengguna_id', $pengguna->id)
                                ->whereNull('logout_pada')
                                ->latest('login_pada')
                                ->first();

        $roleAktif = $sesiAktif?->roleAktif?->nama ?? $pengguna->roleTertinggi();

        return response()->json([
            'success' => true,
            'data'    => [
                'user'       => new PenggunaResource($pengguna),
                'role_aktif' => $roleAktif,
            ],
        ]);
    }
}
