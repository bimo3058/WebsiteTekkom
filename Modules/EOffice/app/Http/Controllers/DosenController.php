<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\KerjaPraktik;
use Modules\EOffice\Models\KpPenilaian;

class DosenController extends Controller
{
    public function dashboard()
    {
        $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', auth()->id())->first();

        // Ambil data KP + nama mahasiswa dari tabel global users (READ ONLY, tidak mengubah apapun)
        $bimbinganQuery = KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama_mahasiswa',
            'u.email as email_mahasiswa',
            'ud.name as nama_dosen',
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('users as ud', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'ud.id');

        if ($kpDosen) {
            $bimbinganQuery->where('eo_kerja_praktik.dosen_pembimbing_id', $kpDosen->id);
        } else {
            $bimbinganQuery->whereNull('eo_kerja_praktik.id'); // Kosongkan jika dosen belum terdaftar
        }

        $bimbingan = $bimbinganQuery->orderBy('eo_kerja_praktik.created_at', 'desc')->get();

        $stats = [
            'total_bimbingan' => $bimbingan->count(),
            'menunggu_acc' => $bimbingan->where('status_kp', 'pending')->count(),
            'sedang_kp' => $bimbingan->where('status_kp', 'active')->count(),
            'selesai_kp' => $bimbingan->where('status_kp', 'completed')->count(),
        ];

        return view('eoffice::dosen.dashboard', compact('bimbingan', 'stats'));
    }

    /**
     * Halaman Daftar Bimbingan Mahasiswa
     */
    public function bimbingan()
    {
        $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', auth()->id())->first();

        $query = KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama_mahasiswa',
            'm.nim as nim_user',
            'u.email as email_mahasiswa',
            'ud.name as nama_dosen',
            's.id as seminar_id',
            's.status_validasi_syarat as status_seminar'
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('users as ud', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'ud.id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
            ->leftJoin('eo_kp_seminar as s', 'eo_kerja_praktik.id', '=', 's.kp_id');

        if ($kpDosen) {
            $query->where('eo_kerja_praktik.dosen_pembimbing_id', $kpDosen->id);
        } else {
            $query->whereNull('eo_kerja_praktik.id');
        }

        $bimbingan = $query->with(['nilaiDetail.komponen'])->orderBy('eo_kerja_praktik.created_at', 'desc')->get();

        $mahasiswas = $bimbingan->map(function ($kp) {
            $sudahDaftarSeminar = !is_null($kp->seminar_id);
            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama_mahasiswa ?? 'Unknown',
                'nim' => $kp->nim ?? $kp->nim_user ?? '-',
                'judul_kp' => $kp->judul_kp ?? 'Belum ada judul',
                'tempat_kp' => $kp->instansi_kp ?? 'Belum ada tempat',
                'tanggal_mulai' => $kp->tanggal_mulai
                    ? \Carbon\Carbon::parse($kp->tanggal_mulai)->translatedFormat('d M Y')
                    : null,
                'tanggal_selesai' => $kp->tanggal_selesai
                    ? \Carbon\Carbon::parse($kp->tanggal_selesai)->translatedFormat('d M Y')
                    : null,
                'status_kp' => $kp->status_kp,
                'status_dokumen' => 'Lengkap',
                'nilai_seminar' => null, // Legacy deprecated
                'nilai_laporan' => null, // Legacy deprecated
                'sudah_daftar_seminar' => $sudahDaftarSeminar,
                'status_seminar' => $kp->status_seminar,
                'progress' => $kp->status_kp === 'completed' ? 100
                    : ($kp->status_kp === 'active' ? 60 : 20),
            ];
        });



        return view('eoffice::dosen.bimbingan', compact('mahasiswas'));
    }

    /**
     * Halaman Penilaian Mahasiswa (Dosen) - Menampilkan tabel khusus penilaian
     */
    public function penilaianIndex()
    {
        $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', auth()->id())->first();

        $query = KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama_mahasiswa',
            'm.nim as nim_user',
            'u.email as email_mahasiswa',
            'ud.name as nama_dosen',
            'p.nilai_akhir',
            's.id as seminar_id',
            's.status_validasi_syarat as status_seminar'
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('users as ud', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'ud.id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
            ->leftJoin('eo_kp_seminar as s', 'eo_kerja_praktik.id', '=', 's.kp_id');

        if ($kpDosen) {
            $query->where('eo_kerja_praktik.dosen_pembimbing_id', $kpDosen->id);
            // Optional: Filter hanya mahasiswa yang setidaknya sudah aktif KP atau siap dinilai
            $query->whereIn('eo_kerja_praktik.status_kp', ['active', 'completed']);
        } else {
            $query->whereNull('eo_kerja_praktik.id');
        }

        $bimbingan = $query->orderBy('eo_kerja_praktik.created_at', 'desc')->get();

        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->get();

        $mahasiswas = $bimbingan->map(function ($kp) use ($allPeriodes) {
            $sudahDaftarSeminar = !is_null($kp->seminar_id);

            // Bind Rubrik Items for Dosen
            $rubrikItems = [];
            $matchedPeriodeId = $kp->periode_id; // Using our custom accessor!
            if ($matchedPeriodeId) {
                $periode = $allPeriodes->firstWhere('id', $matchedPeriodeId);
                if ($periode && $periode->komponenNilai) {
                    $dosenRubrik = $periode->komponenNilai->where('role_penilai', 'dosen_pembimbing');

                    // Fetch existing scores if evaluated
                    $details = \Modules\EOffice\Models\KpNilaiDetail::where('kp_id', $kp->id)->get();

                    $rubrikItems = $dosenRubrik->map(function ($r) use ($details) {
                        $saved = $details->firstWhere('komponen_id', $r->id);
                        return [
                            'id' => $r->id,
                            'deskripsi' => $r->deskripsi,
                            'bobot' => $r->bobot,
                            'nilai_angka' => $saved ? $saved->nilai_angka : null
                        ];
                    })->values()->toArray();
                }
            }

            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama_mahasiswa ?? 'Unknown',
                'nim' => $kp->nim ?? $kp->nim_user ?? '-',
                'judul_kp' => $kp->judul_kp ?? 'Belum ada judul',
                'tempat_kp' => $kp->instansi_kp ?? 'Belum ada tempat',
                'tanggal_mulai' => $kp->tanggal_mulai
                    ? \Carbon\Carbon::parse($kp->tanggal_mulai)->translatedFormat('d M Y')
                    : null,
                'tanggal_selesai' => $kp->tanggal_selesai
                    ? \Carbon\Carbon::parse($kp->tanggal_selesai)->translatedFormat('d M Y')
                    : null,
                'status_kp' => $kp->status_kp,
                'status_dokumen' => 'Lengkap',
                'nilai_akhir' => $kp->nilai_akhir,
                'sudah_daftar_seminar' => $sudahDaftarSeminar,
                'status_seminar' => $kp->status_seminar,
                'progress' => $kp->status_kp === 'completed' ? 100
                    : ($kp->status_kp === 'active' ? 60 : 20),
                'rubrik_items' => $rubrikItems,
            ];
        });

        return view('eoffice::dosen.penilaian_index', compact('mahasiswas'));
    }

    /**
     * Menampilkan detail pengajuan dan progres KP Mahasiswa
     */
    public function show($id)
    {
        $kp = KerjaPraktik::findOrFail($id);
        return view('eoffice::dosen.show', compact('kp'));
    }

    /**
     * Dosen menyetujui Topik dan Tempat KP (Fase PRA KP)
     */
    public function approvePraKp(Request $request, $id)
    {
        $kp = KerjaPraktik::findOrFail($id);

        // Sesuai Activity Diagram: Dosen menyetujui Topik dan Tempat KP
        $kp->status_kp = 'active'; // Berubah dari pending (Pra KP) menjadi active (Saat KP)
        $kp->save();

        return redirect()->back()->with('success', 'Topik dan Tempat KP berhasil disetujui. Mahasiswa sekarang berada di fase SAAT KP.');
    }

    /**
     * Dosen menyetujui dokumen (Laporan / Makalah)
     */
    public function approveDokumen($id, $dokumenId)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::where('kp_id', $id)->findOrFail($dokumenId);
        $dokumen->approval_status = 'approved';
        $dokumen->save();

        return back()->with('success', 'Dokumen ' . $dokumen->jenis_dokumen . ' berhasil disetujui (ACC).');
    }

    /**
     * Dosen menolak / meminta revisi dokumen (Laporan / Makalah)
     */
    public function rejectDokumen(Request $request, $id, $dokumenId)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::where('kp_id', $id)->findOrFail($dokumenId);
        $dokumen->approval_status = 'rejected';
        $dokumen->save();

        return back()->with('error', 'Dokumen ' . $dokumen->jenis_dokumen . ' ditolak. Mahasiswa harus merevisi dan mengunggah ulang.');
    }

    /**
     * Halaman Validasi & Approval Berkas — menampilkan semua dokumen KP
     * dari seluruh mahasiswa bimbingan (Laporan, Makalah)
     * Hanya READ dari tabel global users, tidak mengubah apapun di sana.
     */
    public function validasiBerkas()
    {
        $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', auth()->id())->first();

        $templates = \Modules\EOffice\Models\TemplateDokumenKP::all()->groupBy('periode_id');

        $query = \Modules\EOffice\Models\KpDokumen::select(
            'eo_kp_dokumen.*',
            'kp.nim',
            'kp.judul_kp',
            'kp.instansi_kp',
            'kp.status_kp',
            'u.name as nama_mahasiswa',
        )
            ->join('eo_kerja_praktik as kp', 'eo_kp_dokumen.kp_id', '=', 'kp.id')
            ->leftJoin('eo_kp_mahasiswa as m', 'kp.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id');

        if ($kpDosen) {
            $query->where('kp.dosen_pembimbing_id', $kpDosen->id);
        } else {
            $query->whereNull('kp.id'); // Kosongkan jika dosen belum terdaftar
        }

        $allDokumens = $query->orderByRaw("CASE eo_kp_dokumen.approval_status WHEN 'pending' THEN 0 ELSE 1 END")
            ->orderBy('eo_kp_dokumen.tanggal_upload', 'desc')
            ->with(['kerjaPraktik'])
            ->get();

        $dokumens = $allDokumens->filter(function ($d) use ($templates) {
            $kp = $d->kerjaPraktik;
            if (!$kp)
                return true;

            $periodTemplates = $templates[$kp->periode_id] ?? collect();
            $t = $periodTemplates->firstWhere('title', $d->jenis_dokumen);

            if ($t) {
                return in_array($t->approver_role, ['dosen_pembimbing', 'keduanya']);
            }

            // Legacy fallback defaults
            return in_array($d->jenis_dokumen, ['Laporan', 'Makalah']);
        })->values();

        // Statistik ringkas untuk header halaman
        $stats = [
            'total' => $dokumens->count(),
            'pending' => $dokumens->where('approval_status', 'pending')->count(),
            'approved' => $dokumens->where('approval_status', 'approved')->count(),
            'rejected' => $dokumens->where('approval_status', 'rejected')->count(),
        ];

        return view('eoffice::dosen.validasi_berkas', compact('dokumens', 'stats'));
    }

    /**
     * Menampilkan form penilaian KP untuk mahasiswa tertentu
     */
    public function showPenilaian($id)
    {
        $kp = KerjaPraktik::select('eo_kerja_praktik.*', 'u.name as nama_mahasiswa')
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->with(['penilaian', 'nilaiDetail'])
            ->findOrFail($id);

        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->get();
        $matchedPeriode = $allPeriodes->first(function ($p) use ($kp) {
            if (!$kp->created_at || !$p->pra_kp_mulai || !$p->pra_kp_akhir)
                return false;
            return $kp->created_at->format('Y-m-d') >= $p->pra_kp_mulai->format('Y-m-d')
                && $kp->created_at->format('Y-m-d') <= $p->pra_kp_akhir->format('Y-m-d');
        });

        // Temporarily inject the resolved periode into the model object so blade template can read $kp->periode->komponenNilai
        $kp->setRelation('periode', $matchedPeriode);

        return view('eoffice::dosen.penilaian', compact('kp'));
    }

    /**
     * Menyimpan / memperbarui nilai KP mahasiswa ke Supabase (tabel eo_kp_penilaian)
     */
    public function storePenilaian(Request $request, $id)
    {
        $kp = KerjaPraktik::findOrFail($id);

        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->get();
        $matchedPeriode = $allPeriodes->first(function ($p) use ($kp) {
            if (!$kp->created_at || !$p->pra_kp_mulai || !$p->pra_kp_akhir)
                return false;
            return $kp->created_at->format('Y-m-d') >= $p->pra_kp_mulai->format('Y-m-d')
                && $kp->created_at->format('Y-m-d') <= $p->pra_kp_akhir->format('Y-m-d');
        });

        $komponenDosen = null;
        if ($matchedPeriode && $matchedPeriode->komponenNilai) {
            $komponenDosen = $matchedPeriode->komponenNilai->where('role_penilai', 'dosen_pembimbing');
        }

        if ($komponenDosen && $komponenDosen->isNotEmpty()) {

            $rules = [];
            foreach ($komponenDosen as $komp) {
                $rules['nilai_' . $komp->id] = 'required|numeric|min:0|max:100';
            }
            $validated = $request->validate($rules);

            // 1. Simpan kepingan nilai ke dalam Detail Penilaian
            foreach ($komponenDosen as $komp) {
                \Modules\EOffice\Models\KpNilaiDetail::updateOrCreate(
                    ['kp_id' => $kp->id, 'komponen_id' => $komp->id],
                    ['nilai_angka' => $validated['nilai_' . $komp->id]]
                );
            }

            // 2. Kalkulasi ulang Total Nilai Akhir dari KESELURUHAN komponen (baik yang diisi Dosen & Koord)
            $allComponents = $matchedPeriode->komponenNilai;
            $allDetails = \Modules\EOffice\Models\KpNilaiDetail::where('kp_id', $kp->id)->get();

            $totalAccumulated = 0;
            foreach ($allComponents as $comp) {
                $detail = $allDetails->firstWhere('komponen_id', $comp->id);
                $angka = $detail ? (float) $detail->nilai_angka : 0;
                $bobotPersen = ($comp->bobot / 100);
                $totalAccumulated += ($angka * $bobotPersen);
            }

            // 3. Simpan Kalkulasi Final sebagai Snapshot ke tabel master eo_kp_penilaian
            \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                ['kp_id' => $kp->id],
                ['nilai_akhir' => round($totalAccumulated, 2)]
            );

        } else {
            // Fallback for Legacy without Rubric Configuration
            $validated = $request->validate([
                'nilai_seminar_pembimbing' => 'required|numeric|min:0|max:100',
            ]);

            $existing = \Modules\EOffice\Models\KpPenilaian::where('kp_id', $kp->id)->first();

            $nilai_akhir = null;
            if ($existing && $kp->nilai_lapangan !== null) {
                $nilai_akhir = round(
                    ($kp->nilai_lapangan * 0.6) + ($validated['nilai_seminar_pembimbing'] * 0.4),
                    2
                );
            }

            if ($nilai_akhir !== null) {
                \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                    ['kp_id' => $kp->id],
                    [
                        'nilai_akhir' => $nilai_akhir,
                    ]
                );
            }
        }

        // Update status KP menjadi completed setelah dosen memberi nilai
        $kp->status_kp = 'completed';
        $kp->save();

        return back()->with('success', 'Nilai Penilaian berhasil disimpan!');
    }

    /**
     * Halaman Penilaian Seminar (Dosen)
     */
    public function penilaianSeminar()
    {
        $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', auth()->id())->first();

        $query = KerjaPraktik::select(
            'eo_kerja_praktik.id as kp_id',
            'u.name as nama_mahasiswa',
            'm.nim',
            's.id as seminar_id',
            's.tanggal_seminar',
            's.waktu_seminar',
            's.ruangan',
            's.status_validasi_dosen'
        )
            ->join('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->join('users as u', 'm.user_id', '=', 'u.id')
            ->join('eo_kp_seminar as s', 'eo_kerja_praktik.id', '=', 's.kp_id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id');

        if ($kpDosen) {
            $query->where('eo_kerja_praktik.dosen_pembimbing_id', $kpDosen->id);
        } else {
            $query->whereNull('eo_kerja_praktik.id');
        }

        $seminars = $query->with(['nilaiDetail.komponen'])->orderBy('s.created_at', 'desc')->get();

        return view('eoffice::dosen.penilaian_seminar', compact('seminars'));
    }

    /**
     * Dosen approve jadwal seminar
     */
    public function approveSeminar(Request $request, $id)
    {
        $seminar = \Modules\EOffice\Models\KpSeminar::where('kp_id', $id)->firstOrFail();
        $seminar->status_validasi_dosen = 'approved';
        $seminar->save();

        return back()->with('success', 'Seminar berhasil disetujui.');
    }

    /**
     * Dosen reject jadwal seminar
     */
    public function rejectSeminar(Request $request, $id)
    {
        $seminar = \Modules\EOffice\Models\KpSeminar::where('kp_id', $id)->firstOrFail();
        $seminar->status_validasi_dosen = 'rejected';
        $seminar->save();

        return back()->with('error', 'Seminar ditolak.');
    }
}
