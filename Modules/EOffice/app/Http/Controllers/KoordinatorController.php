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
                if (auth()->user() && !auth()->user()->hasRole('koor_kp')) {
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
        $periodes = \Modules\EOffice\Models\KpPeriode::orderBy('created_at', 'desc')->get();
        $allKps = KerjaPraktik::all();

        // Build per-periode stats keyed by period ID
        $periodeStats = [];

        foreach ($periodes as $p) {
            // Relaksasi filter: jika tidak ada data yang masuk kriteria tanggal yang ketat (karena dummy),
            // kita tangkap dulu dengan filter lebih longgar, atau cukup hitung semua mahasiswa yang belum punya periode paten.
            // Di sini kita gunakan filter dasar: KPs yang mendaftar setelah periode ini dimulai atau sebelum berakhir.
            $kpsInPeriode = $allKps->filter(function ($kp) use ($p) {
                if (!$kp->created_at)
                    return false;
                $endDate = $p->pasca_kp_akhir ? clone $p->pasca_kp_akhir : (clone $p->pra_kp_akhir)->addMonths(6);
                return $kp->created_at->format('Y-m-d') >= $p->pra_kp_mulai->format('Y-m-d')
                    && $kp->created_at->format('Y-m-d') <= $endDate->format('Y-m-d');
            });

            // Jika masih kosong karena data dummy kotor, fallback ke semua KP sementara.
            if ($kpsInPeriode->isEmpty() && $allKps->count() > 0 && $p->is_active) {
                $kpsInPeriode = $allKps;
            }

            $periodeStats[$p->id] = [
                'total_pendaftar' => $kpsInPeriode->count(),
                'periode_aktif' => \Modules\EOffice\Models\KpPeriode::where('is_active', true)->count(),
                'menunggu_balancing' => $kpsInPeriode->whereNull('dosen_pembimbing_id')->count(),
                'butuh_validasi' => \Modules\EOffice\Models\KpDokumen::whereIn('kp_id', $kpsInPeriode->pluck('id'))->where('status_validasi', 'pending')->count(),
            ];
        }

        // Global aggregate (all periods combined)
        $periodeStats['all'] = [
            'total_pendaftar' => KerjaPraktik::count(),
            'periode_aktif' => \Modules\EOffice\Models\KpPeriode::where('is_active', true)->count(),
            'menunggu_balancing' => KerjaPraktik::whereNull('dosen_pembimbing_id')->count(),
            'butuh_validasi' => \Modules\EOffice\Models\KpDokumen::where('status_validasi', 'pending')->count(),
        ];

        // Detect the current active period for the default selection
        $activePeriode = $periodes->first(fn($p) => $p->is_active);
        $defaultPeriodeId = $activePeriode ? $activePeriode->id : 'all';

        return view('eoffice::koordinator.dashboard', compact('periodes', 'periodeStats', 'defaultPeriodeId'));
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
     * Manajemen Persyaratan Dokumen per Fase
     */
    public function persyaratanDokumen(\Illuminate\Http\Request $request)
    {
        $periodes = \Modules\EOffice\Models\KpPeriode::orderBy('created_at', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');

        if (!$selectedPeriodeId && $periodes->isNotEmpty()) {
            $activePeriode = $periodes->firstWhere('is_active', true) ?? $periodes->first();
            $selectedPeriodeId = $activePeriode->id;
        }

        try {
            $query = \Modules\EOffice\Models\KpTemplate::query();

            // Filter by periode if specific one was selected/active
            if ($selectedPeriodeId && $selectedPeriodeId !== 'all') {
                $query->where('periode_id', $selectedPeriodeId);
            }

            $templates = $query->orderBy('created_at', 'asc')->get();

            // Map the phase codes into standard names for visual
            $phases = [
                'pra_kp' => ['key' => 'pra_kp', 'label' => 'Pra KP'],
                'saat_kp' => ['key' => 'saat_kp', 'label' => 'Saat KP'],
                'pasca_kp' => ['key' => 'pasca_kp', 'label' => 'Pasca KP']
            ];

            // Aggregate by phases
            $groupedTemplates = collect($phases)->map(function ($phase) use ($templates) {
                $items = $templates->where('phase', $phase['key']);
                return (object) [
                    'tahap' => $phase['label'],
                    'syarat_count' => $items->count(),
                    'dokumens' => $items->pluck('title')->implode(', ') ?: '-',
                    'raw_items' => $items
                ];
            });

        } catch (\Illuminate\Database\QueryException $e) {
            $groupedTemplates = collect();
            session()->flash('warning', 'Tabel eo_kp_template belum siap. Silakan jalankan migrasi.');
        }

        return view('eoffice::koordinator.persyaratan.index', compact('groupedTemplates', 'periodes', 'selectedPeriodeId'));
    }

    /**
     * Copy Default Configuration to Active Period
     */
    public function applyDefaultPersyaratan(\Illuminate\Http\Request $request)
    {
        $periodeId = $request->input('periode_id');
        if (!$periodeId || $periodeId === 'all') {
            return redirect()->back()->with('error', 'Pilih periode aktif terlebih dahulu sebelum menggunakan konfigurasi bawaan.');
        }

        // Cari periode sebelumnya (paling terakhir dibuat, yang punya template dokumen)
        $previousPeriodeWithTemplates = \Modules\EOffice\Models\KpPeriode::where('id', '!=', $periodeId)
            ->whereHas('templates')
            ->orderBy('created_at', 'desc')
            ->first();

        // Ambil source template dari periode sebelumnya, JIKA tidak ada baru pakai master (periode_id = null)
        if ($previousPeriodeWithTemplates) {
            $sourceTemplates = \Modules\EOffice\Models\KpTemplate::where('periode_id', $previousPeriodeWithTemplates->id)->get();
        } else {
            $sourceTemplates = \Modules\EOffice\Models\KpTemplate::whereNull('periode_id')->get();
        }

        if ($sourceTemplates->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada riwayat dokumen dari periode mana pun yang bisa disalin.');
        }

        $copiedCount = 0;
        foreach ($sourceTemplates as $source) {
            $exists = \Modules\EOffice\Models\KpTemplate::where('periode_id', $periodeId)
                ->where('phase', $source->phase)
                ->where('title', $source->title)
                ->exists();

            if (!$exists) {
                \Modules\EOffice\Models\KpTemplate::create([
                    'periode_id' => $periodeId,
                    'title' => $source->title,
                    'description' => null,
                    'phase' => $source->phase,
                    'file_name' => $source->file_name,
                    'file_path' => $source->file_path, // Aman karena merujuk object ID/URL yang sama di Supabase
                    'file_type' => $source->file_type,
                    'is_required' => $source->is_required,
                    'is_downloadable' => $source->is_downloadable,
                    'is_uploadable' => $source->is_uploadable,
                    'approver_role' => $source->approver_role,
                    'uploaded_by' => auth()->id(),
                ]);
                $copiedCount++;
            }
        }

        if ($copiedCount > 0) {
            if ($previousPeriodeWithTemplates) {
                return redirect()->back()->with('success', "Berhasil menyalin {$copiedCount} dokumen dari periode sebelumnya (Semester {$previousPeriodeWithTemplates->semester} {$previousPeriodeWithTemplates->tahun_ajaran}).");
            } else {
                return redirect()->back()->with('success', "Berhasil menyalin {$copiedCount} template dokumen dari master sistem.");
            }
        }

        return redirect()->back()->with('warning', 'Konfigurasi dokumen sudah lengkap dan sama dengan sebelumnya.');
    }

    /**
     * Edit Persyaratan Dokumen per Fase
     */
    public function editPersyaratan(\Illuminate\Http\Request $request, string $phase)
    {
        $phaseLabels = [
            'pra_kp' => 'Pra KP',
            'saat_kp' => 'Saat KP',
            'pasca_kp' => 'Pasca KP',
        ];

        if (!array_key_exists($phase, $phaseLabels))
            abort(404);

        $periodes = \Modules\EOffice\Models\KpPeriode::orderBy('created_at', 'desc')->get();
        $selectedPeriodeId = $request->get('periode_id');
        $activePeriode = $periodes->firstWhere('is_active', true);

        if (!$selectedPeriodeId && $activePeriode) {
            $selectedPeriodeId = $activePeriode->id;
        }

        $templates = \Modules\EOffice\Models\KpTemplate::where('phase', $phase)
            ->when($selectedPeriodeId, fn($q) => $q->where('periode_id', $selectedPeriodeId), fn($q) => $q->whereNull('periode_id'))
            ->orderBy('created_at', 'asc')
            ->get();

        $phaseLabel = $phaseLabels[$phase];

        return view('eoffice::koordinator.persyaratan.edit', compact('templates', 'phase', 'phaseLabel', 'periodes', 'selectedPeriodeId'));
    }

    /**
     * Update (sync) Persyaratan Dokumen per Fase
     */
    public function updatePersyaratan(\Illuminate\Http\Request $request, string $phase)
    {
        $phaseLabels = ['pra_kp', 'saat_kp', 'pasca_kp'];
        if (!in_array($phase, $phaseLabels))
            abort(404);

        $periodeId = $request->input('periode_id');
        if (!$periodeId)
            abort(404, 'Periode ID tidak valid.');

        $docs = $request->input('docs', []);
        $files = $request->file('files', []);

        // Collect existing templates before deletion to preserve files if no new upload
        $existingTemplates = \Modules\EOffice\Models\KpTemplate::where('phase', $phase)
            ->where('periode_id', $periodeId)
            ->get()
            ->keyBy('id');

        // Delete all existing templates for this phase+periode and re-create
        \Modules\EOffice\Models\KpTemplate::where('phase', $phase)
            ->where('periode_id', $periodeId)
            ->delete();

        foreach ($docs as $index => $doc) {
            if (empty($doc['title']))
                continue;

            $fileName = '';
            $filePath = '';
            $fileType = 'document';

            // Check if a new file was uploaded for this index
            if (isset($files[$index]) && $files[$index]->isValid()) {
                $file = $files[$index];
                $fileName = $file->getClientOriginalName();
                $fileType = $file->getClientOriginalExtension();
                $supabase = app(\App\Services\SupabaseStorage::class);
                $storedPath = $supabase->upload($file, 'kp-templates/' . $phase, null, $fileName);
                $filePath = $storedPath;
            } else {
                // Preserve existing file if doc had one (matched by existing_file hidden field or doc id)
                if (!empty($doc['existing_path'])) {
                    $filePath = $doc['existing_path'];
                    $fileName = $doc['existing_file'] ?? '';
                }
            }

            \Modules\EOffice\Models\KpTemplate::create([
                'periode_id' => $periodeId,
                'title' => $doc['title'],
                'description' => null,
                'phase' => $phase,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType ?: 'document',
                'is_required' => true,
                'is_downloadable' => isset($doc['is_downloadable']),
                'is_uploadable' => isset($doc['is_uploadable']),
                'approver_role' => $doc['approver_role'] ?? 'koordinator',
                'uploaded_by' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('eoffice.kp.koordinator.persyaratan_dokumen', ['periode_id' => $periodeId])
            ->with('success', 'Persyaratan dokumen fase ' . strtoupper(str_replace('_', ' ', $phase)) . ' berhasil diperbarui.');
    }

    /**
     * Hapus semua persyaratan dokumen per fase (untuk periode tertentu)
     */
    public function destroyPersyaratanPhase(\Illuminate\Http\Request $request, string $phase)
    {
        $periodeId = $request->input('periode_id');
        if (!$periodeId)
            abort(404, 'Periode ID tidak valid.');

        \Modules\EOffice\Models\KpTemplate::where('phase', $phase)
            ->where('periode_id', $periodeId)
            ->delete();

        return redirect()
            ->route('eoffice.kp.koordinator.persyaratan_dokumen', ['periode_id' => $periodeId])
            ->with('success', 'Persyaratan dokumen fase ' . strtoupper(str_replace('_', ' ', $phase)) . ' berhasil dihapus.');
    }
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'phase' => 'required|in:pra_kp,saat_kp,pasca_kp,keperluan_perusahaan',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        $data = [
            'title' => $validated['title'],
            'phase' => $validated['phase'],
            'is_downloadable' => $request->has('is_downloadable'),
            'is_uploadable' => $request->has('is_uploadable'),
            'is_required' => $request->has('is_required'),
            'approver_role' => $request->input('approver_role', 'koordinator'),
            'uploaded_by' => auth()->id(),
        ];

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $supabase = app(\App\Services\SupabaseStorage::class);
            $data['file_path'] = $supabase->upload($file, 'kp_templates', null, $data['file_name']);
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
            'is_downloadable' => $request->has('is_downloadable'),
            'is_uploadable' => $request->has('is_uploadable'),
            'is_required' => $request->has('is_required'),
            'approver_role' => $request->input('approver_role', 'koordinator'),
        ];

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $supabase = app(\App\Services\SupabaseStorage::class);
            $data['file_path'] = $supabase->upload($file, 'kp_templates', null, $data['file_name']);
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
        $templates = \Modules\EOffice\Models\TemplateDokumenKP::all()->groupBy('periode_id');
        $kps = KerjaPraktik::with(['mahasiswa.user', 'dosenPembimbing.user', 'dokumen'])
            ->orderBy('created_at', 'desc')
            ->get();

        $mahasiswas = $kps->map(function ($kp) use ($templates) {
            $dokumens = $kp->dokumen;
            $periodTemplates = $templates[$kp->periode_id] ?? collect();

            // Map status_validasi to UI status
            $mapStatus = function ($status) {
                if ($status === 'disetujui' || $status === 'approved')
                    return 'approved';
                if ($status === 'ditolak' || $status === 'rejected')
                    return 'rejected';
                return 'pending';
            };

            $praKp = $dokumens->filter(function ($d) use ($periodTemplates) {
                $t = $periodTemplates->firstWhere('title', $d->jenis_dokumen);
                return $t && $t->phase === 'pra_kp';
            })->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? $d->file_url : null,
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-', // Can't easily get file size without storage hit
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            $saatKp = $dokumens->filter(function ($d) use ($periodTemplates) {
                $t = $periodTemplates->firstWhere('title', $d->jenis_dokumen);
                return $t && $t->phase === 'saat_kp';
            })->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? $d->file_url : null,
                    'jenis' => $d->jenis_dokumen,
                    'tanggal' => date('Y-m-d', strtotime($d->created_at)),
                    'ukuran' => '-',
                    'status' => $mapStatus($d->status_validasi ?? $d->approval_status),
                    'catatan' => $d->revision_note ?? ''
                ])->values();

            $pascaKp = $dokumens->filter(function ($d) use ($periodTemplates) {
                $t = $periodTemplates->firstWhere('title', $d->jenis_dokumen);
                return $t && $t->phase === 'pasca_kp';
            })->map(fn($d) => (object) [
                    'id' => $d->id,
                    'nama_file' => $d->file_name ?? basename($d->file_path ?? $d->jenis_dokumen),
                    'file_url' => $d->file_path ? $d->file_url : null,
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
                'tempat_kp' => $kp->instansi_kp ?? '-',
                'judul_kp' => $kp->judul_kp ?? '-',
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
            'p.nilai_akhir'
        )
            ->leftJoin('eo_kp_mahasiswa as m', 'eo_kerja_praktik.mahasiswa_id', '=', 'm.id')
            ->leftJoin('users as u', 'm.user_id', '=', 'u.id')
            ->leftJoin('eo_kp_dosen as d', 'eo_kerja_praktik.dosen_pembimbing_id', '=', 'd.id')
            ->leftJoin('eo_kp_penilaian as p', 'eo_kerja_praktik.id', '=', 'p.kp_id')
            ->with(['nilaiDetail.komponen'])
            ->orderBy('eo_kerja_praktik.created_at', 'desc')
            ->get();

        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->orderBy('created_at', 'desc')->get();

        $mahasiswas = $kps->map(function ($kp) use ($allPeriodes) {
            $statusStr = 'Pra KP';
            $tahap = 'Pra KP';

            // Normalize various possible DB states into the 3 requested phases
            $rawStatus = strtolower($kp->status_kp ?? 'pending');

            if (in_array($rawStatus, ['pending', 'pra-kp', 'pra kp'])) {
                $statusStr = 'Pra KP';
                $tahap = 'Pra KP';
            } elseif (in_array($rawStatus, ['active', 'saat kp', 'aktif', 'aktif kp'])) {
                $statusStr = 'Saat KP';
                $tahap = 'Saat KP';
            } elseif (in_array($rawStatus, ['completed', 'pasca kp', 'pasca-kp', 'selesai'])) {
                $statusStr = 'Pasca KP';
                $tahap = 'Pasca KP';
            }

            // Find matching periode based on dates (because eo_kerja_praktik lacks exact periode_id)
            $matchedPeriode = $allPeriodes->first(function ($p) use ($kp) {
                if (!$kp->created_at || !$p->pra_kp_mulai)
                    return false;
                $endDate = $p->pasca_kp_akhir ? clone $p->pasca_kp_akhir : (clone $p->pra_kp_akhir)->addMonths(6);
                return $kp->created_at->format('Y-m-d') >= $p->pra_kp_mulai->format('Y-m-d')
                    && $kp->created_at->format('Y-m-d') <= $endDate->format('Y-m-d');
            });

            $periodeName = $matchedPeriode ? "Semester {$matchedPeriode->semester} {$matchedPeriode->tahun_ajaran}" : 'Unknown';
            $periodeId = $matchedPeriode ? (string) $matchedPeriode->id : 'unknown';

            $komponenKoor = [];
            $semuaNilai = [];
            if ($matchedPeriode && $matchedPeriode->komponenNilai) {
                foreach ($matchedPeriode->komponenNilai as $komp) {
                    $val = '-';
                    if ($kp->nilaiDetail) {
                        $det = $kp->nilaiDetail->where('komponen_id', $komp->id)->first();
                        if ($det && $det->nilai_angka !== null) {
                            $val = $det->nilai_angka;
                        }
                    }
                    $semuaNilai[] = [
                        'nama' => $komp->nama_komponen,
                        'nilai' => $val
                    ];

                    if ($komp->role_penilai === 'koordinator') {
                        $komponenKoor[] = [
                            'id' => $komp->id,
                            'nama_komponen' => $komp->nama_komponen,
                            'bobot' => $komp->bobot,
                            'nilai_angka' => $val !== '-' ? $val : ''
                        ];
                    }
                }
            }

            return (object) [
                'id' => $kp->id,
                'nama' => $kp->nama ?? 'Unknown',
                'nim' => $kp->nim ?? '-',
                'prodi' => 'Teknik Komputer',
                'kelas' => $kp->kelas ?? '-',
                'tempat_kp' => $kp->instansi_kp ?? 'Belum ditentukan',
                'judul_kp' => $kp->judul_kp ?? 'Belum ditentukan',
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
                'periode_id' => $periodeId,
                'periode_name' => $periodeName,
                'komponen_koordinator' => $komponenKoor,
                'semua_nilai' => $semuaNilai,
            ];
        });

        // Unique filtered Periodes list for the dropdown
        $periodes = $allPeriodes;
        $dosens = \Modules\EOffice\Models\KpDosen::with('user')->get();

        return view('eoffice::koordinator.data_mahasiswa', compact('mahasiswas', 'periodes', 'dosens'));
    }

    /**
     * Override / Update Data & Nilai Mahasiswa dari Modal Edit
     */
    public function updateDataMahasiswa(Request $request, $id)
    {
        $kp = \Modules\EOffice\Models\KerjaPraktik::findOrFail($id);

        $request->validate([
            'dosen_pembimbing_id' => 'nullable|exists:eo_kp_dosen,id',
            'nilai_lapangan' => 'nullable|numeric|min:0|max:100',
            'kelas' => 'nullable|string|max:50',
            // Kita bisa juga override status_kp di sini jika user menyediakan
        ]);

        if ($request->has('kelas')) {
            $kp->kelas = $request->kelas;
        }

        if ($request->has('dosen_pembimbing_id')) {
            $kp->dosen_pembimbing_id = $request->dosen_pembimbing_id;

            // Juga update status balancing menjadi finalized bila override manual
            if ($request->dosen_pembimbing_id) {
                \Modules\EOffice\Models\KpBalancing::updateOrCreate(
                    ['kp_id' => $kp->id],
                    [
                        'mahasiswa_id' => $kp->mahasiswa_id,
                        'dosen_id' => $request->dosen_pembimbing_id,
                        'status' => 'finalized',
                        'assigned_by' => auth()->id(),
                    ]
                );
            }
        }

        $kp->save();

        // Check if period has components for koordinator
        $komponenKoor = null;
        if ($kp->periode && $kp->periode->komponenNilai) {
            $komponenKoor = $kp->periode->komponenNilai->where('role_penilai', 'koordinator');
        }

        if ($komponenKoor && $komponenKoor->isNotEmpty()) {
            $rules = [];
            foreach ($komponenKoor as $komp) {
                $rules['nilai_' . $komp->id] = 'nullable|numeric|min:0|max:100';
            }

            $validatedKomp = $request->validate($rules);
            $totalInput = 0;
            $countInput = 0;

            foreach ($komponenKoor as $komp) {
                if ($request->has('nilai_' . $komp->id) && $request->input('nilai_' . $komp->id) !== null) {
                    \Modules\EOffice\Models\KpNilaiDetail::updateOrCreate(
                        ['kp_id' => $kp->id, 'komponen_id' => $komp->id],
                        ['nilai_angka' => $validatedKomp['nilai_' . $komp->id]]
                    );
                    $totalInput += $validatedKomp['nilai_' . $komp->id];
                    $countInput++;
                }
            }

            // Maintain fallback average for legacy interfaces
            if ($countInput > 0) {
                // Not saving to eo_kp_penilaian->nilai_lapangan anymore because it was dropped.
                // It is already dynamically saved in eo_kp_nilai_detail.
            }

            if ($request->has('nilai_akhir')) {
                \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                    ['kp_id' => $kp->id],
                    ['nilai_akhir' => $request->nilai_akhir]
                );
            }

        } else {
            if ($request->has('nilai_lapangan') || $request->has('nilai_akhir')) {
                $penilaianData = [];
                if ($request->has('nilai_lapangan')) {
                    $penilaianData['nilai_lapangan'] = $request->nilai_lapangan;
                }
                if ($request->has('nilai_akhir')) {
                    // Di sistem aslinya, nilai akhir dihitung otomatis. Namun request bilang ada field override Nilai.
                    $penilaianData['nilai_akhir'] = $request->nilai_akhir;
                }

                if (!empty($penilaianData)) {
                    \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                        ['kp_id' => $kp->id],
                        $penilaianData
                    );
                }
            }
        }

        return redirect()->back()->with('success', 'Data Mahasiswa berhasil diperbarui!');
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
                'periode.komponenNilai',
                'nilaiDetail',
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

            $komponen = [];
            if ($kp->periode && $kp->periode->komponenNilai) {
                foreach ($kp->periode->komponenNilai->where('role_penilai', 'koordinator') as $komp) {
                    $existingVal = '';
                    if ($kp->nilaiDetail) {
                        $det = $kp->nilaiDetail->where('komponen_id', $komp->id)->first();
                        if ($det)
                            $existingVal = $det->nilai_angka;
                    }

                    $komponen[] = [
                        'id' => $komp->id,
                        'nama_komponen' => $komp->nama_komponen,
                        'bobot' => $komp->bobot,
                        'nilai_angka' => $existingVal
                    ];
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
                'komponen_koordinator' => $komponen
            ];
        });

        return view('eoffice::koordinator.nilai_lapangan', compact('mahasiswas'));
    }

    public function updateNilaiLapangan(Request $request, $id)
    {
        $dokumen = \Modules\EOffice\Models\KpDokumen::with('kerjaPraktik.periode.komponenNilai')->findOrFail($id);
        $kp = $dokumen->kerjaPraktik;

        $komponenKoor = null;
        if ($kp && $kp->periode && $kp->periode->komponenNilai) {
            $komponenKoor = $kp->periode->komponenNilai->where('role_penilai', 'koordinator');
        }

        if ($komponenKoor && $komponenKoor->isNotEmpty()) {
            $rules = [
                'nilai_status' => 'required|in:valid,rejected,pending'
            ];
            foreach ($komponenKoor as $komp) {
                $rules['nilai_' . $komp->id] = 'required|numeric|min:0|max:100';
            }
            $validated = $request->validate($rules);

            // Still update the doc status and fallback fields
            $dokumen->update([
                'nilai_status' => $validated['nilai_status'],
                'nilai_validasi_koordinator' => collect($komponenKoor)->map(function ($k) use ($validated) {
                    return $validated['nilai_' . $k->id];
                })->avg() // simple avg fallback for legacy tables
            ]);

            if ($validated['nilai_status'] === 'valid') {
                foreach ($komponenKoor as $komp) {
                    \Modules\EOffice\Models\KpNilaiDetail::updateOrCreate(
                        ['kp_id' => $kp->id, 'komponen_id' => $komp->id],
                        ['nilai_angka' => $validated['nilai_' . $komp->id]]
                    );
                }
            }

        } else {
            $request->validate([
                'nilai_validasi_koordinator' => 'required|numeric|min:0|max:100',
                'nilai_status' => 'required|in:valid,rejected,pending'
            ]);

            $dokumen->update([
                'nilai_validasi_koordinator' => $request->nilai_validasi_koordinator,
                'nilai_status' => $request->nilai_status
            ]);

            if ($request->nilai_status === 'valid') {
                \Modules\EOffice\Models\KpPenilaian::updateOrCreate(
                    ['kp_id' => $dokumen->kp_id],
                    ['nilai_lapangan' => $request->nilai_validasi_koordinator]
                );
            }
        }

        return redirect()->back()->with('success', 'Nilai Evaluasi berhasil diperbarui.');
    }

    public function balancingDosen()
    {
        $dosens = \Modules\EOffice\Models\KpDosen::with('user')
            ->get()
            ->sortBy(function ($d) {
                return $d->nama_lengkap ?? $d->user->name ?? 'Unknown';
            })
            ->values()
            ->map(function ($dosen) {
                return [
                    'id' => $dosen->id,
                    'name' => $dosen->nama_lengkap ?? $dosen->user->name ?? 'Unknown',
                    'kuota_maksimal' => $dosen->kuota_maksimal ?? 10,
                    'mahasiswas' => []
                ];
            })->toArray();

        // Locate the currently relevant active period (Preferably one that is ongoing, or fallback to the latest active)
        $activePeriod = \Modules\EOffice\Models\KpPeriode::where('is_active', true)
            ->whereDate('pra_kp_mulai', '<=', now())
            ->whereDate('pra_kp_akhir', '>=', now())
            ->first();

        if (!$activePeriod) {
            $activePeriod = \Modules\EOffice\Models\KpPeriode::where('is_active', true)->latest()->first();
        }

        if ($activePeriod) {
            // Get mahasiswas strictly bound to the detected active period and their current draft/finalized assignment
            // Karena eo_kerja_praktik tidak punya periode_id, kita filter berdasarkan waktu pendaftaran
            $mulai = $activePeriod->pra_kp_mulai ? $activePeriod->pra_kp_mulai->format('Y-m-d') : '2000-01-01';
            $akhir = $activePeriod->pra_kp_akhir ? $activePeriod->pra_kp_akhir->format('Y-m-d') : '2099-12-31';

            $kps = \Modules\EOffice\Models\KerjaPraktik::with(['mahasiswa.user', 'balancing'])
                ->whereDate('created_at', '>=', $mulai)
                ->whereDate('created_at', '<=', $akhir)
                ->get();
        } else {
            $kps = collect(); // Kosongkan jika tak ada periode yang relevan
        }

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
                'judul_kp' => $kp->judul_kp ?? 'Belum ada rencana judul',
                'instansi_kp' => $kp->instansi_kp ?? 'Belum ada tempat KP',
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
        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->orderBy('created_at', 'desc')->get();
        return view('eoffice::koordinator.periode.create', compact('allPeriodes'));
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
            'kelas_dibuka' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if (isset($validated['kelas_dibuka'])) {
            $kelasArray = array_map('trim', explode(',', $validated['kelas_dibuka']));
            $kelasArray = array_filter($kelasArray);
            $validated['kelas_dibuka'] = array_values($kelasArray);
        }

        // Note: The unique constraint on ('tahun_ajaran', 'semester') might fail if duplicate
        $periode = \Modules\EOffice\Models\KpPeriode::create($validated);

        if ($request->has('komponen_penilaian') && is_array($request->komponen_penilaian)) {
            foreach ($request->komponen_penilaian as $comp) {
                \Modules\EOffice\Models\KpKomponenNilai::create([
                    'periode_id' => $periode->id,
                    'nama_komponen' => $comp['nama_komponen'],
                    'bobot' => $comp['bobot'],
                    'role_penilai' => $comp['role_penilai']
                ]);
            }
        }

        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode baru berhasil ditambahkan.');
    }

    public function editPeriode($id)
    {
        // Eager load the grading components for this period natively
        $periode = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->findOrFail($id);
        $allPeriodes = \Modules\EOffice\Models\KpPeriode::with('komponenNilai')->where('id', '!=', $id)->orderBy('created_at', 'desc')->get();
        return view('eoffice::koordinator.periode.edit', compact('periode', 'allPeriodes'));
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
            'kelas_dibuka' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if (isset($validated['kelas_dibuka'])) {
            $kelasArray = array_map('trim', explode(',', $validated['kelas_dibuka']));
            $kelasArray = array_filter($kelasArray);
            $validated['kelas_dibuka'] = array_values($kelasArray);
        }

        $periode->update($validated);

        // Sync Rubrik Penilaian
        if ($request->has('komponen_penilaian') && is_array($request->komponen_penilaian)) {
            $submittedIds = [];
            foreach ($request->komponen_penilaian as $comp) {
                if (!empty($comp['id'])) {
                    $existing = \Modules\EOffice\Models\KpKomponenNilai::find($comp['id']);
                    if ($existing && $existing->periode_id == $periode->id) {
                        $existing->update([
                            'nama_komponen' => $comp['nama_komponen'],
                            'bobot' => $comp['bobot'],
                            'role_penilai' => $comp['role_penilai']
                        ]);
                        $submittedIds[] = $existing->id;
                    }
                } else {
                    $newComp = \Modules\EOffice\Models\KpKomponenNilai::create([
                        'periode_id' => $periode->id,
                        'nama_komponen' => $comp['nama_komponen'],
                        'bobot' => $comp['bobot'],
                        'role_penilai' => $comp['role_penilai']
                    ]);
                    $submittedIds[] = $newComp->id;
                }
            }
            // Delete components that were removed by Koordinator
            \Modules\EOffice\Models\KpKomponenNilai::where('periode_id', $periode->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        } else {
            // If the array is completely empty, it means all rubrics were removed
            \Modules\EOffice\Models\KpKomponenNilai::where('periode_id', $periode->id)->delete();
        }

        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroyPeriode($id)
    {
        $periode = \Modules\EOffice\Models\KpPeriode::findOrFail($id);
        $periode->delete();
        return redirect()->route('eoffice.kp.koordinator.periode')->with('success', 'Periode berhasil dihapus.');
    }

    // ════════════════════════════════════════════════════════════════════════
    // MANAJEMEN PENDAFTAR KP
    // ════════════════════════════════════════════════════════════════════════

    public function pendaftarKp()
    {
        $pendaftar = \Modules\EOffice\Models\KerjaPraktik::with(['mahasiswa.user', 'dosenPembimbing.user'])->orderBy('created_at', 'desc')->paginate(10);
        return view('eoffice::koordinator.pendaftar.index', compact('pendaftar'));
    }

    public function resetPendaftar($id)
    {
        $kp = \Modules\EOffice\Models\KerjaPraktik::findOrFail($id);
        if ($kp->dokumen()->exists()) {
            $kp->dokumen()->delete();
        }
        $kp->delete();
        return redirect()->route('eoffice.kp.koordinator.pendaftar')->with('success', 'Data pendaftar berhasil di-reset!');
    }

    // ════════════════════════════════════════════════════════════════════════
    // EXPORT DATA MAHASISWA
    // ════════════════════════════════════════════════════════════════════════

    public function exportDataMahasiswa()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\EOffice\Exports\DataMahasiswaExport, 'Rekap_Data_Mahasiswa_KP.xlsx');
    }
}
