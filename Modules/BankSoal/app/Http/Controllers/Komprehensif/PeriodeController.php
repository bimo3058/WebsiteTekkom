<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
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
        ], [
            'tanggal_mulai_ujian.after_or_equal' => 'Tanggal mulai ujian tidak boleh sebelum tanggal tutup pendaftaran.',
            'tanggal_selesai_ujian.after_or_equal' => 'Tanggal selesai ujian tidak boleh sebelum tanggal mulai ujian.',
            'tanggal_selesai.after_or_equal' => 'Tanggal tutup pendaftaran tidak boleh sebelum tanggal buka pendaftaran.',
        ]);

        // --- Validasi Overlap: Cegah dua periode berjalan bersamaan ---
        $mulaiPendaftaran  = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesaiUjian      = $request->tanggal_selesai_ujian
            ? Carbon::parse($request->tanggal_selesai_ujian)->endOfDay()
            : Carbon::parse($request->tanggal_selesai)->endOfDay();

        $overlap = PeriodeUjian::where('status', '!=', 'selesai')
            ->where(function ($q) use ($mulaiPendaftaran, $selesaiUjian) {
                // Cek apakah rentang baru (mulai_pendaftaran s.d. selesai_ujian) overlap
                // dengan rentang periode lain (tanggal_mulai s.d. tanggal_selesai_ujian/tanggal_selesai)
                $q->where('tanggal_mulai', '<=', $selesaiUjian)
                  ->where(function ($q2) use ($mulaiPendaftaran) {
                      $q2->where('tanggal_selesai_ujian', '>=', $mulaiPendaftaran)
                         ->orWhere(function ($q3) use ($mulaiPendaftaran) {
                             $q3->whereNull('tanggal_selesai_ujian')
                                ->where('tanggal_selesai', '>=', $mulaiPendaftaran);
                         });
                  });
            })->first();

        if ($overlap) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => "Tanggal bertabrakan dengan periode aktif: \"{$overlap->nama_periode}\" 
                    (Pendaftaran: {$overlap->tanggal_mulai->format('d M Y')} – {$overlap->tanggal_selesai->format('d M Y')}" .
                    ($overlap->tanggal_selesai_ujian ? ", Ujian s.d. {$overlap->tanggal_selesai_ujian->format('d M Y')}" : "") .
                    "). Selesaikan atau tutup periode tersebut terlebih dahulu.",
                ]);
        }
        // --- Akhir Validasi Overlap ---

        // Status ditentukan otomatis berdasarkan tanggal
        $now    = now();
        $mulai  = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $status = $now->gte($mulai) ? 'aktif' : 'draft';

        PeriodeUjian::create([
            'nama_periode'          => $request->nama_periode,
            'slug'                  => \Illuminate\Support\Str::slug($request->nama_periode . '-' . time()),
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
        ], [
            'tanggal_mulai_ujian.after_or_equal' => 'Tanggal mulai ujian tidak boleh sebelum tanggal tutup pendaftaran.',
            'tanggal_selesai_ujian.after_or_equal' => 'Tanggal selesai ujian tidak boleh sebelum tanggal mulai ujian.',
            'tanggal_selesai.after_or_equal' => 'Tanggal tutup pendaftaran tidak boleh sebelum tanggal buka pendaftaran.',
        ]);

        // --- Validasi Overlap (kecuali dirinya sendiri) ---
        $mulaiPendaftaran = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesaiUjianBaru = $request->tanggal_selesai_ujian
            ? Carbon::parse($request->tanggal_selesai_ujian)->endOfDay()
            : Carbon::parse($request->tanggal_selesai)->endOfDay();

        $overlap = PeriodeUjian::where('id', '!=', $id)
            ->where('status', '!=', 'selesai')
            ->where(function ($q) use ($mulaiPendaftaran, $selesaiUjianBaru) {
                $q->where('tanggal_mulai', '<=', $selesaiUjianBaru)
                  ->where(function ($q2) use ($mulaiPendaftaran) {
                      $q2->where('tanggal_selesai_ujian', '>=', $mulaiPendaftaran)
                         ->orWhere(function ($q3) use ($mulaiPendaftaran) {
                             $q3->whereNull('tanggal_selesai_ujian')
                                ->where('tanggal_selesai', '>=', $mulaiPendaftaran);
                         });
                  });
            })->first();

        if ($overlap) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'tanggal_mulai' => "Tanggal bertabrakan dengan periode aktif: \"{$overlap->nama_periode}\" " .
                    "(Pendaftaran: {$overlap->tanggal_mulai->format('d M Y')} – {$overlap->tanggal_selesai->format('d M Y')}" .
                    ($overlap->tanggal_selesai_ujian ? ", Ujian s.d. {$overlap->tanggal_selesai_ujian->format('d M Y')}" : "") .
                    "). Selesaikan atau tutup periode tersebut terlebih dahulu.",
                ]);
        }
        // --- Akhir Validasi Overlap ---

        // Hitung ulang status berdasarkan tanggal yang baru di-edit
        $now          = now();
        $mulai        = Carbon::parse($request->tanggal_mulai)->startOfDay();
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
