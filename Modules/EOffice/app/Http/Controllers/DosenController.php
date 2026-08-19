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
            'menunggu_acc' => $bimbingan->where('status_kp', 'Pra-KP')->count(),
            'sedang_kp' => $bimbingan->where('status_kp', 'Saat KP')->count(),
            'selesai_kp' => $bimbingan->whereIn('status_kp', ['Pasca KP', 'Selesai'])->count(),
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
                'progress' => in_array($kp->status_kp, ['Selesai', 'Selesai KP']) ? 100
                    : ($kp->status_kp === 'Pasca KP' ? 80
                        : ($kp->status_kp === 'Saat KP' ? 50 : 20)),
            ];
        });



        return view('eoffice::dosen.bimbingan', compact('mahasiswas'));
    }

    /**
     * Menampilkan Workspace Bimbingan Terpadu (Master Detail)
     */
    public function showBimbinganDetail($id)
    {
        $kp = KerjaPraktik::select('eo_kerja_praktik.*', 'u.name as nama_mahasiswa', 'm.nim')
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->with(['dokumen', 'periode.komponenNilai'])
            ->findOrFail($id);

        // Filter Documents by Dosen Role
        $templates = \Modules\EOffice\Models\TemplateDokumenKP::all()->groupBy('periode_id');
        $periodTemplates = collect();
        if ($kp->periode_id && isset($templates[$kp->periode_id])) {
            $periodTemplates = $templates[$kp->periode_id];
        }

        // Keep only documents where approver_role is 'dosen_pembimbing' or 'keduanya', AND not draft
        $filteredDokumens = $kp->dokumen->filter(function ($d) use ($periodTemplates) {
            $isDraft = strtolower($d->status_validasi) === 'draft';
            if ($isDraft)
                return false;

            $t = $periodTemplates->firstWhere('title', $d->jenis_dokumen);
            if ($t) {
                return in_array($t->approver_role, ['dosen_pembimbing', 'keduanya']);
            }
            return in_array($d->jenis_dokumen, ['Laporan', 'Makalah']);
        })->values();

        // Inject the filtered relation back onto the model for the view
        $kp->setRelation('dokumen', $filteredDokumens);

        $rubrikItems = [];
        if ($kp->periode && $kp->periode->komponenNilai) {
            $dosenRubrik = $kp->periode->komponenNilai->where('role_penilai', 'dosen_pembimbing');
            $details = \Modules\EOffice\Models\KpNilaiDetail::where('kp_id', $kp->id)->get();

            $rubrikItems = $dosenRubrik->map(function ($r) use ($details) {
                $saved = $details->firstWhere('komponen_id', $r->id);
                return (object) [
                    'id' => $r->id,
                    'deskripsi' => $r->nama_komponen,
                    'bobot' => $r->bobot,
                    'nilai_angka' => $saved ? $saved->nilai_angka : null
                ];
            })->values()->toArray();
        }

        $seminar = \Modules\EOffice\Models\KpSeminar::where('kp_id', $id)->first();

        return view('eoffice::dosen.bimbingan_detail', compact('kp', 'rubrikItems', 'seminar'));
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

        // Permanent Lock Check
        if ($dokumen->approval_status === 'approved') {
            return back()->with('error', 'Gagal: Kunci Permanen aktif. Dokumen yang sudah disetujui tidak dapat ditarik kembali menjadi revisi. Silahkan hubungi Koordinator KP.');
        }

        $dokumen->approval_status = 'rejected';
        if ($request->filled('revision_note')) {
            $dokumen->revision_note = $request->input('revision_note');
        }
        $dokumen->save();

        return back()->with('error', 'Dokumen ' . $dokumen->jenis_dokumen . ' ditolak. Mahasiswa harus merevisi dan mengunggah ulang.');
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
            return back()->with('error', 'Cetakan Master Rubrik untuk periode mahasiswa ini belum diatur oleh Koordinator! Harap hubungi Koordinator KP sebelum memberikan nilai.');
        }

        // Update status KP menjadi completed setelah dosen memberi nilai
        $kp->status_kp = 'completed';
        $kp->save();

        return back()->with('success', 'Nilai Penilaian berhasil disimpan!');
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
