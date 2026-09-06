<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CvProfile;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Services\KegiatanCvService;

class CvBuilderController extends Controller
{
    public function __construct(private KegiatanCvService $kegiatanService)
    {
    }

    public function index()
    {
        return view('profile.cv.index');
    }

    public function loadStep($step)
    {
        $user = auth()->user();

        // Use firstOrNew to avoid creating a DB record on GET requests (CV-7/CV-9).
        // The record is only persisted when saveStep() calls ->save().
        $cvProfile = CvProfile::firstOrNew(
            ['user_id' => $user->id],
            ['tentang_diri' => '', 'template' => 'modern']
        );

        // Model $attributes defaults (Task 1) guarantee JSON fields are never null,
        // so no manual null-checking is needed here.

        $data = ['cv' => $cvProfile];

        // Ambil data auto-sync berdasarkan step
        if ($step == 1) {
            $data['user'] = [
                'name' => $user->name,
                'email' => $user->email,
                'personal_email' => !empty($cvProfile->cv_email) ? $cvProfile->cv_email : ($user->personal_email ?? null),
                'whatsapp' => !empty($cvProfile->cv_whatsapp) ? $cvProfile->cv_whatsapp : data_get($user, 'whatsapp'),
                'avatar_url' => $user->avatar_url_format ?? $user->avatar_url ?? null,
            ];

            // Sync NIM & Angkatan
            if ($user->hasRole('mahasiswa') && $user->student) {
                $data['user']['nim'] = $user->student->student_number;
                $data['user']['angkatan'] = $user->student->cohort_year;
            } elseif ($user->hasRole('alumni')) {
                $alumni = Alumni::where('user_id', $user->id)->first();
                if ($alumni) {
                    $data['user']['nim'] = $alumni->nim;
                    $data['user']['angkatan'] = $alumni->angkatan;
                }
            }
        } elseif ($step == 2) {
            // Pendidikan
            $pendidikanSync = [];

            // Prioritas 1: dari mk_kemahasiswaan (data lengkap)
            $kemahasiswaan = Kemahasiswaan::where('user_id', $user->id)->first();
            if ($kemahasiswaan) {
                $pendidikanSync[] = [
                    'institusi' => 'Universitas Diponegoro',
                    'jurusan' => 'S1 Teknik Komputer',
                    'tahun_masuk' => $kemahasiswaan->angkatan,
                    'tahun_lulus' => $kemahasiswaan->tahun_lulus,
                    'is_sync' => true
                ];
            } elseif ($user->student) {
                // Prioritas 2: dari tabel students (otomatis terisi saat login Microsoft)
                $pendidikanSync[] = [
                    'institusi' => 'Universitas Diponegoro',
                    'jurusan' => 'S1 Teknik Komputer',
                    'tahun_masuk' => $user->student->cohort_year,
                    'tahun_lulus' => null,
                    'is_sync' => true
                ];
            } else {
                // Prioritas 3: dari mk_alumni
                $alumni = Alumni::where('user_id', $user->id)->first();
                if ($alumni) {
                    $pendidikanSync[] = [
                        'institusi' => 'Universitas Diponegoro',
                        'jurusan' => $alumni->program_studi ?? 'S1 Teknik Komputer',
                        'tahun_masuk' => $alumni->angkatan,
                        'tahun_lulus' => $alumni->tahun_lulus,
                        'is_sync' => true
                    ];
                }
            }
            $data['pendidikan_sync'] = $pendidikanSync;

        } elseif ($step == 3) {
            // Pengalaman & Kegiatan — delegated to KegiatanCvService (CV-2/CV-4)
            $kegiatanSync = $this->kegiatanService->getMergedKegiatan($user)->toArray();

            $pengalamanSync = [];
            $alumni = Alumni::where('user_id', $user->id)->first();
            if ($alumni && $alumni->perusahaan) {
                $pengalamanSync[] = [
                    'perusahaan' => $alumni->perusahaan,
                    'posisi' => $alumni->jabatan,
                    'tahun_mulai' => $alumni->tahun_mulai_bekerja ?? null,
                    'tahun_selesai' => null,
                    'deskripsi' => 'Data tersinkronisasi dari direktori alumni.',
                    'is_sync' => true
                ];
            }

            $data['kegiatan_sync'] = $kegiatanSync;
            $data['pengalaman_sync'] = $pengalamanSync;

        } elseif ($step == 4) {
            // mk_prestasi punya verification_status dan kolom tanggal (date) sesuai skema DB
            $prestasiSync = [];
            $kemahasiswaan = Kemahasiswaan::with([
                'prestasi' => function ($q) {
                    $q->where('verification_status', 'approved');
                }
            ])->where('user_id', $user->id)->first();
            if ($kemahasiswaan && $kemahasiswaan->prestasi) {
                foreach ($kemahasiswaan->prestasi as $p) {
                    $prestasiSync[] = [
                        'nama' => $p->nama_prestasi,
                        'tingkat' => $p->tingkat,
                        'tahun' => $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->format('Y') : null,
                        'is_sync' => true
                    ];
                }
            }
            $data['prestasi_sync'] = $prestasiSync;

        } elseif ($step == 5) {
            // Keahlian
            // Tidak ada sync khusus
        } elseif ($step == 6) {
            // Preview - ambil semua
            $data = $this->getAllCvData($user, $cvProfile);
        }

        return response()->json($data);
    }

    public function saveStep(Request $request, $step)
    {
        $user = auth()->user();
        $cvProfile = CvProfile::where('user_id', $user->id)->first();

        if (!$cvProfile) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        if ($step == 1) {
            $request->validate([
                'tentang_diri' => 'nullable|string|max:1000',
                'personal_email' => 'nullable|email|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'cv_domisili' => 'nullable|string|max:255',
                'cv_portfolio' => 'nullable|string|max:255',
            ]);
            $cvProfile->tentang_diri = $request->tentang_diri;
            $cvProfile->cv_email = $request->personal_email;
            $fullWa = $request->whatsapp;

            $cvProfile->cv_whatsapp = $fullWa;
            $cvProfile->cv_domisili = $request->cv_domisili;
            $cvProfile->cv_portfolio = $request->cv_portfolio;

            // Catatan: WhatsApp & email pribadi bersifat read-only di wizard ini
            // (sumber kebenaran = Profil Utama), jadi TIDAK ditulis balik ke tabel
            // users / mk_kemahasiswaan agar tidak menimpa data yang lebih baru.
        } elseif ($step == 2) {
            $request->validate([
                'pendidikan' => 'nullable|array',
                'bahasa' => 'nullable|array',
            ]);
            $cvProfile->pendidikan = $request->pendidikan ?? [];
            $cvProfile->bahasa = $request->bahasa ?? [];
        } elseif ($step == 3) {
            $request->validate([
                'pengalaman_kerja' => 'nullable|array',
                'kegiatan_organisasi' => 'nullable|array',
            ]);
            $cvProfile->pengalaman_kerja = $request->pengalaman_kerja ?? [];
            $cvProfile->kegiatan_organisasi = $request->kegiatan_organisasi ?? [];
        } elseif ($step == 4) {
            $request->validate([
                'proyek' => 'nullable|array',
                'sertifikasi' => 'nullable|array',
            ]);
            $cvProfile->proyek = $request->proyek ?? [];
            $cvProfile->sertifikasi = $request->sertifikasi ?? [];
        } elseif ($step == 5) {
            $request->validate([
                'keahlian' => 'nullable|array',
            ]);
            $cvProfile->keahlian = $request->keahlian ?? [];
        }

        $cvProfile->save();

        return response()->json(['success' => true]);
    }

    public function preview()
    {
        return view('profile.cv.template-ats', $this->buildPreviewData());
    }

    public function generate()
    {
        // Same as preview but triggers print via JS
        return view('profile.cv.template-ats', $this->buildPreviewData(isPrint: true));
    }

    /**
     * Shared preview/generate data builder (CV-5 DRY).
     */
    private function buildPreviewData(bool $isPrint = false): array
    {
        $user = auth()->user();
        $cvProfile = CvProfile::firstOrNew(
            ['user_id' => $user->id],
            ['tentang_diri' => '', 'template' => 'modern']
        );

        $data = $this->getAllCvData($user, $cvProfile);
        $data['is_print'] = $isPrint;

        return $data;
    }

    public function getAllCvData($user, $cvProfile)
    {
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'personal_email' => !empty($cvProfile->cv_email) ? $cvProfile->cv_email : ($user->personal_email ?? null),
                'whatsapp' => !empty($cvProfile->cv_whatsapp) ? $cvProfile->cv_whatsapp : data_get($user, 'whatsapp'),
                'avatar_url' => $user->avatar_url_format ?? $user->avatar_url ?? null,
                'nim' => '-',
                'angkatan' => '-'
            ],
            'cv' => $cvProfile,
            'pendidikan' => $cvProfile->pendidikan ?? [],
            'bahasa' => $cvProfile->bahasa ?? [],
            'pengalaman' => $cvProfile->pengalaman_kerja ?? [],
            'kegiatan_manual' => $cvProfile->kegiatan_organisasi ?? [],
            'kegiatan' => [], // auto-sync activities
            'proyek' => $cvProfile->proyek ?? [],
            'prestasi' => [], // auto-sync prestasi
            'keahlian' => $cvProfile->keahlian ?? [],
            'sertifikasi' => $cvProfile->sertifikasi ?? [],
        ];

        // Populate sync data — kegiatan via centralised service (CV-2/CV-4)
        if ($user->student) {
            $data['user']['nim'] = $user->student->student_number;
            $data['user']['angkatan'] = $user->student->cohort_year;

            $data['kegiatan'] = $this->kegiatanService
                ->getMergedKegiatan($user)
                ->map(fn($item) => [
                    'nama'    => $item['nama'],
                    'peran'   => $item['peran'],
                    'tanggal' => $item['tanggal'],
                ])
                ->toArray();
        }

        if ($user->hasRole('alumni')) {
            $alumni = Alumni::where('user_id', $user->id)->first();
            if ($alumni) {
                $data['user']['nim'] = $alumni->nim ?? $data['user']['nim'];
                $data['user']['angkatan'] = $alumni->angkatan ?? $data['user']['angkatan'];

                if ($alumni->perusahaan) {
                    array_unshift($data['pengalaman'], [
                        'perusahaan' => $alumni->perusahaan,
                        'posisi' => $alumni->jabatan,
                        'tahun_mulai' => $alumni->tahun_mulai_bekerja,
                        'tahun_selesai' => 'Sekarang',
                        'deskripsi' => 'Data tersinkronisasi dari direktori alumni.'
                    ]);
                }
            }
        }

        // mk_prestasi punya verification_status dan kolom tanggal (date) sesuai skema DB
        $kemahasiswaan = Kemahasiswaan::with([
            'prestasi' => function ($q) {
                $q->where('verification_status', 'approved');
            }
        ])->where('user_id', $user->id)->first();
        if ($kemahasiswaan) {
            array_unshift($data['pendidikan'], [
                'institusi' => 'Universitas Diponegoro',
                'jurusan' => 'S1 Teknik Komputer',
                'tahun_masuk' => $kemahasiswaan->angkatan,
                'tahun_lulus' => $kemahasiswaan->tahun_lulus ?? 'Sekarang'
            ]);

            if ($kemahasiswaan->prestasi) {
                foreach ($kemahasiswaan->prestasi as $p) {
                    $data['prestasi'][] = [
                        'nama' => $p->nama_prestasi,
                        'tingkat' => $p->tingkat,
                        'tahun' => $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->format('Y') : null,
                    ];
                }
            }
        } elseif ($user->student) {
            // Fallback: ambil dari tabel students jika mk_kemahasiswaan belum ada
            array_unshift($data['pendidikan'], [
                'institusi' => 'Universitas Diponegoro',
                'jurusan' => 'S1 Teknik Komputer',
                'tahun_masuk' => $user->student->cohort_year,
                'tahun_lulus' => 'Sekarang'
            ]);
        }

        return $data;
    }
}
