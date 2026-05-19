<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Modules\EOffice\Models\KerjaPraktik;

class KoordinatorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (auth()->user() && auth()->user()->email !== 'ike.pertiwi@undip.ac.id') {
                    abort(403, 'Akses Ditolak. Halaman ini khusus Koordinator KP.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Halaman Utama Dashboard Koordinator KP
     */
    public function dashboard()
    {
        // Statistik untuk dashboard Koordinator (Hanya READ)
        $totalMahasiswa = KerjaPraktik::count();
        $menungguDosen = KerjaPraktik::whereNull('dosen_pembimbing_id')->count();
        $sedangKp = KerjaPraktik::where('status_kp', 'active')->count();
        $menungguValidasi = \Modules\EOffice\Models\KpDokumen::where('status_validasi', 'pending')->count();

        $stats = [
            'total_mahasiswa' => $totalMahasiswa,
            'menunggu_dosen' => $menungguDosen,
            'sedang_kp' => $sedangKp,
            'menunggu_validasi' => $menungguValidasi,
        ];

        return view('eoffice::koordinator.dashboard', compact('stats'));
    }

    /**
     * Halaman Balancing Dosen
     */
    public function balancingDosen()
    {
        // 1. Ambil mahasiswa yang belum punya dosen
        $mahasiswas = KerjaPraktik::select(
                'eo_kerja_praktik.id',
                'eo_kerja_praktik.nim',
                'eo_kerja_praktik.rencana_judul',
                'u.name as nama_mahasiswa'
            )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->whereNull('eo_kerja_praktik.dosen_pembimbing_id')
            ->orderBy('eo_kerja_praktik.created_at', 'asc')
            ->get();

        // 2. Ambil semua dosen berdasarkan domain email @undip.ac.id (bukan students)
        $dosens = \App\Models\User::where('email', 'like', '%@undip.ac.id')
            ->where('email', 'not like', '%@students.undip.ac.id')
            ->select('id', 'name')
            ->get();

        // 3. Hitung jumlah mahasiswa bimbingan dan ambil daftar mahasiswa untuk masing-masing dosen
        foreach ($dosens as $dosen) {
            $kpDosen = \Modules\EOffice\Models\KpDosen::where('user_id', $dosen->id)->first();
            
            if ($kpDosen) {
                $assigned = KerjaPraktik::select(
                        'eo_kerja_praktik.id',
                        'eo_kerja_praktik.nim',
                        'eo_kerja_praktik.rencana_judul',
                        'u.name as nama_mahasiswa'
                    )
                    ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
                    ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
                    ->where('eo_kerja_praktik.dosen_pembimbing_id', $kpDosen->id)
                    ->get();
            } else {
                $assigned = collect();
            }
            
            $dosen->jumlah_bimbingan = $assigned->count();
            $dosen->mahasiswas = $assigned;
            // Dummy kuota maksimal (karena belum ada di tabel users/dosen)
            $dosen->kuota_maksimal = 10;
        }

        // Urutkan dosen berdasarkan jumlah bimbingan (paling sedikit di atas untuk prioritas)
        $dosens = collect($dosens)->sortBy('jumlah_bimbingan')->values();

        return view('eoffice::koordinator.balancing', compact('mahasiswas', 'dosens'));
    }

    /**
     * Proses Simpan Balancing Dosen
     */
    public function storeBalancing(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.kp_id' => 'required|exists:eo_kerja_praktik,id',
            'assignments.*.dosen_id' => 'nullable|exists:users,id',
        ]);

        $assignedCount = 0;
        foreach ($validated['assignments'] as $assign) {
            if (!empty($assign['dosen_id'])) {
                // Mapping ID global ke tabel lokal eo_kp_dosen agar tidak FK violation
                $userDosen = \App\Models\User::find($assign['dosen_id']);
                $kpDosen = \Modules\EOffice\Models\KpDosen::firstOrCreate(
                    ['user_id' => $assign['dosen_id']],
                    ['nama_lengkap' => $userDosen->name ?? 'Dosen KP', 'nip' => '-']
                );

                KerjaPraktik::where('id', $assign['kp_id'])
                    ->update(['dosen_pembimbing_id' => $kpDosen->id]);
                $assignedCount++;
            }
        }

        return redirect()->route('eoffice.kp.koordinator.balancing')
            ->with('success', "Berhasil menetapkan Dosen Pembimbing untuk $assignedCount mahasiswa!");
    }

    /**
     * Halaman Pengumuman & Timeline
     */
    public function pengumuman()
    {
        $allData = \Modules\EOffice\Models\KpPengumuman::with('pembuat')->orderBy('created_at', 'desc')->get();
        $pengumumen = $allData->where('tipe', 'pengumuman');
        $faqs = $allData->where('tipe', 'faq');
        $timelines = $allData->where('tipe', 'timeline');
        
        return view('eoffice::koordinator.pengumuman', compact('pengumumen', 'faqs', 'timelines'));
    }

    /**
     * Proses Simpan Pengumuman Baru
     */
    public function storePengumuman(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:pengumuman,timeline,faq',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $dataToSave = [
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['konten'],
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_active'),
            'created_by' => auth()->id() ?? 1,
        ];

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('pengumuman');
            $dataToSave['lampiran'] = $path;
        }

        \Modules\EOffice\Models\KpPengumuman::create($dataToSave);

        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil dipublikasikan!');
    }

    /**
     * Proses Update Pengumuman
     */
    public function updatePengumuman(Request $request, $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:pengumuman,timeline,faq',
            'konten' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $pengumuman = \Modules\EOffice\Models\KpPengumuman::findOrFail($id);
        
        $dataToUpdate = [
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['konten'],
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_active'),
        ];

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran) {
                Storage::delete($pengumuman->lampiran);
            }
            $path = $request->file('lampiran')->store('pengumuman');
            $dataToUpdate['lampiran'] = $path;
        }

        $pengumuman->update($dataToUpdate);

        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil diperbarui!');
    }

    /**
     * Hapus Pengumuman
     */
    public function destroyPengumuman($id)
    {
        $pengumuman = \Modules\EOffice\Models\KpPengumuman::findOrFail($id);
        if ($pengumuman->lampiran) {
            Storage::delete($pengumuman->lampiran);
        }
        $pengumuman->delete();
        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil dihapus!');
    }

    public function validasiBerkas()
    {
        $kps = KerjaPraktik::with(['mahasiswa.user', 'dosenPembimbing.user', 'dokumen'])
            ->orderBy('created_at', 'desc')
            ->get();

        $mahasiswas = $kps->map(function($kp) {
            $dokumens = $kp->dokumen;
            
            // Map status_validasi to UI status
            $mapStatus = function($status) {
                if ($status === 'disetujui' || $status === 'approved') return 'approved';
                if ($status === 'ditolak' || $status === 'rejected') return 'rejected';
                return 'pending';
            };

            $praKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['Transkrip', 'Surat Pengantar', 'Form Pendaftaran', 'Proposal']))
                ->map(fn($d) => (object)[
                    'id' => $d->id,
                    'nama_file' => basename($d->file_path ?? $d->jenis_dokumen),
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-', // Can't easily get file size without storage hit
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();
            
            $saatKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['Logbook', 'Laporan Progress', 'Laporan']))
                ->map(fn($d) => (object)[
                    'id' => $d->id,
                    'nama_file' => basename($d->file_path ?? $d->jenis_dokumen),
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-',
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            $pascaKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['A2', 'Kartu Hijau', 'Nilai Lapangan', 'Laporan Akhir', 'Bukti Terima', 'Makalah']))
                ->map(fn($d) => (object)[
                    'id' => $d->id,
                    'nama_file' => basename($d->file_path ?? $d->jenis_dokumen),
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-',
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            // Status keseluruhan
            $allDocs = $dokumens;
            $status_keseluruhan = 'Belum Upload';
            if ($allDocs->count() > 0) {
                if ($allDocs->contains(fn($d) => in_array($d->status_validasi ?? $d->approval_status, ['ditolak', 'rejected']))) {
                    $status_keseluruhan = 'Revisi';
                } elseif ($allDocs->contains(fn($d) => in_array($d->status_validasi ?? $d->approval_status, ['pending', 'menunggu']))) {
                    $status_keseluruhan = 'Menunggu Review';
                } else {
                    $status_keseluruhan = 'Disetujui';
                }
            }

            return (object) [
                'id' => $kp->id,
                'nama' => $kp->mahasiswa->user->name ?? 'Unknown',
                'nim' => $kp->mahasiswa->nim ?? '-',
                'prodi' => 'Teknik Komputer',
                'dosen_pembimbing' => $kp->dosenPembimbing->nama_lengkap ?? null,
                'tempat_kp' => $kp->tempat_fix ?? $kp->rencana_tempat ?? '-',
                'judul_kp' => $kp->judul_fix ?? $kp->rencana_judul ?? '-',
                'durasi_kp' => ($kp->tanggal_mulai ? date('d M Y', strtotime($kp->tanggal_mulai)) : '-') . ' - ' . ($kp->tanggal_selesai ? date('d M Y', strtotime($kp->tanggal_selesai)) : '-'),
                'status_keseluruhan' => $status_keseluruhan,
                'tahap_aktif' => $kp->status_kp === 'active' ? 'Saat KP' : ($kp->status_kp === 'Selesai' || $kp->status_kp === 'Pasca KP' ? 'Pasca KP' : 'Pra KP'),
                'jumlah_dokumen' => $allDocs->count(),
                'dokumen' => [
                    'pra_kp' => $praKp,
                    'saat_kp' => $saatKp,
                    'pasca_kp' => $pascaKp
                ]
            ];
        });

        return view('eoffice::koordinator.validasi_berkas', compact('mahasiswas'));
    }

    public function approveDokumen($id)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'status_validasi' => 'disetujui',
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'revision_note' => null
        ]);
        return response()->json(['success' => true]);
    }

    public function rejectDokumen(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);
        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'status_validasi' => 'ditolak',
            'approval_status' => 'rejected',
            'revision_note' => $request->catatan
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Halaman FAQ & Dokumen Panduan
     */
    public function faq()
    {
        // Menggunakan dummy collections agar UI bisa di-render meskipun database belum ada
        // Info untuk developer: Perlu dibuat tabel eo_faq dan eo_dokumen_panduan di Supabase
        
        $dokumens = collect([
            (object) [
                'id' => 1,
                'judul' => 'Panduan Penulisan Kerja Praktik 2026',
                'file_name' => 'Buku_Panduan_KP_2026.pdf',
                'file_size' => '2.4 MB',
                'version' => 'v1.2',
                'is_active' => true,
                'created_at' => now()->subDays(2),
                'pembuat' => (object) ['name' => 'Koordinator KP']
            ],
            (object) [
                'id' => 2,
                'judul' => 'Formulir Penilaian Pembimbing Lapangan',
                'file_name' => 'Form_Nilai_Instansi.docx',
                'file_size' => '156 KB',
                'version' => 'v1.0',
                'is_active' => true,
                'created_at' => now()->subDays(5),
                'pembuat' => (object) ['name' => 'Koordinator KP']
            ]
        ]);

        $faqs = collect([
            (object) [
                'id' => 1,
                'pertanyaan' => 'Bagaimana alur pendaftaran Kerja Praktik?',
                'jawaban' => 'Mahasiswa harus mencari instansi terlebih dahulu, meminta surat pengantar ke tata usaha, lalu mendaftar melalui SIKP.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(1),
                'pembuat' => (object) ['name' => 'Koordinator KP']
            ],
            (object) [
                'id' => 2,
                'pertanyaan' => 'Apakah boleh kerja praktik di startup?',
                'jawaban' => 'Boleh, asalkan startup tersebut sudah berbadan hukum (PT/CV) dan memiliki tim IT/engineer yang bisa membimbing.',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
                'pembuat' => (object) ['name' => 'Koordinator KP']
            ]
        ]);

        return view('eoffice::koordinator.faq', compact('dokumens', 'faqs'));
    }

    public function storeDokumenPanduan(Request $request)
    {
        // Dummy logic: Karena tabel eo_dokumen_panduan belum ada.
        return redirect()->route('eoffice.kp.koordinator.faq')->with('success', 'Dokumen Panduan berhasil diupload!');
    }

    public function destroyDokumenPanduan($id)
    {
        // Dummy logic: Karena tabel eo_dokumen_panduan belum ada.
        return redirect()->route('eoffice.kp.koordinator.faq')->with('success', 'Dokumen Panduan berhasil dihapus!');
    }

    public function storeFaq(Request $request)
    {
        // Dummy logic: Karena tabel eo_faq belum ada.
        return redirect()->route('eoffice.kp.koordinator.faq')->with('success', 'FAQ berhasil ditambahkan!');
    }

    public function destroyFaq($id)
    {
        // Dummy logic: Karena tabel eo_faq belum ada.
        return redirect()->route('eoffice.kp.koordinator.faq')->with('success', 'FAQ berhasil dihapus!');
    }

    /**
     * Halaman Data Mahasiswa
     */
    public function dataMahasiswa()
    {
        $kps = \Modules\EOffice\Models\KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama',
            'm.nim as nim',
            'd.nama_lengkap as dosen_pembimbing',
            'p.nilai_seminar_pembimbing',
            'p.nilai_lapangan',
            'p.nilai_akhir'
        )
        ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
        ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
        ->leftJoin('eo_kp_dosen as d', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'd.id')
        ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
        ->orderBy('eo_kerja_praktik.created_at', 'desc')
        ->get();

        $mahasiswas = $kps->map(function($kp) {
            $statusStr = 'Pending';
            $tahap = 'Pra KP';
            if ($kp->status_kp === 'pending') {
                $statusStr = 'Pending';
                $tahap = 'Pra KP';
            } elseif ($kp->status_kp === 'active') {
                $statusStr = 'Aktif KP';
                $tahap = 'Saat KP';
            } elseif ($kp->status_kp === 'completed') {
                $statusStr = 'Selesai';
                $tahap = 'Pasca KP';
            }
            
            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama ?? 'Unknown',
                'nim' => $kp->nim ?? '-',
                'prodi' => 'Teknik Komputer',
                'tempat_kp' => $kp->rencana_tempat ?? 'Belum ditentukan',
                'judul_kp' => $kp->rencana_judul ?? 'Belum ditentukan',
                'dosen_pembimbing' => $kp->dosen_pembimbing,
                'status_kp' => $statusStr,
                'semester' => 'Genap',
                'tahun_kp' => date('Y', strtotime($kp->created_at)),
                'nilai_seminar' => $kp->nilai_seminar_pembimbing,
                'nilai_lapangan' => $kp->nilai_lapangan,
                'nilai_akhir' => $kp->nilai_akhir,
                'status_dokumen' => '-',
                'riwayat_approval' => [
                    ['tanggal' => date('Y-m-d', strtotime($kp->updated_at)), 'status' => 'Info', 'keterangan' => 'Tahap saat ini: ' . $tahap]
                ],
                'status_seminar' => '-',
            ];
        });

        return view('eoffice::koordinator.data_mahasiswa', compact('mahasiswas'));
    }

    public function nilaiLapangan()
    {
        $mahasiswas = collect([
            (object) [
                'id' => 1,
                'nama' => 'Ahmad Budi Santoso',
                'nim' => '2100018111',
                'file_nilai' => 'Nilai_Lapangan_Ahmad.pdf',
                'status_nilai' => 'Belum Dinilai',
            ],
            (object) [
                'id' => 2,
                'nama' => 'Siti Nurhaliza',
                'nim' => '2100018199',
                'file_nilai' => 'Form_Penilaian_Siti.pdf',
                'status_nilai' => 'Sudah Dinilai',
            ],
            (object) [
                'id' => 3,
                'nama' => 'Bima Sakti',
                'nim' => '2100018155',
                'file_nilai' => null,
                'status_nilai' => 'Menunggu Berkas',
            ]
        ]);

        return view('eoffice::koordinator.nilai_lapangan', compact('mahasiswas'));
    }

    /**
     * Halaman Upload Berkas (Template, dll)
     */
    public function uploadBerkas()
    {
        return view('eoffice::koordinator.upload_berkas');
    }

    /**
     * Proses Simpan Template A2
     */
    public function storeTemplateA2(Request $request)
    {
        $request->validate([
            'template_a2' => 'required|mimes:doc,docx|max:10240', // Maks 10MB
        ], [
            'template_a2.required' => 'File template wajib diunggah.',
            'template_a2.mimes' => 'File harus berupa dokumen Word (doc/docx).',
            'template_a2.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('template_a2');
        
        // Simpan file dengan nama yang tetap (akan di-overwrite)
        $file->storeAs('templates', 'form_a2.docx', 'local');

        return redirect()->route('eoffice.kp.koordinator.upload_berkas')
            ->with('success', 'Template Form Kehadiran & Nilai (A2) berhasil diunggah dan diperbarui!');
    }
}
