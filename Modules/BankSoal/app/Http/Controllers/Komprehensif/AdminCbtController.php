<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCbtController extends Controller
{
    /**
     * Fitur Live Proctoring untuk memantau mahasiswa yang sedang ujian.
     */
    public function liveProctoring()
    {
        $sessions = \Modules\BankSoal\Models\KompreSession::with(['user', 'jawabans'])
            ->where('status', 'ongoing')
            ->orderBy('started_at', 'desc')
            ->get();

        return view('banksoal::admin.cbt.live-proctoring', compact('sessions'));
    }

    /**
     * Laporan Riwayat Hasil Ujian yang sudah selesai.
     */
    public function riwayat(\Illuminate\Http\Request $request)
    {
        $query = \Modules\BankSoal\Models\KompreSession::with(['user', 'jadwal.periode'])
            ->where('status', 'finished')
            ->orderBy('finished_at', 'desc');

        if ($request->filled('periode_id')) {
            $query->whereHas('jadwal', function($q) use ($request) {
                $q->where('periode_id', $request->periode_id);
            });
        }

        $sessions = $query->get();
        $periodes = \Modules\BankSoal\Models\PeriodeUjian::orderBy('created_at', 'desc')->get();

        return view('banksoal::admin.cbt.riwayat', compact('sessions', 'periodes'));
    }

    /**
     * Detail hasil jawaban per sesi mahasiswa.
     */
    public function detailHasil($id)
    {
        $session = \Modules\BankSoal\Models\KompreSession::with([
            'user', 
            'jawabans.pertanyaan', 
            'jawabans.jawaban'
        ])->findOrFail($id);

        return view('banksoal::admin.cbt.detail-hasil', compact('session'));
    }

    /**
     * Aksi untuk Force Submit secara sepihak oleh admin jika perlu.
     */
    public function forceSubmit($id)
    {
        $session = \Modules\BankSoal\Models\KompreSession::findOrFail($id);
        
        if ($session->status === 'ongoing') {
            $session->status = 'finished';
            $session->finished_at = now();
            // TODO: Recalculate score logic
            $session->save();

            return back()->with('success', 'Sesi ujian berhasil diakhiri paksa.');
        }

        return back()->with('error', 'Sesi ujian sudah selesai sebelumnya.');
    }
}
