<?php

namespace Modules\Capstone\Http\Controllers;

use App\Services\AcademicRoleSynchronizer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Capstone\Support\CapstoneActor;

class LaunchController extends Controller
{
    public function launch(Request $request, AcademicRoleSynchronizer $academicRoles)
    {
        $user = $request->user();

        // Repair akun lama yang sudah memiliki profil akademik, tetapi role
        // globalnya belum pernah tersinkron saat login SSO.
        $academicRoles->syncIfNeeded($user);

        if (! $user->can('capstone.view')) {
            return response('Anda tidak memiliki akses ke modul Capstone.', 403);
        }

        $role = CapstoneActor::role($user);
        if (! $role) {
            return response('Akun Anda tidak memiliki role Capstone.', 403);
        }

        if ($role === 'mahasiswa' && ! $user->student) {
            $user->tokens()->where('name', 'capstone-fe')->delete();

            return redirect()->route('dashboard')->with(
                'error',
                'Data mahasiswa Anda belum terdaftar di SICATA. Silakan hubungi administrator.'
            );
        }
        if ($role === 'dosen' && ! $user->lecturer) {
            return response('Profil dosen untuk akun SSO ini tidak ditemukan.', 403);
        }

        $ott = Str::random(64);
        Cache::put("capstone:ott:{$ott}", [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => hash('sha256', (string) $request->userAgent()),
            'csrf_token' => csrf_token(),
            'created_at' => now()->toIso8601String(),
        ], now()->addSeconds((int) config('capstone.ott_ttl_seconds', 60)));

        $feUrl = rtrim((string) config('capstone.frontend_url'), '/');

        return redirect()->away($feUrl.'/auth/exchange?'.http_build_query(['ott' => $ott]));
    }
}
