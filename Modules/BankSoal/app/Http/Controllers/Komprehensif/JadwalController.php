<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();

        // Default ke periode aktif jika tidak ada filter di URL
        if (!$request->filled('periode_id')) {
            $activePeriodeId = $periodes->firstWhere('status', 'aktif')?->id ?? $periodes->first()?->id;
            if ($activePeriodeId) {
                return redirect()->route('banksoal.periode.jadwal', ['periode_id' => $activePeriodeId]);
            }
        }

        $selectedPeriodeId = $request->query('periode_id');
        $jadwals = collect();
        $selectedPeriode = null;

        if ($selectedPeriodeId) {
            $jadwals = JadwalUjian::where('periode_ujian_id', $selectedPeriodeId)
                ->orderBy('waktu_mulai')
                ->get();
            $selectedPeriode = PeriodeUjian::find($selectedPeriodeId);
        }

        return view('banksoal::jadwal.index', compact('periodes', 'selectedPeriodeId', 'jadwals', 'selectedPeriode'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'periode_ujian_id' => 'required|exists:bs_periode_ujians,id',
                'nama_sesi' => [
                    'required',
                    'numeric',
                    'min:1',
                    Rule::unique('bs_jadwal_ujians', 'nama_sesi')->where(function ($query) use ($request) {
                        return $query->where('periode_ujian_id', $request->periode_ujian_id)
                                     ->where('tanggal_ujian', $request->tanggal_ujian)
                                     ->whereNull('deleted_at');
                    })
                ],
                'tanggal_ujian' => 'required|date',
                'waktu_mulai'   => 'required|date_format:H:i',
                'waktu_selesai' => [
                    'required',
                    'date_format:H:i',
                    function ($attribute, $value, $fail) use ($request) {
                        if (! $request->waktu_mulai) return;

                        $mulai      = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_mulai);
                        $selesai    = \Carbon\Carbon::createFromFormat('H:i', $value);
                        $minSelesai = $mulai->copy()->addMinutes(100);

                        if ($selesai->lte($mulai)) {
                            $fail('Waktu selesai harus lebih dari waktu mulai.');
                            return;
                        }

                        if ($selesai->lt($minSelesai)) {
                            $fail(
                                'Waktu selesai harus minimal 100 menit setelah waktu mulai. ' .
                                'Minimum: pukul ' . $minSelesai->format('H:i') . ' WIB.'
                            );
                        }
                    },
                ],
                'kuota'    => 'required|integer|min:1',
                'ruangan'  => 'nullable|string|max:255',
            ], [
                'nama_sesi.unique' => 'Sesi ke-' . $request->nama_sesi . ' sudah ada pada tanggal ini.',
            ]);

            return DB::transaction(function () use ($validatedData, $request) {
                // Cek duplikat atomik dengan lock — cegah race double-click
                $exists = JadwalUjian::where('periode_ujian_id', $request->periode_ujian_id)
                    ->where('nama_sesi', $request->nama_sesi)
                    ->where('tanggal_ujian', $request->tanggal_ujian)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw \Illuminate\Validation\ValidationException::withMessages(
                        ['nama_sesi' => 'Sesi ke-' . $request->nama_sesi . ' sudah ada pada tanggal ini.']
                    );
                }

                JadwalUjian::create($validatedData);

                return redirect()->route('banksoal.pendaftaran.alokasi-sesi.index', ['periode_id' => $request->periode_ujian_id])
                    ->with('success', 'Sesi ujian berhasil ditambahkan.');
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'uniq_jadwal') || $e->getCode() === '23505') {
                return back()->withErrors(['nama_sesi' => 'Sesi ke-' . $request->nama_sesi . ' sudah ada pada tanggal ini.'])->withInput();
            }
            \Log::error('JadwalUjian Store Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['database' => 'Gagal menyimpan sesi. Silakan coba lagi.'])->withInput();
        } catch (\Throwable $e) {
            \Log::error('JadwalUjian Store Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['database' => 'Gagal menyimpan sesi. Silakan coba lagi.'])->withInput();
        }
    }

    public function destroy($id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        $periodeId = $jadwal->periode_ujian_id;

        // Reset status mahasiswa yang sebelumnya dialokasikan ke sesi ini
        $jadwal->pendaftars()->update(['jadwal_ujian_id' => null]);

        $jadwal->delete();

        return redirect()->route('banksoal.pendaftaran.alokasi-sesi.index', ['periode_id' => $periodeId])
            ->with('success', 'Sesi ujian berhasil dihapus dan peserta telah dikembalikan ke daftar belum dialokasi.');
    }
}
