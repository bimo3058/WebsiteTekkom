<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Illuminate\Support\Str;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $this->updateStatusOtomatis();

        $search  = $request->get('search', '');
        $statusFilter = $request->get('status', 'all');
        $perPage = in_array((int) $request->get('perPage', 5), [5, 10, 25, 50])
            ? (int) $request->get('perPage', 5)
            : 5;

        $query = PeriodeUjian::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_periode', 'ilike', '%' . $search . '%')
                  ->orWhere('status', 'ilike', '%' . $search . '%');
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $periodes = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage)
                          ->withQueryString();

        return view('banksoal::periode.index', compact('periodes', 'search', 'perPage', 'statusFilter'));
    }

    /**
     * Update status periode secara otomatis berdasarkan tanggal.
     *
     * Menggunakan 3 batch UPDATE — tidak ada loop/N+1.
     * Urutan eksekusi penting: selesai → aktif → draft.
     */
    private function updateStatusOtomatis(): void
    {
        $today = now()->toDateString();

        // 1. Selesai: tanggal akhir ujian (atau pendaftaran jika ujian null) sudah berlalu.
        //    Ekuivalen SQL: COALESCE(tanggal_selesai_ujian, tanggal_selesai) < $today
        PeriodeUjian::where('status', '!=', 'selesai')
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {
                    $q2->whereNotNull('tanggal_selesai_ujian')
                       ->where('tanggal_selesai_ujian', '<', $today);
                })->orWhere(function ($q2) use ($today) {
                    $q2->whereNull('tanggal_selesai_ujian')
                       ->where('tanggal_selesai', '<', $today);
                });
            })
            ->update(['status' => 'selesai']);

        // 2. Draft → Aktif: tanggal mulai pendaftaran sudah tiba.
        PeriodeUjian::where('status', 'draft')
            ->where('tanggal_mulai', '<=', $today)
            ->update(['status' => 'aktif']);

        // 3. Aktif → Draft: tanggal mulai dimajukan ke depan (kasus setelah edit periode).
        PeriodeUjian::where('status', 'aktif')
            ->where('tanggal_mulai', '>', $today)
            ->update(['status' => 'draft']);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'              => 'required|string|max:255',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian'       => 'required|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_ujian'     => 'required|date|after_or_equal:tanggal_mulai_ujian',
            'deskripsi'                 => 'nullable|string',
            'kuota_peserta'             => 'required|integer|min:1|max:9999',
            'target_wisuda_options'     => 'required|array',
            'target_wisuda_options.*'   => 'required|string|max:200',
        ], [
            'tanggal_mulai_ujian.after_or_equal' => 'Tanggal mulai ujian tidak boleh sebelum tanggal tutup pendaftaran.',
            'tanggal_selesai_ujian.after_or_equal' => 'Tanggal selesai ujian tidak boleh sebelum tanggal mulai ujian.',
            'tanggal_selesai.after_or_equal' => 'Tanggal tutup pendaftaran tidak boleh sebelum tanggal buka pendaftaran.',
            'target_wisuda_options.required' => 'Pilihan target wisuda wajib diisi.',
            'target_wisuda_options.*.required' => 'Pilihan target wisuda wajib diisi.',
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

        // Parse target_wisuda_options: filter item kosong dari input array
        $rawOptions = $request->input('target_wisuda_options', []);
        $targetOptions = array_values(array_filter(array_map('trim', (array) $rawOptions))) ?: null;

        PeriodeUjian::create([
            'nama_periode'              => $request->nama_periode,
            'slug'                      => \Illuminate\Support\Str::slug($request->nama_periode . '-' . time()),
            'tanggal_mulai'             => $request->tanggal_mulai,
            'tanggal_selesai'           => $request->tanggal_selesai,
            'tanggal_mulai_ujian'       => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian'     => $request->tanggal_selesai_ujian,
            'status'                    => $status,
            'deskripsi'                 => $request->deskripsi,
            'kuota_peserta'             => $request->kuota_peserta ?: null,
            'target_wisuda_options'     => $targetOptions,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        $request->validate([
            'nama_periode'              => 'required|string|max:255',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_mulai_ujian'       => 'required|date|after_or_equal:tanggal_selesai',
            'tanggal_selesai_ujian'     => 'required|date|after_or_equal:tanggal_mulai_ujian',
            'deskripsi'                 => 'nullable|string',
            'kuota_peserta'             => 'required|integer|min:1|max:9999',
            'target_wisuda_options'     => 'required|array',
            'target_wisuda_options.*'   => 'required|string|max:200',
        ], [
            'tanggal_mulai_ujian.after_or_equal' => 'Tanggal mulai ujian tidak boleh sebelum tanggal tutup pendaftaran.',
            'tanggal_selesai_ujian.after_or_equal' => 'Tanggal selesai ujian tidak boleh sebelum tanggal mulai ujian.',
            'tanggal_selesai.after_or_equal' => 'Tanggal tutup pendaftaran tidak boleh sebelum tanggal buka pendaftaran.',
            'target_wisuda_options.required' => 'Pilihan target wisuda wajib diisi.',
            'target_wisuda_options.*.required' => 'Pilihan target wisuda wajib diisi.',
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

        // Parse target_wisuda_options: filter item kosong dari input array
        $rawOptions = $request->input('target_wisuda_options', []);
        $targetOptions = array_values(array_filter(array_map('trim', (array) $rawOptions))) ?: null;

        $periode->update([
            'nama_periode'              => $request->nama_periode,
            'tanggal_mulai'             => $request->tanggal_mulai,
            'tanggal_selesai'           => $request->tanggal_selesai,
            'tanggal_mulai_ujian'       => $request->tanggal_mulai_ujian,
            'tanggal_selesai_ujian'     => $request->tanggal_selesai_ujian,
            'status'                    => $newStatus,
            'deskripsi'                 => $request->deskripsi,
            'kuota_peserta'             => $request->kuota_peserta ?: null,
            'target_wisuda_options'     => $targetOptions,
        ]);

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil diupdate.');
    }

    public function closePendaftaran($id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        if (!$periode->pendaftaran_terbuka) {
            return redirect()->route('banksoal.periode.setup')
                ->with('error', 'Pendaftaran tidak sedang terbuka pada periode ini.');
        }

        $periode->update(['pendaftaran_ditutup_paksa' => true]);

        return redirect()->route('banksoal.periode.setup')
            ->with('success', "Pendaftaran \"" . $periode->nama_periode . "\" berhasil ditutup.");
    }

    public function openPendaftaran($id)
    {
        $periode = PeriodeUjian::findOrFail($id);

        $tglSelesai = Carbon::parse($periode->tanggal_selesai)->endOfDay();
        if (now()->gt($tglSelesai)) {
            return redirect()->route('banksoal.periode.setup')
                ->with('error', 'Tidak dapat membuka kembali: tanggal pendaftaran sudah berakhir.');
        }

        if (!$periode->pendaftaran_ditutup_paksa) {
            return redirect()->route('banksoal.periode.setup')
                ->with('error', 'Pendaftaran periode ini tidak sedang ditutup paksa.');
        }

        $periode->update(['pendaftaran_ditutup_paksa' => false]);

        return redirect()->route('banksoal.periode.setup')
            ->with('success', "Pendaftaran \"" . $periode->nama_periode . "\" berhasil dibuka kembali.");
    }

    public function destroy($id)
    {
        $hasPendaftar = PendaftarUjian::where('periode_ujian_id', $id)->exists();
        if ($hasPendaftar) {
            return redirect()->route('banksoal.periode.setup')
                ->with('error', 'Gagal menghapus periode. Sudah ada mahasiswa yang mendaftar.');
        }

        $periode = PeriodeUjian::findOrFail($id);
        $periode->delete();

        return redirect()->route('banksoal.periode.setup')->with('success', 'Periode Ujian berhasil dihapus.');
    }
}
