<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Exports\PeminjamanExport;

class PersetujuanController extends Controller
{
    public function index(Request $request)
    {
        // System Event: Auto-kill expired dangling pending requests
        Peminjaman::autoExpirePending();

        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $query = Peminjaman::with(['user.student', 'user.lecturer', 'user.roles', 'creator.roles', 'ruangan', 'replacesPeminjaman.ruangan']);

        // 1. Filter Status Pengajuan
        if ($request->filled('status')) {
            $reqStatus = strtolower(trim((string) $request->status));
            if ($reqStatus === 'menunggu') {
                $query->where('status', 'menunggu');
            } elseif ($reqStatus === 'disetujui') {
                $query->where('status', 'disetujui')
                    ->whereRaw("(tanggal_pinjam > ? OR (tanggal_pinjam = ? AND jam_selesai > ?))", [$date, $date, $time]);
            } else {
                $query->where('status', $reqStatus);
            }
        } else {
            // Default: Gabungan antrean 'menunggu' + jadwal 'disetujui' aktif (belum kedaluwarsa)
            $query->where(function ($q) use ($date, $time) {
                $q->where('status', 'menunggu')
                    ->orWhere(function ($q2) use ($date, $time) {
                        $q2->where('status', 'disetujui')
                            ->whereRaw("(tanggal_pinjam > ? OR (tanggal_pinjam = ? AND jam_selesai > ?))", [$date, $date, $time]);
                    });
            });
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->where('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal_pinjam', '<=', $request->end_date);
        }

        // 3. Filter Khusus Bentrok (Only Conflict)
        if ($request->boolean('only_conflict')) {
            $query->where('status', 'menunggu')
                ->whereExists(function ($sub) {
                    $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('eo_mr_peminjamans as p2')
                        ->whereColumn('p2.ruangan_id', 'eo_mr_peminjamans.ruangan_id')
                        ->whereColumn('p2.tanggal_pinjam', 'eo_mr_peminjamans.tanggal_pinjam')
                        ->where('p2.status', 'menunggu')
                        ->whereColumn('p2.id', '!=', 'eo_mr_peminjamans.id')
                        ->whereNull('p2.deleted_at')
                        ->whereRaw('p2.jam_mulai < eo_mr_peminjamans.jam_selesai')
                        ->whereRaw('p2.jam_selesai > eo_mr_peminjamans.jam_mulai');
                });
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('source')) {
            $reqSource = strtolower(trim((string) $request->source));
            if ($reqSource === 'umum') {
                $query->whereNull('source');
            } elseif (in_array($reqSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
                $query->where(function ($sq) {
                    $sq->whereIn('source', ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])
                        ->orWhere('tujuan', 'like', '%Seminar KP%')
                        ->orWhere('tujuan', 'like', '%Kerja Praktik%');
                });
            } elseif ($reqSource === 'capstone') {
                $query->where(function ($sq) {
                    $sq->where('source', 'capstone')
                        ->orWhere('tujuan', 'like', '[CAPSTONE%');
                });
            } else {
                $query->where('source', $request->source);
            }
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(external_id) LIKE ?', ["%{$search}%"])
                        ->orWhereHas('student', function ($ssq) use ($search) {
                            $ssq->whereRaw('LOWER(student_number) LIKE ?', ["%{$search}%"]);
                        })
                        ->orWhereHas('lecturer', function ($lsq) use ($search) {
                            $lsq->whereRaw('LOWER(employee_number) LIKE ?', ["%{$search}%"]);
                        });
                })->orWhereHas('ruangan', function ($sq) use ($search) {
                    $sq->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
                });
            });
        }

        $peminjamans = $query->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('ruangan_id', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->orderByRaw("CASE 
                WHEN source IN ('capstone', 'praktikum') OR tujuan LIKE '[CAPSTONE%' OR tujuan LIKE '%Praktikum%' THEN 1
                WHEN source IN ('kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik') OR tujuan LIKE '%Seminar KP%' OR tujuan LIKE '%Kerja Praktik%' THEN 2
                ELSE 3
            END ASC")
            ->paginate((int) $request->input('per_page', 10))->withQueryString();

        $ruangans = Ruangan::orderBy('nama', 'asc')->get();

        return view('eoffice::manajemen-ruangan.admin.persetujuan.index', compact('peminjamans', 'ruangans'));
    }

    public function riwayat(Request $request)
    {
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $query = Peminjaman::withTrashed()
            ->with(['user.student', 'user.lecturer', 'user.roles', 'creator.roles', 'ruangan', 'replacesPeminjaman.ruangan'])
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

        if ($request->filled('source')) {
            $reqSource = strtolower(trim((string) $request->source));
            if ($reqSource === 'umum') {
                $query->whereNull('source');
            } elseif (in_array($reqSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
                $query->where(function ($sq) {
                    $sq->whereIn('source', ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])
                        ->orWhere('tujuan', 'like', '%Seminar KP%')
                        ->orWhere('tujuan', 'like', '%Kerja Praktik%');
                });
            } elseif ($reqSource === 'capstone') {
                $query->where(function ($sq) {
                    $sq->where('source', 'capstone')
                        ->orWhere('tujuan', 'like', '[CAPSTONE%');
                });
            } else {
                $query->where('source', $request->source);
            }
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

        $sort = $request->input('sort', 'tanggal_desc');
        if ($sort === 'updated_desc') {
            $query->orderBy('updated_at', 'desc');
        } elseif ($sort === 'tanggal_asc') {
            $query->orderBy('tanggal_pinjam', 'asc')->orderBy('jam_mulai', 'asc');
        } else {
            // Default: tanggal_desc
            $query->orderBy('tanggal_pinjam', 'desc')->orderBy('jam_mulai', 'desc');
        }

        $peminjamans = $query->paginate((int) $request->input('per_page', 10))->withQueryString();

        $ruangans = Ruangan::orderBy('nama', 'asc')->get();

        return view('eoffice::manajemen-ruangan.admin.persetujuan.riwayat', compact('peminjamans', 'ruangans'));
    }

    private function getExportQuery(Request $request)
    {
        $now = now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $query = Peminjaman::withTrashed()
            ->with(['user.student', 'user.lecturer', 'user.roles', 'creator.roles', 'ruangan', 'replacesPeminjaman.ruangan'])
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

        if ($request->filled('source')) {
            $reqSource = strtolower(trim((string) $request->source));
            if ($reqSource === 'umum') {
                $query->whereNull('source');
            } elseif (in_array($reqSource, ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])) {
                $query->where(function ($sq) {
                    $sq->whereIn('source', ['kp', 'kerja_praktik', 'kerja praktik', 'kerjapraktik'])
                        ->orWhere('tujuan', 'like', '%Seminar KP%')
                        ->orWhere('tujuan', 'like', '%Kerja Praktik%');
                });
            } elseif ($reqSource === 'capstone') {
                $query->where(function ($sq) {
                    $sq->where('source', 'capstone')
                        ->orWhere('tujuan', 'like', '[CAPSTONE%');
                });
            } else {
                $query->where('source', $request->source);
            }
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

        return $query->orderBy('tanggal_pinjam', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get();
    }

    public function exportExcel(Request $request)
    {
        $peminjamans = $this->getExportQuery($request);
        $fileName = 'Arsip_Peminjaman_' . now()->format('Ymd_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new PeminjamanExport($peminjamans),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $peminjamans = $this->getExportQuery($request);

        // Menggunakan fitur print browser (tanpa dependensi eksternal seperti dompdf)
        return view('eoffice::manajemen-ruangan.admin.persetujuan.export', [
            'peminjamans' => $peminjamans,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'isPdf' => true
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::with(['user', 'ruangan', 'replacesPeminjaman'])->findOrFail($id);

        if ($request->status == 'disetujui') {
            // Re-check Collision - Peminjaman (Race Condition Guard)
            $isConflict = Peminjaman::where('ruangan_id', $peminjaman->ruangan_id)
                ->where('tanggal_pinjam', $peminjaman->tanggal_pinjam)
                ->where('status', 'disetujui')
                ->where('id', '!=', $peminjaman->id)
                ->when($peminjaman->replaces_peminjaman_id, function ($q, $repId) {
                    $q->where('id', '!=', $repId);
                })
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
            $isInternalConflict = MrJadwalInternal::where('ruangan_id', $peminjaman->ruangan_id)
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

        $originalStatus = $peminjaman->status;
        $statusToSave = $request->status;
        $alasanPenolakan = null;

        if ($request->status == 'ditolak') {
            if ($originalStatus == 'disetujui') {
                $statusToSave = 'dibatalkan';
                $alasanPenolakan = 'Dibatalkan oleh Admin Ruang: ' . ($request->alasan_penolakan ?: 'Hak peminjaman dicabut');
            } else {
                $statusToSave = 'ditolak';
                $alasanPenolakan = 'Ditolak oleh Admin Ruang: ' . ($request->alasan_penolakan ?: 'Pengajuan tidak disetujui');
            }
        }

        DB::transaction(function () use ($peminjaman, $statusToSave, $alasanPenolakan, $originalStatus, $request) {
            $peminjaman->update([
                'status' => $statusToSave,
                'alasan_penolakan' => $alasanPenolakan,
                'waktu_approval' => now(),
            ]);

            // TWO-PHASE SWAP: Jika peminjaman ini disetujui dan merupakan jadwal pengganti (reschedule)
            if ($request->status == 'disetujui' && $peminjaman->replaces_peminjaman_id) {
                $oldBooking = Peminjaman::find($peminjaman->replaces_peminjaman_id);
                if ($oldBooking && in_array($oldBooking->status, ['disetujui', 'menunggu'])) {
                    $tglBaru = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d M Y');
                    $jamBaru = substr($peminjaman->jam_mulai, 0, 5) . '-' . substr($peminjaman->jam_selesai, 0, 5) . ' WIB';
                    $alasanBatal = "Dijadwalkan ulang oleh Admin Ruang ke tanggal {$tglBaru} ({$jamBaru}) - Pengajuan #{$peminjaman->id}";

                    $oldPrevStatus = $oldBooking->status;
                    $oldBooking->update([
                        'status' => 'dibatalkan',
                        'alasan_penolakan' => $alasanBatal,
                        'waktu_approval' => now(),
                    ]);

                    event(new \Modules\EOffice\Events\RoomBookingStatusChanged($oldBooking, 'dibatalkan', $oldPrevStatus, $alasanBatal));
                }
            }

            // AUTO-REJECT CASCADE: Jika peminjaman ini disetujui, tolak otomatis antrean 'menunggu' lain yang bertabrakan di ruangan & jam yang sama
            if ($request->status == 'disetujui') {
                $collidingPendings = Peminjaman::with(['user', 'ruangan'])->where('ruangan_id', $peminjaman->ruangan_id)
                    ->where('tanggal_pinjam', $peminjaman->tanggal_pinjam)
                    ->where('status', 'menunggu')
                    ->where('id', '!=', $peminjaman->id)
                    ->when($peminjaman->replaces_peminjaman_id, function ($q, $repId) {
                        $q->where('id', '!=', $repId);
                    })
                    ->where(function ($q) use ($peminjaman) {
                        $q->where('jam_mulai', '<', $peminjaman->jam_selesai)
                            ->where('jam_selesai', '>', $peminjaman->jam_mulai);
                    })->get();

                foreach ($collidingPendings as $cp) {
                    $autoReason = "Ditolak otomatis oleh sistem: Ruangan dialokasikan untuk peminjaman #{$peminjaman->id} ({$peminjaman->source_label}: {$peminjaman->tujuan}) yang telah disetujui Admin Ruang.";
                    $cp->update([
                        'status' => 'ditolak',
                        'alasan_penolakan' => $autoReason,
                        'waktu_approval' => now(),
                    ]);

                    // Dispatch Event perubahan status peminjaman untuk modul luar (Praktikum, KP, Capstone)
                    event(new \Modules\EOffice\Events\RoomBookingStatusChanged($cp, 'ditolak', 'menunggu', $autoReason));

                    // Notifikasi ke user pemohon yang ditolak otomatis
                    if ($cp->user) {
                        $cp->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($cp, false));
                    }
                }
            }

            // Dispatch Event perubahan status peminjaman untuk modul luar (Praktikum, KP, Capstone)
            event(new \Modules\EOffice\Events\RoomBookingStatusChanged($peminjaman, $statusToSave, $originalStatus, $alasanPenolakan));
        });

        if ($peminjaman->user) {
            // Hapus notifikasi lama untuk peminjaman ini agar user tidak bingung melihat 2 status ganda di lonceng
            $oldNotifs = $peminjaman->user->notifications()
                ->where('type', \Modules\EOffice\Notifications\PeminjamanStatusUpdated::class)
                ->get();

            foreach ($oldNotifs as $notif) {
                if (isset($notif->data['peminjaman_id']) && $notif->data['peminjaman_id'] == $peminjaman->id) {
                    $notif->delete();
                }
            }

            // Check if this was a cancellation of an already approved booking
            $isCancelByAdmin = ($request->status == 'ditolak' && $originalStatus == 'disetujui');
            $peminjaman->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($peminjaman, $isCancelByAdmin));
        }

        if ($statusToSave == 'dibatalkan') {
            $msg = 'berhasil dibatalkan (hak dicabut oleh Admin Ruang)';
        } else {
            $msg = $request->status == 'disetujui' ? 'berhasil disetujui' : 'telah ditolak';
        }
        return redirect()->back()->with('success', "Pengajuan peminjaman {$msg}.");
    }
    public function updateOverride(Request $request, $id)
    {
        $request->validate([
            'override_ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'override_tanggal_pinjam' => 'required|date',
            'override_jam_mulai' => 'required|date_format:H:i',
            'override_jam_selesai' => 'required|date_format:H:i|after:override_jam_mulai',
            'override_tujuan' => 'required|string|max:255',
        ]);

        $peminjaman = Peminjaman::with(['user', 'ruangan', 'replacesPeminjaman'])->findOrFail($id);

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
        $isInternalConflict = MrJadwalInternal::where('ruangan_id', $request->override_ruangan_id)
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
            'tujuan' => $request->override_tujuan,
        ]);

        if ($peminjaman->user) {
            $peminjaman->user->notify(new \Modules\EOffice\Notifications\PeminjamanStatusUpdated($peminjaman, false));
        }

        event(new \Modules\EOffice\Events\RoomBookingStatusChanged($peminjaman, 'disetujui', 'disetujui', 'Waktu dan Ruangan diubah oleh Admin Ruang (Override)'));

        return redirect()->back()->with('success', 'Override sukses! Waktu dan Ruangan peminjaman tersebut berhasil diubah oleh Admin Ruang.');
    }

    /**
     * Mengecek bentrok secara asynchronous via AJAX/Fetch
     * Mengumpulkan seluruh jadwal yang beririsan (Akademik, Pemeliharaan, maupun Peminjaman lain)
     */
    public function checkCollision(Request $request)
    {
        $ruanganId = $request->ruangan_id;
        $tanggal = $request->tanggal_pinjam;
        $jamMulai = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;
        $excludeId = $request->exclude_id; // ID Peminjaman yang sedang diedit (biar tidak bentrok dengan diri sendiri)

        if (!$ruanganId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['conflict' => false, 'conflicts_list' => []]);
        }

        $conflictsList = [];

        // 1. Cek bentrok dengan Jadwal Internal (Akademik & Pemeliharaan / Blokir)
        $dayOfWeek = \Carbon\Carbon::parse($tanggal)->format('N');
        $internalConflicts = MrJadwalInternal::where('ruangan_id', $ruanganId)
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
            ->orderBy('jam_mulai', 'asc')
            ->get();

        foreach ($internalConflicts as $c) {
            $range = \Carbon\Carbon::parse($c->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($c->jam_selesai)->format('H:i') . ' WIB';
            $isAkademik = ($c->kategori === 'Jadwal Akademik (Kuliah)' || !empty($c->mata_kuliah));
            $tipe = $isAkademik ? 'Akademik' : 'Pemeliharaan';

            if ($isAkademik) {
                $nama = $c->mata_kuliah ? ($c->mata_kuliah . ($c->kelas ? ' - Kelas ' . $c->kelas : '')) : ($c->keterangan ?: 'Jadwal Perkuliahan');
            } else {
                $nama = $c->keterangan ?: 'Pemeliharaan / Blokir Ruangan';
            }

            $conflictsList[] = [
                'tipe' => $tipe,
                'nama' => $nama,
                'waktu' => $range,
            ];
        }

        // 2. Cek bentrok dengan Peminjaman lain yang telah disetujui
        $peminjamanConflicts = Peminjaman::with('user')
            ->where('ruangan_id', $ruanganId)
            ->where('tanggal_pinjam', $tanggal)
            ->where('status', 'disetujui')
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        foreach ($peminjamanConflicts as $p) {
            $userPart = $p->user ? " (Oleh: {$p->user->name})" : "";
            $range = \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') . ' WIB';
            $conflictsList[] = [
                'tipe' => 'Peminjaman',
                'nama' => ($p->tujuan ?: 'Peminjaman Ruangan') . $userPart,
                'waktu' => $range,
            ];
        }

        if (count($conflictsList) > 0) {
            return response()->json([
                'conflict' => true,
                'message' => 'Terdapat jadwal beririsan di ruangan dan waktu yang dipilih.',
                'conflicts_list' => $conflictsList,
            ]);
        }

        return response()->json(['conflict' => false, 'conflicts_list' => []]);
    }
    /**
     * Soft delete arsip peminjaman (hanya superadmin)
     */
    public function destroy($id)
    {
        abort_unless(auth()->user()->hasRole('superadmin'), 403, 'Aksi ini hanya dapat dilakukan oleh Superadmin.');

        $peminjaman = Peminjaman::with(['user', 'ruangan', 'replacesPeminjaman'])->findOrFail($id);

        if ($peminjaman->berkas_pendukung) {
            app(\App\Services\SupabaseStorage::class)->delete($peminjaman->berkas_pendukung, 'eoffice');
        }

        $peminjaman->forceDelete();

        return redirect()->back()->with('success', 'Data arsip peminjaman berhasil dihapus secara permanen.');
    }

    /**
     * Get pending approval count via API (AJAX)
     */
    public function getPendingCount()
    {
        $count = Peminjaman::where('status', 'menunggu')->count();
        return response()->json(['count' => $count]);
    }
}
