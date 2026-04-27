<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CvProfile;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;

class CvBuilderController extends Controller
{
    public function index()
    {
        return view('profile.cv.index');
    }

    public function loadStep($step)
    {
        $user = auth()->user();
        $cvProfile = CvProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'tentang_diri' => '',
                'pendidikan' => [],
                'pengalaman_kerja' => [],
                'keahlian' => [],
                'sertifikasi' => [],
                'template' => 'modern'
            ]
        );

        $data = ['cv' => $cvProfile];

        // Ambil data auto-sync berdasarkan step
        if ($step == 1) {
            $data['user'] = [
                'name' => $user->name,
                'email' => $user->email,
                'personal_email' => $cvProfile->cv_email ?? $user->personal_email ?? null,
                'whatsapp' => $cvProfile->cv_whatsapp ?? data_get($user, 'whatsapp'),
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
            
            // Coba ambil dari Kemahasiswaan
            $kemahasiswaan = Kemahasiswaan::where('user_id', $user->id)->first();
            if ($kemahasiswaan) {
                $pendidikanSync[] = [
                    'institusi' => 'Universitas Diponegoro',
                    'jurusan' => 'S1 Teknik Komputer',
                    'tahun_masuk' => $kemahasiswaan->angkatan,
                    'tahun_lulus' => $kemahasiswaan->tahun_lulus,
                    'is_sync' => true
                ];
            } else {
                // Coba ambil dari Alumni
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
            // Pengalaman & Kegiatan
            $kegiatanSync = [];
            
            if ($user->hasRole('mahasiswa') && $user->student) {
                $riwayat = RiwayatKegiatan::with('kegiatan')->where('student_id', $user->student->id)->get();
                foreach ($riwayat as $rw) {
                    $kegiatanSync[] = [
                        'nama' => $rw->nama_kegiatan,
                        'peran' => $rw->peran_label,
                        'tanggal' => $rw->tanggal_display,
                        'is_sync' => true
                    ];
                }
            }
            
            $pengalamanSync = [];
            $alumni = Alumni::where('user_id', $user->id)->first();
            if ($alumni && $alumni->perusahaan) {
                $pengalamanSync[] = [
                    'perusahaan' => $alumni->perusahaan,
                    'posisi' => $alumni->jabatan,
                    'tahun_mulai' => $alumni->tahun_mulai_bekerja,
                    'tahun_selesai' => null,
                    'deskripsi' => 'Data tersinkronisasi dari direktori alumni.',
                    'is_sync' => true
                ];
            }
            
            $data['kegiatan_sync'] = $kegiatanSync;
            $data['pengalaman_sync'] = $pengalamanSync;
            
        } elseif ($step == 4) {
            // Prestasi & Keahlian
            $prestasiSync = [];
            $kemahasiswaan = Kemahasiswaan::with('prestasi')->where('user_id', $user->id)->first();
            if ($kemahasiswaan && $kemahasiswaan->prestasi) {
                foreach ($kemahasiswaan->prestasi as $p) {
                    $prestasiSync[] = [
                        'nama' => $p->nama_prestasi,
                        'tingkat' => $p->tingkat,
                        'tahun' => $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->format('Y') : '-',
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
            $cvProfile->cv_whatsapp = $request->whatsapp;
            $cvProfile->cv_domisili = $request->cv_domisili;
            $cvProfile->cv_portfolio = $request->cv_portfolio;
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
        $user = auth()->user();
        $cvProfile = CvProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['tentang_diri' => '', 'pendidikan' => [], 'pengalaman_kerja' => [], 'keahlian' => [], 'sertifikasi' => [], 'template' => 'modern']
        );
        $data = $this->getAllCvData($user, $cvProfile);
        
        return view('profile.cv.template-ats', $data);
    }

    public function generate()
    {
        // For now, same as preview but intended to trigger print via JS
        $user = auth()->user();
        $cvProfile = CvProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['tentang_diri' => '', 'pendidikan' => [], 'pengalaman_kerja' => [], 'keahlian' => [], 'sertifikasi' => [], 'template' => 'modern']
        );
        $data = $this->getAllCvData($user, $cvProfile);
        $data['is_print'] = true;
        
        return view('profile.cv.template-ats', $data);
    }
    
    public function getAllCvData($user, $cvProfile)
    {
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'personal_email' => $cvProfile->cv_email ?? $user->personal_email ?? null,
                'whatsapp' => $cvProfile->cv_whatsapp ?? data_get($user, 'whatsapp'),
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
        
        // Populate sync data
        if ($user->hasRole('mahasiswa') && $user->student) {
            $data['user']['nim'] = $user->student->student_number;
            $data['user']['angkatan'] = $user->student->cohort_year;
            
            $riwayat = RiwayatKegiatan::with('kegiatan')->where('student_id', $user->student->id)->get();
            foreach ($riwayat as $rw) {
                $data['kegiatan'][] = [
                    'nama' => $rw->nama_kegiatan,
                    'peran' => $rw->peran_label,
                    'tanggal' => $rw->tanggal_display,
                ];
            }
        } elseif ($user->hasRole('alumni')) {
            $alumni = Alumni::where('user_id', $user->id)->first();
            if ($alumni) {
                $data['user']['nim'] = $alumni->nim;
                $data['user']['angkatan'] = $alumni->angkatan;
                
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
        
        $kemahasiswaan = Kemahasiswaan::with('prestasi')->where('user_id', $user->id)->first();
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
                        'tahun' => $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->format('Y') : '-',
                    ];
                }
            }
        }
        
        return $data;
    }
}
