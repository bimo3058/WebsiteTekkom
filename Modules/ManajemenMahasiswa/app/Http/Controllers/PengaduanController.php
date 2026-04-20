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

        $dosenList = [
            'Prof. Dr. Adian Fatchur Rochim, S.T., M.T.',
            'Prof. Dr. Ir. R. Rizal Isnanto, S.T., M.M., M.T., IPU, ASEAN Eng.',
            'Dr. Oky Dwi Nurhayati, S.T., M.T.',
            'Agung Budi Prasetijo, S.T., M.I.T., Ph.D.',
            'Dr. Maman Somantri, S.T., M.T.',
            'Rinta Kridalukmana, S.Kom., M.T., Ph.D.',
            'Kuntoro Adi Nugroho, S.T., M.Eng., Ph.D.',
            'Yudi Eko Windarto, S.T., M.Kom.',
            'Dr. Delphi Hanggoro, S.T., M.T.',
            'Dania Eridani, S.T., M.Eng.',
            'Ike Pertiwi Windasari, S.T., M.T.',
            'Eko Didik Widianto, S.T., M.T.',
            'Kurniawan Teguh Martono, S.T., M.T.',
            'Risma Septiana, S.T., M.Eng.',
            'Adnan Fauzi, S.T., M.Kom.',
            'Patricia Evericho Mountaines, S.T., M.Cs.',
            'Bellia Dwi Cahya Putri, S.T., M.T.',
            'Ilmam Fauzi Hashbil Alim, S.T., M.Kom.',
            'Erwin Adriono, S.T., M.T.',
            'Arseto Satriyo Nugroho, S.T., M.Eng.',
        ];

        $frekuensiList = [
            'Sekali' => 'Sekali',
            'Kadang-kadang' => 'Kadang-kadang',
            'Sering' => 'Sering',
            'Hampir Setiap Pertemuan Kuliah' => 'Hampir Setiap Pertemuan Kuliah',
        ];

        return view('manajemenmahasiswa::pengaduan.create', compact('kategoriList', 'dosenList', 'frekuensiList', 'isAdmin'));
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
            'template.hal_aduan' => ['required', 'string', 'max:1000'],
            'template.kronologi' => ['required', 'string', 'min:20', 'max:5000'],
            'template.angkatan' => ['nullable', 'string', 'max:20'],
            'template.lokasi' => ['nullable', 'string', 'max:255'],
            'template.waktu_kejadian' => ['nullable', 'date'],
            // Backward compatibility: older form/key
            'template.tanggal_kejadian' => ['nullable', 'date'],
            'template.mata_kuliah' => ['nullable', 'string', 'max:255'],
            'template.nama_dosen' => ['nullable', 'string', 'max:255'],
            'template.nama_tendik' => ['nullable', 'string', 'max:255'],
            'template.frekuensi' => ['nullable', 'string', 'max:100'],
            'template.link_bukti' => ['nullable', 'url', 'max:2048'],
        ]);

        $template = Arr::only($validated['template'], [
            'judul',
            'hal_aduan',
            'kronologi',
            'angkatan',
            'lokasi',
            'waktu_kejadian',
            'tanggal_kejadian',
            'mata_kuliah',
            'nama_dosen',
            'nama_tendik',
            'frekuensi',
            'link_bukti',
        ]);

        if (!isset($template['waktu_kejadian']) && isset($template['tanggal_kejadian'])) {
            $template['waktu_kejadian'] = $template['tanggal_kejadian'];
        }

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
