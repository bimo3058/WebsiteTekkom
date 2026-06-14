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

    public function pengaturan()
    {
        $isOpen = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_buka', '0');
        $startDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_mulai', '');
        $endDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_selesai', '');

        return view('eoffice::koordinator.pengaturan', compact('isOpen', 'startDate', 'endDate'));
    }

    public function storePengaturan(Request $request)
    {
        $validated = $request->validate([
            'pendaftaran_kp_buka' => 'required|in:1,0',
            'pendaftaran_kp_mulai' => 'nullable|date',
            'pendaftaran_kp_selesai' => 'nullable|date|after_or_equal:pendaftaran_kp_mulai',
        ]);

        \Modules\EOffice\Models\KpSetting::set('pendaftaran_kp_buka', $validated['pendaftaran_kp_buka']);

        if ($validated['pendaftaran_kp_mulai']) {
            \Modules\EOffice\Models\KpSetting::set('pendaftaran_kp_mulai', $validated['pendaftaran_kp_mulai']);
        } else {
            // Jika kosong, hapus / set null (walau KpSetting get nya default '')
            \Modules\EOffice\Models\KpSetting::set('pendaftaran_kp_mulai', '');
        }

        if ($validated['pendaftaran_kp_selesai']) {
            \Modules\EOffice\Models\KpSetting::set('pendaftaran_kp_selesai', $validated['pendaftaran_kp_selesai']);
        } else {
            \Modules\EOffice\Models\KpSetting::set('pendaftaran_kp_selesai', '');
        }

        return redirect()->back()->with('success', 'Pengaturan pendaftaran KP berhasil diperbarui.');
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
     * Halaman Pengumuman & Timeline
     */
    public function pengumuman()
    {
        $allData = \Modules\EOffice\Models\KpPengumuman::with('pembuat')->orderBy('created_at', 'desc')->get();
        $pengumumen = $allData->where('tipe', 'pengumuman');
        $faqs = $allData->where('tipe', 'faq');
        $timelines = $allData->where('tipe', 'timeline');
        $keperluans = $allData->where('tipe', 'keperluan_perusahaan');

        return view('eoffice::koordinator.pengumuman', compact('pengumumen', 'faqs', 'timelines', 'keperluans'));
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
            'tipe' => 'required|in:pengumuman,timeline,faq,keperluan_perusahaan',
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
            'phase' => 'required|in:pra_kp,saat_kp,pasca_kp,keperluan_perusahaan',
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
            'phase' => 'required|in:pra_kp,saat_kp,pasca_kp,keperluan_perusahaan',
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
        $kps = KerjaPraktik::with(['mahasiswa.user', 'dosenPembimbing.user', 'dokumen'])
            ->orderBy('created_at', 'desc')
            ->get();

        $mahasiswas = $kps->map(function ($kp) {
            $dokumens = $kp->dokumen;

            // Map status_validasi to UI status
            $mapStatus = function ($status) {
                if ($status === 'disetujui' || $status === 'approved')
                    return 'approved';
                if ($status === 'ditolak' || $status === 'rejected')
                    return 'rejected';
                return 'pending';
            };

            $praKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['Transkrip', 'Surat Pengantar', 'Form Pendaftaran', 'Proposal']))
                ->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? asset('storage/' . $d->file_path) : null,
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-', // Can't easily get file size without storage hit
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            $saatKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['Bukti Terima', 'Laporan', 'Laporan Progress', 'Logbook', 'Makalah']))
                ->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? asset('storage/' . $d->file_path) : null,
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-',
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            $pascaKp = $dokumens->filter(fn($d) => in_array($d->jenis_dokumen, ['CV', 'Foto', 'A2', 'Kartu Hijau', 'Nilai Lapangan', 'Laporan Akhir']))
                ->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? asset('storage/' . $d->file_path) : null,
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

        $mahasiswas = $kps->map(function ($kp) {
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
                'nilai_laporan' => $kp->nilai_seminar_pembimbing, // alias, sama dengan nilai_seminar (diisi dosen)
                'nilai_lapangan' => $kp->nilai_lapangan,            // diisi koordinator dari menu nilai lapangan
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
            ->with([
                'dokumen' => function ($query) {
                    $query->whereIn('jenis_dokumen', ['Nilai Lapangan', 'Form Penilaian Pembimbing', 'Form Penilaian']);
                }
            ])
            ->orderBy('eo_kerja_praktik.created_at', 'desc')
            ->get();

        $mahasiswas = $kps->map(function ($kp) {
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

    public function balancingDosen()
    {
        $dosens = \Modules\EOffice\Models\KpDosen::with('user')->get()->map(function ($dosen) {
            return [
                'id' => $dosen->id,
                'name' => $dosen->nama_lengkap ?? $dosen->user->name ?? 'Unknown',
                'kuota_maksimal' => $dosen->kuota_maksimal ?? 10,
                'mahasiswas' => []
            ];
        })->toArray();

        // Get mahasiswas and their current draft/finalized assignment
        $kps = \Modules\EOffice\Models\KerjaPraktik::with(['mahasiswa.user', 'balancing'])->get();

        $unassignedStudents = [];
        $dosenMap = collect($dosens)->keyBy('id')->toArray();

        foreach ($kps as $kp) {
            if (!$kp->mahasiswa || !$kp->mahasiswa->user)
                continue;

            $mhsData = [
                'id' => $kp->id,
                'mahasiswa_id' => $kp->mahasiswa_id,
                'nama_mahasiswa' => $kp->mahasiswa->nama_lengkap ?? $kp->mahasiswa->user->name ?? 'Unknown',
                'nim' => $kp->mahasiswa->nim ?? '-',
                'rencana_judul' => $kp->rencana_judul ?? 'Belum ada rencana judul',
                // Jika ada record balancing, pakai statusnya.
                // Jika tidak ada record tapi sudah punya dosen_pembimbing_id → berarti sudah finalized (assign manual/legacy).
                'status' => $kp->balancing
                    ? $kp->balancing->status
                    : ($kp->dosen_pembimbing_id ? 'finalized' : 'belum'),
            ];

            $dosenId = null;
            if ($kp->balancing) {
                $dosenId = $kp->balancing->dosen_id;
            } elseif ($kp->dosen_pembimbing_id) {
                // Legacy / manual assign — dosen_pembimbing_id sudah ada tapi belum ada balancing record
                $dosenId = $kp->dosen_pembimbing_id;
            }

            if ($dosenId && isset($dosenMap[$dosenId])) {
                $dosenMap[$dosenId]['mahasiswas'][] = $mhsData;
            } else {
                $unassignedStudents[] = $mhsData;
            }
        }

        $dosens = array_values($dosenMap);

        return view('eoffice::koordinator.balancing', compact('dosens', 'unassignedStudents'));
    }

    public function storeBalancing(Request $request)
    {
        $dosens = json_decode($request->input('dosens'), true);

        if (empty($dosens)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data tidak valid.']);
            }
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        \DB::beginTransaction();
        try {
            $assignedMahasiswaIds = [];

            foreach ($dosens as $dosen) {
                // Update kuota dosen jika berubah
                if (isset($dosen['kuota_maksimal'])) {
                    \Modules\EOffice\Models\KpDosen::where('id', $dosen['id'])
                        ->update(['kuota_maksimal' => $dosen['kuota_maksimal']]);
                }

                foreach ($dosen['mahasiswas'] as $mhs) {
                    $assignedMahasiswaIds[] = $mhs['id'];

                    // Simpan/update record balancing — selalu finalized
                    \Modules\EOffice\Models\KpBalancing::updateOrCreate(
                        ['kp_id' => $mhs['id']],
                        [
                            'mahasiswa_id' => $mhs['mahasiswa_id'],
                            'dosen_id' => $dosen['id'],
                            'status' => 'finalized',
                            'assigned_by' => auth()->id(),
                            'assigned_at' => now(),
                            'finalized_at' => now(),
                        ]
                    );

                    // Langsung update dosen_pembimbing_id di tabel utama KP
                    \Modules\EOffice\Models\KerjaPraktik::where('id', $mhs['id'])
                        ->update(['dosen_pembimbing_id' => $dosen['id']]);
                }
            }

            // Tangani mahasiswa yang dikembalikan ke daftar 'Belum Penempatan'
            if (!empty($assignedMahasiswaIds)) {
                // Semua KP yang memiliki dosen pembimbing tapi tidak ada di payload saat ini
                // berarti mereka dikembalikan ke 'Belum Penempatan'
                \Modules\EOffice\Models\KerjaPraktik::whereNotNull('dosen_pembimbing_id')
                    ->whereNotIn('id', $assignedMahasiswaIds)
                    ->update(['dosen_pembimbing_id' => null]);

                \Modules\EOffice\Models\KpBalancing::whereNotIn('kp_id', $assignedMahasiswaIds)->delete();
            } else {
                // Jika semua dikosongkan (tidak ada satupun yang diassign)
                \Modules\EOffice\Models\KerjaPraktik::whereNotNull('dosen_pembimbing_id')
                    ->update(['dosen_pembimbing_id' => null]);
                \Modules\EOffice\Models\KpBalancing::truncate();
            }

            \DB::commit();

            $message = 'Balancing berhasil disimpan! Semua mahasiswa sudah ter-plot ke dosen pembimbing.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Balancing error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
    /**
     * Halaman Manajemen Periode
     */
    public function periode()
    {
        $periodes = \Modules\EOffice\Models\KpPeriode::orderBy('created_at', 'desc')->get();
        return view('eoffice::koordinator.periode.index', compact('periodes'));
    }

    public function createPeriode()
    {
        return view('eoffice::koordinator.periode.create');
    }

    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date',
            'pra_kp_mulai' => 'nullable|date',
            'pra_kp_akhir' => 'nullable|date',
            'pra_kp_pengingat' => 'nullable|date',
            'saat_kp_mulai' => 'nullable|date',
            'saat_kp_akhir' => 'nullable|date',
            'saat_kp_pengingat' => 'nullable|date',
            'pasca_kp_mulai' => 'nullable|date',
            'pasca_kp_akhir' => 'nullable|date',
            'pasca_kp_pengingat' => 'nullable|date',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Note: The unique constraint on ('tahun_ajaran', 'semester') might fail if duplicate
        \Modules\EOffice\Models\KpPeriode::create($validated);

        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode baru berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, $id)
    {
        $periode = \Modules\EOffice\Models\KpPeriode::findOrFail($id);

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date',
            'pra_kp_mulai' => 'nullable|date',
            'pra_kp_akhir' => 'nullable|date',
            'pra_kp_pengingat' => 'nullable|date',
            'saat_kp_mulai' => 'nullable|date',
            'saat_kp_akhir' => 'nullable|date',
            'saat_kp_pengingat' => 'nullable|date',
            'pasca_kp_mulai' => 'nullable|date',
            'pasca_kp_akhir' => 'nullable|date',
            'pasca_kp_pengingat' => 'nullable|date',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $periode->update($validated);

        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroyPeriode($id)
    {
        $periode = \Modules\EOffice\Models\KpPeriode::findOrFail($id);
        $periode->delete();
        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode berhasil dihapus.');
    }
}
