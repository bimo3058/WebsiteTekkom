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
            'p.nilai_seminar_pembimbing',
            'p.nilai_lapangan',
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

        $bimbingan = $query->orderBy('eo_kerja_praktik.created_at', 'desc')->get();

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
                'nilai_seminar' => $kp->nilai_seminar_pembimbing,
                'nilai_laporan' => $kp->nilai_lapangan,
                'sudah_daftar_seminar' => $sudahDaftarSeminar,
                'status_seminar' => $kp->status_seminar,
                'progress' => $kp->status_kp === 'completed' ? 100
                    : ($kp->status_kp === 'active' ? 60 : 20),
            ];
        });



        return view('eoffice::dosen.bimbingan', compact('mahasiswas'));
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
        $dokumen->status_validasi = 'approved';
        $dokumen->save();

        return back()->with('success', 'Dokumen ' . $dokumen->jenis_dokumen . ' berhasil disetujui (ACC).');
    }

    /**
     * Dosen menolak / meminta revisi dokumen (Laporan / Makalah)
     */
    public function rejectDokumen(Request $request, $id, $dokumenId)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::where('kp_id', $id)->findOrFail($dokumenId);
        $dokumen->status_validasi = 'rejected';
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

        // Ambil semua dokumen (Laporan & Makalah) beserta nama mahasiswa dari global users
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
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->join('eo_kp_template as t', function ($join) {
                $join->on('eo_kp_dokumen.jenis_dokumen', '=', 't.title')
                    ->on('kp.periode_id', '=', 't.periode_id');
            })
            ->whereIn('t.approver_role', ['dosen_pembimbing', 'keduanya']);

        if ($kpDosen) {
            $query->where('kp.dosen_pembimbing_id', $kpDosen->id);
        } else {
            $query->whereNull('kp.id'); // Kosongkan jika dosen belum terdaftar
        }

        $dokumens = $query->orderByRaw("CASE eo_kp_dokumen.status_validasi WHEN 'pending' THEN 0 ELSE 1 END")
            ->orderBy('eo_kp_dokumen.tanggal_upload', 'desc')
            ->get();

        // Statistik ringkas untuk header halaman
        $stats = [
            'total' => $dokumens->count(),
            'pending' => $dokumens->where('status_validasi', 'pending')->count(),
            'approved' => $dokumens->where('status_validasi', 'approved')->count(),
            'rejected' => $dokumens->where('status_validasi', 'rejected')->count(),
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
            // Dynamic Grading Based on Components
            $rules = [];
            foreach ($komponenDosen as $komp) {
                $rules['nilai_' . $komp->id] = 'required|numeric|min:0|max:100';
            }
            $validated = $request->validate($rules);

            foreach ($komponenDosen as $komp) {
                \Modules\EOffice\Models\KpNilaiDetail::updateOrCreate(
                    ['kp_id' => $kp->id, 'komponen_id' => $komp->id],
                    ['nilai_angka' => $validated['nilai_' . $komp->id]]
                );
            }

            // Logic to calculate `nilai_akhir` from the pivot if Koordinator also filled theirs
            $allComponents = $matchedPeriode->komponenNilai;
            $totalAllWeight = $allComponents->sum('bobot');
            // Let Mahasiswa or integration instructions handle the complex weighted sum for total Nilai Akhir
            // The Dosen component storage is complete.

        } else {
            // Fallback for Legacy without Rubric Configuration
            $validated = $request->validate([
                'nilai_seminar_pembimbing' => 'required|numeric|min:0|max:100',
            ]);

            $existing = \Modules\EOffice\Models\KpPenilaian::where('kp_id', $kp->id)->first();
            if ($existing && $existing->nilai_lapangan !== null) {
                $validated['nilai_akhir'] = round(
                    ($existing->nilai_lapangan * 0.6) + ($validated['nilai_seminar_pembimbing'] * 0.4),
                    2
                );
            }

            \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                ['kp_id' => $kp->id],
                $validated
            );
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
            's.status_validasi_dosen',
            'p.nilai_seminar_pembimbing'
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

        $seminars = $query->orderBy('s.created_at', 'desc')->get();

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
