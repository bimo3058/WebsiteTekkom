<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Peminjaman;

class PersetujuanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with(['user', 'ruangan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('eoffice::manajemen-ruangan.admin.persetujuan.index', compact('peminjamans'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => $request->status,
            'alasan_penolakan' => $request->status == 'ditolak' ? $request->alasan_penolakan : null,
            'waktu_approval' => now(),
        ]);

        $msg = $request->status == 'disetujui' ? 'berhasil disetujui' : 'telah ditolak';
        return redirect()->back()->with('success', "Pengajuan peminjaman {$msg}.");
    }
}
