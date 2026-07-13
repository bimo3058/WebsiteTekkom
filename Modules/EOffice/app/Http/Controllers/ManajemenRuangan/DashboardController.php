<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan;

use App\Http\Controllers\Controller;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\Peminjaman;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('superadmin') || $user->hasRole('admin_eoffice'));

        if ($isAdmin) {
            $totalRuangan = Ruangan::where('is_active', true)->count();
            $dipakaiHariIni = Peminjaman::where('status', 'disetujui')
                ->whereDate('tanggal_pinjam', \Carbon\Carbon::today())
                ->count();
            $recentRuangan = Ruangan::orderBy('created_at', 'desc')->take(5)->get();

            Peminjaman::autoExpirePending(); // Ensure garbage collection triggers metrics updates
            $pendingApproval = Peminjaman::where('status', 'menunggu')->count();

            return view('eoffice::manajemen-ruangan.admin.dashboard', compact('totalRuangan', 'dipakaiHariIni', 'recentRuangan', 'pendingApproval'));
        }

        // Return regular user dashboard
        return view('eoffice::manajemen-ruangan.user.dashboard');
    }
}
