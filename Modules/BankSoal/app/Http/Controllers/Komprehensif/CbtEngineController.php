<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CbtEngineController extends Controller
{
    public function validateToken(Request $request)
    {
        $request->validate(['token' => 'required|string|size:6']);
        $token = strtoupper($request->token);

        $pendaftar = \Modules\BankSoal\Models\PendaftarUjian::where('mahasiswa_id', auth()->id())
            ->where('status_pendaftaran', 'approved')
            ->whereHas('jadwal')
            ->with('jadwal')
            ->first();

        if (!$pendaftar || !$pendaftar->jadwal) {
            return back()->with('error', 'Anda tidak terdaftar atau belum dialokasikan ke sesi ujian apapun.');
        }

        $jadwal = $pendaftar->jadwal;

        if ($jadwal->token !== $token) {
            return back()->with('error', 'Token yang Anda masukkan salah.');
        }

        if (!now()->isSameDay($jadwal->tanggal_ujian)) {
            return back()->with('error', 'Ujian tidak dijadwalkan pada hari ini.');
        }

        $waktuSelesai = \Carbon\Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai);
        if (now()->gt($waktuSelesai)) {
            return back()->with('error', 'Sesi ujian telah berakhir. Anda tidak dapat masuk lagi.');
        }

        session(['cbt_token_valid' => true, 'cbt_jadwal_id' => $jadwal->id]);

        return redirect()->route('komprehensif.mahasiswa.engine.waiting');
    }

    public function waitingRoom()
    {
        if (!session('cbt_token_valid')) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Silakan masukkan token terlebih dahulu.');
        }

        $pendaftar = \Modules\BankSoal\Models\PendaftarUjian::where('mahasiswa_id', auth()->id())
            ->where('status_pendaftaran', 'approved')
            ->first();

        $jadwal = \Modules\BankSoal\Models\JadwalUjian::find(session('cbt_jadwal_id'));
        $waktuMulai = \Carbon\Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_mulai);
        
        $canStart = now()->gte($waktuMulai);

        // Jika ujian sudah ongoing, langsung lempar ke soal
        $kompreSession = \Modules\BankSoal\Models\KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($kompreSession) {
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        }

        return view('banksoal::mahasiswa.cbt.waiting-room', compact('pendaftar', 'jadwal', 'waktuMulai', 'canStart'));
    }

    public function startUjian(Request $request)
    {
        if (!session('cbt_token_valid') || !session('cbt_jadwal_id')) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Sesi tidak valid.');
        }

        $jadwal = \Modules\BankSoal\Models\JadwalUjian::find(session('cbt_jadwal_id'));
        $waktuMulai = \Carbon\Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_mulai);
        
        if (now()->lt($waktuMulai)) {
            return back()->with('error', 'Waktu ujian belum dimulai.');
        }

        // Cek apakah ujian sudah berjalan
        $existingSession = \Modules\BankSoal\Models\KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($existingSession) {
            return redirect()->route('komprehensif.mahasiswa.engine.run');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Buat Record Sesi Ujian (KompreSession)
            $session = \Modules\BankSoal\Models\KompreSession::create([
                'user_id' => auth()->id(),
                'jadwal_id' => $jadwal->id,
                'title' => 'Ujian Komprehensif ' . $jadwal->nama_sesi,
                'started_at' => now(),
                'status' => 'ongoing'
            ]);

            // 2. Acak 100 Soal dari 10 CPL
            $cpls = \Modules\BankSoal\Models\Shared\Cpl::inRandomOrder()->limit(10)->get();
            $soals = collect();

            foreach ($cpls as $cpl) {
                // Ambil 10 soal acak dari CPL ini
                $pertanyaans = \Modules\BankSoal\Models\Pertanyaan::where('cpl_id', $cpl->id)
                    ->where('status', 'disetujui') // Pastikan soal valid
                    ->inRandomOrder()
                    ->limit(10)
                    ->get();
                $soals = $soals->merge($pertanyaans);
            }

            // 3. Acak seluruh gabungan 100 soal tersebut
            $soals = $soals->shuffle();

            // 4. Masukkan ke KompreJawaban dan acak opsi jawabannya
            $urutan = 1;
            foreach ($soals as $soal) {
                // Ambil semua id jawaban dari soal ini, lalu acak urutannya
                $opsiIds = $soal->jawabans()->pluck('id')->shuffle()->toArray();

                \Modules\BankSoal\Models\KompreJawaban::create([
                    'kompre_session_id' => $session->id,
                    'pertanyaan_id' => $soal->id,
                    'urutan_soal' => $urutan++,
                    'urutan_opsi' => $opsiIds,
                    'kesulitan_now' => $soal->kesulitan
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('komprehensif.mahasiswa.engine.run');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal membangkitkan soal ujian: ' . $e->getMessage());
        }
    }

    public function run()
    {
        $session = \Modules\BankSoal\Models\KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if (!$session) {
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('error', 'Tidak ada sesi ujian yang sedang berjalan.');
        }

        // Cek apakah waktu sudah habis (100 menit)
        $endTime = $session->started_at->addMinutes(100);
        if (now()->gt($endTime)) {
            $session->update(['status' => 'finished']);
            return redirect()->route('komprehensif.mahasiswa.dashboard')->with('info', 'Waktu ujian telah habis.');
        }

        $jadwal = $session->jadwal;

        // Ambil semua soal beserta opsinya (Hanya data teks dan id)
        // Kita butuh JSON data yang bersih untuk dikirim ke Alpine.js
        $jawabans = \Modules\BankSoal\Models\KompreJawaban::where('kompre_session_id', $session->id)
            ->orderBy('urutan_soal')
            ->with(['pertanyaan', 'pertanyaan.jawabans'])
            ->get()
            ->map(function ($j) {
                // Urutkan opsi sesuai $j->urutan_opsi array
                $opsiMap = collect($j->pertanyaan->jawabans)->keyBy('id');
                $opsiSorted = collect($j->urutan_opsi)->map(function ($oId) use ($opsiMap) {
                    return $opsiMap->get($oId);
                })->filter()->values();

                return [
                    'id' => $j->id, // KompreJawaban ID
                    'urutan' => $j->urutan_soal,
                    'soal' => $j->pertanyaan->soal,
                    'opsi' => $opsiSorted->map(function ($o, $idx) {
                        return [
                            'id' => $o->id,
                            'teks' => $o->deskripsi,
                            'label' => chr(65 + $idx) // A, B, C, D, E berdasarkan urutan acak
                        ];
                    }),
                    'jawaban_terpilih' => $j->jawaban_id,
                    'ragu_ragu' => (bool) $j->is_ragu
                ];
            });

        return view('banksoal::mahasiswa.cbt.engine', compact('session', 'jawabans', 'endTime', 'jadwal'));
    }

    public function saveAnswer(Request $request)
    {
        $request->validate([
            'jawaban_id' => 'required|exists:bs_kompre_jawaban,id',
            'opsi_terpilih' => 'required|exists:bs_jawaban,id'
        ]);

        $jawaban = \Modules\BankSoal\Models\KompreJawaban::where('id', $request->jawaban_id)
            ->whereHas('session', function($q) {
                $q->where('user_id', auth()->id())->where('status', 'ongoing');
            })->first();

        if (!$jawaban) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        $jawaban->update(['jawaban_id' => $request->opsi_terpilih]);

        return response()->json(['success' => true]);
    }

    public function toggleRagu(Request $request)
    {
        $request->validate([
            'jawaban_id' => 'required|exists:bs_kompre_jawaban,id',
            'is_ragu' => 'required|boolean'
        ]);

        $jawaban = \Modules\BankSoal\Models\KompreJawaban::where('id', $request->jawaban_id)
            ->whereHas('session', function($q) {
                $q->where('user_id', auth()->id())->where('status', 'ongoing');
            })->first();

        if (!$jawaban) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        $jawaban->update(['is_ragu' => $request->is_ragu]);

        return response()->json(['success' => true]);
    }

    public function finish()
    {
        $session = \Modules\BankSoal\Models\KompreSession::where('user_id', auth()->id())
            ->where('status', 'ongoing')
            ->first();

        if ($session) {
            // Kalkulasi skor akhir (Asumsi skor sederhana: (benar / total soal) * 100)
            $jawabans = \Modules\BankSoal\Models\KompreJawaban::where('kompre_session_id', $session->id)->with('opsiTerpilih')->get();
            $benar = 0;
            $total = $jawabans->count();

            foreach ($jawabans as $j) {
                if ($j->opsiTerpilih && $j->opsiTerpilih->is_benar) {
                    $benar++;
                }
            }

            $skor = $total > 0 ? round(($benar / $total) * 100, 2) : 0;

            $session->update([
                'status' => 'finished',
                'finished_at' => now(),
                'score' => $skor
            ]);
        }

        return redirect()->route('komprehensif.mahasiswa.dashboard')->with('success', 'Ujian telah selesai. Terima kasih.');
    }
}
