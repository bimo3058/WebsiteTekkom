<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\Praktikum;

class DaftarAsprakController extends Controller
{
    /**
     * Tampilkan form & status pendaftaran asprak.
     */
    public function index()
    {
        $user = auth()->user();

        // Status pendaftaran asprak terakhir
        $statusPendaftaran = PendaftaranAsprak::with('praktikum')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        // Daftar praktikum yang bisa didaftarkan (yang user sudah ikut)
        $praktikumList = Praktikum::whereHas('daftarPraktikan', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'aktif')
            ->get();

        return view('eoffice::manajemen-praktikum.mahasiswa.daftar-asprak', compact(
            'statusPendaftaran',
            'praktikumList'
        ));
    }

    /**
     * Submit pendaftaran asprak.
     */
    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'ipk'          => 'required|numeric|min:0|max:4',
            'motivasi'     => 'required|string|min:20|max:1000',
            'cv'           => 'nullable|file|max:5120|mimes:pdf,docx',
            'jadwal'       => 'nullable|array',
        ]);

        $user = auth()->user();

        // Cek sudah pernah daftar di praktikum ini dan masih pending/approved
        $existing = PendaftaranAsprak::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran aktif untuk praktikum ini.');
        }

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('asprak-cv/' . $user->id, 'local');
        }

        PendaftaranAsprak::create([
            'user_id'      => $user->id,
            'praktikum_id' => $request->praktikum_id,
            'ipk'          => $request->ipk,
            'motivasi'     => $request->motivasi,
            'cv_path'      => $cvPath,
            'jadwal'       => $request->jadwal ?? [],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Pendaftaran asprak berhasil dikirim! Tunggu konfirmasi dari Admin/Koordinator.');
    }

    /**
     * Submit pendaftaran koordinator.
     */
    public function daftarKoor(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'ipk'          => 'required|numeric|min:0|max:4',
            'motivasi'     => 'required|string|min:20|max:1000',
        ]);

        $user = auth()->user();

        $existing = PendaftaranKoordinator::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran koordinator aktif untuk praktikum ini.');
        }

        PendaftaranKoordinator::create([
            'user_id'      => $user->id,
            'praktikum_id' => $request->praktikum_id,
            'ipk'          => $request->ipk,
            'motivasi'     => $request->motivasi,
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Pendaftaran koordinator berhasil dikirim!');
    }
}
