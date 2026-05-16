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
        // Ambil data KP + nama mahasiswa dari tabel global users (READ ONLY, tidak mengubah apapun)
        $bimbingan = KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama_mahasiswa',
            'u.email as email_mahasiswa',
            'ud.name as nama_dosen',
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('users as ud', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'ud.id')
            ->orderBy('eo_kerja_praktik.created_at', 'desc')
            ->get();

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
        $bimbingan = KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama_mahasiswa',
            'm.nim as nim_user',
            'u.email as email_mahasiswa',
            'ud.name as nama_dosen',
            'p.nilai_seminar_pembimbing'
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('users as ud', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'ud.id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
            // Dummy limit for current user ID context if needed, but keeping global for now
            // ->where('eo_kerja_praktik.dosen_pembimbing_id', auth()->id())
            ->orderBy('eo_kerja_praktik.created_at', 'desc')
            ->get();
            
        // Map data dummy buat UI supaya lengkap
        $mahasiswas = $bimbingan->map(function ($kp) {
            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama_mahasiswa ?? 'Unknown',
                'nim' => $kp->nim ?? $kp->nim_user ?? '2100018000',
                'prodi' => 'Informatika',
                'judul_kp' => $kp->rencana_judul ?? 'Belum ada judul',
                'tempat_kp' => $kp->rencana_tempat ?? 'Belum ada tempat',
                'durasi_kp' => 'Feb 2026 - Mei 2026',
                'status_kp' => $kp->status_kp,
                'status_dokumen' => 'Lengkap',
                'nilai_seminar' => $kp->nilai_seminar_pembimbing,
                'nilai_laporan' => null, // Column doesn't exist yet in DB
                'progress' => rand(40, 100), // dummy progress
            ];
        });

        // Add dummy data jika kosong
        if ($mahasiswas->isEmpty()) {
            $mahasiswas = collect([
                (object) [
                    'id' => 101,
                    'nama' => 'Ahmad Fathanah',
                    'nim' => '2100018112',
                    'prodi' => 'Informatika',
                    'judul_kp' => 'Pengembangan Sistem Microservice Backend',
                    'tempat_kp' => 'PT GoTo Gojek Tokopedia',
                    'durasi_kp' => '1 Juni 2026 - 31 Agustus 2026',
                    'status_kp' => 'active',
                    'status_dokumen' => 'Lengkap',
                    'nilai_seminar' => null,
                    'progress' => 60,
                ],
                (object) [
                    'id' => 102,
                    'nama' => 'Siti Nurhaliza',
                    'nim' => '2100018199',
                    'prodi' => 'Informatika',
                    'judul_kp' => 'Analisis Jaringan Fiber Optic',
                    'tempat_kp' => 'PT Telkom Indonesia',
                    'durasi_kp' => '15 Mei 2026 - 15 Agustus 2026',
                    'status_kp' => 'pending',
                    'status_dokumen' => 'Kurang',
                    'nilai_seminar' => null,
                    'progress' => 30,
                ],
                (object) [
                    'id' => 103,
                    'nama' => 'Bima Sakti',
                    'nim' => '2100018155',
                    'prodi' => 'Informatika',
                    'judul_kp' => 'Implementasi Fraud Detection System',
                    'tempat_kp' => 'Bank Mandiri IT Group',
                    'durasi_kp' => '1 Februari 2026 - 30 April 2026',
                    'status_kp' => 'completed',
                    'status_dokumen' => 'Lengkap',
                    'nilai_seminar' => 85,
                    'progress' => 100,
                ]
            ]);
        }

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
        // Ambil semua dokumen (Laporan & Makalah) beserta nama mahasiswa dari global users
        $dokumens = \Modules\EOffice\Models\KpDokumen::select(
            'eo_kp_dokumen.*',
            'kp.nim',
            'kp.rencana_judul',
            'kp.rencana_tempat',
            'kp.status_kp',
            'u.name as nama_mahasiswa',
        )
            ->join('eo_kerja_praktik as kp', 'eo_kp_dokumen.kp_id', '=', 'kp.id')
            ->leftJoin('eo_kp_mahasiswa as m', 'kp.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->whereIn('eo_kp_dokumen.jenis_dokumen', ['Laporan', 'Makalah'])
            ->orderByRaw("CASE eo_kp_dokumen.status_validasi WHEN 'pending' THEN 0 ELSE 1 END")
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
            ->with('penilaian')
            ->findOrFail($id);

        return view('eoffice::dosen.penilaian', compact('kp'));
    }

    /**
     * Menyimpan / memperbarui nilai KP mahasiswa ke Supabase (tabel eo_kp_penilaian)
     */
    public function storePenilaian(Request $request, $id)
    {
        $kp = KerjaPraktik::findOrFail($id);

        $validated = $request->validate([
            'nilai_seminar_pembimbing' => 'required|numeric|min:0|max:100',
        ]);

        // Hitung nilai_akhir HANYA jika nilai_lapangan sudah diisi oleh Koordinator
        $existing = KpPenilaian::where('kp_id', $kp->id)->first();
        if ($existing && $existing->nilai_lapangan !== null) {
            $validated['nilai_akhir'] = round(
                ($existing->nilai_lapangan * 0.6) + ($validated['nilai_seminar_pembimbing'] * 0.4),
                2
            );
        }

        // updateOrCreate: update jika sudah pernah dinilai, create jika belum
        KpPenilaian::updateOrCreate(
            ['kp_id' => $kp->id],
            $validated
        );

        // Update status KP menjadi completed setelah dosen memberi nilai
        $kp->status_kp = 'completed';
        $kp->save();

        return redirect()->route('eoffice.kp.dosen.bimbingan.show', $kp->id)
            ->with('success', 'Nilai Seminar berhasil disimpan!');
    }
}
