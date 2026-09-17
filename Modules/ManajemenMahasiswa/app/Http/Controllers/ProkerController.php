<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Student;
use App\Models\Lecturer;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Services\PengelolaKegiatanService;

class ProkerController extends Controller
{
    /**
     * Field yang wajib terisi sebelum proker boleh diajukan ke Subbab 2.
     * Label dipakai untuk pesan error saat ajukan(), tooltip tombol "Ajukan Proker",
     * dan penanda "wajib sebelum diajukan" di form Rencana Proker.
     */
    private const SYARAT_AJUKAN = [
        'banner'             => 'Banner kegiatan',
        'tanggal_mulai'      => 'Tanggal mulai',
        'lokasi'             => 'Lokasi',
        'ketua_pelaksana_id' => 'Ketua pelaksana',
        'anggaran'           => 'Anggaran',
    ];

    public function __construct(
        private SupabaseStorage $supabase,
        private PengelolaKegiatanService $pengelolaService
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — daftar rencana proker
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        // DPM disertakan agar flag $isAdmin konsisten antar-subbab (Proker show, Pelaksanaan, Arsip).
        // Sebelumnya 'dpm' hilang di sini sehingga DPM diperlakukan berbeda di daftar Rencana Proker.
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        // $canManage merender tombol "Buat Proker". WAJIB whitelist eksplisit, JANGAN
        // pakai $isPengurus: staff_himpunan boleh masuk & mengedit Rencana Proker,
        // tapi TIDAK boleh membuat proker baru — proker dibuat oleh ketua.
        $canManage = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();

        $query = Kegiatan::with(['bidangs', 'kategoris', 'ketuaPelaksana.user'])
            ->where('status', Kegiatan::STATUS_DRAFT)
            ->orderBy('created_at', 'desc');

        // Filter bidang
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                $query->where(function($q) {
                    $q->whereDoesntHave('bidangs')
                      ->orWhereHas('kategoris', fn($k) => $k->where('nama_kategori', 'like', '%Prodi%'));
                });
            } else {
                $query->whereHas('bidangs', fn($q) => $q->where('mk_bidang.id', $request->bidang));
            }
        }


        // Search judul + deskripsi
        // Dibungkus closure supaya orWhere tidak "membocorkan" filter bidang di atas.
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('judul', 'like', $term)
                  ->orWhere('deskripsi', 'like', $term);
            });
        }

        $prokerList = $query->paginate(12);


        return view('manajemenmahasiswa::proker.index', compact(
            'prokerList', 'bidangList', 'kategoriList',
            'isAdmin', 'isPengurus', 'canManage'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Show — detail proker
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user', 'creator', 'disetujuiOleh',
        ])->findOrFail($id);
        // Catatan: TIDAK difilter status agar detail proker tetap bisa dibuka
        // setelah diajukan (disetujui) / diarsipkan (selesai). Sebelumnya findOrFail
        // dibatasi STATUS_DRAFT sehingga URL proker/{id} 404 begitu proker diajukan.

        $user    = Auth::user();
        $roles   = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm'])->isNotEmpty();
        $isPengurus = $roles->intersect(['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'])->isNotEmpty();
        // Hanya role tertentu yang boleh menekan tombol "Ajukan Proker"
        // (staff_himpunan, dosen_dpm, dosen_gpm TIDAK termasuk)
        $canAjukan = $roles->intersect([
            'superadmin', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        // Siapa yang boleh MELIHAT area tombol "Ajukan Proker" sama sekali —
        // whitelist eksplisit, bukan "semua kecuali pengawas" (lihat pelajaran
        // whitelist di modul ini). Yang tidak masuk daftar tidak dirender tombolnya:
        //  • staff_himpunan  → tugasnya melengkapi rencana, pengajuan urusan ketua
        //  • gpm/kadep/dpm   → pengawas view-only, tidak melakukan aksi apa pun
        // Admin tetap melihatnya dalam keadaan nonaktif supaya jelas bahwa
        // pengajuan adalah kewenangan ketua (sinkron dengan whitelist ajukan()).
        $canSeeAjukan = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        // Role yang boleh edit proker (sinkron dengan route middleware edit) — GPM, Kadep & DPM view-only.
        // staff_himpunan ikut di sini: pengurus himpunan melengkapi rencana yang dibuat ketua.
        $canEdit = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan',
        ])->isNotEmpty();
        // Role yang boleh hapus proker (sinkron dengan route middleware destroy) — GPM, Kadep & DPM view-only.
        // staff_himpunan TIDAK termasuk: menghapus proker tetap kewenangan ketua.
        $canDelete = $roles->intersect([
            'superadmin', 'admin', 'admin_kemahasiswaan',
            'ketua_himpunan', 'ketua_bidang', 'ketua_unit',
        ])->isNotEmpty();
        // Lapis kedua di belakang role: proker hanya boleh diubah pemiliknya,
        // pengelola yang ia tunjuk, atau override (KegiatanPolicy). Tanpa ini ketua
        // bidang mana pun melihat tombol Edit/Hapus/Ajukan di proker bidang lain.
        $bolehUbah = Gate::allows('update', $proker);
        $pesanBukanPengelola = $canEdit && !$bolehUbah
            ? $this->pengelolaService->pesanTolak($proker)
            : null;
        $canEdit      = $canEdit && $bolehUbah;
        $canAjukan    = $canAjukan && $bolehUbah;
        $canSeeAjukan = $canSeeAjukan && $bolehUbah;
        $canDelete    = $canDelete && Gate::allows('delete', $proker);

        // Anggaran disembunyikan dari mahasiswa & alumni — konsisten dengan Pelaksanaan & Arsip.
        $canViewRestricted = $roles->diff(['mahasiswa', 'alumni'])->isNotEmpty();
        $isCreator = $proker->user_id === Auth::id();

        // Dipakai view untuk mengaktifkan/menonaktifkan tombol "Ajukan Proker"
        // sekaligus menyusun tooltip berisi field yang masih kosong.
        $kelengkapan = $this->cekKelengkapan($proker);

        return view('manajemenmahasiswa::proker.show', compact(
            'proker', 'isAdmin', 'isPengurus', 'isCreator', 'canAjukan', 'canEdit', 'canDelete',
            'canSeeAjukan', 'canViewRestricted', 'kelengkapan', 'pesanBukanPengelola'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Create & Store
    // ─────────────────────────────────────────────────────────────────────────

    public function create()
    {
        // Model kosong supaya partial form bisa dipakai seragam untuk create & edit.
        $proker = new Kegiatan();

        $bidangList   = Bidang::orderBy('nama_bidang')->get();
        $kategoriList = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingPanitia     = collect();
        $existingPanitiaIds  = old('panitia_ids', []);
        $selectedKategoriIds = old('kategori_kegiatan_id', []);
        $selectedBidangIds   = old('bidang_id', []);
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosen       = $dosenList->whereIn('id', old('dosen_pendamping_ids', []));

        return view('manajemenmahasiswa::proker.create', compact(
            'proker', 'bidangList', 'kategoriList',
            'mahasiswaList', 'dosenList',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds'
        ) + $this->pengelolaService->dataForm($proker));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProker($request);

        $validated['user_id'] = Auth::id();
        // Proker baru SELALU dimulai sebagai draft (Subbab 1).
        // Untuk maju ke Subbab 2, user harus klik "Ajukan Proker" di halaman detail.
        $validated['status'] = Kegiatan::STATUS_DRAFT;

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $this->supabase->upload($request->file('banner'), 'mk_mulmed/image');
        }

        $proker = Kegiatan::create($this->kolomKegiatan($validated) + [
            'user_id' => $validated['user_id'],
            'status'  => $validated['status'],
            'banner'  => $validated['banner'] ?? null,
        ]);

        $this->syncRelasiKegiatan($proker, $validated, $request);
        $this->pengelolaService->sync($proker, $request);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil disimpan sebagai draft. Lengkapi datanya, lalu klik "Ajukan Proker" untuk melanjutkan ke tahap pelaksanaan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Edit & Update
    // ─────────────────────────────────────────────────────────────────────────

    public function edit($id)
    {
        $proker = Kegiatan::with([
            'bidangs', 'kategoris',
            'ketuaPelaksana.user', 'dosenPendampings.user',
            'panitia.user',
        ])->findOrFail($id);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        $bidangList    = Bidang::orderBy('nama_bidang')->get();
        $kategoriList  = KategoriKegiatan::orderBy('nama_kategori')->get();
        $mahasiswaList = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList     = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        $existingPanitia = $proker->panitia ?? collect();

        $selectedKategoriIds = old('kategori_kegiatan_id', $proker->kategoris->pluck('id')->toArray());
        $selectedBidangIds   = old('bidang_id', $proker->bidangs->pluck('id')->toArray());
        $existingPanitiaIds  = old('panitia_ids', $existingPanitia->pluck('id')->toArray());
        // Chip dosen pendamping di-pre-populate lewat JS dari koleksi ini
        $existingDosenIds    = old('dosen_pendamping_ids', $proker->dosenPendampings->pluck('id')->toArray());
        $existingDosen       = $dosenList->whereIn('id', $existingDosenIds);

        return view('manajemenmahasiswa::proker.edit', compact(
            'proker', 'bidangList', 'kategoriList',
            'mahasiswaList', 'dosenList',
            'existingPanitia', 'existingPanitiaIds', 'existingDosen',
            'selectedKategoriIds', 'selectedBidangIds'
        ) + $this->pengelolaService->dataForm($proker));
    }

    public function update(Request $request, $id)
    {
        $proker = Kegiatan::findOrFail($id);

        if ($tolak = $this->tolakBilaBukanDraft($proker)) {
            return $tolak;
        }
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        $validated = $this->validateProker($request);

        $proker->update($this->kolomKegiatan($validated));

        // Handle banner upload — hapus banner lama dulu supaya tidak meninggalkan
        // file yatim di storage (perilaku sama dengan subbab Pelaksanaan).
        if ($request->hasFile('banner')) {
            if ($proker->banner) {
                $this->supabase->delete($proker->banner);
            }
            $proker->update([
                'banner' => $this->supabase->upload($request->file('banner'), 'mk_mulmed/image'),
            ]);
        }

        $this->syncRelasiKegiatan($proker, $validated, $request);
        $this->pengelolaService->sync($proker, $request);

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('success', 'Rencana proker berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Workflow: Ajukan → Subbab 2 (Pelaksanaan)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pengurus mengajukan proker — status berubah ke disetujui dan masuk Subbab 2.
     */
    public function ajukan($id)
    {
        // Proteksi backend: hanya role yang berhak boleh mengajukan proker
        $allowedRoles = ['superadmin', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit'];
        $userRoles = Auth::user()->roles->pluck('name');
        $canAjukan = $userRoles->intersect($allowedRoles)->isNotEmpty();

        if (!$canAjukan) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengajukan proker.');
        }

        $proker = Kegiatan::where('status', Kegiatan::STATUS_DRAFT)->findOrFail($id);

        // Mengajukan = mengubah status proker, jadi butuh hak ubah atas proker ini.
        if ($tolak = $this->tolakBilaBukanPengelola($proker)) {
            return $tolak;
        }

        // Rencana wajib lengkap sebelum boleh naik ke tahap Pelaksanaan (Subbab 2).
        $kurang = collect($this->cekKelengkapan($proker))
            ->reject(fn($item) => $item['terisi'])
            ->pluck('label');

        if ($kurang->isNotEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Rencana proker belum lengkap. Lengkapi dulu: ' . $kurang->implode(', ') . '.');
        }

        $proker->update(['status' => Kegiatan::STATUS_DISETUJUI]);

        return redirect()
            ->route('manajemenmahasiswa.pelaksanaan.index')
            ->with('success', 'Rencana Proker berhasil diajukan dan masuk ke tahap pelaksanaan kegiatan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Destroy
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy($id)
    {
        // Cari proker by id saja (TIDAK difilter status). Sebelumnya findOrFail
        // dibatasi STATUS_DRAFT — sama seperti bug lama di show() — sehingga begitu
        // status proker bukan 'draft' lagi (mis. halaman daftar/detail basi karena
        // bfcache), DELETE melempar ModelNotFoundException → handler global
        // mengalihkan ke /error/404 alih-alih kembali ke daftar rencana proker.
        $proker = Kegiatan::findOrFail($id);

        // Hanya proker berstatus draft yang boleh dihapus. Jika sudah diajukan/
        // diarsipkan, kembalikan ke daftar dengan pesan, bukan halaman 404.
        if ($proker->status !== Kegiatan::STATUS_DRAFT) {
            return redirect()
                ->route('manajemenmahasiswa.proker.index')
                ->with('error', 'Hanya rencana proker berstatus draft yang dapat dihapus.');
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

        $proker->delete();

        return redirect()
            ->route('manajemenmahasiswa.proker.index')
            ->with('success', 'Rencana proker berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Rencana proker hanya boleh diubah selama masih berstatus draft.
     *
     * Prokernya dicari by id dulu, statusnya baru diperiksa di sini — pola yang
     * sama dengan destroy(). Kalau findOrFail langsung dibatasi STATUS_DRAFT
     * (perilaku lama edit() & update()), proker yang keburu diajukan dari tab,
     * perangkat, atau akun lain akan melempar ModelNotFoundException: user
     * dibuang ke halaman error dengan seluruh isian form hilang dan tanpa
     * penjelasan apa pun. Sekarang ia diarahkan ke detail proker + pesan.
     *
     * @return RedirectResponse|null null bila proker masih draft (boleh diubah)
     */
    private function tolakBilaBukanDraft(Kegiatan $proker): ?RedirectResponse
    {
        if ($proker->status === Kegiatan::STATUS_DRAFT) {
            return null;
        }

        $lanjutanNya = $proker->status === Kegiatan::STATUS_SELESAI
            ? 'Laporan & Arsip'
            : 'Pelaksanaan Kegiatan';

        return redirect()
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', 'Rencana proker ini sudah diajukan, jadi tidak bisa lagi diubah dari halaman Rencana Proker. Perubahan datanya sekarang dilakukan di subbab ' . $lanjutanNya . '.');
    }

    /**
     * Role saja tidak cukup: proker hanya boleh diubah/dihapus pemiliknya,
     * pengelola yang ia tunjuk, atau override (lihat KegiatanPolicy). Tanpa cek
     * ini ketua bidang mana pun bisa mengubah proker bidang lain lewat URL langsung.
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
            ->route('manajemenmahasiswa.proker.show', $proker->id)
            ->with('error', $this->pengelolaService->pesanTolak($proker));
    }

    private function validateProker(Request $request): array
    {
        // Aturannya dipusatkan di model supaya Rencana Proker & Pelaksanaan tidak
        // bisa lepas sinkron. Definisi lama ("tepat 1 kategori DAN itu Prodi") juga
        // berbeda dari form, yang menyembunyikan kolom Bidang begitu SEMUA kategori
        // terpilih berkategori Prodi — beda itu bisa memunculkan error "bidang wajib"
        // pada kolom yang sudah disembunyikan JS.
        $kategoriDipilih = $request->input('kategori_kegiatan_id', []);
        $isOnlyProdi = is_array($kategoriDipilih) && Kegiatan::hanyaKategoriProdi($kategoriDipilih);

        $bidangRule = $isOnlyProdi ? 'nullable|array' : 'required|array|min:1';

        // Jam selesai hanya dibandingkan dengan jam mulai bila kegiatan berlangsung
        // dalam SATU hari. Kegiatan lintas hari (mis. 15 Jan 15.00 → 16 Jan 09.00)
        // memang wajar punya jam selesai yang lebih awal, jadi jangan diblokir.
        $satuHari = !$request->filled('tanggal_selesai')
            || $request->input('tanggal_selesai') === $request->input('tanggal_mulai');

        $jamSelesaiRules = ['nullable', 'date_format:H:i,H:i:s'];
        if ($satuHari && $request->filled('jam_mulai')) {
            $jamSelesaiRules[] = 'after:jam_mulai';
        }

        // Field perencanaan diduplikat dari Subbab 2 (Pelaksanaan) supaya rencana
        // bisa disusun lengkap sejak awal. Semuanya `nullable` saat menyimpan:
        // kelengkapan baru ditegakkan di ajukan() — ketua bisa menyimpan kerangka,
        // staff_himpunan yang melengkapi kemudian.
        //
        // Foto & dokumen kegiatan TIDAK ada di sini: keduanya dokumentasi acara
        // yang sudah berlangsung, diunggah di Subbab 2 (Pelaksanaan Kegiatan).
        return $request->validate([
            'judul'                  => 'required|string|max:255',
            'deskripsi'              => 'required|string|min:20|max:3000',
            'kategori_kegiatan_id'   => 'required|array|min:1|max:2',
            'kategori_kegiatan_id.*' => 'integer|exists:mk_kategori_kegiatan,id',
            'bidang_id'              => $bidangRule,
            'bidang_id.*'            => 'integer|exists:mk_bidang,id',
            // Tanggal mulai tetap boleh kosong (draft), TAPI tidak boleh kosong bila
            // tanggal selesai sudah diisi — kombinasi itu menghasilkan kegiatan
            // "Belum ditentukan — 05 Januari 2026" sekaligus membuat kolom `tahun`
            // ikut kosong, sehingga kegiatan hilang dari filter Tahun di Subbab 2 & 3.
            'tanggal_mulai'          => 'nullable|date|required_with:tanggal_selesai',
            'tanggal_selesai'        => 'nullable|date|after_or_equal:tanggal_mulai',
            // Kolom jam di database bertipe TIME: `string` bebas membuat isian ngawur
            // lolos ke query dan memunculkan halaman error saat disimpan.
            'jam_mulai'              => 'nullable|date_format:H:i,H:i:s',
            'jam_selesai'            => $jamSelesaiRules,
            'lokasi'                 => 'nullable|string|max:255',
            'target_peserta'         => 'nullable|integer|min:1',
            'anggaran'               => 'nullable|numeric|min:0',
            'ketua_pelaksana_id'     => 'nullable|exists:students,id',
            'dosen_pendamping_ids'   => 'nullable|array',
            'dosen_pendamping_ids.*' => 'exists:lecturers,id',
            'panitia_ids'            => 'nullable|array',
            'panitia_ids.*'          => 'exists:students,id',
            'panitia_peran'          => 'nullable|array',
            'panitia_peran.*'        => 'nullable|string|max:255',
            'banner'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            // Pesan bawaan Laravel masih berbahasa Inggris dan menyebut nama kolom
            // mentah ("The tanggal mulai field is required when..."), jadi aturan
            // baru di atas diberi pesan sendiri supaya jelas di kotak error form.
            'bidang_id.required'             => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'bidang_id.min'                  => 'Bidang wajib dipilih minimal satu, kecuali kegiatan ini murni Kegiatan Prodi.',
            'tanggal_mulai.required_with'    => 'Tanggal mulai wajib diisi kalau tanggal selesai sudah ditentukan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'jam_mulai.date_format'          => 'Jam mulai harus berupa jam yang benar, contoh 09:00.',
            'jam_selesai.date_format'        => 'Jam selesai harus berupa jam yang benar, contoh 15:00.',
            'jam_selesai.after'              => 'Jam selesai harus lebih lambat dari jam mulai. Kalau kegiatannya lintas hari, isi dulu tanggal selesainya.',
        ]);
    }

    /**
     * Petakan hasil validasi ke kolom mk_kegiatan.
     *
     * Catatan penting: `is_pelaksanaan_updated` sengaja TIDAK disentuh di sini.
     * Flag itu milik Subbab 2 dan menjadi penanda "sudah dicek sesuai realisasi"
     * sebelum kegiatan boleh diunggah ke Arsip.
     */
    private function kolomKegiatan(array $validated): array
    {
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds   = $validated['bidang_id'] ?? [];

        return [
            'judul'              => $validated['judul'],
            'deskripsi'          => $validated['deskripsi'],
            'tanggal_mulai'      => $validated['tanggal_mulai'] ?? null,
            // Isi kolom `tahun` dari tanggal mulai supaya kegiatan hasil alur himpunan
            // tidak ber-`tahun` NULL dan tetap muncul di filter tahun Pelaksanaan & Arsip.
            'tahun'              => !empty($validated['tanggal_mulai'])
                ? \Carbon\Carbon::parse($validated['tanggal_mulai'])->year
                : null,
            'tanggal_selesai'    => $validated['tanggal_selesai'] ?? null,
            'jam_mulai'          => $validated['jam_mulai'] ?? null,
            'jam_selesai'        => $validated['jam_selesai'] ?? null,
            'lokasi'             => $validated['lokasi'] ?? null,
            'target_peserta'     => $validated['target_peserta'] ?? null,
            'anggaran'           => $validated['anggaran'] ?? null,
            'ketua_pelaksana_id' => $validated['ketua_pelaksana_id'] ?? null,
            // Kolom FK lama disinkronkan dengan elemen pertama agar view lama tetap konsisten
            'kategori_kegiatan_id' => $kategoriIds[0] ?? null,
            'bidang_id'            => $bidangIds[0] ?? null,
        ];
    }

    /**
     * Sinkronisasi seluruh relasi many-to-many + penanggung jawab.
     * Dipakai bersama oleh store() dan update().
     */
    private function syncRelasiKegiatan(Kegiatan $proker, array $validated, Request $request): void
    {
        // Penanggung jawab diturunkan dari ketua pelaksana (kolom legacy)
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $proker->update(['penanggung_jawab' => $student?->user?->name]);
        } else {
            $proker->update(['penanggung_jawab' => null]);
        }

        // Kategori & bidang — detach bila kosong supaya pivot tidak "nyangkut"
        $kategoriIds = $validated['kategori_kegiatan_id'] ?? [];
        $bidangIds   = $validated['bidang_id'] ?? [];

        if (!empty($kategoriIds)) {
            $proker->kategoris()->sync($kategoriIds);
        } else {
            $proker->kategoris()->detach();
        }
        if (!empty($bidangIds)) {
            $proker->bidangs()->sync($bidangIds);
        } else {
            $proker->bidangs()->detach();
        }

        // Panitia + jabatan masing-masing
        $panitiaIds   = $validated['panitia_ids'] ?? [];
        $panitiaPeran = $request->panitia_peran ?? [];
        $panitiaSyncData = [];
        foreach ($panitiaIds as $pid) {
            $panitiaSyncData[$pid] = ['peran' => $panitiaPeran[$pid] ?? null];
        }
        $proker->panitia()->sync($panitiaSyncData);

        // Dosen pendamping (many-to-many)
        $proker->dosenPendampings()->sync($validated['dosen_pendamping_ids'] ?? []);
    }

    /**
     * Checklist kelengkapan rencana sebelum boleh diajukan ke Subbab 2.
     *
     * @return array<int, array{field: string, label: string, terisi: bool}>
     */
    private function cekKelengkapan(Kegiatan $proker): array
    {
        return collect(self::SYARAT_AJUKAN)->map(fn($label, $field) => [
            'field'  => $field,
            'label'  => $label,
            // anggaran 0 itu sah (kegiatan tanpa biaya) — pakai is_null, bukan empty()
            'terisi' => $field === 'anggaran'
                ? !is_null($proker->anggaran)
                : !empty($proker->{$field}),
        ])->values()->all();
    }

}
