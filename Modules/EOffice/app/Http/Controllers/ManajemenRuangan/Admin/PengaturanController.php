<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Pengaturan;
use Modules\EOffice\Models\TanggalLibur;

class PengaturanController extends Controller
{
    public function index()
    {
        // Get all settings mapped by key
        $settings = Pengaturan::pluck('value', 'key')->toArray();

        // Defaults if table is empty
        $jamBuka = $settings['jam_buka'] ?? '08:00';
        $jamTutup = $settings['jam_tutup'] ?? '16:00';
        $bukaAkhirPekan = filter_var($settings['buka_akhir_pekan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Get blackout dates
        $tanggalLibur = TanggalLibur::orderBy('tanggal')->get();

        return view('eoffice::manajemen-ruangan.admin.setting.index', compact(
            'jamBuka',
            'jamTutup',
            'bukaAkhirPekan',
            'tanggalLibur'
        ));
    }

    public function updateOperasional(Request $request)
    {
        $request->validate([
            'jam_buka' => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i',
        ]);

        Pengaturan::updateOrCreate(['key' => 'jam_buka'], ['value' => $request->jam_buka]);
        Pengaturan::updateOrCreate(['key' => 'jam_tutup'], ['value' => $request->jam_tutup]);
        Pengaturan::updateOrCreate(['key' => 'buka_akhir_pekan'], ['value' => $request->has('buka_akhir_pekan') ? '1' : '0']);

        return redirect()->back()->with('success', 'Jam operasional berhasil diperbarui.');
    }

    public function addTanggalLibur(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:eo_mr_tanggal_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ], [
            'tanggal.unique' => 'Tanggal libur ini sudah ditambahkan sebelumnya.'
        ]);

        TanggalLibur::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Tanggal libur baru berhasil ditambahkan.');
    }

    public function destroyTanggalLibur($id)
    {
        $libur = TanggalLibur::findOrFail($id);
        $libur->delete();

        return redirect()->back()->with('success', 'Tanggal libur berhasil dihapus.');
    }
}
