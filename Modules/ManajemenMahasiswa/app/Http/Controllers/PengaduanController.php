<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Services\PengaduanService;

class PengaduanController extends Controller
{
    private const ADMIN_ROLES = [
        'superadmin',
        'admin',
        'admin_kemahasiswaan',
        'gpm',
    ];

    public function __construct(private PengaduanService $pengaduanService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        $isAdmin = $this->isAdmin($user);

        $pengaduan = $isAdmin
            ? $this->pengaduanService->listAll()
            : $this->pengaduanService->listForUser($user->id);

        return view('manajemenmahasiswa::pengaduan.index', compact('pengaduan', 'isAdmin'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        $isAdmin = $this->isAdmin($user);

        $kategoriList = [
            Pengaduan::KATEGORI_AKADEMIK => 'Akademik',
            Pengaduan::KATEGORI_PEMBELAJARAN => 'Proses Pembelajaran di Kelas',
            Pengaduan::KATEGORI_TENDIK => 'Masalah dengan Tenaga Kependidikan (Tendik)',
            Pengaduan::KATEGORI_TUGAS_BEBAN => 'Masalah dengan Tugas yang Terlalu Banyak',
            Pengaduan::KATEGORI_LAINNYA => 'Keluhan Umum Lainnya',
        ];

        return view('manajemenmahasiswa::pengaduan.create', compact('kategoriList', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        $validated = $request->validate([
            'is_anonim' => ['nullable', 'boolean'],
            'kategori' => ['required', 'string', 'in:' . implode(',', Pengaduan::KATEGORI_LIST)],
            'template' => ['required', 'array'],
            'template.judul' => ['required', 'string', 'max:255'],
            'template.kronologi' => ['required', 'string', 'min:20', 'max:5000'],
            'template.lokasi' => ['nullable', 'string', 'max:255'],
            'template.tanggal_kejadian' => ['nullable', 'date'],
            'template.mata_kuliah' => ['nullable', 'string', 'max:255'],
            'template.nama_dosen' => ['nullable', 'string', 'max:255'],
            'template.nama_tendik' => ['nullable', 'string', 'max:255'],
        ]);

        $template = Arr::only($validated['template'], [
            'judul',
            'kronologi',
            'lokasi',
            'tanggal_kejadian',
            'mata_kuliah',
            'nama_dosen',
            'nama_tendik',
        ]);

        $pengaduan = $this->pengaduanService->create(
            userId: $user->id,
            kategori: $validated['kategori'],
            isAnonim: (bool)($validated['is_anonim'] ?? false),
            template: $template,
        );

        return redirect()
            ->route('manajemenmahasiswa.pengaduan.show', $pengaduan->id)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function show(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        if (!$user || !$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        $isAdmin = $this->isAdmin($user);

        if (!$isAdmin && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        if ($isAdmin) {
            $this->pengaduanService->markRead($pengaduan, $user->id);
        }

        return view('manajemenmahasiswa::pengaduan.show', compact('pengaduan', 'isAdmin'));
    }

    public function reply(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        if (!$user || !$user->can('kemahasiswaan.view')) {
            abort(403, 'Anda tidak memiliki izin akses ke modul Manajemen Mahasiswa.');
        }

        if (!$this->isAdmin($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menjawab pengaduan.');
        }

        $validated = $request->validate([
            'jawaban' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $this->pengaduanService->reply($pengaduan, $user->id, $validated['jawaban']);

        return back()->with('success', 'Jawaban berhasil dikirim.');
    }

    private function isAdmin($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(self::ADMIN_ROLES)
            : false;
    }
}
