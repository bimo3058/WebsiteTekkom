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
            $upcomingActivities = Peminjaman::with(['ruangan', 'user'])
                ->whereIn('status', ['disetujui', 'menunggu'])
                ->where(function ($q) {
                    $q->whereDate('tanggal_pinjam', '>', \Carbon\Carbon::today())
                        ->orWhere(function ($subq) {
                            $subq->whereDate('tanggal_pinjam', \Carbon\Carbon::today())
                                ->whereTime('jam_selesai', '>', \Carbon\Carbon::now()->format('H:i:s'));
                        });
                })
                ->orderBy('tanggal_pinjam', 'asc')
                ->orderBy('jam_mulai', 'asc')
                ->take(7)
                ->get();

            Peminjaman::autoExpirePending(); // Ensure garbage collection triggers metrics updates
            $pendingApproval = Peminjaman::where('status', 'menunggu')->count();

            return view('eoffice::manajemen-ruangan.admin.dashboard', compact('totalRuangan', 'dipakaiHariIni', 'upcomingActivities', 'pendingApproval'));
        }

        // Return regular user dashboard
        return view('eoffice::manajemen-ruangan.user.dashboard');
    }
}
