<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Lecturer;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\Kepengurusan;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;

class KegiatanController extends Controller
{
    /**
     * Halaman utama daftar kegiatan — mahasiswa & alumni.
     */
    public function index(Request $request)
    {
        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kepengurusanList = Kepengurusan::orderBy('tahun_periode', 'desc')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();

        $query = Kegiatan::with(['bidang', 'kategoriKegiatan', 'kepengurusan', 'ketuaPelaksana.user', 'dosenPendamping.user'])
            ->orderBy('tanggal_mulai', 'desc');

        // Filter by bidang or prodi
        if ($request->filled('bidang') && $request->bidang !== 'semua') {
            if ($request->bidang === 'prodi') {
                // Kegiatan Prodi = kegiatan tanpa bidang (bidang_id is null)
                $query->whereNull('bidang_id');
            } else {
                $query->where('bidang_id', $request->bidang);
            }
        }

        // Filter by kepengurusan (tahun periode)
        if ($request->filled('kepengurusan') && $request->kepengurusan !== 'semua') {
            $query->where('kepengurusan_id', $request->kepengurusan);
        }

        // Search by judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $kegiatan = $query->paginate(12);

        // Cek apakah user adalah admin/pengurus (untuk tombol Tambah)
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'pengurus_himpunan'])->isNotEmpty();

        return view('manajemenmahasiswa::kegiatan.index', compact(
            'kegiatan',
            'bidangList',
            'kepengurusanList',
            'kategoriList',
            'isAdmin',
        ));
    }

    /**
     * Detail kegiatan — halaman lengkap.
     */
    public function show($id)
    {
        $kegiatan = Kegiatan::with([
            'bidang',
            'kategoriKegiatan',
            'kepengurusan',
            'repoMulmed',
            'ketuaPelaksana.user',
            'dosenPendamping.user',
        ])->findOrFail($id);

        // Cek apakah user adalah admin/pengurus (untuk tombol Edit/Hapus)
        $user  = Auth::user();
        $roles = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin_kemahasiswaan', 'pengurus_himpunan'])->isNotEmpty();

        return view('manajemenmahasiswa::kegiatan.show', compact('kegiatan', 'isAdmin'));
    }

    /**
     * Form buat kegiatan baru.
     * Akses: pengurus_himpunan, admin_kemahasiswaan, superadmin
     */
    public function create()
    {
        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();
        $kepengurusanList = Kepengurusan::orderBy('tahun_periode', 'desc')->get();
        $mahasiswaList    = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList        = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        return view('manajemenmahasiswa::kegiatan.create', compact(
            'bidangList',
            'kategoriList',
            'kepengurusanList',
            'mahasiswaList',
            'dosenList',
        ));
    }

    /**
     * Simpan kegiatan baru.
     */
    public function store(Request $request)
    {
        // Check if selected kategori is "Kegiatan Prodi" (bidang not needed)
        $isProdi = false;
        if ($request->filled('kategori_kegiatan_id')) {
            $kategori = KategoriKegiatan::find($request->kategori_kegiatan_id);
            $isProdi  = $kategori && stripos($kategori->nama_kategori, 'prodi') !== false;
        }

        $validated = $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'required|string|min:20',
            'kategori_kegiatan_id'=> 'required|exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $isProdi ? 'nullable|exists:mk_bidang,id' : 'required|exists:mk_bidang,id',
            'kepengurusan_id'     => 'nullable|exists:mk_kepengurusan,id',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => 'nullable|date_format:H:i',
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => 'nullable|date_format:H:i',
            'lokasi'              => 'nullable|string|max:255',
            'banner'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'anggaran'            => 'nullable|numeric|min:0',
            'ketua_pelaksana_id'  => 'nullable|exists:students,id',
            'dosen_pendamping_id' => 'nullable|exists:lecturers,id',
            'target_peserta'      => 'nullable|integer|min:1',
            'status'              => 'required|in:akan_datang,berlangsung,selesai',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'    => 'nullable|array|max:10',
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('kegiatan/banners', 'public');
        }

        $validated['user_id'] = Auth::id();

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $validated['penanggung_jawab'] = $student?->user?->name;
        }

        // Remove non-kegiatan fields before creating
        unset($validated['foto_kegiatan'], $validated['dokumen_kegiatan']);

        $kegiatan = Kegiatan::create($validated);

        // Handle foto uploads
        $this->handleFileUploads($request, $kegiatan);

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Form edit kegiatan.
     */
    public function edit($id)
    {
        $kegiatan         = Kegiatan::with('repoMulmed')->findOrFail($id);
        $bidangList       = Bidang::orderBy('nama_bidang')->get();
        $kategoriList     = KategoriKegiatan::orderBy('nama_kategori')->get();
        $kepengurusanList = Kepengurusan::orderBy('tahun_periode', 'desc')->get();
        $mahasiswaList    = Student::with('user')->get()->sortBy(fn($s) => $s->user->name ?? '');
        $dosenList        = Lecturer::with('user')->get()->sortBy(fn($l) => $l->user->name ?? '');

        return view('manajemenmahasiswa::kegiatan.edit', compact(
            'kegiatan',
            'bidangList',
            'kategoriList',
            'kepengurusanList',
            'mahasiswaList',
            'dosenList',
        ));
    }

    /**
     * Update kegiatan.
     */
    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        // Check if selected kategori is "Kegiatan Prodi" (bidang not needed)
        $isProdi = false;
        if ($request->filled('kategori_kegiatan_id')) {
            $kategori = KategoriKegiatan::find($request->kategori_kegiatan_id);
            $isProdi  = $kategori && stripos($kategori->nama_kategori, 'prodi') !== false;
        }

        $validated = $request->validate([
            'judul'               => 'required|string|max:255',
            'deskripsi'           => 'required|string|min:20',
            'kategori_kegiatan_id'=> 'required|exists:mk_kategori_kegiatan,id',
            'bidang_id'           => $isProdi ? 'nullable|exists:mk_bidang,id' : 'required|exists:mk_bidang,id',
            'kepengurusan_id'     => 'nullable|exists:mk_kepengurusan,id',
            'tanggal_mulai'       => 'required|date',
            'jam_mulai'           => 'nullable|date_format:H:i',
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_selesai'         => 'nullable|date_format:H:i',
            'lokasi'              => 'nullable|string|max:255',
            'banner'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'anggaran'            => 'nullable|numeric|min:0',
            'ketua_pelaksana_id'  => 'nullable|exists:students,id',
            'dosen_pendamping_id' => 'nullable|exists:lecturers,id',
            'target_peserta'      => 'nullable|integer|min:1',
            'status'              => 'required|in:akan_datang,berlangsung,selesai',
            'foto_kegiatan'       => 'nullable|array|max:10',
            'foto_kegiatan.*'     => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen_kegiatan'    => 'nullable|array|max:10',
            'dokumen_kegiatan.*'  => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'hapus_file'          => 'nullable|array',
            'hapus_file.*'        => 'integer|exists:mk_repo_mulmed,id',
        ]);

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($kegiatan->banner) {
                Storage::disk('public')->delete($kegiatan->banner);
            }
            $validated['banner'] = $request->file('banner')->store('kegiatan/banners', 'public');
        }

        // Set penanggung_jawab from ketua pelaksana name for backward compatibility
        if (!empty($validated['ketua_pelaksana_id'])) {
            $student = Student::with('user')->find($validated['ketua_pelaksana_id']);
            $validated['penanggung_jawab'] = $student?->user?->name;
        } else {
            $validated['penanggung_jawab'] = null;
        }

        // Handle file deletions
        if ($request->filled('hapus_file')) {
            foreach ($request->hapus_file as $fileId) {
                $file = RepoMulmed::where('kegiatan_id', $kegiatan->id)->find($fileId);
                if ($file) {
                    Storage::disk('public')->delete($file->path_file);
                    $file->delete();
                }
            }
        }

        // Remove non-kegiatan fields before updating
        unset($validated['foto_kegiatan'], $validated['dokumen_kegiatan'], $validated['hapus_file']);

        $kegiatan->update($validated);

        // Handle new file uploads
        $this->handleFileUploads($request, $kegiatan);

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.show', $id)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus kegiatan.
     */
    public function destroy($id)
    {
        $kegiatan = Kegiatan::with('repoMulmed')->findOrFail($id);

        if ($kegiatan->banner) {
            Storage::disk('public')->delete($kegiatan->banner);
        }

        // Delete all associated files
        foreach ($kegiatan->repoMulmed as $file) {
            Storage::disk('public')->delete($file->path_file);
        }

        $kegiatan->delete();

        return redirect()
            ->route('manajemenmahasiswa.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * Handle upload foto dan dokumen kegiatan ke repo_mulmed.
     */
    private function handleFileUploads(Request $request, Kegiatan $kegiatan): void
    {
        // Upload foto kegiatan
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $path = $foto->store('kegiatan/foto', 'public');
                RepoMulmed::create([
                    'kegiatan_id'       => $kegiatan->id,
                    'nama_file'         => $foto->getClientOriginalName(),
                    'path_file'         => $path,
                    'tipe_file'         => 'image',
                    'judul_file'        => pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME),
                    'deskripsi_meta'    => null,
                    'visibility_status' => 'public',
                    'status_arsip'      => 'aktif',
                ]);
            }
        }

        // Upload dokumen kegiatan
        if ($request->hasFile('dokumen_kegiatan')) {
            foreach ($request->file('dokumen_kegiatan') as $doc) {
                $path = $doc->store('kegiatan/dokumen', 'public');
                RepoMulmed::create([
                    'kegiatan_id'       => $kegiatan->id,
                    'nama_file'         => $doc->getClientOriginalName(),
                    'path_file'         => $path,
                    'tipe_file'         => 'document',
                    'judul_file'        => pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME),
                    'deskripsi_meta'    => null,
                    'visibility_status' => 'public',
                    'status_arsip'      => 'aktif',
                ]);
            }
        }
    }
}
