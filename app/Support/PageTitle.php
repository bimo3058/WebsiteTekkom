<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class PageTitle
{
    /** @var array<string, string> */
    private const EXACT_TITLES = [
        'login' => 'Masuk',
        'register' => 'Daftar Akun',
        'microsoft.redirect' => 'Masuk dengan SSO',
        'microsoft.callback' => 'Menghubungkan SSO',
        'microsoft.switch' => 'Ganti Akun',
        'sso.password' => 'Verifikasi SSO',
        'password.request' => 'Lupa Kata Sandi',
        'password.reset' => 'Atur Ulang Kata Sandi',
        'password.confirm' => 'Konfirmasi Kata Sandi',
        'verification.notice' => 'Verifikasi Email',
        'dashboard' => 'Dashboard',
        'profile.edit' => 'Profil Saya',
        'profile.cv.index' => 'CV Saya',
        'profile.cv.preview' => 'Pratinjau CV',
        'profile.cv.generate' => 'Unduh CV',
        'superadmin.dashboard' => 'Dashboard Superadmin',
        'superadmin.users.index' => 'Manajemen Pengguna',
        'superadmin.users.show' => 'Detail Pengguna',
        'superadmin.users.edit' => 'Edit Pengguna',
        'superadmin.users.online' => 'Pengguna Online',
        'superadmin.users.suspended' => 'Pengguna Ditangguhkan',
        'superadmin.modules' => 'Manajemen Modul',
        'superadmin.permissions' => 'Manajemen Hak Akses',
        'superadmin.permissions.show' => 'Detail Hak Akses',
        'superadmin.permissions.category' => 'Hak Akses per Kategori',
        'superadmin.audit-logs' => 'Log Audit',
        'capstone.dashboard' => 'Buka SICATA',
    ];

    /** @var array<string, string> */
    private const LABELS = [
        'aktivasi' => 'Aktivasi Sesi',
        'alokasi-sesi' => 'Alokasi Sesi',
        'analytics' => 'Analitik',
        'arsip' => 'Arsip',
        'assessment-bank' => 'Bank Asesmen',
        'audit-logs' => 'Log Audit',
        'bidding' => 'Penawaran Judul',
        'bimbingan' => 'Bimbingan',
        'cbt' => 'CBT',
        'cpl' => 'CPL',
        'cpmk' => 'CPMK',
        'dashboard' => 'Dashboard',
        'data-mahasiswa' => 'Data Mahasiswa',
        'direktori' => 'Direktori',
        'document-requirements' => 'Persyaratan Dokumen',
        'document-types' => 'Tipe Dokumen',
        'document-uploads' => 'Unggahan Dokumen',
        'documents' => 'Dokumen',
        'evaluation' => 'Penilaian',
        'evaluation-setup' => 'Pengaturan Penilaian',
        'expo' => 'Expo Capstone',
        'finalization' => 'Finalisasi',
        'forum' => 'Forum',
        'groups' => 'Kelompok',
        'jadwal' => 'Jadwal',
        'kegiatan' => 'Kegiatan',
        'kelola-role' => 'Kelola Role',
        'locations' => 'Lokasi',
        'mata-kuliah' => 'Mata Kuliah',
        'modules' => 'Modul Sistem',
        'notifications' => 'Notifikasi',
        'pendaftaran' => 'Pendaftaran',
        'pengaduan' => 'Pengaduan',
        'pengumuman' => 'Pengumuman',
        'permissions' => 'Hak Akses',
        'periode' => 'Periode',
        'periods' => 'Periode',
        'profile' => 'Profil',
        'proker' => 'Program Kerja',
        'reports' => 'Laporan',
        'riwayat' => 'Riwayat',
        'rps' => 'RPS',
        'schedule' => 'Jadwal',
        'sempro' => 'Sidang Proposal',
        'settings' => 'Pengaturan',
        'soal' => 'Bank Soal',
        'students' => 'Mahasiswa',
        'ta-defense' => 'Sidang Tugas Akhir',
        'titles' => 'Judul',
        'users' => 'Pengguna',
        'validasi-berkas' => 'Validasi Berkas',
        'verifikasi' => 'Verifikasi',
    ];

    /** @var array<string, string> */
    private const ACTIONS = [
        'create' => 'Tambah',
        'detail' => 'Detail',
        'edit' => 'Edit',
        'history' => 'Riwayat',
        'preview' => 'Pratinjau',
        'review' => 'Review',
        'show' => 'Detail',
    ];

    /** @var list<string> */
    private const CONTEXT_SEGMENTS = [
        'admin',
        'alumni',
        'api',
        'asprak',
        'banksoal',
        'capstone',
        'dosen',
        'eoffice',
        'gpm',
        'koor',
        'koordinator',
        'kp',
        'mahasiswa',
        'manajemenmahasiswa',
        'manprak',
        'pengurus',
        'superadmin',
        'user',
        'v1',
    ];

    /** @return array{page: string, brand: string, full: string} */
    public static function forRequest(Request $request): array
    {
        $path = '/'.trim($request->path(), '/');
        $path = $path === '/.' ? '/' : $path;
        $routeName = $request->route()?->getName();
        $page = self::resolve($routeName, $path);
        $brand = self::brandForPath($path);

        return [
            'page' => $page,
            'brand' => $brand,
            'full' => "{$page} | {$brand}",
        ];
    }

    public static function resolve(?string $routeName, string $path = '/'): string
    {
        if ($routeName !== null && isset(self::EXACT_TITLES[$routeName])) {
            return self::EXACT_TITLES[$routeName];
        }

        $segments = $routeName
            ? explode('.', $routeName)
            : explode('/', trim($path, '/'));
        $segments = array_values(array_filter(
            array_map(self::normalizeSegment(...), $segments),
            fn (string $segment) => $segment !== '' && ! ctype_digit($segment)
        ));

        if ($segments === []) {
            return 'Beranda';
        }

        $last = end($segments);
        if ($last === 'index') {
            array_pop($segments);
            $last = self::lastMeaningfulSegment($segments);

            return self::label($last ?? 'beranda');
        }

        if ($last === 'dashboard') {
            $role = self::lastRoleSegment(array_slice($segments, 0, -1));

            return $role ? 'Dashboard '.self::label($role) : 'Dashboard';
        }

        if (isset(self::ACTIONS[$last])) {
            array_pop($segments);
            $subject = self::lastMeaningfulSegment($segments);

            return trim(self::ACTIONS[$last].' '.self::label($subject ?? 'halaman'));
        }

        return self::label(self::lastMeaningfulSegment($segments) ?? $last);
    }

    public static function brandForPath(string $path): string
    {
        $path = '/'.trim($path, '/');

        return match (true) {
            Str::startsWith($path, ['/bank-soal', '/ujian-komprehensif']) => 'SIBASO',
            Str::startsWith($path, '/capstone') => 'SICATA',
            Str::startsWith($path, '/eoffice/kp') => 'SIKP',
            Str::startsWith($path, '/eoffice/manprak') => 'SIPERKOM',
            Str::startsWith($path, '/eoffice') => 'E-Office',
            Str::startsWith($path, '/manajemen-mahasiswa') => 'Manajemen Mahasiswa',
            default => 'SITKOM',
        };
    }

    public static function isGenericExistingTitle(string $title): bool
    {
        $title = Str::of(html_entity_decode(strip_tags($title)))
            ->squish()
            ->lower()
            ->toString();

        return $title === ''
            || str_contains($title, 'laravel')
            || in_array($title, [
                'portal mahasiswa',
                'portal dosen',
                'portal mahasiswa - admin',
                'admin portal',
            ], true);
    }

    private static function normalizeSegment(string $segment): string
    {
        return Str::of($segment)->replace('_', '-')->lower()->toString();
    }

    /** @param list<string> $segments */
    private static function lastMeaningfulSegment(array $segments): ?string
    {
        foreach (array_reverse($segments) as $segment) {
            if (! in_array($segment, self::CONTEXT_SEGMENTS, true)) {
                return $segment;
            }
        }

        return $segments === [] ? null : end($segments);
    }

    /** @param list<string> $segments */
    private static function lastRoleSegment(array $segments): ?string
    {
        foreach (array_reverse($segments) as $segment) {
            if (in_array($segment, ['admin', 'alumni', 'asprak', 'dosen', 'gpm', 'koordinator', 'mahasiswa', 'pengurus', 'superadmin'], true)) {
                return $segment;
            }
        }

        return null;
    }

    private static function label(string $segment): string
    {
        if (isset(self::LABELS[$segment])) {
            return self::LABELS[$segment];
        }

        return Str::of($segment)->replace('-', ' ')->headline()->toString();
    }
}
