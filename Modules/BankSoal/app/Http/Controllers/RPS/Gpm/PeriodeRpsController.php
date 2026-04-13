<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeRps;
use Illuminate\Support\Facades\DB;

class PeriodeRpsController extends Controller
{
    public function index()
    {
        $periodes = PeriodeRps::orderBy('created_at', 'desc')->get();
        return view('banksoal::gpm.periode-rps.index', compact('periodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika checkbox is_active dicentang, matikan periode aktif lainnya
            if (!empty($validated['is_active'])) {
                PeriodeRps::where('is_active', 'true')->update(['is_active' => 'false']);
            }

            PeriodeRps::create([
                'judul' => $validated['judul'],
                'semester' => $validated['semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => !empty($validated['is_active']) ? 'true' : 'false',
            ]);

            DB::commit();
            return redirect()->route('banksoal.rps.gpm.validasi-rps')->with('success', 'Berhasil menambahkan periode unggah RPS.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $periode = PeriodeRps::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika checkbox is_active dicentang, matikan periode aktif lainnya
            if (!empty($validated['is_active'])) {
                PeriodeRps::where('id', '!=', $periode->id)
                    ->where('is_active', 'true')
                    ->update(['is_active' => 'false']);
            }

            $periode->update([
                'judul' => $validated['judul'],
                'semester' => $validated['semester'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_active' => !empty($validated['is_active']) ? 'true' : 'false',
            ]);

            DB::commit();
            return redirect()->route('banksoal.rps.gpm.validasi-rps')->with('success', 'Berhasil update periode unggah RPS.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat update data.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $periode = PeriodeRps::findOrFail($id);
            $periode->delete(); // Soft deletes
            return redirect()->route('banksoal.rps.gpm.validasi-rps')->with('success', 'Berhasil menghapus periode unggah RPS.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }
    }

    public function closeSession(Request $request)
    {
        DB::beginTransaction();
        try {
            PeriodeRps::where('is_active', 'true')->update(['is_active' => 'false']);
            DB::commit();
            return redirect()->route('banksoal.rps.gpm.validasi-rps')->with('success', 'Berhasil menutup sesi pengajuan RPS.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat menutup sesi.');
        }
    }
}