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
        Peminjaman::autoExpirePending();

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


            $pendingApproval = Peminjaman::where('status', 'menunggu')->count();

            return view('eoffice::manajemen-ruangan.admin.dashboard', compact('totalRuangan', 'dipakaiHariIni', 'upcomingActivities', 'pendingApproval'));
        }

        // Return regular user dashboard
        $now = \Carbon\Carbon::now();
        $dateToday = $now->format('Y-m-d');
        $timeNow = $now->format('H:i:s');

        $pendingCount = Peminjaman::where('user_id', $user->id)
            ->where('is_hidden_by_user', false)
            ->where('status', 'menunggu')
            ->count();

        $approvedCount = Peminjaman::where('user_id', $user->id)
            ->where('is_hidden_by_user', false)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($dateToday, $timeNow) {
                $q->where('tanggal_pinjam', '>', $dateToday)
                    ->orWhere(function ($sq) use ($dateToday, $timeNow) {
                        $sq->where('tanggal_pinjam', '=', $dateToday)
                            ->where('jam_selesai', '>', $timeNow);
                    });
            })
            ->count();

        $rejectedCount = Peminjaman::where('user_id', $user->id)
            ->where('is_hidden_by_user', false)
            ->where('status', 'ditolak')
            ->count();

        $upcomingBookings = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->where('is_hidden_by_user', false)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($dateToday, $timeNow) {
                $q->where('tanggal_pinjam', '>', $dateToday)
                    ->orWhere(function ($sq) use ($dateToday, $timeNow) {
                        $sq->where('tanggal_pinjam', '=', $dateToday)
                            ->where('jam_selesai', '>', $timeNow);
                    });
            })
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(3)
            ->get();

        $recentBookings = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->where('is_hidden_by_user', false)
            ->latest()
            ->take(5)
            ->get();

        return view('eoffice::manajemen-ruangan.user.dashboard', compact(
            'user',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'upcomingBookings',
            'recentBookings'
        ));
    }
}
