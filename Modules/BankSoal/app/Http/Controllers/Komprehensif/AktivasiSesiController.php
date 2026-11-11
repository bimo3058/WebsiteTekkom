<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\BankSoal\Models\Komprehensif\CheatLog;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;

class AktivasiSesiController extends Controller
{
    /**
     * Halaman gabungan: aktivasi sesi/token + pantau peserta ujian.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', KompreSession::class);

        $periodes = PeriodeUjian::orderBy('tanggal_mulai', 'desc')->get();

        // Default ke periode aktif jika tidak ada filter di URL
        if (! $request->filled('periode_id')) {
            $activePeriodeId = $periodes->firstWhere('status', 'aktif')?->id ?? $periodes->first()?->id;
            if ($activePeriodeId) {
                return redirect()->route('banksoal.aktivasi.index', ['periode_id' => $activePeriodeId]);
            }
        }

        $selectedPeriodeId = $request->query('periode_id');

        $selectedPeriode = null;
        if ($selectedPeriodeId) {
            $selectedPeriode = $periodes->firstWhere('id', $selectedPeriodeId);
        }

        $jadwals = collect();
        if ($selectedPeriode) {
            $jadwals = JadwalUjian::withCount('pendaftars')
                ->where('periode_ujian_id', $selectedPeriodeId)
                ->orderBy('tanggal_ujian', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        $sessions = KompreSession::with([
            'user.student',
            'jadwal',
            'cheatLogs' => fn ($q) => $q->orderBy('created_at'),
        ])
            ->withCount([
                'jawabans',
                'jawabans as terjawab_count' => fn ($q) => $q->whereNotNull('jawaban_dipilih'),
                'cheatLogs',
            ])
            ->where('status', 'ongoing')
            ->orderBy('started_at', 'desc')
            ->get();

        $violationDetails = $sessions->mapWithKeys(fn ($session) => [
            $session->id => CheatLog::detailsFrom($session->cheatLogs),
        ]);

        return view('banksoal::aktivasi.index', compact(
            'periodes', 'selectedPeriode', 'selectedPeriodeId', 'jadwals', 'sessions', 'violationDetails'
        ));
    }

    /**
     * Ubah status dari sebuah JadwalUjian dan generate/hapus token secara otomatis.
     */
    public function toggle(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu_jadwal,aktif,selesai',
        ]);

        $jadwal = JadwalUjian::findOrFail($id);

        $token = null;
        if ($request->status === 'aktif') {
            $token = Str::upper(Str::random(6));
        }

        $jadwal->update([
            'status' => $request->status,
            'token' => $token,
        ]);

        return redirect()->back()->with('success', 'Status Sesi "'.$jadwal->nama_sesi.'" berhasil diubah menjadi '.strtoupper($request->status).'.');
    }
}
