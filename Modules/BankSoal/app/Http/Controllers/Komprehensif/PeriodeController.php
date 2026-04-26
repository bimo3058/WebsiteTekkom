<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Illuminate\Support\Str;

class PeriodeController extends Controller
{
    public function index()
    {
        return view('banksoal::periode.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'          => 'required|string|max:255',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_ujian' => 'nullable|date|after_or_equal:tanggal_mulai_ujian',
            'deskripsi'             => 'nullable|string',
        ]);

        // Status ditentukan otomatis berdasarkan tanggal — tidak ada input manual dari admin
        $now = now();
        $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $status = $now->gte($mulai) ? 'aktif' : 'draft';

        PeriodeUjian::create([
            'nama_periode'          => $request->nama_periode,
            'slug'                  => Str::slug($request->nama_periode . '-' . time()),
            'tanggal_mulai'         => $request->tanggal_mulai,
            'tanggal_selesai'       => $request->tanggal_selesai,
            'tanggal_mulai_ujian'   => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian' => $request->tanggal_selesai_ujian,
            'status'                => $status,
            'deskripsi'             => $request->deskripsi,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        $request->validate([
            'nama_periode'          => 'required|string|max:255',
            'tanggal_mulai'         => 'required|date',
            'tanggal_selesai'       => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian'   => 'nullable|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_ujian' => 'nullable|date|after_or_equal:tanggal_mulai_ujian',
            'deskripsi'             => 'nullable|string',
        ]);

        // Hitung ulang status berdasarkan tanggal yang baru di-edit
        $now = now();
        $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesaiUjian = $request->tanggal_selesai_ujian
            ? Carbon::parse($request->tanggal_selesai_ujian)
            : null;

        if ($selesaiUjian && $now->startOfDay()->gt($selesaiUjian)) {
            $newStatus = 'selesai';
        } elseif ($now->gte($mulai)) {
            $newStatus = 'aktif';
        } else {
            $newStatus = 'draft';
        }

        $periode->update([
            'nama_periode'          => $request->nama_periode,
            'tanggal_mulai'         => $request->tanggal_mulai,
            'tanggal_selesai'       => $request->tanggal_selesai,
            'tanggal_mulai_ujian'   => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian' => $request->tanggal_selesai_ujian,
            'status'                => $newStatus,
            'deskripsi'             => $request->deskripsi,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil diupdate.');
    }

    public function closePendaftaran($id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        // Guard: hanya bisa tutup jika pendaftaran sedang terbuka
        if (!$periode->pendaftaran_terbuka) {
            return redirect()->route('banksoal.periode.setup')
                ->with('error', 'Pendaftaran tidak sedang terbuka pada periode ini.');
        }

        $periode->update(['pendaftaran_ditutup_paksa' => true]);

        return redirect()->route('banksoal.periode.setup')
            ->with('success', "Pendaftaran untuk \"{$periode->nama_periode}\" berhasil ditutup paksa.");
    }

    public function destroy($id)
    {
        $periode = PeriodeUjian::findOrFail($id);
        $periode->delete();

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil dihapus.');
    }
}
