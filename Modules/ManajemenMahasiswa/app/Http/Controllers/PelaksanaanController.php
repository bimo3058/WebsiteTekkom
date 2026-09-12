<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

class PelaksanaanController extends Controller
{
    /** Batas jumlah foto & dokumen yang boleh tersimpan pada satu kegiatan. */
    private const MAKS_FILE = 10;

    public function __construct(
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar proker yang siap/sedang/sudah dilaksanakan
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bidangList = Bidang::orderBy('nama_bidang')->get();
        // Ikut menghitung kegiatan yang kolom `tahun`-nya belum terisi lewat
        // tanggal mulainya — lihat Kegiatan::daftarTahun().
        $tahunList = Kegiatan::daftarTahun(Kegiatan::STATUS_DISETUJUI);
        // Opsi "Belum ada tanggal" hanya dirender bila memang ada isinya.
        $adaTanpaTahun = Kegiatan::where('status', Kegiatan::STATUS_DISETUJUI)->tanpaTahun()->exists();

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->where('status', Kegiatan::STATUS_DISETUJUI)
            ->orderBy('created_at', 'desc');

        // Filter bidang
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                // Selaras dengan filter Prodi di Rencana Proker: tanpa bidang ATAU berkategori Prodi
                $query->where(function ($q) {
                    $q->whereDoesntHave('bidangs')
                      ->orWhereHas('kategoris', fn($k) => $k->where('nama_kategori', 'like', '%Prodi%'));
                });
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }

        // Filter tahun — pakai scope supaya kegiatan ber-`tahun` NULL tetap
        // ketemu lewat tahun pada tanggal mulainya, bukan hilang dari daftar.
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            if ($request->tahun === Kegiatan::FILTER_TANPA_TAHUN) {
                $query->tanpaTahun();
            } else {
                $query->filterTahun($request->tahun);
            }
        }

        // Search judul + deskripsi
        // Dibungkus closure supaya orWhere tidak "membocorkan" filter bidang/tahun di atas.
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('judul', 'like', $term)
                  ->orWhere('deskripsi', 'like', $term);
            });
        }

        $pelaksanaanList = $query->paginate(12);


        return view('manajemenmahasiswa::pelaksanaan.index', compact(
            'pelaksanaanList', 'bidangList', 'tahunList', 'adaTanpaTahun',
            'isAdmin', 'isPengurus', 'canManage'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator', 'disetujuiOleh',
            'repoMulmed',
        ])->find($id);

        // 'selesai' tetap boleh DILIHAT agar halaman detail masih bisa dibuka
        // setelah diarsipkan — berbeda dari edit/update yang dibatasi 'disetujui'.
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [
            Kegiatan::STATUS_DISETUJUI,
            Kegiatan::STATUS_SELESAI,
        ])) {
            return $tolak;
        }

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only
        // Anggaran & Dokumen tampil untuk SEMUA role kecuali mahasiswa & alumni murni.
        // (denylist agar konsisten dengan halaman Arsip dan tidak ada role pengelola yang terlewat — mis. DPM)
        $canViewRestricted = $roles->diff(['mahasiswa', 'alumni'])->isNotEmpty();
        // Hanya role tertentu yang boleh menekan "Unggah ke Arsip"
        // (staff_himpunan TIDAK termasuk, dosen DPM/GPM juga tidak — hanya pengurus inti + admin)
        $canArsip = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        // Hak hapus pelaksanaan: admin + ketua-ketua himpunan (GPM, Kadep & DPM view-only)
        $canDelete = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        // Lapis kedua di belakang role: kegiatan hanya boleh diubah pemiliknya,
        // pengelola yang ia tunjuk, atau override (KegiatanPolicy).
        $bolehUbah = Gate::allows('update', $proker);
        $pesanBukanPengelola = $canManage && !$bolehUbah
            ? $this->pengelolaService->pesanTolak($proker)
            : null;
        $canManage = $canManage && $bolehUbah;
        $canArsip  = $canArsip && $bolehUbah;
        $canDelete = $canDelete && Gate::allows('delete', $proker);

        $images    = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'image') : collect();
        $documents = $proker->repoMulmed ? $proker->repoMulmed->where('tipe_file', 'document') : collect();

        return view('manajemenmahasiswa::pelaksanaan.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'canManage', 'canArsip', 'canDelete',
            'canViewRestricted', 'images', 'documents', 'pesanBukanPengelola'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit — form edit data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator',
            'repoMulmed',
        ])->find($id);

        // Hanya tahap Pelaksanaan. Kegiatan yang sudah diarsipkan diubah dari
        // subbab Arsip — lihat catatan wewenang di tolakBilaStatusTakSesuai().
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        $canManage = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty() || $isPengurus; // GPM, Kadep & DPM view-only

        if (!$canManage) {
            abort(403, 'Akses ditolak.');
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $tahunList    = Kegiatan::select('tahun')->whereNotNull('tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingFoto    = $proker->repoMulmed->where('tipe_file', 'image');
        $existingDokumen = $proker->repoMulmed->where('tipe_file', 'document');
        $existingPanitia = $proker->panitia ?? collect();

        $selectedKategoriIds = old('kategori_kegiatan_id', $proker->kategoris->pluck('id')->toArray());
        $selectedBidangIds   = old('bidang_id', $proker->bidangs->pluck('id')->toArray());
        $existingPanitiaIds  = old('panitia_ids', $existingPanitia->pluck('id')->toArray());
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosenIds    = old('dosen_pendamping_ids', $proker->dosenPendampings->pluck('id')->toArray());
        $existingDosen       = $dosenList->whereIn('id', $existingDosenIds);

        return view('manajemenmahasiswa::pelaksanaan.edit', compact(
            'proker', 'bidangList', 'kategoriList', 'tahunList',
            'mahasiswaList', 'dosenList',
            'existingFoto', 'existingDokumen',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds',
            'isAdmin', 'isPengurus', 'canManage'
        ) + $this->pengelolaService->dataForm($proker));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Update — simpan perubahan data pelaksanaan
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::find($id);

        // Sinkron dengan edit(): kegiatan yang sudah diarsipkan tidak boleh
        // ditulis dari sini, termasuk oleh permintaan PUT langsung.
        if ($tolak = $this->tolakBilaStatusTakSesuai($proker, [Kegiatan::STATUS_DISETUJUI])) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        // Bidang wajib kecuali seluruh kategori yang dipilih berkategori Prodi —
        // aturan yang sama persis dengan Rencana Proker. Sebelumnya di sini
        // `bidang_id` selalu nullable, sehingga kegiatan "Kegiatan Himpunan" bisa
        // disimpan tanpa bidang lalu tampil berlabel "Prodi" di kartu & detailnya,
        // padahal aksi yang sama ditolak di Subbab 1.
        $kategoriDipilih = $request->input('kategori_kegiatan_id', []);
        $isOnlyProdi = is_array($kategoriDipilih) && Kegiatan::hanyaKategoriProdi($kategoriDipilih);
        $bidangRule  = $isOnlyProdi ? 'nullable|array' : 'required|array|min:1';

        // Jam selesai hanya dibandingkan dengan jam mulai bila kegiatan berlangsung
        // dalam SATU hari — kegiatan lintas hari wajar berakhir di jam yang lebih awal.
        $satuHari = !$request->filled('tanggal_selesai')
            || $request->input('tanggal_selesai') === $request->input('tanggal_mulai');

        $jamSelesaiRules = ['nullable', 'date_format:H:i,H:i:s'];
        if ($satuHari && $request->filled('jam_mulai')) {
            $jamSelesaiRules[] = 'after:jam_mulai';
        }

        $validated = $request->validate([
            'judul'                     => 'required|string|max:255',
            // Disamakan dengan aturan di Laporan & Arsip agar kegiatan yang diarsipkan
            // selalu lolos validasi saat diedit di subbab Arsip (hindari "jebakan" validasi).
            'kategori_kegiatan_id'      => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*'    => 'integer|exists:mk_kategori_kegiatan,id',
            'bidang_id'                 => $bidangRule,
            'bidang_id.*'               => 'integer|exists:mk_bidang,id',
            'deskripsi'                 => 'required|string|min:20',
            'tanggal_mulai'             => 'required|date',
            'tanggal_selesai'           => 'nullable|date|after_or_equal:tanggal_mulai',
            // Kolom jam di database bertipe TIME: `string` bebas membuat isian ngawur
            // lolos ke query dan memunculkan halaman error saat disimpan.
            'jam_mulai'                 => 'nullable|date_format:H:i,H:i:s',
            'jam_selesai'               => $jamSelesaiRules,
            'lokasi'                    => 'nullable|string|max:255',
            'target_peserta'            => 'nullable|integer|min:1',
            'anggaran'                  => 'nullable|numeric|min:0',
            'ketua_pelaksana_id'        => 'nullable|exists:students,id',
            'dosen_pendamping_ids'      => 'nullable|array',
            'dosen_pendamping_ids.*'    => 'exists:lecturers,id',
            'panitia_ids'               => 'nullable|array',
            'panitia_ids.*'             => 'exists:students,id',
            'panitia_peran'             => 'nullable|array',
            'panitia_peran.*'           => 'nullable|string|max:255',
            'banner'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'foto_kegiatan'             => 'nullable|array|max:10',
            'foto_kegiatan.*'           => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'dokumen_kegiatan'          => 'nullable|array|max:10',
            'dokumen_kegiatan.*'        => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'hapus_file'                => 'nullable|array',
            'hapus_file.*'              => 'integer|exists:mk_repo_mulmed,id',
        ], [
            // Pesan bawaan Laravel masih berbahasa Inggris dan menyebut nama kolom mentah.
            'bidang_id.required'             => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'bidang_id.min'                  => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'tanggal_mulai.required'         => 'Tanggal mulai wajib diisi. Kegiatan yang sudah masuk tahap pelaksanaan harus punya tanggal pasti supaya bisa muncul di filter Tahun dan diarsipkan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
        ]);

        // Batas 10 file berlaku untuk TOTAL yang tersimpan, bukan per sekali simpan.
        $this->pastikanKuotaFile($request, $proker);

        // Update data utama
        $proker->update([
            'judul'              => $validated['judul'],
            'deskripsi'          => $validated['deskripsi'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            // Isi kolom `tahun` dari tahun tanggal mulai. Tanpa ini, kegiatan hasil
            // alur himpunan (Proker → Pelaksanaan → Arsip) selalu ber-`tahun` NULL
            // sehingga hilang dari filter tahun di Pelaksanaan & Laporan/Arsip.
            'tahun'              => \Carbon\Carbon::parse($validated['tanggal_mulai'])->year,
            'tanggal_selesai'    => $validated['tanggal_selesai'] ?? null,
            'jam_mulai'          => $validated['jam_mulai'] ?? null,
            'jam_selesai'        => $validated['jam_selesai'] ?? null,
            'lokasi'             => $validated['lokasi'] ?? null,
            'target_peserta'     => $validated['target_peserta'] ?? null,
            'anggaran'           => $validated['anggaran'] ?? null,
            'ketua_pelaksana_id' => $validated['ketua_pelaksana_id'] ?? null,
            // Penanda bahwa data pelaksanaan sudah pernah diperbarui — ini yang
            // membuka tombol "Unggah ke Arsip" di halaman detail. Naik saat form
            // Pelaksanaan disimpan; sekali true tidak diturunkan lagi.
            'is_pelaksanaan_updated' => true,
        ]);

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $proker->update(['penanggung_jawab' => $student?->user?->name]);
        } else {
            $proker->update(['penanggung_jawab' => null]);
        }

        // Sync kategori & bidang (pivot tables)
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds = $validated['bidang_id'] ?? [];

        if (!empty($kategoriIds)) {
            $proker->kategoris()->sync($kategoriIds);
        } else {
            // Simetris dengan bidang: kosongkan pivot bila tak ada kategori dipilih
            // (mencegah kategori lama "nyangkut" sementara kolom FK sudah null).
            $proker->kategoris()->detach();
        }
        if (!empty($bidangIds)) {
            $proker->bidangs()->sync($bidangIds);
        } else {
            $proker->bidangs()->detach();
        }

        // Sync backward-compat FK columns (first item) — agar view lama tetap konsisten
        $proker->update([
            'kategori_kegiatan_id' => $kategoriIds[0] ?? null,
            'bidang_id'            => $bidangIds[0] ?? null,
        ]);

        // Sync panitia
        $panitiaIds = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $pid) {
            $panitiaSyncData[$pid] = ['peran' => $panitiaPeran[$pid] ?? null];
        }
        $proker->panitia()->sync($panitiaSyncData);

        // Sync dosen pendamping (many-to-many)
        $proker->dosenPendampings()->sync($validated['dosen_pendamping_ids'] ?? []);

        // Daftar pengelola — hanya diproses bila penyimpan berhak mengatur akses
        $this->pengelolaService->sync($proker, $request);

        // Upload banner — simpan ke kolom `banner` di mk_kegiatan (bukan repo_mulmed)
        if ($request->hasFile('banner')) {
            // Hapus banner lama jika ada
            if ($proker->banner) {
                $this->supabase->delete($proker->banner);
            }
            $proker->update([
                'banner' => $this->supabase->upload($request->file('banner'), 'mk_mulmed/image'),
            ]);
        }

        // Handle file deletions
        if ($request->filled('hapus_file')) {
            foreach ($request->hapus_file as $fileId) {
                $file = RepoMulmed::where('kegiatan_id', $proker->id)->find($fileId);
                if ($file) {
                    $this->repoMulmedService->deletePermanent($file->id);
                }
            }
        }

        $this->handleFileUploads($request, $proker);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('success', 'Data pelaksanaan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Publish ke Arsip — pindahkan kegiatan ke subbab 3 (status = selesai)
    // ─────────────────────────────────────────────────────────────────────────

    public function publishToArsip($id)
    {
        // Proteksi backend: sinkron dengan route middleware + $canArsip di show()
        // GPM & DPM adalah view-only — TIDAK boleh melakukan arsip
        $allowedRoles = [
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ];
        $userRoles = Auth::user()->roles->pluck('name');
        $canArsip = $userRoles->intersect($allowedRoles)->isNotEmpty();

        if (!$canArsip) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengunggah kegiatan ke arsip.');
        }

        // Dicari by id dulu, status diperiksa sesudahnya. Kalau findOrFail langsung
        // dibatasi STATUS_DISETUJUI, permintaan publish kedua — klik ganda pada modal,
        // atau tombol Back lalu klik lagi — melempar ModelNotFoundException dan user
        // dibuang ke halaman error, padahal kegiatannya justru sudah berhasil masuk arsip.
        $proker = Kegiatan::findOrFail($id);

        if ($proker->status === Kegiatan::STATUS_SELESAI) {
            return redirect()
                ->route('manajemenmahasiswa.kegiatan.show', $proker->id)
                ->with('success', 'Kegiatan ini sudah berada di Laporan & Arsip.');
        }

        if ($proker->status !== Kegiatan::STATUS_DISETUJUI) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Hanya kegiatan yang berada di tahap Pelaksanaan yang bisa diunggah ke arsip.');
        }

        // Mengunggah ke arsip = mengubah status kegiatan, jadi butuh hak ubah atas kegiatan ini.
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        if (!$proker->is_pelaksanaan_updated) {
            return redirect()
                ->back()
                ->with('error', 'Silakan edit/update data pelaksanaan kegiatan terlebih dahulu sebelum mengunggah ke arsip.');
        }

        // Banner wajib diisi sebelum kegiatan boleh diunggah ke arsip (subbab 3).
        if (empty($proker->banner)) {
            return redirect()
                ->back()
                ->with('error', 'Banner kegiatan wajib diunggah terlebih dahulu sebelum mengunggah ke arsip.');
        }

        $proker->update(['status' => Kegiatan::STATUS_SELESAI]);

        // Konsisten dengan alur subbab 1 → 2 (ajukan() yang mengarahkan ke
        // pelaksanaan.index): setelah unggah ke arsip, arahkan ke daftar
        // subbab 3 (Laporan & Arsip), bukan kembali ke detail pelaksanaan.
        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diunggah ke Laporan & Arsip.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Hapus — Hapus pelaksanaan kegiatan (termasuk foto/dokumen)
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        // Hanya kegiatan berstatus 'disetujui' yang boleh dihapus dari Pelaksanaan.
        // Kegiatan yang sudah diarsipkan (selesai) harus dihapus dari subbab Arsip.
        //
        // Statusnya diperiksa SETELAH pencarian by id — sama seperti publishToArsip():
        // dengan findOrFail yang dibatasi status, klik Hapus kedua kali (mis. setelah
        // tombol Back) berakhir di halaman error alih-alih pesan yang menjelaskan.
        $proker = Kegiatan::with('repoMulmed')->findOrFail($id);

        if ($proker->status !== Kegiatan::STATUS_DISETUJUI) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', $proker->status === Kegiatan::STATUS_SELESAI
                    ? 'Kegiatan ini sudah diarsipkan, jadi penghapusannya dilakukan dari subbab Laporan & Arsip.'
                    : 'Hanya kegiatan yang berada di tahap Pelaksanaan yang bisa dihapus dari halaman ini.');
        }

        if ($tolak = $this->tolakBilaBukanPengelola($proker, 'delete')) {
            return $tolak;
        }

        if ($proker->banner) {
            $this->supabase->delete($proker->banner);
        }
        if ($proker->surat_proker) {
            $this->supabase->delete($proker->surat_proker);
        }

        // Hapus semua file foto & dokumen dari repo
        if ($proker->repoMulmed) {
            foreach ($proker->repoMulmed as $file) {
                $this->repoMulmedService->deletePermanent($file->id);
            }
        }

        $proker->delete();

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', 'Data pelaksanaan kegiatan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pastikan kegiatan memang berada di tahap yang ditangani halaman ini.
     *
     * Dua alasan:
     *
     * 1. Wewenang. Halaman Pelaksanaan boleh diakses staff_himpunan, sedangkan
     *    Laporan & Arsip TIDAK. Selama edit()/update() masih menerima status
     *    'selesai', staff_himpunan bisa membuka /pelaksanaan/{id}/edit untuk
     *    kegiatan yang sudah diarsipkan dan mengubah judul, tanggal, anggaran,
     *    bahkan menghapus foto & dokumen arsipnya — persis yang dilarang lewat
     *    /kegiatan/{id}/edit. Karena itu edit & update dibatasi ke 'disetujui'
     *    saja; kegiatan yang sudah diarsipkan diubah dari subbab Arsip.
     *
     * 2. Pesan, bukan halaman error. findOrFail yang dibatasi status melempar
     *    ModelNotFoundException untuk id yang statusnya belum/sudah lewat tahap
     *    ini, sehingga user yang mengetik URL atau menekan Back mendarat di
     *    halaman error tanpa keterangan.
     *
     * @param array<int, string> $statusDiizinkan
     * @return RedirectResponse|null null bila kegiatan boleh diproses di sini
     */
    private function tolakBilaStatusTakSesuai(?Kegiatan $proker, array $statusDiizinkan): ?RedirectResponse
    {
        if ($proker && in_array($proker->status, $statusDiizinkan, true)) {
            return null;
        }

        if ($proker === null) {
            return redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Kegiatan yang Anda buka sudah tidak ada — kemungkinan sudah dihapus lebih dulu.');
        }

        return match ($proker->status) {
            Kegiatan::STATUS_DRAFT => redirect()
                ->route('manajemenmahasiswa.proker.show', $proker->id)
                ->with('error', 'Kegiatan ini masih berada di tahap Rencana Proker, jadi belum bisa dikelola dari halaman Pelaksanaan.'),
            Kegiatan::STATUS_SELESAI => redirect()
                ->route('manajemenmahasiswa.kegiatan.show', $proker->id)
                ->with('error', 'Kegiatan ini sudah diarsipkan. Perubahan datanya kini dilakukan dari subbab Laporan & Arsip.'),
            default => redirect()
                ->route('manajemenmahasiswa.pelaksanaan.index')
                ->with('error', 'Kegiatan ini tidak berada di tahap Pelaksanaan.'),
        };
    }

    /**
     * Role saja tidak cukup: kegiatan hanya boleh diubah/dihapus pemiliknya,
     * pengelola yang ia tunjuk, atau override (lihat KegiatanPolicy).
     *
     * @param string $aksi 'update' atau 'delete'
     * @return RedirectResponse|null null bila boleh
     */
    private function tolakBilaBukanPengelola(Kegiatan $proker, string $aksi = 'update'): ?RedirectResponse
    {
        if (Gate::allows($aksi, $proker)) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.show', $proker->id)
            ->with('error', $this->pengelolaService->pesanTolak($proker));
    }

    /**
     * Batas foto & dokumen berlaku untuk TOTAL file yang tersimpan pada kegiatan.
     *
     * Aturan `max:10` di validasi hanya membatasi satu kali unggah, sehingga user
     * bisa mengunggah 10 foto → Simpan → buka Edit → unggah 10 lagi, berulang tanpa
     * batas. Di sini sisa file lama (setelah dikurangi yang ditandai hapus pada
     * form yang sama) dijumlahkan dengan file baru, dan diperiksa sebelum ada satu
     * pun file yang benar-benar diunggah ke penyimpanan.
     *
     * @throws ValidationException
     */
    private function pastikanKuotaFile(Request $request, Kegiatan $proker): void
    {
        $akanDihapus = collect($request->input('hapus_file', []))->map(fn($fileId) => (int) $fileId);

        $sisaFileLama = fn(string $tipe) => $proker->repoMulmed
            ->where('tipe_file', $tipe)
            ->reject(fn($file) => $akanDihapus->contains($file->id))
            ->count();

        $pesan = [];

        $totalFoto = $sisaFileLama('image') + count($request->file('foto_kegiatan', []));
        if ($totalFoto > self::MAKS_FILE) {
            $pesan['foto_kegiatan'] = 'Total foto kegiatan maksimal ' . self::MAKS_FILE
                . ", sedangkan unggahan ini membuatnya menjadi {$totalFoto}. Hapus dulu sebagian foto lama.";
        }

        $totalDokumen = $sisaFileLama('document') + count($request->file('dokumen_kegiatan', []));
        if ($totalDokumen > self::MAKS_FILE) {
            $pesan['dokumen_kegiatan'] = 'Total dokumen kegiatan maksimal ' . self::MAKS_FILE
                . ", sedangkan unggahan ini membuatnya menjadi {$totalDokumen}. Hapus dulu sebagian dokumen lama.";
        }

        if ($pesan) {
            throw ValidationException::withMessages($pesan);
        }
    }

    private function handleFileUploads(Request $request, Kegiatan $proker): void
    {
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $this->repoMulmedService->upload($foto, [
                    'kegiatan_id'       => $proker->id,
                    'judul_file'        => pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }

        if ($request->hasFile('dokumen_kegiatan')) {
            foreach ($request->file('dokumen_kegiatan') as $doc) {
                $this->repoMulmedService->upload($doc, [
                    'kegiatan_id'       => $proker->id,
                    'judul_file'        => pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }
    }
}
