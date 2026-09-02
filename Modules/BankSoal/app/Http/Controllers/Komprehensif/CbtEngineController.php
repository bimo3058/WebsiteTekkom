<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Http\Requests\Komprehensif\LogViolationRequest;
use Modules\BankSoal\Http\Requests\Komprehensif\SaveAnswerRequest;
use Modules\BankSoal\Http\Requests\Komprehensif\ToggleRaguRequest;
use Modules\BankSoal\Models\Komprehensif\CheatLog;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Services\CbtSessionService;

class CbtEngineController extends Controller
{
    /** Session key untuk menandai token sudah divalidasi. */
    private const SESSION_TOKEN_KEY = 'cbt_token_valid';

    /** Session key untuk menyimpan ID jadwal ujian yang aktif. */
    private const SESSION_JADWAL_KEY = 'cbt_jadwal_id';

    public function __construct(private CbtSessionService $cbtService) {}

    /**
     * Validasi token ujian dan langsung mulai sesi CBT.
     *
     * Ruang tunggu dieliminasi — token valid = langsung ke engine.
     * Konfirmasi "siap ujian" ditangani via JS confirm() di form dashboard.
     */
    public function validateToken(Request $request)
    {
        $request->validate(['token' => 'required|string|size:6']);

        $token = strtoupper($request->token);

        // Cari JadwalUjian yang aktif dengan token cocok dan berisi mahasiswa ini sebagai pendaftar yang approved
        $jadwal = JadwalUjian::where('status', 'aktif')
            ->where('token', $token)
            ->whereHas('pendaftars', function ($q) {
                $q->where('mahasiswa_id', auth()->id())
                  ->where('status_pendaftaran', PendaftaranStatus::Approved->value)
                  ->whereNull('deleted_at');
            })
            ->first();

        if (! $jadwal) {
            return back()->with('error', 'Anda tidak terdaftar atau belum dialokasikan ke sesi ujian yang aktif.');
        }

        if (! now()->isSameDay($jadwal->tanggal_ujian)) {
            return back()->with('error', 'Ujian tidak dijadwalkan pada hari ini.');
        }

        $tanggal      = $jadwal->tanggal_ujian->format('Y-m-d');
        $waktuSelesai = Carbon::parse($tanggal . ' ' . $jadwal->waktu_selesai);

        if (now()->gte($waktuSelesai)) {
            return back()->with('error', 'Sesi ujian telah berakhir. Anda tidak dapat masuk lagi.');
        }

        // Jika sudah ada sesi ongoing (misal token diinput ulang), langsung ke engine
        $existingSession = KompreSession::where('user_id', auth()->id())
            ->where('status', KompreSessionStatus::Ongoing)
            ->first();

        if ($existingSession) {
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        }

        // ✅ Langsung generate soal & mulai sesi — tidak perlu waiting room
        try {
            $this->cbtService->startSession($jadwal, auth()->id());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membangkitkan soal ujian: ' . $e->getMessage());
        }

        return redirect()->route('komprehensif.mahasiswa.engine.run');
    }

    /**
     * Cek token via AJAX sebelum memunculkan popup konfirmasi.
     */
    public function checkToken(Request $request)
    {
        $request->validate(['token' => 'required|string|size:6']);

        $token = strtoupper($request->token);

        // Cari JadwalUjian yang aktif dengan token cocok dan berisi mahasiswa ini sebagai pendaftar yang approved
        $jadwal = JadwalUjian::where('status', 'aktif')
            ->where('token', $token)
            ->whereHas('pendaftars', function ($q) {
                $q->where('mahasiswa_id', auth()->id())
                  ->where('status_pendaftaran', PendaftaranStatus::Approved->value)
                  ->whereNull('deleted_at');
            })
            ->first();

        if (! $jadwal) {
            return response()->json(['valid' => false, 'message' => 'Anda tidak terdaftar atau belum dialokasikan ke sesi ujian yang aktif.']);
        }

        if (! now()->isSameDay($jadwal->tanggal_ujian)) {
            return response()->json(['valid' => false, 'message' => 'Ujian tidak dijadwalkan pada hari ini.']);
        }

        $tanggal      = $jadwal->tanggal_ujian->format('Y-m-d');
        $waktuSelesai = Carbon::parse($tanggal . ' ' . $jadwal->waktu_selesai);

        if (now()->gte($waktuSelesai)) {
            return response()->json(['valid' => false, 'message' => 'Sesi ujian telah berakhir. Anda tidak dapat masuk lagi.']);
        }

        return response()->json(['valid' => true]);
    }

    /**
     * Ruang tunggu sebelum ujian dimulai.
     */
    public function waitingRoom()
    {
        if (! session(self::SESSION_TOKEN_KEY)) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', 'Silakan masukkan token terlebih dahulu.');
        }

        // Jika ujian sudah berjalan, langsung lempar ke soal
        $ongoingSession = KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($ongoingSession) {
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        }

        $pendaftar  = PendaftarUjian::where('mahasiswa_id', auth()->id())
            ->where('status_pendaftaran', PendaftaranStatus::Approved->value)
            ->first();

        $jadwal     = JadwalUjian::find(session(self::SESSION_JADWAL_KEY));
        $waktuMulai = Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_mulai);
        $canStart   = now()->gte($waktuMulai);

        return view('banksoal::mahasiswa.cbt.waiting-room', compact('pendaftar', 'jadwal', 'waktuMulai', 'canStart'));
    }

    /**
     * Mulai sesi ujian — hasilkan soal via CbtSessionService.
     */
    public function startUjian(Request $request)
    {
        if (! session(self::SESSION_TOKEN_KEY) || ! session(self::SESSION_JADWAL_KEY)) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', 'Sesi tidak valid.');
        }

        $jadwal      = JadwalUjian::findOrFail(session(self::SESSION_JADWAL_KEY));
        $tanggal     = $jadwal->tanggal_ujian->format('Y-m-d');
        $waktuMulai  = Carbon::parse($tanggal . ' ' . $jadwal->waktu_mulai);
        $waktuSelesai = Carbon::parse($tanggal . ' ' . $jadwal->waktu_selesai);

        if (now()->lt($waktuMulai)) {
            return back()->with('error', 'Waktu ujian belum dimulai.');
        }

        // ✅ Ketat: tolak jika gate sudah ditutup
        if (now()->gte($waktuSelesai)) {
            return back()->with('error', 'Waktu ujian sudah berakhir. Anda tidak dapat memulai ujian.');
        }

        // Jika sesi sudah ada (misal refresh), lanjutkan
        $existingSession = KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($existingSession) {
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        }

        try {
            $this->cbtService->startSession($jadwal, auth()->id());
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membangkitkan soal ujian: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman ujian dengan data soal terstruktur untuk Alpine.js.
     */
    public function run()
    {
        $session = KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->with('jadwal')  // diperlukan oleh getEndTime() untuk strict gate enforcement
            ->first();

        if (! $session) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('error', 'Tidak ada sesi ujian yang sedang berjalan.');
        }

        $endTime = $this->cbtService->getEndTime($session);

        if (now()->gt($endTime)) {
            $this->cbtService->finishSession($session);
            return redirect()->route('komprehensif.mahasiswa.dashboard')
                ->with('info', 'Waktu ujian telah habis.');
        }

        $jadwal   = $session->jadwal;
        $jawabans = KompreJawaban::where('kompre_session_id', $session->id)
            ->orderBy('urutan_soal')
            ->with(['pertanyaan', 'pertanyaan.jawaban', 'pertanyaan.cpl'])
            ->get()
            ->map(function ($j) {
                $opsiMap    = collect($j->pertanyaan->jawaban)->keyBy('id');
                $opsiSorted = collect($j->urutan_opsi)
                    ->map(fn($oId) => $opsiMap->get($oId))
                    ->filter()
                    ->values();

                return [
                    'id'               => $j->id,
                    'urutan'           => $j->urutan_soal,
                    'soal'             => $j->pertanyaan->soal,
                    'opsi'             => $opsiSorted->map(fn($o, $idx) => [
                        'id'    => $o->id,
                        'teks'  => $o->deskripsi,
                        'label' => chr(65 + $idx), // A, B, C, D, E
                    ]),
                    'jawaban_terpilih' => $j->jawaban_dipilih,
                    'ragu_ragu'        => (bool) $j->is_ragu,
                    'cpl_kode'         => $j->pertanyaan->cpl->kode ?? 'CPL',
                ];
            });

        return view('banksoal::mahasiswa.cbt.engine', compact('session', 'jawabans', 'endTime', 'jadwal'));
    }

    /**
     * Simpan pilihan jawaban (AJAX).
     */
    public function saveAnswer(SaveAnswerRequest $request)
    {
        $jawaban = KompreJawaban::where('id', $request->jawaban_id)
            ->whereHas('session', fn($q) => $q->where('user_id', auth()->id())->where('status', KompreSessionStatus::Ongoing))
            ->with('session.jadwal')  // eager-load jadwal untuk strict gate time check
            ->first();

        if (! $jawaban) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        // ✅ Server-side timer validation — tidak bisa dimanipulasi dari client
        if (now()->gt($this->cbtService->getEndTime($jawaban->session))) {
            $this->cbtService->finishSession($jawaban->session);
            return response()->json(['success' => false, 'expired' => true], 403);
        }

        // ✅ Answer integrity check — opsi_terpilih harus merupakan opsi valid
        // untuk soal ini. Mencegah exploit: kirim jawaban_id soal A tapi
        // opsi_terpilih dari soal B yang kebetulan is_benar = true.
        if (! in_array($request->opsi_terpilih, $jawaban->urutan_opsi ?? [])) {
            return response()->json(['success' => false, 'message' => 'Opsi jawaban tidak valid.'], 422);
        }

        $jawaban->update(['jawaban_dipilih' => $request->opsi_terpilih]);

        return response()->json(['success' => true]);
    }

    /**
     * Toggle flag ragu-ragu pada soal (AJAX).
     */
    public function toggleRagu(ToggleRaguRequest $request)
    {
        $jawaban = KompreJawaban::where('id', $request->jawaban_id)
            ->whereHas('session', fn($q) => $q->where('user_id', auth()->id())->where('status', KompreSessionStatus::Ongoing))
            ->with('session.jadwal')  // eager-load jadwal untuk strict gate time check
            ->first();

        if (! $jawaban) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        // ✅ Server-side timer validation
        if (now()->gt($this->cbtService->getEndTime($jawaban->session))) {
            $this->cbtService->finishSession($jawaban->session);
            return response()->json(['success' => false, 'expired' => true], 403);
        }

        $jawaban->update(['is_ragu' => $request->is_ragu]);

        return response()->json(['success' => true]);
    }

    /**
     * Catat pelanggaran/kejadian selama ujian (AJAX).
     */
    public function logViolation(LogViolationRequest $request)
    {
        $session = KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Tidak ada sesi berjalan'], 403);
        }

        CheatLog::create([
            'kompre_session_id' => $session->id,
            'event_type'        => $request->event_type,
            'description'       => $request->description,
            'metadata'          => [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Selesaikan ujian secara mandiri oleh mahasiswa.
     */
    public function finish()
    {
        $session = KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($session) {
            $this->cbtService->finishSession($session);
        }

        return redirect()->route('komprehensif.mahasiswa.dashboard')
            ->with('success', 'Ujian telah selesai. Terima kasih.');
    }
}
