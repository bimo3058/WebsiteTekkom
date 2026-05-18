<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
=======
use Illuminate\Support\Facades\Storage;
>>>>>>> 6a466f3 (feat(eoffice): implementasi upload template A2 koordinator & generate dokumen dinamis mahasiswa)
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
        ]);

        $dataToSave = [
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['konten'],
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_active'),
            'created_by' => auth()->id() ?? 1,
        ];

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
        ]);

        $pengumuman = \Modules\EOffice\Models\KpPengumuman::findOrFail($id);
        $pengumuman->update([
            'judul' => $validated['judul'],
            'tipe' => $validated['tipe'],
            'konten' => $validated['konten'],
            'is_active' => $request->has('is_active'),
            'is_published' => $request->has('is_active'),
        ]);

        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil diperbarui!');
    }

    /**
     * Hapus Pengumuman
     */
    public function destroyPengumuman($id)
    {
        \Modules\EOffice\Models\KpPengumuman::findOrFail($id)->delete();
        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil dihapus!');
    }

    /**
     * Manajemen Template Dokumen
     */
    public function template()
    {
        // Try to fetch real templates if table exists, otherwise return empty collection safely
        try {
            $templates = \Modules\EOffice\Models\KpTemplate::orderBy('created_at', 'desc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $templates = collect(); // Table might not exist yet
            session()->flash('warning', 'Tabel eo_kp_template belum ada di database. Silakan jalankan migrasi.');
        }

        return view('eoffice::koordinator.template', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'fase' => 'required|in:pra_kp,saat_kp,pasca_kp',
            'file_path' => 'required|file|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('kp_templates', 'public');
            $validated['file_path'] = $path;
        }

        try {
            \Modules\EOffice\Models\KpTemplate::create($validated);
            return redirect()->back()->with('success', 'Template berhasil diunggah!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: Tabel eo_kp_template belum ada di database Supabase.');
        }
    }

    public function updateTemplate(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'fase' => 'required|in:pra_kp,saat_kp,pasca_kp',
            'file_path' => 'nullable|file|max:5120',
        ]);

        $template = \Modules\EOffice\Models\KpTemplate::findOrFail($id);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('kp_templates', 'public');
            $validated['file_path'] = $path;
        }

        $template->update($validated);

        return redirect()->back()->with('success', 'Template berhasil diperbarui!');
    }

    public function destroyTemplate($id)
    {
        $template = \Modules\EOffice\Models\KpTemplate::findOrFail($id);
        $template->delete();
        return redirect()->back()->with('success', 'Template berhasil dihapus!');
    }

    public function validasiBerkas()
    {
        // Data dummy untuk UI design karena tabel di database belum lengkap
        $mahasiswas = collect([
            (object) [
                'id' => 1,
                'nama' => 'Ahmad Fathanah',
                'nim' => '2100018112',
                'prodi' => 'Informatika',
                'dosen_pembimbing' => 'Dr. Budi Santoso, M.Kom',
                'tempat_kp' => 'PT GoTo Gojek Tokopedia',
                'judul_kp' => 'Pengembangan Sistem Microservice Backend',
                'durasi_kp' => '1 Juni 2026 - 31 Agustus 2026',
                'status_keseluruhan' => 'Menunggu Review',
                'tahap_aktif' => 'Pra KP',
                'jumlah_dokumen' => 3,
                'dokumen' => [
                    'pra_kp' => [
                        (object) ['id' => 101, 'nama_file' => 'Surat Pengantar KP.pdf', 'jenis' => 'Surat Pengantar', 'tanggal' => '2026-05-10', 'ukuran' => '1.2 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 102, 'nama_file' => 'Transkrip Nilai Sementara.pdf', 'jenis' => 'Transkrip', 'tanggal' => '2026-05-10', 'ukuran' => '450 KB', 'status' => 'pending', 'catatan' => ''],
                        (object) ['id' => 103, 'nama_file' => 'Form Pendaftaran KP.pdf', 'jenis' => 'Form Pendaftaran', 'tanggal' => '2026-05-12', 'ukuran' => '800 KB', 'status' => 'pending', 'catatan' => ''],
                    ],
                    'saat_kp' => [],
                    'pasca_kp' => []
                ]
            ],
            (object) [
                'id' => 2,
                'nama' => 'Siti Nurhaliza',
                'nim' => '2100018199',
                'prodi' => 'Informatika',
                'dosen_pembimbing' => 'Ir. Cipto Mangunkusumo, M.T',
                'tempat_kp' => 'PT Telkom Indonesia',
                'judul_kp' => 'Analisis Jaringan Fiber Optic Regional 5',
                'durasi_kp' => '15 Mei 2026 - 15 Agustus 2026',
                'status_keseluruhan' => 'Revisi',
                'tahap_aktif' => 'Saat KP',
                'jumlah_dokumen' => 5,
                'dokumen' => [
                    'pra_kp' => [
                        (object) ['id' => 201, 'nama_file' => 'Surat_Pengantar_Siti.pdf', 'jenis' => 'Surat Pengantar', 'tanggal' => '2026-04-20', 'ukuran' => '1.1 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 202, 'nama_file' => 'Transkrip_Siti.pdf', 'jenis' => 'Transkrip', 'tanggal' => '2026-04-20', 'ukuran' => '500 KB', 'status' => 'approved', 'catatan' => ''],
                    ],
                    'saat_kp' => [
                        (object) ['id' => 203, 'nama_file' => 'Logbook_Bulan_1.pdf', 'jenis' => 'Logbook', 'tanggal' => '2026-06-15', 'ukuran' => '2.5 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 204, 'nama_file' => 'Laporan_Progress_V1.pdf', 'jenis' => 'Laporan Progress', 'tanggal' => '2026-06-20', 'ukuran' => '3.1 MB', 'status' => 'rejected', 'catatan' => 'Format penulisan bab 2 belum sesuai standar panduan KP terbaru. Tolong direvisi bagian daftar pustaka.'],
                        (object) ['id' => 205, 'nama_file' => 'Laporan_Progress_V2.pdf', 'jenis' => 'Laporan Progress', 'tanggal' => '2026-06-22', 'ukuran' => '3.2 MB', 'status' => 'pending', 'catatan' => ''],
                    ],
                    'pasca_kp' => []
                ]
            ],
            (object) [
                'id' => 3,
                'nama' => 'Bima Sakti',
                'nim' => '2100018155',
                'prodi' => 'Informatika',
                'dosen_pembimbing' => 'Prof. Dian Sastro, Ph.D',
                'tempat_kp' => 'Bank Mandiri IT Group',
                'judul_kp' => 'Implementasi Fraud Detection System',
                'durasi_kp' => '1 Februari 2026 - 30 April 2026',
                'status_keseluruhan' => 'Disetujui',
                'tahap_aktif' => 'Pasca KP',
                'jumlah_dokumen' => 7,
                'dokumen' => [
                    'pra_kp' => [
                        (object) ['id' => 301, 'nama_file' => 'Surat_Pengantar_Bima.pdf', 'jenis' => 'Surat Pengantar', 'tanggal' => '2026-01-10', 'ukuran' => '1.0 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 302, 'nama_file' => 'Transkrip_Bima.pdf', 'jenis' => 'Transkrip', 'tanggal' => '2026-01-10', 'ukuran' => '400 KB', 'status' => 'approved', 'catatan' => ''],
                    ],
                    'saat_kp' => [
                        (object) ['id' => 303, 'nama_file' => 'Logbook_Lengkap_Bima.pdf', 'jenis' => 'Logbook', 'tanggal' => '2026-05-01', 'ukuran' => '5.5 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 304, 'nama_file' => 'Surat_Keterangan_Selesai_Instansi.pdf', 'jenis' => 'Surat Monitoring', 'tanggal' => '2026-05-02', 'ukuran' => '800 KB', 'status' => 'approved', 'catatan' => ''],
                    ],
                    'pasca_kp' => [
                        (object) ['id' => 305, 'nama_file' => 'Laporan_Akhir_KP_Bima_Final.pdf', 'jenis' => 'Laporan Akhir', 'tanggal' => '2026-05-10', 'ukuran' => '12.4 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 306, 'nama_file' => 'Lembar_Pengesahan_TTD.pdf', 'jenis' => 'Lembar Pengesahan', 'tanggal' => '2026-05-12', 'ukuran' => '1.5 MB', 'status' => 'approved', 'catatan' => ''],
                        (object) ['id' => 307, 'nama_file' => 'Form_Penilaian_Pembimbing.pdf', 'jenis' => 'Form Penilaian', 'tanggal' => '2026-05-12', 'ukuran' => '900 KB', 'status' => 'approved', 'catatan' => ''],
                    ]
                ]
            ],
            (object) [
                'id' => 4,
                'nama' => 'Joko Widodo',
                'nim' => '2100018101',
                'prodi' => 'Informatika',
                'dosen_pembimbing' => null,
                'tempat_kp' => '-',
                'judul_kp' => '-',
                'durasi_kp' => '-',
                'status_keseluruhan' => 'Belum Upload',
                'tahap_aktif' => 'Pra KP',
                'jumlah_dokumen' => 0,
                'dokumen' => [
                    'pra_kp' => [],
                    'saat_kp' => [],
                    'pasca_kp' => []
                ]
            ],
        ]);

        return view('eoffice::koordinator.validasi_berkas', compact('mahasiswas'));
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
