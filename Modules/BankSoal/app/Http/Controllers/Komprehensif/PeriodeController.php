<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Illuminate\Support\Str;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();

        // Auto-update status to 'selesai' if current date is past the end of exams
        foreach ($periodes as $periode) {
            if ($periode->status !== 'selesai' && $periode->tanggal_selesai_ujian && now()->startOfDay()->gt($periode->tanggal_selesai_ujian)) {
                $periode->update(['status' => 'selesai']);
            }
        }

        return view('banksoal::periode.index', compact('periodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian' => 'nullable|date',
            'tanggal_selesai_ujian' => 'nullable|date|after_or_equal:tanggal_mulai_ujian',
            'status' => 'nullable|in:aktif,draft',
            'deskripsi' => 'nullable|string',
        ]);

        if ($request->status === 'aktif' && PeriodeUjian::where('status', 'aktif')->exists()) {
            return back()->withErrors(['status' => 'Gagal: Sudah ada Periode Ujian Komprehensif yang sedang aktif. Harap selesaikan periode sebelumnya terlebih dahulu.'])->withInput();
        }

        PeriodeUjian::create([
            'nama_periode' => $request->nama_periode,
            'slug' => Str::slug($request->nama_periode . '-' . time()),
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_mulai_ujian' => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian' => $request->tanggal_selesai_ujian,
            'status' => $request->status ?? 'draft',
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian' => 'nullable|date',
            'tanggal_selesai_ujian' => 'nullable|date|after_or_equal:tanggal_mulai_ujian',
            'status' => 'required|in:draft,aktif,selesai',
            'deskripsi' => 'nullable|string',
        ]);

        if ($request->status === 'aktif' && PeriodeUjian::where('status', 'aktif')->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['status' => 'Gagal: Sudah ada Periode Ujian Komprehensif LAIN yang sedang aktif. Harap selesaikan periode sebelumnya.'])->withInput();
        }

        $periode->update([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_mulai_ujian' => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian' => $request->tanggal_selesai_ujian,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil diupdate.');
    }

    public function destroy($id)
    {
        $periode = PeriodeUjian::findOrFail($id);
        $periode->delete();

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil dihapus.');
    }
}
