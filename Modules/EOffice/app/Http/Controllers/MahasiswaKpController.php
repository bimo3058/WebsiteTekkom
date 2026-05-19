<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\EOffice\Models\KerjaPraktik;
use Modules\EOffice\Models\KpDokumen;
use Modules\EOffice\Models\KpMahasiswa;
use Modules\EOffice\Models\KpPengumuman;
use Modules\EOffice\Models\KpSeminar;
use PhpOffice\PhpWord\TemplateProcessor;

class MahasiswaKpController extends Controller
{
    // =========================================================================
    // DASHBOARD
    // =========================================================================

    /**
     * Dashboard utama mahasiswa KP — menampilkan status stepper,
     * ringkasan data, pengumuman, dan checklist fase saat ini.
     */
    public function dashboard()
    {
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        // Ambil KP aktif milik mahasiswa (paling baru)
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->with(['dokumen', 'seminar', 'penilaian', 'dosenPembimbing'])
            ->latest()
            ->first();

        // Pengumuman terbaru dari Koordinator KP (hanya yang aktif)
        $pengumuman = KpPengumuman::with('pembuat')
            ->where('is_active', true)
            ->where('tipe', 'pengumuman')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // Timeline KP
        $timeline = KpPengumuman::where('is_active', true)
            ->where('tipe', 'timeline')
            ->orderBy('created_at', 'asc')
            ->get();

        // Hitung jumlah dokumen per status
        $dokumenStats = [
            'total'     => 0,
            'menunggu'  => 0,
            'disetujui' => 0,
            'ditolak'   => 0,
        ];

        if ($kp) {
            $dokumenStats['total']     = $kp->dokumen->count();
            $dokumenStats['menunggu']  = $kp->dokumen->where('status_validasi', 'menunggu')->count();
            $dokumenStats['disetujui'] = $kp->dokumen->where('status_validasi', 'disetujui')->count();
            $dokumenStats['ditolak']   = $kp->dokumen->where('status_validasi', 'ditolak')->count();
        }

        $activePhase = 'pra_kp';
        if ($kp) {
            $status = strtolower($kp->status_kp);
            if (str_contains($status, 'saat kp') || str_contains($status, 'active')) {
                $activePhase = 'saat_kp';
            } elseif (str_contains($status, 'pasca kp') || str_contains($status, 'selesai')) {
                $activePhase = 'pasca_kp';
            }
        }

        try {
            $templates = \Modules\EOffice\Models\TemplateDokumenKP::where('phase', $activePhase)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $templates = collect();
        }

        return view('eoffice::kp.mahasiswa.dashboard', compact(
            'mahasiswa', 'kp', 'pengumuman', 'timeline', 'dokumenStats', 'templates', 'activePhase'
        ));
    }

    /**
     * Serve pengumuman lampiran PDF securely and dynamically.
     */
    public function serveLampiran($id)
    {
        $pengumuman = KpPengumuman::findOrFail($id);

        if (!$pengumuman->lampiran) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        if (!Storage::exists($pengumuman->lampiran)) {
            abort(404, 'File lampiran tidak ditemukan di storage.');
        }

        return Storage::response($pengumuman->lampiran);
    }

    // =========================================================================
    // INFORMASI — KEPERLUAN PERUSAHAAN (PRA KP)
    // =========================================================================

    /**
     * Halaman informasi persuratan & keperluan perusahaan.
     * Juga berfungsi sebagai tempat membuat proposal KP sederhana.
     */
    public function informasi()
    {
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)->latest()->first();

        // Ambil pengumuman bertipe 'pengumuman' atau 'timeline'
        $infoPersuratan = KpPengumuman::with('pembuat')
            ->where('is_active', true)
            ->whereIn('tipe', ['pengumuman', 'timeline'])
            ->orderByDesc('updated_at')
            ->get();

        $templateContent = Storage::disk('public')->exists('templates/proposal_kp.html') 
            ? Storage::disk('public')->get('templates/proposal_kp.html') 
            : '<h2>1. Latar Belakang</h2><p><br></p>
            <h2>2. Rumusan Masalah</h2><p><br></p>
            <h2>3. Batasan Masalah</h2><p><br></p>
            <h2>4. Tujuan Kerja Praktek</h2><p><br></p>
            <h2>5. Bentuk Kegiatan</h2><p><br></p>
            <h2>6. Tempat dan Waktu Pelaksanaan</h2><p><br></p>
            <h2>7. Penutup</h2><p><br></p>';

        $templatesKeperluan = collect();
        try {
            $templatesKeperluan = \Modules\EOffice\Models\KpPengumuman::where('tipe', 'keperluan_perusahaan')->where('is_active', true)->get();
        } catch (\Exception $e) {
            // Ignore if table doesn't exist
        }

        return view('eoffice::kp.mahasiswa.informasi', compact(
            'mahasiswa', 'kp', 'infoPersuratan', 'templateContent', 'templatesKeperluan'
        ));
    }



    // =========================================================================
    // EXPORT DOCUMENTS (Surat Pengantar & Proposal)
    // =========================================================================

    public function exportSuratPengantar(Request $request)
    {
        $data = $request->validate([
            'format' => 'required|in:word,pdf',
            'instansi' => 'required|string',
            'alamat' => 'required|string',
            'durasi' => 'required|string',
            'anggota' => 'required|string', // JSON string
        ]);

        $anggota = json_decode($data['anggota'], true) ?? [];
        $view = view('eoffice::kp.mahasiswa.templates.surat_pengantar', compact('data', 'anggota'));

        if ($data['format'] === 'word') {
            return response($view)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="Surat_Pengantar_KP.doc"');
        }

        // PDF relies on print preview / browser PDF generation logic or HTML to PDF library if we had one.
        // For now, we return the view, which will automatically print via JS if we structure it that way.
        return $view;
    }

    public function exportProposal(Request $request)
    {
        $data = $request->validate([
            'format' => 'required|in:word,pdf',
            'judul' => 'nullable|string',
            'instansi' => 'nullable|string',
            'content' => 'required|string', // HTML string
        ]);

        $view = view('eoffice::kp.mahasiswa.templates.proposal', compact('data'));

        if ($data['format'] === 'word') {
            return response($view)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="Proposal_KP.doc"');
        }

        return $view;
    }

    /**
     * Generate A2 template from uploaded docx
     */
    public function generateA2(Request $request)
    {
        $validated = $request->validate([
            'nama_pembimbing' => 'required|string',
            'nip_pembimbing' => 'required|string',
            'jabatan_pembimbing' => 'required|string',
            'perusahaan' => 'required|string',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)->latest()->first();

        // 1. Ambil path template dari storage
        $templatePath = storage_path('app/templates/form_a2.docx');
        
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template A2 belum diunggah oleh Koordinator.');
        }

        // 2. Load template menggunakan PhpWord
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // 3. Replace variabel-variabel di dalam template Word
        $templateProcessor->setValue('nama', $mahasiswa->user->name ?? '-');
        $templateProcessor->setValue('nip', $mahasiswa->nim ?? '-');
        $templateProcessor->setValue('topik', $kp->rencana_judul ?? ($kp->judul_fix ?? '-'));
        
        $templateProcessor->setValue('nama_pembimbing', $validated['nama_pembimbing']);
        $templateProcessor->setValue('nip_pembimbing', $validated['nip_pembimbing']);
        $templateProcessor->setValue('jabatan_pembimbing', $validated['jabatan_pembimbing']);
        $templateProcessor->setValue('perusahaan', $validated['perusahaan']);

        // 4. Buat file temporary untuk dikirim ke user
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $templateProcessor->saveAs($tempFile);

        // 5. Kembalikan file sebagai download
        return response()->download($tempFile, "Form_Kehadiran_A2_{$mahasiswa->nim}.docx")->deleteFileAfterSend(true);
    }

    // =========================================================================
    // PENGUMUMAN
    // =========================================================================

    /**
     * Halaman Pengumuman — melihat semua pengumuman aktif.
     */
    public function pengumuman()
    {
        $pengumumanItems = KpPengumuman::with('pembuat')
            ->where('is_active', true)
            ->whereIn('tipe', ['pengumuman', 'timeline'])
            ->orderByDesc('updated_at')
            ->get();

        return view('eoffice::kp.mahasiswa.pengumuman', compact('pengumumanItems'));
    }

    // =========================================================================
    // FAQ
    // =========================================================================

    /**
     * Halaman FAQ — cara pinjam ruangan, alur KP, persyaratan, dll.
     */
    public function faq()
    {
        // Ambil FAQ dari database jika ada, atau gunakan data statis
        $faqItems = KpPengumuman::where('is_active', true)
            ->where('tipe', 'faq')
            ->orderByDesc('created_at')
            ->get();

        return view('eoffice::kp.mahasiswa.faq', compact('faqItems'));
    }

    // =========================================================================
    // PENDAFTARAN KP (PRA KP)
    // =========================================================================

    /**
     * Menampilkan form pendaftaran KP.
     * Jika mahasiswa sudah punya KP aktif, redirect ke dashboard.
     */
    public function pendaftaran()
    {
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        // Cek apakah sudah punya KP yang belum selesai
        $existingKp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotIn('status_kp', ['Selesai'])
            ->first();

        // Cek pengaturan pendaftaran KP
        $isOpen = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_buka', '0') == '1';
        $startDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_mulai', '');
        $endDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_selesai', '');
        
        $isPeriodValid = true;
        $now = now();
        
        if ($isOpen) {
            if (!empty($startDate) && $now->startOfDay()->lt(\Carbon\Carbon::parse($startDate)->startOfDay())) {
                $isPeriodValid = false;
            }
            if (!empty($endDate) && $now->startOfDay()->gt(\Carbon\Carbon::parse($endDate)->startOfDay())) {
                $isPeriodValid = false;
            }
        }
        
        $registrationOpen = $isOpen && $isPeriodValid;

        return view('eoffice::kp.mahasiswa.pendaftaran', compact('mahasiswa', 'existingKp', 'registrationOpen', 'startDate', 'endDate'));
    }

    /**
     * Proses simpan pendaftaran KP baru.
     * - Membuat record di eo_kerja_praktik
     * - Upload transkrip ke storage
     */
    public function storePendaftaran(Request $request)
    {
        // Cek apakah pendaftaran dibuka
        $isOpen = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_buka', '0') == '1';
        $startDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_mulai', '');
        $endDate = \Modules\EOffice\Models\KpSetting::get('pendaftaran_kp_selesai', '');
        
        $isPeriodValid = true;
        $now = now();
        
        if ($isOpen) {
            if (!empty($startDate) && $now->startOfDay()->lt(\Carbon\Carbon::parse($startDate)->startOfDay())) {
                $isPeriodValid = false;
            }
            if (!empty($endDate) && $now->startOfDay()->gt(\Carbon\Carbon::parse($endDate)->startOfDay())) {
                $isPeriodValid = false;
            }
        }
        
        if (!$isOpen || !$isPeriodValid) {
            return redirect()->back()->with('error', 'Pendaftaran Kerja Praktik saat ini sedang ditutup.');
        }
        $validated = $request->validate([
            'rencana_judul'   => 'required|string|max:255',
            'rencana_tempat'  => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        // Cegah duplikasi: mahasiswa hanya boleh punya 1 KP aktif
        $existingKp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotIn('status_kp', ['Selesai'])
            ->first();

        if ($existingKp) {
            return redirect()->back()->with('error', 'Anda sudah memiliki pendaftaran KP yang sedang berjalan.');
        }

        // Buat record KP baru
        $kp = KerjaPraktik::create([
            'nim'             => $mahasiswa->nim,
            'mahasiswa_id'    => $mahasiswa->id,
            'rencana_judul'   => $validated['rencana_judul'],
            'rencana_tempat'  => $validated['rencana_tempat'],
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status_kp'       => 'Pra-KP',
            'is_acc_admin'    => false,
        ]);

        return redirect()
            ->route('eoffice.kp.mahasiswa.dashboard')
            ->with('success', 'Pendaftaran KP berhasil! Data Anda sedang direview oleh Koordinator.');
    }

    // =========================================================================
    // DOKUMEN (SAAT KP)
    // =========================================================================

    /**
     * Halaman manajemen dokumen KP.
     * Menampilkan form pengisian judul/tempat fix, upload dokumen, download template.
     */
    public function dokumen()
    {
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->with(['dokumen'])
            ->latest()
            ->first();

        if (!$kp) {
            return redirect()
                ->route('eoffice.kp.mahasiswa.pendaftaran')
                ->with('error', 'Anda belum mendaftar KP. Silakan daftar terlebih dahulu.');
        }

        // Kelompokkan dokumen berdasarkan jenis
        $dokumenByJenis = $kp->dokumen->groupBy('jenis_dokumen');

        return view('eoffice::kp.mahasiswa.dokumen', compact('mahasiswa', 'kp', 'dokumenByJenis'));
    }

    /**
     * Upload dokumen KP (bukti terima, laporan, makalah, kartu hijau, dll).
     */
    public function storeDokumen(Request $request)
    {
        $validated = $request->validate([
            'jenis_dokumen' => 'required|string|in:Bukti Terima,Laporan,Makalah,CV,Foto,Kartu Hijau,Nilai Lapangan,A2',
            'file'          => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'nilai_input_mahasiswa' => 'nullable|numeric|min:0|max:100',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->firstOrFail();

        // Determine phase based on status_kp
        $activePhase = 'pra_kp';
        $status_kp = strtolower($kp->status_kp);
        if (str_contains($status_kp, 'saat kp') || str_contains($status_kp, 'active')) {
            $activePhase = 'saat_kp';
        } elseif (str_contains($status_kp, 'pasca kp') || str_contains($status_kp, 'selesai')) {
            $activePhase = 'pasca_kp';
        }

        // Simpan file
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $folder = strtolower(str_replace(' ', '_', $validated['jenis_dokumen']));
        $path = $file->store("kp/{$mahasiswa->nim}/{$folder}", 'public');

        // Cek apakah sudah ada dokumen dengan jenis yang sama, update jika ya
        $existing = KpDokumen::where('kp_id', $kp->id)
            ->where('jenis_dokumen', $validated['jenis_dokumen'])
            ->first();
            
        $data = [
            'file_path' => $path,
            'file_name' => $fileName,
            'phase' => $activePhase,
            'status_validasi' => 'menunggu', // Legacy compatibility
            'approval_status' => 'pending', // New approval workflow
            'tanggal_upload' => now(),
        ];
        
        if (isset($validated['nilai_input_mahasiswa'])) {
            $data['nilai_input_mahasiswa'] = $validated['nilai_input_mahasiswa'];
        }

        if ($existing) {
            // Hapus file lama
            Storage::disk('public')->delete($existing->file_path);
            KpDokumen::where('id', $existing->id)->update($data);
        } else {
            $data['kp_id'] = $kp->id;
            $data['jenis_dokumen'] = $validated['jenis_dokumen'];
            KpDokumen::create($data);
        }

        return redirect()->back()->with('success', "Dokumen {$validated['jenis_dokumen']} berhasil diunggah!");
    }

    /**
     * Update data KP: judul fix dan tempat fix (diisi setelah diterima di tempat KP).
     */
    public function updateDataKp(Request $request)
    {
        $validated = $request->validate([
            'judul_fix'  => 'required|string|max:255',
            'tempat_fix' => 'required|string|max:255',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->firstOrFail();

        KerjaPraktik::where('id', $kp->id)->update($validated);

        return redirect()->back()->with('success', 'Judul dan tempat KP berhasil diperbarui!');
    }

    /**
     * Download template dokumen (Laporan KP / Makalah IEEE).
     * Untuk saat ini mengembalikan placeholder — file template bisa ditambahkan nanti.
     */
    public function downloadTemplate(string $type)
    {
        $template = \Modules\EOffice\Models\TemplateDokumenKP::find($type);
        if ($template && Storage::disk('public')->exists($template->file_path)) {
            return Storage::disk('public')->download($template->file_path, $template->file_name);
        }

        return redirect()->back()->with('error', 'File template tidak ditemukan.');
    }

    /**
     * Generate form A2 (Presensi & Nilai Lapangan) dengan TemplateProcessor
     */
    public function exportA2(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string',
            'nip'        => 'required|string',
            'jabatan'    => 'required|string',
            'perusahaan' => 'required|string',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->whereNotIn('status_kp', ['Selesai'])
            ->first();

        if (!$kp) {
            return redirect()->back()->with('error', 'Anda belum memiliki pendaftaran KP aktif.');
        }

        // Lokasi file template (yang diupload koordinator)
        $templatePath = storage_path('app/templates/form_a2.docx');
        
        // Cek jika template belum ada, bisa menggunakan fallback atau error
        if (!file_exists($templatePath)) {
            // Coba cek path default aplikasi sebagai fallback
            $templatePath = storage_path('app/public/templates/kp/Form_A2.docx');
            if (!file_exists($templatePath)) {
                return redirect()->back()->with('error', 'Template Form A2 belum tersedia. Harap hubungi Koordinator KP.');
            }
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);

            // Replace variabel
            $templateProcessor->setValue('nama', htmlspecialchars($request->nama));
            $templateProcessor->setValue('nip', htmlspecialchars($request->nip));
            $templateProcessor->setValue('jabatan', htmlspecialchars($request->jabatan));
            $templateProcessor->setValue('perusahaan', htmlspecialchars($request->perusahaan));
            
            $templateProcessor->setValue('nama_pembimbing', htmlspecialchars($request->nama));
            $templateProcessor->setValue('nip_pembimbing', htmlspecialchars($request->nip));
            $templateProcessor->setValue('jabatan_pembimbing', htmlspecialchars($request->jabatan));

            $templateProcessor->setValue('nama_mahasiswa', htmlspecialchars($mahasiswa->user->name));
            $templateProcessor->setValue('nim_mahasiswa', htmlspecialchars($mahasiswa->nim));
            $templateProcessor->setValue('topik', htmlspecialchars($kp->rencana_judul ?? '-'));

            // Output file sementara
            $fileName = 'Form_A2_' . preg_replace('/[^a-zA-Z0-9]+/', '_', $mahasiswa->user->name) . '.docx';
            $tempPath = storage_path('app/temp_' . $fileName);
            $templateProcessor->saveAs($tempPath);

            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat dokumen A2: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // SEMINAR (PASCA KP)
    // =========================================================================

    /**
     * Halaman pendaftaran seminar KP.
     * Menampilkan form daftar seminar, upload kartu hijau & nilai lapangan,
     * checklist validasi syarat, serta status undangan seminar.
     */
    public function seminar()
    {
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->with(['dokumen', 'seminar', 'penilaian'])
            ->latest()
            ->first();

        if (!$kp) {
            return redirect()
                ->route('eoffice.kp.mahasiswa.pendaftaran')
                ->with('error', 'Anda belum mendaftar KP.');
        }

        // Kelompokkan dokumen berdasarkan jenis
        $dokumenByJenis = $kp->dokumen->groupBy('jenis_dokumen');

        // Ambil dokumen spesifik secara eksplisit (untuk history)
        $cvDoc          = $dokumenByJenis->get('CV')?->sortByDesc('created_at')->first();
        $fotoDoc        = $dokumenByJenis->get('Foto')?->sortByDesc('created_at')->first();
        $kartuHijauDoc  = $dokumenByJenis->get('Kartu Hijau')?->sortByDesc('created_at')->first();
        $nilaiLapanganDoc = $dokumenByJenis->get('Nilai Lapangan')?->sortByDesc('created_at')->first();

        // Cek kelengkapan syarat seminar
        $syaratSeminar = $this->cekSyaratSeminar($kp, $dokumenByJenis);

        return view('eoffice::kp.mahasiswa.seminar', compact(
            'mahasiswa', 'kp', 'dokumenByJenis', 'syaratSeminar',
            'cvDoc', 'fotoDoc', 'kartuHijauDoc', 'nilaiLapanganDoc'
        ));
    }

    /**
     * Proses pendaftaran seminar KP.
     * Membuat atau memperbarui record di eo_kp_seminar.
     */
    public function storeSeminar(Request $request)
    {
        $validated = $request->validate([
            'tanggal_seminar' => 'required|date',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i',
            'ruangan'         => 'required|string|max:100',
        ]);

        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();
        $kp = KerjaPraktik::where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->firstOrFail();

        // Cek syarat (opsional, bisa submit kapan saja sekarang)
        $dokumenByJenis = $kp->dokumen->groupBy('jenis_dokumen');
        $syarat = $this->cekSyaratSeminar($kp, $dokumenByJenis);

        // Buat atau update seminar
        $waktuSeminar = $validated['waktu_mulai'] . ' - ' . $validated['waktu_selesai'];
        KpSeminar::updateOrCreate(
            ['kp_id' => $kp->id],
            [
                'tanggal_seminar'         => $validated['tanggal_seminar'],
                'waktu_seminar'           => $waktuSeminar,
                'ruangan'                 => $validated['ruangan'],
                'status_validasi_syarat'  => 'proses',
            ]
        );

        // Update status KP ke Pasca KP jika masih di Saat KP
        if ($kp->status_kp === 'Saat KP' || $kp->status_kp === 'active') {
            KerjaPraktik::where('id', $kp->id)->update(['status_kp' => 'Pasca KP']);
        }

        return redirect()->back()->with('success', 'Pendaftaran seminar berhasil! Menunggu validasi dari Koordinator KP.');
    }

    // =========================================================================
    // HELPERS (PRIVATE)
    // =========================================================================

    /**
     * Cek kelengkapan syarat seminar.
     * Mengembalikan array checklist beserta flag apakah semua terpenuhi.
     */
    private function cekSyaratSeminar(KerjaPraktik $kp, $dokumenByJenis): array
    {
        // Laporan & Makalah don't need approval, just need to be uploaded
        $laporanAcc = isset($dokumenByJenis['Laporan'])
            && $dokumenByJenis['Laporan']->isNotEmpty();

        $makalahAcc = isset($dokumenByJenis['Makalah'])
            && $dokumenByJenis['Makalah']->isNotEmpty();

        $kartuHijau = isset($dokumenByJenis['Kartu Hijau'])
            && $dokumenByJenis['Kartu Hijau']->where('approval_status', 'approved')->isNotEmpty();

        $nilaiLapangan = isset($dokumenByJenis['Nilai Lapangan'])
            && $dokumenByJenis['Nilai Lapangan']->where('approval_status', 'approved')->isNotEmpty();

        $buktiTerima = isset($dokumenByJenis['Bukti Terima'])
            && $dokumenByJenis['Bukti Terima']->where('approval_status', 'approved')->isNotEmpty();

        $judulFix = !empty($kp->judul_fix) && !empty($kp->tempat_fix);

        return [
            'laporan_acc'      => $laporanAcc,
            'makalah_acc'      => $makalahAcc,
            'kartu_hijau'      => $kartuHijau,
            'nilai_lapangan'   => $nilaiLapangan,
            'bukti_terima'     => $buktiTerima,
            'judul_fix'        => $judulFix,
            'semua_terpenuhi'  => $laporanAcc && $makalahAcc && $kartuHijau && $nilaiLapangan && $buktiTerima && $judulFix,
        ];
    }
}
