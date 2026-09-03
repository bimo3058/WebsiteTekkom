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
}
