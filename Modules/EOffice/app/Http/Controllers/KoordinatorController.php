<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\KerjaPraktik;

class KoordinatorController extends Controller
{
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

        // 2. Ambil semua dosen (menggunakan scope dari Spatie Permission)
        $dosens = \App\Models\User::role('dosen')->select('id', 'name')->get();

        // 3. Hitung jumlah mahasiswa bimbingan dan ambil daftar mahasiswa untuk masing-masing dosen
        foreach ($dosens as $dosen) {
            $assigned = KerjaPraktik::select(
                    'eo_kerja_praktik.id',
                    'eo_kerja_praktik.nim',
                    'eo_kerja_praktik.rencana_judul',
                    'u.name as nama_mahasiswa'
                )
                ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
                ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
                ->where('eo_kerja_praktik.dosen_pembimbing_id', $dosen->id)
                ->get();
            
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
                KerjaPraktik::where('id', $assign['kp_id'])
                    ->update(['dosen_pembimbing_id' => $assign['dosen_id']]);
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
        $pengumumen = \Modules\EOffice\Models\KpPengumuman::with('pembuat')->orderBy('created_at', 'desc')->get();
        return view('eoffice::koordinator.pengumuman', compact('pengumumen'));
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
            'judul' => strtoupper($validated['tipe']) . ' - ' . $validated['judul'],
            'deskripsi' => $validated['konten'],
            'is_published' => $request->has('is_active'),
            'user_id' => auth()->id() ?? 1,
        ];

        \Modules\EOffice\Models\KpPengumuman::create($dataToSave);

        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil dipublikasikan!');
    }

    /**
     * Hapus Pengumuman
     */
    public function destroyPengumuman($id)
    {
        \Modules\EOffice\Models\KpPengumuman::findOrFail($id)->delete();
        return redirect()->route('eoffice.kp.koordinator.pengumuman')->with('success', 'Informasi berhasil dihapus!');
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
        // Dummy data untuk desain UI/UX Data Mahasiswa
        $mahasiswas = collect([
            (object) [
                'id' => 1,
                'nama' => 'Ahmad Fathanah',
                'nim' => '2100018112',
                'prodi' => 'Informatika',
                'tempat_kp' => 'PT GoTo Gojek Tokopedia',
                'judul_kp' => 'Pengembangan Sistem Microservice Backend',
                'dosen_pembimbing' => 'Dr. Budi Santoso, M.Kom',
                'status_kp' => 'Selesai',
                'semester' => 'Genap',
                'tahun_kp' => '2026',
                'nilai_seminar' => 88,
                'nilai_lapangan' => 90,
                'nilai_akhir' => 89, // (88+90)/2
                'status_dokumen' => 'Lengkap',
                'riwayat_approval' => [
                    ['tanggal' => '2026-06-15', 'status' => 'Disetujui', 'keterangan' => 'Laporan akhir disetujui pembimbing.'],
                    ['tanggal' => '2026-06-10', 'status' => 'Revisi', 'keterangan' => 'Revisi bab 4 dan 5.'],
                ],
                'status_seminar' => 'Lulus',
            ],
            (object) [
                'id' => 2,
                'nama' => 'Siti Nurhaliza',
                'nim' => '2100018199',
                'prodi' => 'Informatika',
                'tempat_kp' => 'PT Telkom Indonesia',
                'judul_kp' => 'Analisis Jaringan Fiber Optic Regional 5',
                'dosen_pembimbing' => 'Ir. Cipto Mangunkusumo, M.T',
                'status_kp' => 'Aktif KP',
                'semester' => 'Genap',
                'tahun_kp' => '2026',
                'nilai_seminar' => null,
                'nilai_lapangan' => null,
                'nilai_akhir' => null,
                'status_dokumen' => 'Tidak Lengkap',
                'riwayat_approval' => [
                    ['tanggal' => '2026-06-20', 'status' => 'Ditolak', 'keterangan' => 'Laporan progress 1 format tidak sesuai.'],
                ],
                'status_seminar' => 'Belum Daftar',
            ],
            (object) [
                'id' => 3,
                'nama' => 'Bima Sakti',
                'nim' => '2100018155',
                'prodi' => 'Informatika',
                'tempat_kp' => 'Bank Mandiri IT Group',
                'judul_kp' => 'Implementasi Fraud Detection System',
                'dosen_pembimbing' => 'Prof. Dian Sastro, Ph.D',
                'status_kp' => 'Seminar',
                'semester' => 'Genap',
                'tahun_kp' => '2026',
                'nilai_seminar' => null,
                'nilai_lapangan' => 85,
                'nilai_akhir' => null,
                'status_dokumen' => 'Lengkap',
                'riwayat_approval' => [
                    ['tanggal' => '2026-05-12', 'status' => 'Disetujui', 'keterangan' => 'Laporan siap diseminarkan.'],
                ],
                'status_seminar' => 'Menunggu Jadwal',
            ],
            (object) [
                'id' => 4,
                'nama' => 'Joko Widodo',
                'nim' => '2100018101',
                'prodi' => 'Informatika',
                'tempat_kp' => 'PT PLN (Persero)',
                'judul_kp' => 'Sistem Informasi Manajemen Aset',
                'dosen_pembimbing' => 'Dr. Budi Santoso, M.Kom',
                'status_kp' => 'Menunggu Nilai',
                'semester' => 'Genap',
                'tahun_kp' => '2026',
                'nilai_seminar' => 85,
                'nilai_lapangan' => null,
                'nilai_akhir' => null,
                'status_dokumen' => 'Lengkap',
                'riwayat_approval' => [
                    ['tanggal' => '2026-05-20', 'status' => 'Disetujui', 'keterangan' => 'Seminar selesai, menunggu nilai lapangan.'],
                ],
                'status_seminar' => 'Lulus',
            ],
            (object) [
                'id' => 5,
                'nama' => 'Rina Gunawan',
                'nim' => '2100018202',
                'prodi' => 'Informatika',
                'tempat_kp' => 'Kementerian Kominfo',
                'judul_kp' => 'Audit Keamanan Aplikasi E-Government',
                'dosen_pembimbing' => null,
                'status_kp' => 'Pending',
                'semester' => 'Genap',
                'tahun_kp' => '2026',
                'nilai_seminar' => null,
                'nilai_lapangan' => null,
                'nilai_akhir' => null,
                'status_dokumen' => 'Tidak Lengkap',
                'riwayat_approval' => [],
                'status_seminar' => 'Belum Daftar',
            ],
        ]);

        return view('eoffice::koordinator.data_mahasiswa', compact('mahasiswas'));
    }
}
