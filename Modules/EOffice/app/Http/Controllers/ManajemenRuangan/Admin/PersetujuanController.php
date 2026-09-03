<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Peminjaman;

class PersetujuanController extends Controller
{
    public function index(Request $request)
    {
        // System Event: Auto-kill expired dangling pending requests
        \Modules\EOffice\Models\Peminjaman::autoExpirePending();

        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $query = Peminjaman::with(['user', 'ruangan'])
            ->where(function ($q) use ($date, $time) {
                $q->where('status', 'menunggu')
                    ->orWhere(function ($q2) use ($date, $time) {
                        $q2->where('status', 'disetujui')
                            ->whereRaw("(tanggal_pinjam > ? OR (tanggal_pinjam = ? AND jam_selesai > ?))", [$date, $date, $time]);
                    });
            });

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(external_id) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('ruangan', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        $peminjamans = $query->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate((int) $request->input('per_page', 10))->withQueryString();

        $ruangans = \Modules\EOffice\Models\Ruangan::orderBy('nama', 'asc')->get();

        return view('eoffice::manajemen-ruangan.admin.persetujuan.index', compact('peminjamans', 'ruangans'));
    }

    public function riwayat(Request $request)
    {
        // System Event: Auto-kill expired dangling pending requests
        \Modules\EOffice\Models\Peminjaman::autoExpirePending();

        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $query = Peminjaman::with(['user', 'ruangan'])
            ->where('status', '!=', 'menunggu')
            ->where(function ($q) use ($date, $time) {
                $q->where('status', '!=', 'disetujui')
                    ->orWhere(function ($q2) use ($date, $time) {
                        $q2->where('status', 'disetujui')
                            ->whereRaw("(tanggal_pinjam < ? OR (tanggal_pinjam = ? AND jam_selesai <= ?))", [$date, $date, $time]);
                    });
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pinjam', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal_pinjam', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal_pinjam', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(external_id) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('ruangan', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
                })->orWhereRaw('LOWER(tujuan) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(nomor_telepon) LIKE ?', ["%{$search}%"]);
            });
        }

        $peminjamans = $query->orderBy('waktu_approval', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate((int) $request->input('per_page', 10))->withQueryString();

        $ruangans = \Modules\EOffice\Models\Ruangan::orderBy('nama', 'asc')->get();

        return view('eoffice::manajemen-ruangan.admin.persetujuan.riwayat', compact('peminjamans', 'ruangans'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);

        if ($request->status == 'disetujui') {
            // Re-check Collision - Peminjaman (Race Condition Guard)
            $isConflict = Peminjaman::where('ruangan_id', $peminjaman->ruangan_id)
                ->where('tanggal_pinjam', $peminjaman->tanggal_pinjam)
                ->where('status', 'disetujui')
                ->where('id', '!=', $peminjaman->id)
                ->where(function ($q) use ($peminjaman) {
                    $q->where('jam_mulai', '<', $peminjaman->jam_selesai)
                        ->where('jam_selesai', '>', $peminjaman->jam_mulai);
                })
                ->exists();

            if ($isConflict) {
                return redirect()->back()->withErrors(['Bentrok' => 'Gagal menyetujui! Ruangan sudah terlanjur disetujui untuk pihak lain di rentang waktu tersebut.']);
            }

            // Re-check Collision - Jadwal Internal (Guard against Excel Imports while pending)
            $dayOfWeek = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('N');
            $isInternalConflict = \Modules\EOffice\Models\MrJadwalInternal::where('ruangan_id', $peminjaman->ruangan_id)
                ->where(function ($query) use ($peminjaman, $dayOfWeek) {
                    $query->where(function ($q) use ($dayOfWeek, $peminjaman) {
                        $q->where('tipe_jadwal', 'rutin')
                            ->where('hari', $dayOfWeek)
                            ->where(function ($tq) use ($peminjaman) {
                                $tq->whereNull('tgl_mulai_efektif')
                                    ->orWhere('tgl_mulai_efektif', '<=', $peminjaman->tanggal_pinjam);
                            })
                            ->where(function ($tq) use ($peminjaman) {
                                $tq->whereNull('tgl_selesai_efektif')
                                    ->orWhere('tgl_selesai_efektif', '>=', $peminjaman->tanggal_pinjam);
                            });
                    })->orWhere(function ($q) use ($peminjaman) {
                        $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $peminjaman->tanggal_pinjam);
                    });
                })
                ->where(function ($query) use ($peminjaman) {
                    $query->where('jam_mulai', '<', $peminjaman->jam_selesai)
                        ->where('jam_selesai', '>', $peminjaman->jam_mulai);
                })
                ->first();

            if ($isInternalConflict) {
                return redirect()->back()->withErrors(['Sistem Internal' => 'Gagal menyetujui! Ruangan terblokir otomatis oleh Jadwal Akademik: ' . $isInternalConflict->keterangan]);
            }
        }

        $peminjaman->update([
            'status' => $request->status,
            'alasan_penolakan' => $request->status == 'ditolak' ? $request->alasan_penolakan : null,
            'waktu_approval' => now(),
        ]);

        $msg = $request->status == 'disetujui' ? 'berhasil disetujui' : 'telah ditolak';
        return redirect()->back()->with('success', "Pengajuan peminjaman {$msg}.");
    }
    public function updateOverride(Request $request, $id)
    {
        $request->validate([
            'override_ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'override_tanggal_pinjam' => 'required|date',
            'override_jam_mulai' => 'required|date_format:H:i',
            'override_jam_selesai' => 'required|date_format:H:i|after:override_jam_mulai',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'disetujui') {
            return redirect()->back()->withErrors(['Status Error' => 'Hanya peminjaman berstatus disetujui yang dapat di-override.']);
        }

        // Re-check Collision - Peminjaman
        $isConflict = Peminjaman::where('ruangan_id', $request->override_ruangan_id)
            ->where('tanggal_pinjam', $request->override_tanggal_pinjam)
            ->where('status', 'disetujui')
            ->where('id', '!=', $peminjaman->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->override_jam_selesai)
                    ->where('jam_selesai', '>', $request->override_jam_mulai);
            })
            ->first();

        if ($isConflict) {
            $range = \Carbon\Carbon::parse($isConflict->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($isConflict->jam_selesai)->format('H:i');
            $tujuanStr = $isConflict->tujuan ? " ('{$isConflict->tujuan}')" : "";
            return redirect()->back()->withErrors(['Bentrok' => "Gagal mengubah jadwal! Rentang waktu menabrak peminjaman lain$tujuanStr pada jam {$range}."]);
        }

        // Re-check Collision - Jadwal Internal (Akademik)
        $dayOfWeek = \Carbon\Carbon::parse($request->override_tanggal_pinjam)->format('N');
        $isInternalConflict = \Modules\EOffice\Models\MrJadwalInternal::where('ruangan_id', $request->override_ruangan_id)
            ->where(function ($query) use ($request, $dayOfWeek) {
                $query->where(function ($q) use ($dayOfWeek, $request) {
                    $q->where('tipe_jadwal', 'rutin')
                        ->where('hari', $dayOfWeek)
                        ->where(function ($tq) use ($request) {
                            $tq->whereNull('tgl_mulai_efektif')
                                ->orWhere('tgl_mulai_efektif', '<=', $request->override_tanggal_pinjam);
                        })
                        ->where(function ($tq) use ($request) {
                            $tq->whereNull('tgl_selesai_efektif')
                                ->orWhere('tgl_selesai_efektif', '>=', $request->override_tanggal_pinjam);
                        });
                })->orWhere(function ($q) use ($request) {
                    $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $request->override_tanggal_pinjam);
                });
            })
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->override_jam_selesai)
                    ->where('jam_selesai', '>', $request->override_jam_mulai);
            })
            ->first();

        if ($isInternalConflict) {
            $range = \Carbon\Carbon::parse($isInternalConflict->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($isInternalConflict->jam_selesai)->format('H:i');
            return redirect()->back()->withErrors(['Sistem Internal' => "Gagal mengubah jadwal! Rentang waktu menabrak Jadwal Akademik: {$isInternalConflict->keterangan} pada jam {$range}."]);
        }

        $peminjaman->update([
            'ruangan_id' => $request->override_ruangan_id,
            'tanggal_pinjam' => $request->override_tanggal_pinjam,
            'jam_mulai' => $request->override_jam_mulai,
            'jam_selesai' => $request->override_jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Override sukses! Waktu dan Ruangan peminjaman tersebut berhasil diubah.');
    }

    /**
     * Mengecek bentrok secara asynchronous via AJAX/Fetch
     * Mencegah hit lemot ke database dengan memberikan API endpoint spesifik
     */
    public function checkCollision(Request $request)
    {
        $ruanganId = $request->ruangan_id;
        $tanggal = $request->tanggal_pinjam;
        $jamMulai = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;
        $excludeId = $request->exclude_id; // ID Peminjaman yang sedang diedit (biar tidak bentrok dengan diri sendiri)

        if (!$ruanganId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['conflict' => false]);
        }

        // 1. Cek bentrok peminjaman lain
        $isConflict = Peminjaman::where('ruangan_id', $ruanganId)
            ->where('tanggal_pinjam', $tanggal)
            ->where('status', 'disetujui')
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })->first();

        if ($isConflict) {
            $range = \Carbon\Carbon::parse($isConflict->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($isConflict->jam_selesai)->format('H:i');
            return response()->json([
                'conflict' => true,
                'message' => "Ruangan terisi pihak lain pada jam {$range}."
            ]);
        }

        // 2. Cek Jadwal Internal Akademik
        $dayOfWeek = \Carbon\Carbon::parse($tanggal)->format('N');
        $isInternalConflict = \Modules\EOffice\Models\MrJadwalInternal::where('ruangan_id', $ruanganId)
            ->where(function ($query) use ($tanggal, $dayOfWeek) {
                $query->where(function ($q) use ($dayOfWeek, $tanggal) {
                    $q->where('tipe_jadwal', 'rutin')
                        ->where('hari', $dayOfWeek)
                        ->where(function ($tq) use ($tanggal) {
                            $tq->whereNull('tgl_mulai_efektif')
                                ->orWhere('tgl_mulai_efektif', '<=', $tanggal);
                        })
                        ->where(function ($tq) use ($tanggal) {
                            $tq->whereNull('tgl_selesai_efektif')
                                ->orWhere('tgl_selesai_efektif', '>=', $tanggal);
                        });
                })->orWhere(function ($q) use ($tanggal) {
                    $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $tanggal);
                });
            })
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->first();

        if ($isInternalConflict) {
            $range = \Carbon\Carbon::parse($isInternalConflict->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($isInternalConflict->jam_selesai)->format('H:i');
            return response()->json([
                'conflict' => true,
                'message' => "Menabrak Jadwal Akademik: {$isInternalConflict->keterangan} (Jam {$range})."
            ]);
        }

        return response()->json(['conflict' => false]);
    }
}
