<?php

namespace Modules\EOffice\Services;

use Illuminate\Http\Request;
use Modules\EOffice\Models\Notifikasi;

class PracticumNotificationAudience
{
    public const ROLES = ['cerc'=>'koordinator_cerc', 'koor'=>'koor_prak', 'asprak'=>'asprak', 'mahasiswa'=>'mahasiswa', 'dosen'=>'dosen', 'admin'=>'admin_praktikum'];

    public static function roleForTitle(string $title): ?string
    {
        $title = mb_strtolower($title);
        if ($title === 'pengajuan jadwal praktikum') return 'cerc';
        if (in_array($title, ['pengajuan modul baru', 'pengajuan modul ditarik'], true)) return 'dosen';
        if (str_starts_with($title, 'pengajuan modul')) return 'koor';
        if (str_contains($title, 'siap diproses')) return 'admin';
        if (str_starts_with($title, 'pendaftaran') || in_array($title, ['perbaikan tugas akhir', 'nilai tugas akhir'], true)) return 'mahasiswa';
        if ($title === 'pengumpulan tugas akhir') return 'asprak';
        return null;
    }

    public static function authorizedRole(Request $request): string
    {
        $role = $request->input('role');
        abort_unless(is_string($role) && isset(self::ROLES[$role]), 422, 'Role notifikasi tidak valid.');
        abort_unless($request->user()->hasRole(self::ROLES[$role]) || $request->user()->hasRole('superadmin'), 403);
        return $role;
    }

    public static function query(int $userId, string $role)
    {
        $query = Notifikasi::where('user_id', $userId);
        if (! \Illuminate\Support\Facades\Schema::hasColumn('eo_notifikasi', 'practicum_role')) return $query->whereRaw('1 = 0');
        return $query->where('practicum_role', $role);
    }
}
