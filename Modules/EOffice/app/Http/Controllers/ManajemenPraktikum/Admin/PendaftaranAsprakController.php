<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PendaftaranAsprak;

class PendaftaranAsprakController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranAsprak::with(['user', 'praktikum'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }
        if ($praktikumId = $request->input('praktikum_id')) {
            $query->where('praktikum_id', $praktikumId);
        }
        if ($statusKoor = $request->input('status_koor')) {
            $query->where('status_koor', $statusKoor);
        }

        $pendaftaran = $query->paginate(15)->withQueryString();

        $praktikumList = \Modules\EOffice\Models\Praktikum::with('matkul')
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-asprak', compact('pendaftaran', 'praktikumList'));
    }

    public function approve(Request $request, int $id)
    {
        $pendaftaran = PendaftaranAsprak::findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }
        if ($pendaftaran->status_koor !== 'disetujui') {
            return back()->with('error', 'Pendaftaran belum disetujui oleh Koordinator.');
        }

        $pendaftaran->update(['status' => 'approved']);

        // Assign role asprak ke user
        $user     = $pendaftaran->user;
        $roleAsprak = Role::where('name', 'asprak')->where('module', 'eoffice')->first();

        if ($user && $roleAsprak) {
            $user->roles()->syncWithoutDetaching([$roleAsprak->id]);
        }

        // Tambahkan ke asprak_praktikum
        \Modules\EOffice\Models\AsprakPraktikum::firstOrCreate([
            'praktikum_id' => $pendaftaran->praktikum_id,
            'user_id'      => $pendaftaran->user_id,
            'role' => 'asprak',
        ]);

        return back()->with('success', "Pendaftaran asprak {$user?->name} berhasil diterima.");
    }

    public function reject(Request $request, int $id)
    {
        $pendaftaran = PendaftaranAsprak::findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status'           => 'rejected',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
        ]);

        return back()->with('success', "Pendaftaran asprak {$pendaftaran->user?->name} ditolak.");
    }
}
