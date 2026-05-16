<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;

class PendaftaranKoorController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranKoordinator::with(['user', 'praktikum'])
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $pendaftaran = $query->paginate(15)->withQueryString();

        return view('eoffice::manajemen-praktikum.admin.pendaftaran-koor', compact('pendaftaran'));
    }

    public function approve(Request $request, int $id)
    {
        $pendaftaran = PendaftaranKoordinator::findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update(['status' => 'approved']);

        $user    = $pendaftaran->user;
        $roleKoor = Role::where('name', 'koor_prak')->where('module', 'eoffice')->first();

        if ($user && $roleKoor) {
            $user->roles()->syncWithoutDetaching([$roleKoor->id]);
        }

        // Set user sebagai koor di tabel praktikum
        Praktikum::where('id', $pendaftaran->praktikum_id)
            ->update(['koor_id' => $pendaftaran->user_id]);

        return back()->with('success', "Pendaftaran koordinator {$user?->name} berhasil diterima.");
    }

    public function reject(Request $request, int $id)
    {
        $pendaftaran = PendaftaranKoordinator::findOrFail($id);

        if ($pendaftaran->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $pendaftaran->update([
            'status'           => 'rejected',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
        ]);

        return back()->with('success', "Pendaftaran koordinator {$pendaftaran->user?->name} ditolak.");
    }
}