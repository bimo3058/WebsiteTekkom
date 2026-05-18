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
            $templates = \Modules\EOffice\Models\TemplateDokumenKP::orderBy('created_at', 'desc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $templates = collect(); // Table might not exist yet
            session()->flash('warning', 'Tabel eo_kp_template belum ada di database. Silakan jalankan migrasi.');
        }

        return view('eoffice::koordinator.template', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'phase' => 'required|in:pra_kp,saat_kp,pasca_kp',
            'file_path' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        $data = [
            'title' => $validated['title'],
            'phase' => $validated['phase'],
            'uploaded_by' => auth()->id(),
        ];

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_path'] = $file->store('kp_templates', 'public');
        }

        try {
            \Modules\EOffice\Models\TemplateDokumenKP::create($data);
            return redirect()->back()->with('success', 'Template berhasil diunggah!');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: Tabel eo_kp_template belum ada di database Supabase.');
        }
    }

    public function updateTemplate(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'phase' => 'required|in:pra_kp,saat_kp,pasca_kp',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $template = \Modules\EOffice\Models\TemplateDokumenKP::findOrFail($id);

        $data = [
            'title' => $validated['title'],
            'phase' => $validated['phase'],
        ];

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_path'] = $file->store('kp_templates', 'public');
        }

        $template->update($data);

        return redirect()->back()->with('success', 'Template berhasil diperbarui!');
    }

    public function destroyTemplate($id)
    {
        $template = \Modules\EOffice\Models\TemplateDokumenKP::findOrFail($id);
        $template->delete();
        return redirect()->back()->with('success', 'Template berhasil dihapus!');
    }

    public function validasiBerkas()
    {
        $kps = \Modules\EOffice\Models\KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama',
            'm.nim as nim',
            'd.nama_lengkap as dosen_pembimbing'
        )
        ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
        ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
        ->leftJoin('eo_kp_dosen as d', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'd.id')
        ->with('dokumen')
        ->orderBy('eo_kerja_praktik.created_at', 'desc')
        ->get();

        $mahasiswas = $kps->map(function($kp) {
            $dokumen_pra = [];
            $dokumen_saat = [];
            $dokumen_pasca = [];

            if ($kp->dokumen) {
                foreach ($kp->dokumen as $dok) {
                    $item = (object)[
                        'id' => $dok->id,
                        'nama_file' => $dok->file_name ?? basename($dok->file_path),
                        'jenis' => $dok->jenis_dokumen,
                        'tanggal' => $dok->created_at->format('Y-m-d'),
                        'ukuran' => '-', // Tidak ada data file size saat ini
                        'status' => $dok->approval_status ?? ($dok->status_validasi === 'disetujui' ? 'approved' : ($dok->status_validasi === 'ditolak' ? 'rejected' : 'pending')),
                        'catatan' => $dok->revision_note ?? ''
                    ];

                    if ($dok->phase === 'pra_kp') {
                        $dokumen_pra[] = $item;
                    } elseif ($dok->phase === 'saat_kp') {
                        $dokumen_saat[] = $item;
                    } elseif ($dok->phase === 'pasca_kp') {
                        $dokumen_pasca[] = $item;
                    } else {
                        // Fallback rule jika phase masih null
                        if (in_array($dok->jenis_dokumen, ['Surat Pengantar', 'Transkrip', 'Bukti Terima', 'Proposal'])) {
                            $dokumen_pra[] = $item;
                        } elseif (in_array($dok->jenis_dokumen, ['Logbook', 'Laporan Progress', 'Kartu Hijau'])) {
                            $dokumen_saat[] = $item;
                        } else {
                            $dokumen_pasca[] = $item;
                        }
                    }
                }
            }

            $tahap = 'Pra KP';
            if ($kp->status_kp === 'active') {
                $tahap = 'Saat KP';
            } elseif ($kp->status_kp === 'completed') {
                $tahap = 'Pasca KP';
            }

            // Determine status keseluruhan
            $all_docs = $kp->dokumen;
            $status_keseluruhan = 'Belum Upload';
            if ($all_docs && $all_docs->count() > 0) {
                $status_keseluruhan = 'Menunggu Review';
                if ($all_docs->where('approval_status', 'revision')->count() > 0) {
                    $status_keseluruhan = 'Revisi';
                } elseif ($all_docs->where('approval_status', 'rejected')->count() > 0) {
                    $status_keseluruhan = 'Ditolak';
                } elseif ($all_docs->where('approval_status', 'pending')->count() == 0) {
                    $status_keseluruhan = 'Disetujui';
                }
            }

            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama ?? 'Unknown',
                'nim' => $kp->nim ?? '-',
                'prodi' => 'Teknik Komputer',
                'dosen_pembimbing' => $kp->dosen_pembimbing ?? '-',
                'tempat_kp' => $kp->rencana_tempat ?? '-',
                'judul_kp' => $kp->rencana_judul ?? '-',
                'durasi_kp' => ($kp->tanggal_mulai && $kp->tanggal_selesai) ? ($kp->tanggal_mulai . ' s/d ' . $kp->tanggal_selesai) : '-',
                'status_keseluruhan' => $status_keseluruhan,
                'tahap_aktif' => $tahap,
                'jumlah_dokumen' => count($dokumen_pra) + count($dokumen_saat) + count($dokumen_pasca),
                'dokumen' => [
                    'pra_kp' => $dokumen_pra,
                    'saat_kp' => $dokumen_saat,
                    'pasca_kp' => $dokumen_pasca
                ]
            ];
        });

        return view('eoffice::koordinator.validasi_berkas', compact('mahasiswas'));
    }

    public function approveBerkas(Request $request, $id)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'approval_status' => 'approved',
            'status_validasi' => 'disetujui',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'revision_note' => null
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen berhasil disetujui.']);
        }
        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }

    public function rejectBerkas(Request $request, $id)
    {
        $request->validate([
            'revision_note' => 'required|string|max:1000'
        ]);

        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'approval_status' => 'rejected',
            'status_validasi' => 'ditolak',
            'revision_note' => $request->revision_note
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen ditolak dan catatan telah diberikan.']);
        }
        return redirect()->back()->with('success', 'Dokumen ditolak dan catatan telah diberikan.');
    }

    public function reviseBerkas(Request $request, $id)
    {
        $request->validate([
            'revision_note' => 'required|string|max:1000'
        ]);

        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'approval_status' => 'revision',
            'status_validasi' => 'ditolak',
            'revision_note' => $request->revision_note
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen dikembalikan untuk revisi.']);
        }
        return redirect()->back()->with('success', 'Dokumen dikembalikan untuk revisi.');
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
        $kps = \Modules\EOffice\Models\KerjaPraktik::select(
            'eo_kerja_praktik.*',
            'u.name as nama',
            'm.nim as nim'
        )
        ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
        ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
        ->with(['dokumen' => function($query) {
            $query->whereIn('jenis_dokumen', ['Nilai Lapangan', 'Form Penilaian Pembimbing', 'Form Penilaian']);
        }])
        ->orderBy('eo_kerja_praktik.created_at', 'desc')
        ->get();

        $mahasiswas = $kps->map(function($kp) {
            $dokumen_nilai = $kp->dokumen->first();
            
            $status_nilai = 'Menunggu Berkas';
            if ($dokumen_nilai) {
                if ($dokumen_nilai->nilai_status === 'valid') {
                    $status_nilai = 'Sudah Dinilai';
                } elseif ($dokumen_nilai->nilai_status === 'pending') {
                    $status_nilai = 'Belum Dinilai';
                } elseif ($dokumen_nilai->nilai_status === 'rejected') {
                    $status_nilai = 'Ditolak';
                }
            }

            return (object) [
                'id' => $kp->id,
                'dokumen_id' => $dokumen_nilai ? $dokumen_nilai->id : null,
                'nama' => $kp->nama ?? 'Unknown',
                'nim' => $kp->nim ?? '-',
                'file_nilai' => $dokumen_nilai ? ($dokumen_nilai->file_name ?? basename($dokumen_nilai->file_path)) : null,
                'file_path' => $dokumen_nilai ? $dokumen_nilai->file_path : null,
                'nilai_input_mahasiswa' => $dokumen_nilai ? $dokumen_nilai->nilai_input_mahasiswa : null,
                'nilai_validasi_koordinator' => $dokumen_nilai ? $dokumen_nilai->nilai_validasi_koordinator : null,
                'status_nilai' => $status_nilai,
            ];
        });

        return view('eoffice::koordinator.nilai_lapangan', compact('mahasiswas'));
    }

    public function updateNilaiLapangan(Request $request, $id)
    {
        $request->validate([
            'nilai_validasi_koordinator' => 'required|numeric|min:0|max:100',
            'nilai_status' => 'required|in:valid,rejected,pending'
        ]);

        $dokumen = \Modules\EOffice\Models\KpDokumen::findOrFail($id);
        $dokumen->update([
            'nilai_validasi_koordinator' => $request->nilai_validasi_koordinator,
            'nilai_status' => $request->nilai_status
        ]);

        // Jika valid, update di eo_kp_penilaian juga jika ada (buat kalau belum ada)
        if ($request->nilai_status === 'valid') {
            \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                ['kp_id' => $dokumen->kp_id],
                ['nilai_lapangan' => $request->nilai_validasi_koordinator]
            );
        }

        return redirect()->back()->with('success', 'Nilai lapangan berhasil diperbarui.');
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
