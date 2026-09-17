<?php

namespace Modules\Capstone\Http\Controllers;

use App\Services\AcademicRoleSynchronizer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

        // Use the authenticated Laravel session for the Blade frontend.
        return redirect()->route('capstone.blade.dashboard');
    }
}
