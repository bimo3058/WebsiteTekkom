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
        // Nanti kita isi logika untuk validasi transkrip, kartu hijau, dll
        return view('eoffice::koordinator.validasi_berkas');
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
}
