<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan;

use App\Http\Controllers\Controller;
use Modules\EOffice\Models\Ruangan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('superadmin') || $user->hasRole('admin_eoffice'));

        if ($isAdmin) {
            $totalRuangan = Ruangan::where('is_active', true)->count();
            $totalUser = User::count();
            $recentRuangan = Ruangan::orderBy('created_at', 'desc')->take(5)->get();
            // Nanti ditambahkan query Peminjaman kalau modulnya sudah siap

            return view('eoffice::manajemen-ruangan.admin.dashboard', compact('totalRuangan', 'totalUser', 'recentRuangan'));
        }

        // Return regular user dashboard
        return view('eoffice::manajemen-ruangan.user.dashboard');
    }
}
