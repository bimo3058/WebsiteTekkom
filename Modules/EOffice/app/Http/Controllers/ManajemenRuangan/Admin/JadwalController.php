<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Ruangan;

class JadwalController extends Controller
{
    /**
     * Dispatcher to Academic or Event view.
     */
    public function index(Request $request)
    {
        $routeName = $request->route()->getName();
        $isAkademik = strpos($routeName, 'jadwal-akademik') !== false;

        $viewMode = $isAkademik ? 'akademik' : 'event';
        $tipe = $request->query('tipe', $isAkademik ? 'rutin' : 'spesifik');

        $search = $request->query('search');
        $filterHari = $request->query('hari');
        $filterRuangan = $request->query('ruangan_id');
        $filterKategori = $request->query('kategori');
        $filterStatusWaktu = $request->query('status_waktu');
        $sort = $request->query('sort', $isAkademik ? 'waktu' : 'terbaru');

        $query = MrJadwalInternal::with('ruangan');

        if ($isAkademik) {
            if ($sort === 'waktu') {
                $query->orderBy('hari', 'asc')->orderBy('jam_mulai', 'asc');
            } elseif ($sort === 'matkul_asc') {
                $query->orderBy('mata_kuliah', 'asc');
            } elseif ($sort === 'matkul_desc') {
                $query->orderBy('mata_kuliah', 'desc');
            } elseif ($sort === 'ruangan') {
                $query->join('eo_mr_ruangans', 'eo_mr_jadwal_internal.ruangan_id', '=', 'eo_mr_ruangans.id')
                    ->orderBy('eo_mr_ruangans.nama', 'asc')
                    ->select('eo_mr_jadwal_internal.*');
            } elseif ($sort === 'terbaru') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('hari', 'asc')->orderBy('jam_mulai', 'asc');
            }
        } else {
            // Sorting khusus Blokir Ruangan
            if ($sort === 'pelaksanaan_asc') {
                $query->orderBy('tanggal_spesifik', 'asc')->orderBy('jam_mulai', 'asc');
            } elseif ($sort === 'pelaksanaan_desc') {
                $query->orderBy('tanggal_spesifik', 'desc')->orderBy('jam_mulai', 'asc');
            } elseif ($sort === 'ruangan_asc') {
                $query->join('eo_mr_ruangans', 'eo_mr_jadwal_internal.ruangan_id', '=', 'eo_mr_ruangans.id')
                    ->orderBy('eo_mr_ruangans.nama', 'asc')
                    ->select('eo_mr_jadwal_internal.*');
            } elseif ($sort === 'ruangan_desc') {
                $query->join('eo_mr_ruangans', 'eo_mr_jadwal_internal.ruangan_id', '=', 'eo_mr_ruangans.id')
                    ->orderBy('eo_mr_ruangans.nama', 'desc')
                    ->select('eo_mr_jadwal_internal.*');
            } elseif ($sort === 'terlama') {
                $query->orderBy('created_at', 'asc');
            } else {
                // Default: terbaru
                $query->orderBy('created_at', 'desc');
            }
        }

        if ($isAkademik) {
            $query->where('eo_mr_jadwal_internal.kategori', 'Jadwal Akademik (Kuliah)');

            if ($search) {
                // Gunakan LOWER() untuk memastikan case-insensitive searching lintas Database (terutama PostgreSQL)
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(mata_kuliah) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(kode_mk) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(kelas) LIKE ?', ["%{$searchTerm}%"])
                        ->orWhereRaw('LOWER(pengampu) LIKE ?', ["%{$searchTerm}%"]);
                });
            }
            if ($filterHari) {
                $query->where('eo_mr_jadwal_internal.hari', $filterHari);
            }
            if ($filterRuangan) {
                $query->where('eo_mr_jadwal_internal.ruangan_id', $filterRuangan);
            }
        } else {
            $query->where('eo_mr_jadwal_internal.kategori', '!=', 'Jadwal Akademik (Kuliah)');

            if ($search) {
                $searchTerm = strtolower($search);
                $query->whereRaw('LOWER(keterangan) LIKE ?', ["%{$searchTerm}%"]);
            }

            if ($filterKategori) {
                $query->where('eo_mr_jadwal_internal.kategori', $filterKategori);
            }

            if ($filterRuangan) {
                $query->where('eo_mr_jadwal_internal.ruangan_id', $filterRuangan);
            }

            if ($filterStatusWaktu) {
                $today = \Carbon\Carbon::today()->format('Y-m-d');
                if ($filterStatusWaktu === 'mendatang') {
                    $query->where('eo_mr_jadwal_internal.tanggal_spesifik', '>=', $today);
                } elseif ($filterStatusWaktu === 'lewat') {
                    $query->where('eo_mr_jadwal_internal.tanggal_spesifik', '<', $today);
                }
            }
        }

        if ($tipe === 'rutin' || $tipe === 'spesifik') {
            $query->where('eo_mr_jadwal_internal.tipe_jadwal', $tipe);
        }

        $jadwals = $query->paginate((int) $request->query('per_page', 10))->withQueryString();
        $ruangans = Ruangan::where('is_active', true)->get();

        $bladeFile = $isAkademik ? 'index' : 'maintenance';
        return view('eoffice::manajemen-ruangan.admin.jadwal.' . $bladeFile, compact('jadwals', 'tipe', 'ruangans', 'viewMode', 'search', 'filterHari', 'filterRuangan', 'sort', 'filterKategori', 'filterStatusWaktu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'nullable|exists:eo_mr_ruangans,id',
            'ruangan_ids' => 'nullable|array',
            'ruangan_ids.*' => 'exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required_unless:kategori,Jadwal Akademik (Kuliah)|nullable|string|max:255',
            'tgl_mulai_efektif' => 'nullable|date',
            'tgl_selesai_efektif' => 'nullable|date|after_or_equal:tgl_mulai_efektif',
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255',
            'is_multiday' => 'nullable|boolean',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        // Normalisasi Ruangan (Dukung 1 ruangan atau banyak ruangan sekaligus)
        $ruangans = [];
        if ($request->has('ruangan_ids') && is_array($request->ruangan_ids) && count($request->ruangan_ids) > 0) {
            $ruangans = $request->ruangan_ids;
        } elseif ($request->has('ruangan_id') && $request->ruangan_id) {
            $ruangans = [$request->ruangan_id];
        }

        if (empty($ruangans)) {
            return redirect()->back()->withErrors(['collision' => 'Silakan pilih minimal 1 ruangan.'])->withInput();
        }

        // Normalisasi Tanggal (Dukung 1 hari atau rentang multi-hari)
        $dates = [];
        if ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
            $dates = [$request->hari];
        } else {
            if ($request->has('is_multiday') && filter_var($request->is_multiday, FILTER_VALIDATE_BOOLEAN)) {
                $request->validate([
                    'tanggal_mulai' => 'required|date',
                    'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                ]);
                $start = \Carbon\Carbon::parse($request->tanggal_mulai);
                $end = \Carbon\Carbon::parse($request->tanggal_selesai);
                for ($d = $start; $d->lte($end); $d->addDay()) {
                    $dates[] = $d->format('Y-m-d');
                }
            } else {
                $request->validate(['tanggal_spesifik' => 'required|date']);
                $dates[] = $request->tanggal_spesifik;
            }
        }

        // Backend Collision Prevention (Loop)
        foreach ($ruangans as $r_id) {
            foreach ($dates as $dateOrHari) {
                $queryCheck = MrJadwalInternal::where('ruangan_id', $r_id)
                    ->where(function ($q) use ($request) {
                        $q->where('jam_mulai', '<', $request->jam_selesai)
                            ->where('jam_selesai', '>', $request->jam_mulai);
                    });

                if ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') {
                    $queryCheck->where('hari', $dateOrHari);
                } else {
                    $queryCheck->where('tanggal_spesifik', $dateOrHari);
                }

                $conflict = $queryCheck->first();
                if ($conflict) {
                    $nama = $conflict->kategori === 'Jadwal Akademik (Kuliah)' ? ($conflict->mata_kuliah . ' - ' . $conflict->kelas) : $conflict->keterangan;
                    $msg = "Gagal menyimpan! Ruangan beririsan dengan jadwal: {$nama} (" . substr($conflict->jam_mulai, 0, 5) . " s.d " . substr($conflict->jam_selesai, 0, 5) . ")";
                    return redirect()->back()->withErrors(['collision' => $msg])->withInput();
                }
            }
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($ruangans as $r_id) {
                foreach ($dates as $dateOrHari) {
                    MrJadwalInternal::create([
                        'ruangan_id' => $r_id,
                        'tipe_jadwal' => $request->tipe_jadwal,
                        'kategori' => $request->kategori,
                        'hari' => ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') ? $dateOrHari : null,
                        'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $dateOrHari : null,
                        'jam_mulai' => $request->jam_mulai,
                        'jam_selesai' => $request->jam_selesai,
                        'keterangan' => $request->kategori === 'Jadwal Akademik (Kuliah)' ? ($request->mata_kuliah . '-' . $request->kelas) : $request->keterangan,
                        'tgl_mulai_efektif' => $request->tgl_mulai_efektif,
                        'tgl_selesai_efektif' => $request->tgl_selesai_efektif,
                        'mata_kuliah' => $request->mata_kuliah,
                        'kode_mk' => $request->kode_mk,
                        'kelas' => $request->kelas,
                        'sks' => $request->sks,
                        'kuota' => $request->kuota,
                        'pengampu' => $request->pengampu,
                    ]);

                    // Super Override: Pembatalan Otomatis Jadwal Mahasiswa
                    if ($request->tipe_jadwal === 'spesifik' && $request->kategori !== 'Jadwal Akademik (Kuliah)') {
                        $peminjamans = \Modules\EOffice\Models\Peminjaman::where('ruangan_id', $r_id)
                            ->where('tanggal_pinjam', $dateOrHari)
                            ->whereIn('status', ['menunggu', 'disetujui'])
                            ->where(function ($q) use ($request) {
                                $q->where('jam_mulai', '<', $request->jam_selesai)
                                    ->where('jam_selesai', '>', $request->jam_mulai);
                            })->get();

                        foreach ($peminjamans as $p) {
                            $newStatus = $p->status === 'disetujui' ? 'dibatalkan' : 'ditolak';
                            $p->update([
                                'status' => $newStatus,
                                'alasan_penolakan' => "Dibatalkan sepihak oleh sistem karena ruangan diblokir untuk {$request->kategori}.",
                                'waktu_approval' => now()
                            ]);

                            // Insert Database Notification (Jejak Audit)
                            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                                'id' => \Illuminate\Support\Str::uuid()->toString(),
                                'type' => 'App\Notifications\PeminjamanDibatalkan',
                                'notifiable_type' => 'App\Models\User',
                                'notifiable_id' => $p->user_id,
                                'data' => json_encode([
                                    'title' => 'Peminjaman Dibatalkan Sistem',
                                    'message' => "Peminjaman Anda {$newStatus} secara otomatis karena ruangan digunakan untuk {$request->kategori}.",
                                    'peminjaman_id' => $p->id
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            $redirectRoute = $request->kategori === 'Jadwal Akademik (Kuliah)'
                ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
                : 'eoffice.peminjaman.admin.jadwal-internal.index';

            return redirect()->route($redirectRoute)
                ->with('success', 'Jadwal berhasil ditambahkan. Ruangan terkait otomatis terblokir pada waktu tersebut.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withErrors(['collision' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);

        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_jadwal' => 'required|in:rutin,spesifik',
            'kategori' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'required_unless:kategori,Jadwal Akademik (Kuliah)|nullable|string|max:255',
            'tgl_mulai_efektif' => 'nullable|date',
            'tgl_selesai_efektif' => 'nullable|date|after_or_equal:tgl_mulai_efektif',
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255',
        ]);

        if ($request->tipe_jadwal === 'rutin') {
            $request->validate(['hari' => 'required|integer|between:1,7']);
        } else {
            $request->validate(['tanggal_spesifik' => 'required|date']);
        }

        // Backend Collision Prevention (Update Mode)
        $queryCheckUpdate = MrJadwalInternal::where('ruangan_id', $request->ruangan_id)
            ->where('id', '!=', $id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            });

        if ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') {
            $queryCheckUpdate->where('hari', $request->hari);
        } else {
            $queryCheckUpdate->where('tanggal_spesifik', $request->tanggal_spesifik);
        }

        $conflictUpdate = $queryCheckUpdate->first();
        if ($conflictUpdate) {
            $namaUpdate = $conflictUpdate->kategori === 'Jadwal Akademik (Kuliah)' ? ($conflictUpdate->mata_kuliah . ' - ' . $conflictUpdate->kelas) : $conflictUpdate->keterangan;
            $msgUpdate = "Gagal memperbarui! Waktu beririsan dengan jadwal: {$namaUpdate} (" . substr($conflictUpdate->jam_mulai, 0, 5) . " s.d " . substr($conflictUpdate->jam_selesai, 0, 5) . ")";
            return redirect()->back()->withErrors(['collision' => $msgUpdate])->withInput();
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $jadwal->update([
                'ruangan_id' => $request->ruangan_id,
                'tipe_jadwal' => $request->tipe_jadwal,
                'kategori' => $request->kategori,
                'hari' => ($request->tipe_jadwal === 'rutin' || $request->kategori === 'Jadwal Akademik (Kuliah)') ? $request->hari : null,
                'tanggal_spesifik' => $request->tipe_jadwal === 'spesifik' ? $request->tanggal_spesifik : null,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'keterangan' => $request->kategori === 'Jadwal Akademik (Kuliah)' ? ($request->mata_kuliah . '-' . $request->kelas) : $request->keterangan,
                'tgl_mulai_efektif' => $request->tgl_mulai_efektif,
                'tgl_selesai_efektif' => $request->tgl_selesai_efektif,
                'mata_kuliah' => $request->mata_kuliah,
                'kode_mk' => $request->kode_mk,
                'kelas' => $request->kelas,
                'sks' => $request->sks,
                'kuota' => $request->kuota,
                'pengampu' => $request->pengampu,
            ]);

            // Super Override: Pembatalan Otomatis Jadwal Mahasiswa
            if ($request->tipe_jadwal === 'spesifik' && $request->kategori !== 'Jadwal Akademik (Kuliah)') {
                $peminjamans = \Modules\EOffice\Models\Peminjaman::where('ruangan_id', $request->ruangan_id)
                    ->where('tanggal_pinjam', $request->tanggal_spesifik)
                    ->whereIn('status', ['menunggu', 'disetujui'])
                    ->where(function ($q) use ($request) {
                        $q->where('jam_mulai', '<', $request->jam_selesai)
                            ->where('jam_selesai', '>', $request->jam_mulai);
                    })->get();

                foreach ($peminjamans as $p) {
                    $newStatus = $p->status === 'disetujui' ? 'dibatalkan' : 'ditolak';
                    $p->update([
                        'status' => $newStatus,
                        'alasan_penolakan' => "Dibatalkan sepihak oleh sistem karena ruangan diblokir untuk {$request->kategori}.",
                        'waktu_approval' => now()
                    ]);

                    // Insert Database Notification (Jejak Audit)
                    \Illuminate\Support\Facades\DB::table('notifications')->insert([
                        'id' => \Illuminate\Support\Str::uuid()->toString(),
                        'type' => 'App\Notifications\PeminjamanDibatalkan',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $p->user_id,
                        'data' => json_encode([
                            'title' => 'Peminjaman Dibatalkan Sistem',
                            'message' => "Peminjaman Anda {$newStatus} secara otomatis karena ruangan digunakan untuk {$request->kategori}.",
                            'peminjaman_id' => $p->id
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            $redirectRoute = $request->kategori === 'Jadwal Akademik (Kuliah)'
                ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
                : 'eoffice.peminjaman.admin.jadwal-internal.index';

            return redirect()->route($redirectRoute)
                ->with('success', 'Konfigurasi Jadwal berhasil diperbarui.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withErrors(['collision' => 'Terjadi kesalahan sistem saat memperbarui data: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $jadwal = MrJadwalInternal::findOrFail($id);
        $redirectRoute = $jadwal->kategori === 'Jadwal Akademik (Kuliah)'
            ? 'eoffice.peminjaman.admin.jadwal-akademik.index'
            : 'eoffice.peminjaman.admin.jadwal-internal.index';

        $jadwal->delete();
        return redirect()->route($redirectRoute)
            ->with('success', 'Jadwal berhasil dihapus. Pemblokiran ruangan telah dicabut.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:eo_mr_jadwal_internal,id'
        ]);

        MrJadwalInternal::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', 'Berhasil menghapus jadwal yang dipilih.');
    }

    public function resetAkademik()
    {
        MrJadwalInternal::where('kategori', 'Jadwal Akademik (Kuliah)')->delete();
        return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
            ->with('success', 'Seluruh jadwal perkuliahan akademik berhasil dihapus (Reset Semester).');
    }



    public function importPreview(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $file = $request->file('file_excel');
        $csvData = [];
        $ext = strtolower($file->getClientOriginalExtension());

        // WE WILL ALWAYS USE PhpSpreadsheet SO IT SUPPORTS CSV and XLSX UNIVERSALLY 
        // AND APPLIES THE SIAP EXTRACTION ENGINE TO ALL!
        // This prevents the bug where uploading a CSV bypasses the SIAP structure match.

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getSheet(0)->toArray();

            $debugLog = "=== UPLOAD DEBUG ===\n";
            $debugLog .= "Total Rows in Sheet 0: " . count($sheet) . "\n";
            if (count($sheet) > 0) {
                $debugLog .= "Row[0] Raw JSON: " . json_encode($sheet[0]) . "\n";
                $debugLog .= "Row[1] Raw JSON: " . (isset($sheet[1]) ? json_encode($sheet[1]) : 'null') . "\n";
                $debugLog .= "Row[20] Raw JSON: " . (isset($sheet[20]) ? json_encode($sheet[20]) : 'null') . "\n";
            }

            $semuaRuangan = Ruangan::where('is_active', true)->get();
            $hariMap = ['SEN' => 1, 'SENIN' => 1, 'SEL' => 2, 'SELASA' => 2, 'RAB' => 3, 'RABU' => 3, 'KAM' => 4, 'KAMIS' => 4, 'JUM' => 5, 'JUMAT' => 5, 'SAB' => 6, 'SABTU' => 6];

            $failHariCount = 0;
            $failGateCount = 0;
            $trackedSchedules = [];

            foreach ($sheet as $i => $row) {
                if (empty($row[0]) || strpos((string) $row[0], ',') === false) {
                    continue;
                }

                $splitDate = explode(',', (string) $row[0]);
                $hariStr = strtoupper(trim($splitDate[0]));

                if (!isset($hariMap[$hariStr])) {
                    $failHariCount++;
                    continue;
                }

                $hariId = $hariMap[$hariStr];

                $jamStr = explode('-', trim($splitDate[1] ?? ''));
                $jamMulai = isset($jamStr[0]) ? trim($jamStr[0]) : '00:00';
                $jamSelesai = isset($jamStr[1]) ? trim($jamStr[1]) : '00:00';

                $sheetWidth = count($row);
                $roomIdx = $sheetWidth >= 10 ? 9 : 7;
                $pengIdx = $sheetWidth >= 10 ? 8 : 6;

                $rawRoom = (string) ($row[$roomIdx] ?? '');
                $rawRoomUpper = strtoupper($rawRoom);
                
                $ruanganIdTarget = null;

                if (strpos($rawRoomUpper, '|') !== false) {
                    $parts = explode('|', $rawRoomUpper);
                    $roomCode = trim($parts[0]);
                    $buildingInfo = trim($parts[1] ?? '');
                    
                    // Bersihkan dari titik dan spasi untuk pengecekan keyword (contoh: 'T. Kom' menjadi 'TKOM')
                    $cleanBuildingInfo = str_replace([' ', '.'], '', $buildingInfo);
                    
                    $isTekkom = false;
                    if (strpos($cleanBuildingInfo, 'TKOM') !== false || 
                        strpos($cleanBuildingInfo, 'TEKKOM') !== false || 
                        strpos($cleanBuildingInfo, 'TEKNIKKOMPUTER') !== false) {
                        $isTekkom = true;
                    }
                    
                    if ($isTekkom) {
                        $cleanRoomCode = str_replace([' ', '.', '-'], '', $roomCode);
                        foreach ($semuaRuangan as $r) {
                            $cleanName = strtoupper(str_replace([' ', '.', '-'], '', $r->nama));
                            if (!empty($cleanName) && strpos($cleanRoomCode, $cleanName) !== false) {
                                $ruanganIdTarget = $r->id;
                                break;
                            }
                        }
                    } else {
                        // Jika bukan Tekkom (misal Teknik Mesin S1), ruanganIdTarget dibiarkan null 
                        // agar muncul di sandbox dengan status kuning (unmapped).
                        $ruanganIdTarget = null;
                    }
                } else {
                    // Kasus tanpa delimiter '|' (misal 'R. Dexlite 401')
                    $cleanRawRoom = str_replace([' ', '.', '-'], '', $rawRoomUpper);
                    foreach ($semuaRuangan as $r) {
                        $cleanName = strtoupper(str_replace([' ', '.', '-'], '', $r->nama));
                        if (!empty($cleanName) && strpos($cleanRawRoom, $cleanName) !== false) {
                            $ruanganIdTarget = $r->id;
                            break;
                        }
                    }
                }

                $matkulRaw = trim((string) ($row[1] ?? ''));
                $kelasRaw = trim((string) ($row[3] ?? ''));
                $sks = (int) filter_var($row[4] ?? '0', FILTER_SANITIZE_NUMBER_INT);
                $pengampu = str_replace("\n", " / ", (string) ($row[$pengIdx] ?? ''));

                if (empty($matkulRaw) || empty($kelasRaw) || empty($jamMulai)) {
                    if ($failGateCount < 10) {
                        $debugLog .= "Fail Gate pada Row $i. Data: RTarget($ruanganIdTarget) Matkul($matkulRaw) Kelas($kelasRaw) Jam($jamMulai)\n";
                    }
                    $failGateCount++;
                    continue;
                }

                // Collision Check (Hanya cek dalam file Excel yang sama)
                $conflictMsg = '';
                if (!isset($trackedSchedules[$ruanganIdTarget][$hariId])) {
                    $trackedSchedules[$ruanganIdTarget][$hariId] = [];
                }
                foreach ($trackedSchedules[$ruanganIdTarget][$hariId] as $exist) {
                    // Cek Irisan Waktu (Overlap)
                    if ($jamMulai < $exist['selesai'] && $jamSelesai > $exist['mulai']) {
                        if (trim($matkulRaw) === trim($exist['matkul'])) {
                            // Abaikan warning: Ini adalah tabrakan fiktif karena SIAP 
                            // seringkali mengulang baris data yang sama persis jika dosen pengampunya lebih dari 1
                            continue;
                        }
                        $conflictMsg = 'Konflik jam dengan: ' . $exist['matkul'];
                        break;
                    }
                }
                $trackedSchedules[$ruanganIdTarget][$hariId][] = ['mulai' => $jamMulai, 'selesai' => $jamSelesai, 'matkul' => $matkulRaw];

                $csvData[] = [
                    $hariId,
                    $ruanganIdTarget,   // NO CAST to (int)
                    $jamMulai,
                    $jamSelesai,
                    $matkulRaw,
                    trim((string) ($row[2] ?? '')),
                    $kelasRaw,
                    $sks,
                    (int) ($row[5] ?? 0),
                    trim($pengampu),
                    $conflictMsg, // [10] Peringatan Tabrakan Soft-Warning
                    trim($rawRoom) // [11] Raw Room String if not mapped
                ];
            }

            $debugLog .= "Total Succcess: " . count($csvData) . "\n";
            $debugLog .= "Total Fail HariMap: $failHariCount\n";
            $debugLog .= "Total Fail Gate: $failGateCount\n";
            \Log::info($debugLog);
            file_put_contents(storage_path('logs/siap_debug.log'), $debugLog);

        } catch (\Exception $e) {
            \Log::error('[SIAP Import] PhpSpreadsheet gagal: ' . $e->getMessage());
            return back()->withErrors(['file_excel' => 'Gagal membaca file SIAP: ' . $e->getMessage()]);
        }

        $ruangans = Ruangan::where('is_active', true)->get()->keyBy('id');
        return view('eoffice::manajemen-ruangan.admin.jadwal.import-preview', compact('csvData', 'ruangans'));
    }

    public function executeImport(Request $request)
    {
        $request->validate([
            'validated_payload' => 'required|string',
            'tgl_mulai_efektif_global' => 'required|date',
            'tgl_selesai_efektif_global' => 'required|date|after_or_equal:tgl_mulai_efektif_global',
        ]);

        $payload = json_decode($request->input('validated_payload'), true);

        if (!$payload || !is_array($payload)) {
            return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
                ->withErrors(['File atau format payload jadwal tidak valid.']);
        }

        $insertBatch = [];
        $now = now();

        foreach ($payload as $row) {
            // Jika ruangan masih kosong karena manual mapping dibiarkan kosong, maka diskip!
            if (empty($row['ruangan_id']))
                continue;

            $insertBatch[] = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'ruangan_id' => $row['ruangan_id'],
                'tipe_jadwal' => 'rutin',
                'kategori' => 'Jadwal Akademik (Kuliah)',
                'hari' => $row['hari'],
                'tanggal_spesifik' => null,
                'jam_mulai' => $row['jam_mulai'],
                'jam_selesai' => $row['jam_selesai'],
                'keterangan' => $row['mata_kuliah'] . '-' . $row['kelas'],
                'tgl_mulai_efektif' => $request->tgl_mulai_efektif_global,
                'tgl_selesai_efektif' => $request->tgl_selesai_efektif_global,
                'mata_kuliah' => $row['mata_kuliah'],
                'kode_mk' => $row['kode_mk'] ?? null,
                'kelas' => $row['kelas'] ?? null,
                'sks' => $row['sks'] ?? null,
                'kuota' => $row['kuota'] ?? null,
                'pengampu' => $row['pengampu'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        MrJadwalInternal::insert($insertBatch);

        // --- MULAI: LOGIKA SAPU BERSIH (AUTO-REJECT / AUTO-REVOKE) ---
        // Cari semua peminjaman mahasiswa yang belum berlalu
        $peminjamans = \Modules\EOffice\Models\Peminjaman::whereIn('status', ['menunggu', 'disetujui'])
            ->where('tanggal_pinjam', '>=', \Carbon\Carbon::today()->format('Y-m-d'))
            ->get();

        $ditolakCount = 0;
        $dibatalkanCount = 0;

        foreach ($peminjamans as $p) {
            $hariPinjam = \Carbon\Carbon::parse($p->tanggal_pinjam)->format('N');
            
            // Cek apakah ada di insertBatch yang bentrok
            $isConflict = false;
            foreach ($insertBatch as $jadwal) {
                if ($jadwal['ruangan_id'] == $p->ruangan_id && $jadwal['hari'] == $hariPinjam) {
                    // Cek rentang tanggal efektif akademik
                    $startEfektif = $jadwal['tgl_mulai_efektif'];
                    $endEfektif = $jadwal['tgl_selesai_efektif'];
                    if ($p->tanggal_pinjam >= $startEfektif && $p->tanggal_pinjam <= $endEfektif) {
                        // Cek irisan waktu (overlap jam)
                        if ($p->jam_mulai < $jadwal['jam_selesai'] && $p->jam_selesai > $jadwal['jam_mulai']) {
                            $isConflict = true;
                            break;
                        }
                    }
                }
            }

            if ($isConflict) {
                if ($p->status === 'menunggu') {
                    $p->update([
                        'status' => 'ditolak',
                        'alasan_penolakan' => 'Ditolak otomatis karena bentrok dengan Jadwal Akademik baru',
                        'waktu_approval' => now()
                    ]);
                    $ditolakCount++;
                } elseif ($p->status === 'disetujui') {
                    $p->update([
                        'status' => 'dibatalkan',
                        'alasan_penolakan' => 'Dibatalkan sepihak oleh sistem. Ruangan dialihfungsikan secara mendadak untuk keperluan Akademik',
                        'waktu_approval' => now()
                    ]);
                    $dibatalkanCount++;
                }
            }
        }
        // --- SELESAI: LOGIKA SAPU BERSIH ---

        $msgInfo = count($insertBatch) . ' row jadwal kelas massal berhasil diimpor!';
        if ($ditolakCount > 0 || $dibatalkanCount > 0) {
            $msgInfo .= " (Sistem telah melakukan sterilisasi: $ditolakCount permohonan baru ditolak otomatis & $dibatalkanCount jadwal disetujui dibatalkan otomatis karena bentrok).";
        }

        return redirect()->route('eoffice.peminjaman.admin.jadwal-akademik.index')
            ->with('success', $msgInfo);
    }

    /**
     * API untuk AJAX Cek Bentrok Realtime di UI
     * Versi Upgrade: Mendukung Multi-Room, Multi-Hari, & Summarization
     */
    public function checkCollision(Request $request)
    {
        $tipe = $request->tipe_jadwal; // rutin atau spesifik
        $kategori = $request->kategori;
        $jamMulai = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;
        $excludeId = $request->exclude_id; // id jika sedang edit

        if (!$jamMulai || !$jamSelesai) {
            return response()->json(['conflict' => false]);
        }

        // Normalisasi Ruangan
        $ruangans = [];
        if ($request->has('ruangan_ids') && is_array($request->ruangan_ids) && count($request->ruangan_ids) > 0) {
            $ruangans = $request->ruangan_ids;
        } elseif ($request->has('ruangan_id') && $request->ruangan_id) {
            $ruangans = [$request->ruangan_id];
        }

        if (empty($ruangans)) {
            return response()->json(['conflict' => false]);
        }

        // Normalisasi Tanggal
        $dates = [];
        if ($tipe === 'rutin' || $kategori === 'Jadwal Akademik (Kuliah)') {
            if (!$request->hari) return response()->json(['conflict' => false]);
            $dates = [$request->hari];
        } else {
            if ($request->has('is_multiday') && filter_var($request->is_multiday, FILTER_VALIDATE_BOOLEAN)) {
                if (!$request->tanggal_mulai || !$request->tanggal_selesai) return response()->json(['conflict' => false]);
                $start = \Carbon\Carbon::parse($request->tanggal_mulai);
                $end = \Carbon\Carbon::parse($request->tanggal_selesai);
                for ($d = $start; $d->lte($end); $d->addDay()) {
                    $dates[] = $d->format('Y-m-d');
                }
            } else {
                if (!$request->tanggal_spesifik) return response()->json(['conflict' => false]);
                $dates[] = $request->tanggal_spesifik;
            }
        }

        $akademikDetails = [];
        $peminjamanDetails = [];

        foreach ($ruangans as $r_id) {
            foreach ($dates as $dateOrHari) {
                // 1. Hitung Bentrok dengan Jadwal Internal/Akademik
                $queryCheck = MrJadwalInternal::where('ruangan_id', $r_id)
                    ->where(function ($q) use ($jamMulai, $jamSelesai) {
                        $q->where('jam_mulai', '<', $jamSelesai)
                            ->where('jam_selesai', '>', $jamMulai);
                    });

                if ($excludeId) {
                    $queryCheck->where('id', '!=', $excludeId);
                }

                if ($tipe === 'rutin' || $kategori === 'Jadwal Akademik (Kuliah)') {
                    $queryCheck->where('hari', $dateOrHari);
                } else {
                    $queryCheck->where('tanggal_spesifik', $dateOrHari);
                }

                $conflicts = $queryCheck->get();
                foreach ($conflicts as $c) {
                    $nama = $c->kategori === 'Jadwal Akademik (Kuliah)' ? ($c->mata_kuliah . ' - ' . $c->kelas) : $c->keterangan;
                    $akademikDetails[] = [
                        'nama' => $nama,
                        'waktu' => substr($c->jam_mulai, 0, 5) . ' - ' . substr($c->jam_selesai, 0, 5)
                    ];
                }

                // 2. Hitung Bentrok dengan Peminjaman Mahasiswa
                if ($tipe === 'spesifik' && $kategori !== 'Jadwal Akademik (Kuliah)') {
                    $peminjamanConflicts = \Modules\EOffice\Models\Peminjaman::where('ruangan_id', $r_id)
                        ->where('tanggal_pinjam', $dateOrHari)
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->where(function ($q) use ($jamMulai, $jamSelesai) {
                            $q->where('jam_mulai', '<', $jamSelesai)
                                ->where('jam_selesai', '>', $jamMulai);
                        })->get();
                    
                    foreach ($peminjamanConflicts as $p) {
                        $peminjamanDetails[] = [
                            'nama' => $p->kegiatan,
                            'waktu' => substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5)
                        ];
                    }
                }
            }
        }

        if (count($akademikDetails) > 0 || count($peminjamanDetails) > 0) {
            return response()->json([
                'conflict' => true,
                'akademik_details' => $akademikDetails,
                'peminjaman_details' => $peminjamanDetails,
            ]);
        }

        return response()->json(['conflict' => false]);
    }


}
