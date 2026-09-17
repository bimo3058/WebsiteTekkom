<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ImportStatus;
use App\Models\User;
use Illuminate\Support\Collection;

class SuperAdminNotifications
{
    public const OPTIONS = [
        'authentication' => ['label' => 'Login dan logout pengguna', 'description' => 'Aktivitas masuk dan keluar sistem yang tercatat di audit log.'],
        'user_management' => ['label' => 'Pengguna dan hak akses', 'description' => 'Pembuatan akun, perubahan role atau permission, suspend, dan force logout.'],
        'module_activity' => ['label' => 'Aktivitas aplikasi', 'description' => 'Aktivitas Bank Soal, Capstone, E-Office, dan Manajemen Mahasiswa yang tercatat di audit log pusat.'],
        'imports' => ['label' => 'Hasil impor pengguna', 'description' => 'Impor yang Anda jalankan telah selesai atau gagal.'],
    ];

    public function preferences(User $user): array
    {
        $defaults = array_fill_keys(array_keys(self::OPTIONS), true);
        $stored = $user->notification_preferences ?? [];

        return array_replace($defaults, array_intersect_key($stored, $defaults));
    }

    public function recent(User $user): Collection
    {
        $preferences = $this->preferences($user);
        $since = now()->subDays(7);
        $modules = [];
        if ($preferences['authentication']) {
            $modules[] = 'auth';
        }
        if ($preferences['user_management']) {
            $modules[] = 'user_management';
        }
        if ($preferences['module_activity']) {
            $modules = array_merge($modules, ['bank_soal', 'capstone', 'eoffice', 'manajemen_mahasiswa']);
        }

        $items = collect();
        if ($modules !== []) {
            $items = AuditLog::query()->whereIn('module', $modules)
                ->where('created_at', '>=', $since)
                ->where(function ($query) {
                    $query->where('module', '!=', 'auth')->orWhereIn('action', ['LOGIN', 'LOGOUT']);
                })
                ->orderByDesc('created_at')->orderByDesc('id')->limit(10)->get()
                ->map(fn ($log) => [
                    'id' => 'audit-'.$log->id,
                    'title' => $log->module_label.' · '.$log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans(),
                    'timestamp' => $log->created_at->timestamp,
                    'url' => route('superadmin.audit-logs', ['module' => $log->module, 'action' => $log->action]),
                ]);
        }

        if ($preferences['imports']) {
            $imports = ImportStatus::query()->where('user_id', $user->id)
                ->whereIn('status', ['completed', 'failed'])->where('updated_at', '>=', $since)
                ->orderByDesc('updated_at')->orderByDesc('id')->limit(10)->get()
                ->map(fn ($import) => [
                    'id' => 'import-'.$import->id,
                    'title' => $import->status === 'completed' ? 'Impor pengguna selesai' : 'Impor pengguna gagal',
                    'description' => $import->filename,
                    'time' => $import->updated_at->diffForHumans(),
                    'timestamp' => $import->updated_at->timestamp,
                    'url' => route('superadmin.dashboard'),
                ]);
            $items = $items->concat($imports);
        }

        return $items->sortByDesc('timestamp')->take(10)->values();
    }
}
