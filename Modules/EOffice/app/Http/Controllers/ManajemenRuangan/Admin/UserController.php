<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\MrBlacklist;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil User ID dari mahasiswa yang pernah booking ruangan
        $activeUserIds = Peminjaman::select('user_id')->distinct()->pluck('user_id');

        $query = User::whereIn('id', $activeUserIds);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Load relationships manually to avoid mutating global App\Models\User
        $blacklists = MrBlacklist::whereIn('user_id', $users->pluck('id'))->get()->keyBy('user_id');

        // Get total booking count manually for these users
        $bookingCounts = Peminjaman::whereIn('user_id', $users->pluck('id'))
            ->selectRaw('user_id, count(*) as count')
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        return view('eoffice::manajemen-ruangan.admin.user.index', compact('users', 'blacklists', 'bookingCounts'));
    }

    public function history(User $user)
    {
        $peminjamans = Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Render a dedicated history partial or return JSON (we will return JSON and render it via alpine)
        return response()->json([
            'success' => true,
            'name' => $user->name,
            'data' => $peminjamans->map(function ($p) {
                return [
                    'ruangan' => $p->ruangan->nama ?? 'Ruangan Dihapus',
                    'tujuan' => $p->tujuan,
                    'tanggal' => \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y'),
                    'waktu' => \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($p->jam_selesai)->format('H:i'),
                    'status' => $p->status
                ];
            })
        ]);
    }

    public function toggleBlacklist(Request $request, User $user)
    {
        $blacklist = MrBlacklist::where('user_id', $user->id)->first();

        if ($blacklist) {
            $blacklist->delete();
            return back()->with('success', 'Status akun ' . $user->name . ' berhasil dipulihkan.');
        } else {
            $request->validate(['alasan' => 'required|string']);
            MrBlacklist::create([
                'user_id' => $user->id,
                'alasan' => $request->alasan
            ]);
            return back()->with('success', 'Akun ' . $user->name . ' berhasil di-suspend dari peminjaman ruangan.');
        }
    }
}
