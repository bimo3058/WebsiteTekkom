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
        $sessions = \Modules\BankSoal\Models\KompreSession::with(['user', 'jawabans', 'cheatLogs'])
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
            'user.student', 
            'jadwal',
            'jawabans.pertanyaan.jawabans',
            'jawabans.opsiTerpilih'
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
            // Kalkulasi skor
            $jawabans = \Modules\BankSoal\Models\KompreJawaban::where('kompre_session_id', $session->id)
                ->with('opsiTerpilih')->get();
            $benar = $jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
            $total = $jawabans->count();
            $skor = $total > 0 ? round(($benar / $total) * 100, 2) : 0;

            $session->update([
                'status' => 'finished',
                'finished_at' => now(),
                'score' => $skor,
            ]);

            return back()->with('success', 'Sesi ujian berhasil diakhiri paksa. Skor: ' . $skor);
        }

        return back()->with('error', 'Sesi ujian sudah selesai sebelumnya.');
    }

    public function analytics()
    {
        // 1. Matriks Umum (Total Peserta, Rata-rata, Tertinggi, Terendah)
        $sessions = \Modules\BankSoal\Models\KompreSession::where('status', 'finished')->get();
        
        $totalPeserta = $sessions->count();
        $rataRata = $totalPeserta > 0 ? round($sessions->avg('score'), 2) : 0;
        $tertinggi = $totalPeserta > 0 ? $sessions->max('score') : 0;
        $terendah = $totalPeserta > 0 ? $sessions->min('score') : 0;
        
        // Distribusi Lulus / Tidak Lulus (Minimal 60)
        $lulus = $sessions->where('score', '>=', 60)->count();
        $tidakLulus = $totalPeserta - $lulus;

        // Ambil semua jawaban ujian yang sudah selesai
        $jawabans = \Modules\BankSoal\Models\KompreJawaban::whereHas('session', function($q) {
            $q->where('status', 'finished');
        })->with(['pertanyaan.cpl', 'opsiTerpilih'])->get();

        // 2. Soal Tersulit (Top 10 yang paling banyak dijawab salah)
        $kesalahanPerSoal = $jawabans->filter(function($j) {
            // Salah = tidak dijawab (null) atau dijawab tapi salah
            return !$j->opsiTerpilih || !$j->opsiTerpilih->is_benar;
        })->groupBy('pertanyaan_id')->map(function($g) {
            return [
                'pertanyaan' => $g->first()->pertanyaan,
                'salah_count' => $g->count()
            ];
        })->sortByDesc('salah_count')->take(10);

        // 3. Pemetaan Capaian CPL
        $cplStats = collect();
        if ($jawabans->count() > 0) {
            $cplStats = $jawabans->groupBy(function($j) {
                return $j->pertanyaan && $j->pertanyaan->cpl ? $j->pertanyaan->cpl->id : 'Unknown';
            })->map(function($g, $key) {
                if ($key === 'Unknown') return null;

                $total = $g->count();
                $benar = $g->filter(function($j) {
                    return $j->opsiTerpilih && $j->opsiTerpilih->is_benar;
                })->count();
                
                $cpl = $g->first()->pertanyaan->cpl;
                
                return [
                    'cpl_kode' => $cpl ? $cpl->kode : 'CPL-?',
                    'deskripsi' => $cpl ? $cpl->deskripsi : '',
                    'persentase' => $total > 0 ? round(($benar / $total) * 100, 2) : 0
                ];
            })->filter()->sortBy('cpl_kode')->values();
        }

        return view('banksoal::admin.cbt.analitik', compact(
            'totalPeserta', 'rataRata', 'tertinggi', 'terendah', 
            'lulus', 'tidakLulus', 'kesalahanPerSoal', 'cplStats'
        ));
    }

    /**
     * Reset semua data ujian komprehensif (Periode, Jadwal, Pendaftar, Sesi, Jawaban, Log).
     * Memerlukan konfirmasi password admin.
     */
    public function resetSemua(Request $request)
    {
        $request->validate([
            'konfirmasi_password' => 'required|string',
        ]);

        // Verifikasi password admin yang sedang login
        if (!\Illuminate\Support\Facades\Hash::check($request->konfirmasi_password, auth()->user()->password)) {
            return redirect()->back()->withErrors([
                'konfirmasi_password' => 'Password yang Anda masukkan salah. Reset dibatalkan.',
            ])->withFragment('reset-section');
        }

        // Pastikan tidak ada sesi ujian yang sedang berlangsung
        $ongoingSessions = \Modules\BankSoal\Models\KompreSession::where('status', 'ongoing')->count();
        if ($ongoingSessions > 0) {
            return redirect()->back()->with(
                'error',
                "Tidak dapat mereset! Masih ada {$ongoingSessions} sesi ujian yang sedang berlangsung. Tunggu hingga semua selesai atau gunakan Force Submit terlebih dahulu."
            )->withFragment('reset-section');
        }

        // Hapus urut dari tabel turunan ke tabel induk
        \DB::table('bs_cheat_logs')->delete();
        \DB::table('bs_kompre_jawaban')->delete();
        \DB::table('bs_kompre_session')->delete();
        \DB::table('bs_pendaftar_ujians')->delete();
        \DB::table('bs_jadwal_ujians')->delete();
        \DB::table('bs_periode_ujians')->delete();

        return redirect()->route('banksoal.admin.cbt.riwayat')
            ->with('success', '✅ Semua data ujian komprehensif berhasil direset. Sistem siap digunakan dari awal.');
    }
}
