<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Ruangan;

class JadwalController extends Controller
{
    /**
     * Display a listing of the internal schedules.
     */
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'semua');
        $query = MrJadwalInternal::with('ruangan')->orderBy('created_at', 'desc');

        if ($tipe === 'rutin') {
            $query->where('tipe_jadwal', 'rutin')->orderBy('hari', 'asc');
        } elseif ($tipe === 'spesifik') {
            $query->where('tipe_jadwal', 'spesifik')->orderBy('tanggal_spesifik', 'asc');
        }

        $jadwals = $query->paginate(15);
        $ruangans = Ruangan::where('is_active', true)->orderBy('nama', 'asc')->get();

        return view('eoffice::manajemen-ruangan.admin.jadwal.index', compact('jadwals', 'ruangans', 'tipe'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string|max:255',
        ]);

        // Logic check dependent on type
        if ($request->tipe_jadwal === 'rutin') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
        } else {
            $request->validate(['tanggal_spesifik' => 'required|date']);
        }

        MrJadwalInternal::create([
            'ruangan_id' => $request->ruangan_id,
            'tipe_jadwal' => $request->tipe_jadwal,
            'kategori' => $request->kategori,
            'hari' => $request->tipe_jadwal === 'rutin' ? $request->hari : null,
            'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $request->tanggal_spesifik : null,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('eoffice.peminjaman.admin.jadwal-internal.index')
            ->with('success', 'Jadwal Internal berhasil ditambahkan. Ruangan terkait akan otomatis terblokir pada waktu tersebut.');
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);

        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required|string|max:255',
        ]);

        if ($request->tipe_jadwal === 'rutin') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
        } else {
            $request->validate(['tanggal_spesifik' => 'required|date']);
        }

        $jadwal->update([
            'ruangan_id' => $request->ruangan_id,
            'tipe_jadwal' => $request->tipe_jadwal,
            'kategori' => $request->kategori,
            'hari' => $request->tipe_jadwal === 'rutin' ? $request->hari : null,
            'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $request->tanggal_spesifik : null,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('eoffice.peminjaman.admin.jadwal-internal.index')
            ->with('success', 'Konfigurasi Jadwal Internal berhasil diperbarui.');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy($id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('eoffice.peminjaman.admin.jadwal-internal.index')
            ->with('success', 'Jadwal Internal berhasil dihapus. Pemblokiran ruangan telah dicabut.');
    }
}
