<?php

namespace Modules\ManajemenMahasiswa\Database\Seeders;

use App\Models\CvProfile;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;

/**
 * 10 akun alumni dummy untuk mengisi Direktori Alumni.
 *
 * Tiap akun dibuat selengkap akun asli: users + students + mk_kemahasiswaan
 * (status alumni) + mk_alumni + role "alumni" + cv_profiles. Idempotent —
 * aman dijalankan berulang (dicocokkan lewat NIM).
 *
 * Jalankan manual (sengaja TIDAK didaftarkan di DatabaseSeeder):
 *   php artisan db:seed --class="Modules\ManajemenMahasiswa\Database\Seeders\AlumniDummySeeder"
 *
 * Login: mahasiswaalumni1..10@student.kampus.ac.id / password123
 */
class AlumniDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->data() as $i => $d) {
                $this->buat($i + 1, $d);
            }
        });

        Cache::forget('mk.alumni.summary');
        Cache::forget('mk.dashboard.snapshot');
    }

    private function buat(int $no, array $d): void
    {
        // NIM Undip Tekkom: 240601 + YY angkatan + 2 digit kelas + 4 digit urut
        $nim = $d['nim'];
        $angkatan = (int) ('20' . substr($nim, 6, 2));
        $lulus = $d['tahun_lulus'];
        $wa = '+6281234500' . str_pad((string) (200 + $no), 3, '0', STR_PAD_LEFT);

        $email = 'mahasiswaalumni' . $no . '@student.kampus.ac.id';

        // Dicocokkan lewat NIM (bukan email) supaya akun hasil run sebelumnya
        // yang emailnya berbeda cukup diperbarui, bukan diduplikasi.
        $user = Student::where('student_number', $nim)->first()?->user
            ?? User::where('email', $email)->first();

        if ($user) {
            $user->update(['email' => $email]);
        } else {
            $user = User::create([
                'external_id'       => (string) Str::uuid(),
                'name'              => $d['nama'],
                'email'             => $email,
                'password'          => Hash::make('password123'),
                'whatsapp'          => $wa,
                'personal_email'    => $d['personal_email'],
                'email_verified_at' => now(),
                'last_login'        => now()->subDays(random_int(2, 60)),
            ]);
        }

        Student::firstOrCreate(
            ['user_id' => $user->id],
            ['student_number' => $nim, 'cohort_year' => $angkatan]
        );

        Kemahasiswaan::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama'        => $d['nama'],
                'nim'         => $nim,
                'angkatan'    => $angkatan,
                'status'      => Kemahasiswaan::STATUS_ALUMNI,
                'tahun_lulus' => $lulus,
                'profesi'     => $d['jabatan'],
                'kontak'      => $wa,
            ]
        );

        Alumni::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim'                 => $nim,
                'angkatan'            => $angkatan,
                'program_studi'       => 'S1 Teknik Komputer',
                'tahun_lulus'         => $lulus,
                'perusahaan'          => $d['perusahaan'],
                'jabatan'             => $d['jabatan'],
                'bidang_industri'     => $d['bidang'],
                'tahun_mulai_bekerja' => $d['mulai'],
                'status_karir'        => $d['status'],
                'linkedin'            => $d['linkedin'],
            ]
        );

        if (! $user->hasRole('alumni')) {
            $user->assignRole('alumni');
        }

        // Pendidikan Undip & pekerjaan saat ini disinkron otomatis ke CV dari
        // mk_kemahasiswaan / mk_alumni, jadi di sini hanya SMA + pengalaman lain.
        CvProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tentang_diri'        => $d['tentang'],
                'pendidikan'          => [[
                    'institusi'   => $d['sma'],
                    'jurusan'     => 'IPA',
                    'tahun_masuk' => (string) ($angkatan - 3),
                    'tahun_lulus' => (string) $angkatan,
                ]],
                'pengalaman_kerja'    => [[
                    'perusahaan'    => $d['magang'][0],
                    'posisi'        => $d['magang'][1],
                    'tahun_mulai'   => (string) ($lulus - 1),
                    'tahun_selesai' => (string) ($lulus - 1),
                    'deskripsi'     => $d['magang'][2],
                ]],
                'keahlian'            => array_map(
                    fn($k) => ['nama' => $k[0], 'level' => $k[1]],
                    $d['keahlian']
                ),
                'sertifikasi'         => [[
                    'nama'     => $d['sertifikat'][0],
                    'penerbit' => $d['sertifikat'][1],
                    'tahun'    => (string) $d['sertifikat'][2],
                ]],
                'kegiatan_organisasi' => [],
                'proyek'              => [],
                'bahasa'              => [
                    ['nama' => 'Indonesia', 'level' => 'Penutur Asli (Native)', 'skor' => '-'],
                    ['nama' => 'Inggris', 'level' => 'Menengah (Intermediate)', 'skor' => $d['toefl']],
                ],
                'template'            => 'modern',
                'cv_email'            => $d['personal_email'],
                'cv_whatsapp'         => $wa,
                'cv_domisili'         => $d['domisili'],
            ]
        );
    }

    private function data(): array
    {
        return [
            [
                'nama' => 'Rizky Aditya Nugroho', 'nim' => '24060119120014', 'tahun_lulus' => 2023,
                'status' => 'bekerja', 'perusahaan' => 'PT GoTo Gojek Tokopedia Tbk', 'jabatan' => 'Backend Engineer',
                'bidang' => 'e_commerce', 'mulai' => 2023,
                'linkedin' => 'https://www.linkedin.com/in/rizky-aditya-nugroho-14',
                'personal_email' => 'rizky.aditya.nugroho@gmail.com', 'domisili' => 'Jakarta Selatan',
                'sma' => 'SMA Negeri 1 Semarang', 'toefl' => 'TOEFL ITP 570',
                'tentang' => 'Lulusan Teknik Komputer Undip yang berfokus pada pengembangan layanan backend skala besar dan sistem terdistribusi.',
                'magang' => ['PT Bukalapak.com', 'Software Engineer Intern', 'Membangun API katalog produk dengan Go dan PostgreSQL.'],
                'keahlian' => [['Go', 'Advanced'], ['PostgreSQL', 'Advanced'], ['Docker', 'Intermediate']],
                'sertifikat' => ['AWS Certified Cloud Practitioner', 'Amazon Web Services', 2023],
            ],
            [
                'nama' => 'Nabila Putri Maharani', 'nim' => '24060119130027', 'tahun_lulus' => 2023,
                'status' => 'bekerja', 'perusahaan' => 'PT Bank Central Asia Tbk', 'jabatan' => 'IT Business Analyst',
                'bidang' => 'keuangan_perbankan', 'mulai' => 2024,
                'linkedin' => 'https://www.linkedin.com/in/nabila-putri-maharani-27',
                'personal_email' => 'nabila.maharani27@gmail.com', 'domisili' => 'Tangerang Selatan',
                'sma' => 'SMA Negeri 3 Yogyakarta', 'toefl' => 'TOEFL ITP 560',
                'tentang' => 'Menjembatani kebutuhan bisnis dan tim teknologi pada layanan perbankan digital. Tertarik pada analisis proses dan data.',
                'magang' => ['PT Telkom Indonesia (Persero) Tbk', 'System Analyst Intern', 'Menyusun dokumentasi kebutuhan sistem dan alur proses bisnis.'],
                'keahlian' => [['SQL', 'Advanced'], ['Business Process Modeling', 'Intermediate'], ['Power BI', 'Intermediate']],
                'sertifikat' => ['Certified Business Analysis Professional Foundation', 'IIBA', 2024],
            ],
            [
                'nama' => 'Dimas Prasetyo Wibowo', 'nim' => '24060120110033', 'tahun_lulus' => 2024,
                'status' => 'bekerja', 'perusahaan' => 'PT Astra Honda Motor', 'jabatan' => 'Embedded Systems Engineer',
                'bidang' => 'manufaktur', 'mulai' => 2024,
                'linkedin' => 'https://www.linkedin.com/in/dimas-prasetyo-wibowo-33',
                'personal_email' => 'dimas.prasetyo.w@gmail.com', 'domisili' => 'Bekasi',
                'sma' => 'SMA Negeri 5 Surabaya', 'toefl' => 'TOEFL ITP 545',
                'tentang' => 'Insinyur sistem tertanam dengan latar belakang IoT dan kendali; mengembangkan firmware untuk komponen kendaraan.',
                'magang' => ['PT Len Industri (Persero)', 'Embedded Engineer Intern', 'Mengembangkan firmware STM32 untuk modul akuisisi data.'],
                'keahlian' => [['C/C++', 'Advanced'], ['STM32 / FreeRTOS', 'Advanced'], ['Python', 'Intermediate']],
                'sertifikat' => ['Embedded Systems Essentials with Arm', 'Arm Education', 2023],
            ],
            [
                'nama' => 'Salsabila Nurul Azizah', 'nim' => '24060120140046', 'tahun_lulus' => 2024,
                'status' => 'studi_lanjut', 'perusahaan' => 'Institut Teknologi Bandung', 'jabatan' => 'Mahasiswa S2 Teknik Elektro',
                'bidang' => 'pendidikan', 'mulai' => 2024,
                'linkedin' => 'https://www.linkedin.com/in/salsabila-nurul-azizah-46',
                'personal_email' => 'salsabila.azizah46@gmail.com', 'domisili' => 'Bandung',
                'sma' => 'SMA Negeri 2 Purwokerto', 'toefl' => 'IELTS 6.5',
                'tentang' => 'Melanjutkan studi magister dengan minat riset pada pengolahan citra medis dan kecerdasan buatan.',
                'magang' => ['Laboratorium Sistem Cerdas Undip', 'Asisten Peneliti', 'Membantu eksperimen klasifikasi citra dengan CNN.'],
                'keahlian' => [['Python', 'Advanced'], ['PyTorch', 'Intermediate'], ['OpenCV', 'Intermediate']],
                'sertifikat' => ['Deep Learning Specialization', 'DeepLearning.AI', 2023],
            ],
            [
                'nama' => 'Fajar Ramadhan Hidayat', 'nim' => '24060120120058', 'tahun_lulus' => 2024,
                'status' => 'bekerja', 'perusahaan' => 'PT Indosat Ooredoo Hutchison Tbk', 'jabatan' => 'Network Operations Engineer',
                'bidang' => 'telekomunikasi', 'mulai' => 2025,
                'linkedin' => 'https://www.linkedin.com/in/fajar-ramadhan-hidayat-58',
                'personal_email' => 'fajar.hidayat58@gmail.com', 'domisili' => 'Depok',
                'sma' => 'SMA Negeri 1 Tegal', 'toefl' => 'TOEFL ITP 530',
                'tentang' => 'Berpengalaman pada operasional jaringan seluler dan monitoring performa layanan.',
                'magang' => ['PT XL Axiata Tbk', 'Network Engineer Intern', 'Memantau KPI jaringan radio dan menyiapkan laporan gangguan.'],
                'keahlian' => [['Routing & Switching', 'Advanced'], ['Linux Administration', 'Intermediate'], ['Network Monitoring', 'Intermediate']],
                'sertifikat' => ['Cisco Certified Network Associate (CCNA)', 'Cisco', 2024],
            ],
            [
                'nama' => 'Aulia Rahmawati', 'nim' => '24060121130019', 'tahun_lulus' => 2025,
                'status' => 'bekerja', 'perusahaan' => 'PT Global Tiket Network (Tiket.com)', 'jabatan' => 'Frontend Engineer',
                'bidang' => 'e_commerce', 'mulai' => 2025,
                'linkedin' => 'https://www.linkedin.com/in/aulia-rahmawati-19',
                'personal_email' => 'aulia.rahmawati19@gmail.com', 'domisili' => 'Jakarta Barat',
                'sma' => 'SMA Negeri 8 Semarang', 'toefl' => 'TOEFL ITP 575',
                'tentang' => 'Frontend engineer yang peduli pada performa web dan aksesibilitas antarmuka.',
                'magang' => ['PT Dicoding Akademi Indonesia', 'Web Developer Intern', 'Mengembangkan komponen antarmuka platform belajar dengan React.'],
                'keahlian' => [['React', 'Advanced'], ['TypeScript', 'Intermediate'], ['Tailwind CSS', 'Advanced']],
                'sertifikat' => ['Belajar Membuat Front-End Web untuk Pemula', 'Dicoding Indonesia', 2023],
            ],
            [
                'nama' => 'Bagas Setiawan Pamungkas', 'nim' => '24060121110062', 'tahun_lulus' => 2025,
                'status' => 'wirausaha', 'perusahaan' => 'CV Cipta Solusi Digital', 'jabatan' => 'Co-Founder & CTO',
                'bidang' => 'startup', 'mulai' => 2025,
                'linkedin' => 'https://www.linkedin.com/in/bagas-setiawan-pamungkas-62',
                'personal_email' => 'bagas.pamungkas62@gmail.com', 'domisili' => 'Semarang',
                'sma' => 'SMA Negeri 4 Semarang', 'toefl' => 'TOEFL ITP 550',
                'tentang' => 'Mendirikan studio pengembangan perangkat lunak yang melayani UMKM dan instansi lokal di Jawa Tengah.',
                'magang' => ['PT Aplikasi Karya Anak Bangsa', 'Mobile Developer Intern', 'Membangun fitur pembayaran pada aplikasi Android.'],
                'keahlian' => [['Laravel', 'Advanced'], ['Flutter', 'Intermediate'], ['Manajemen Proyek', 'Intermediate']],
                'sertifikat' => ['Google Cloud Digital Leader', 'Google Cloud', 2024],
            ],
            [
                'nama' => 'Intan Permatasari', 'nim' => '24060121140077', 'tahun_lulus' => 2025,
                'status' => 'bekerja', 'perusahaan' => 'Dinas Komunikasi dan Informatika Provinsi Jawa Tengah', 'jabatan' => 'Pranata Komputer Ahli Pertama',
                'bidang' => 'pemerintahan', 'mulai' => 2026,
                'linkedin' => 'https://www.linkedin.com/in/intan-permatasari-77',
                'personal_email' => 'intan.permatasari77@gmail.com', 'domisili' => 'Semarang',
                'sma' => 'SMA Negeri 2 Pati', 'toefl' => 'TOEFL ITP 540',
                'tentang' => 'Aparatur sipil negara yang mengelola infrastruktur dan layanan digital pemerintah daerah.',
                'magang' => ['Diskominfo Kota Semarang', 'Magang Pengelola Jaringan', 'Membantu pemeliharaan jaringan dan server aplikasi layanan publik.'],
                'keahlian' => [['Administrasi Server Linux', 'Intermediate'], ['Keamanan Jaringan', 'Intermediate'], ['Basis Data', 'Advanced']],
                'sertifikat' => ['Certified Ethical Hacker Fundamentals', 'EC-Council', 2025],
            ],
            [
                'nama' => 'Muhammad Naufal Hakim', 'nim' => '24060122120021', 'tahun_lulus' => 2026,
                'status' => 'bekerja', 'perusahaan' => 'PT Kalbe Farma Tbk', 'jabatan' => 'IT Infrastructure Engineer',
                'bidang' => 'kesehatan', 'mulai' => 2026,
                'linkedin' => 'https://www.linkedin.com/in/muhammad-naufal-hakim-21',
                'personal_email' => 'naufal.hakim21@gmail.com', 'domisili' => 'Bekasi',
                'sma' => 'SMA Negeri 1 Kudus', 'toefl' => 'TOEFL ITP 555',
                'tentang' => 'Mengelola infrastruktur server dan jaringan untuk mendukung sistem operasional perusahaan farmasi.',
                'magang' => ['PT Pertamina Digital Innovation', 'IT Operations Intern', 'Membantu monitoring server dan otomasi backup basis data.'],
                'keahlian' => [['VMware / Virtualisasi', 'Intermediate'], ['Linux Administration', 'Advanced'], ['Shell Scripting', 'Intermediate']],
                'sertifikat' => ['Red Hat Certified System Administrator Prep', 'Red Hat Academy', 2025],
            ],
            [
                'nama' => 'Tiara Kusuma Dewi', 'nim' => '24060122130035', 'tahun_lulus' => 2026,
                'status' => 'belum_bekerja', 'perusahaan' => null, 'jabatan' => null,
                'bidang' => null, 'mulai' => null,
                'linkedin' => null,
                'personal_email' => 'tiara.kusumadewi35@gmail.com', 'domisili' => 'Magelang',
                'sma' => 'SMA Negeri 1 Magelang', 'toefl' => 'TOEFL ITP 520',
                'tentang' => 'Lulusan baru yang sedang mencari posisi di bidang pengembangan perangkat lunak dan data.',
                'magang' => ['PT Telkomsel', 'Data Analyst Intern', 'Membuat dashboard penggunaan layanan dengan Tableau.'],
                'keahlian' => [['Python', 'Intermediate'], ['SQL', 'Intermediate'], ['Tableau', 'Intermediate']],
                'sertifikat' => ['Google Data Analytics Certificate', 'Coursera', 2025],
            ],
        ];
    }
}
