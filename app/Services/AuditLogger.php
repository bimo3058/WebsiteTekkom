<?php
namespace App\Services;

use App\Models\AuditLog;
use App\Models\BsAuditLog;
use App\Models\MkAuditLog;
use App\Models\EoAuditLog;
use App\Models\CapstoneAuditLog; // model untuk capstone_audit_logs yang sudah ada
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogger
{
    // Map module → model detail per modul
    private static array $moduleModels = [
        'bank_soal'           => BsAuditLog::class,
        'manajemen_mahasiswa' => MkAuditLog::class,
        'eoffice'             => EoAuditLog::class,
        'capstone'            => CapstoneAuditLog::class,
    ];

    /**
     * Method utama — dual write ke audit_logs (terpusat) + tabel per modul
     */
    public static function log(
        string  $module,
        string  $action,
        string  $description,
        ?Model  $subject  = null,
        ?array  $oldData  = null,
        ?array  $newData  = null,
        ?int    $userId   = null,
    ): void {
        $userId      = $userId ?? Auth::id();
        $subjectType = $subject ? class_basename($subject) : null;
        $subjectId   = $subject?->getKey();
        $now         = now();

        DB::transaction(function () use (
            $module, $action, $description,
            $subjectType, $subjectId,
            $oldData, $newData,
            $userId, $now
        ) {
            // 1. Tulis ke tabel terpusat (ringan, tanpa old/new data)
            AuditLog::create([
                'user_id'      => $userId,
                'module'       => $module,
                'action'       => $action,
                'subject_type' => $subjectType,
                'subject_id'   => $subjectId,
                'description'  => $description,
                'created_at'   => $now,
            ]);

            // 2. Tulis ke tabel per modul (lengkap dengan old/new data)
            $modelClass = self::$moduleModels[$module] ?? null;
            if ($modelClass) {
                $modelClass::create([
                    'user_id'      => $userId,
                    'action'       => $action,
                    'subject_type' => $subjectType,
                    'subject_id'   => $subjectId,
                    'description'  => $description,
                    'old_data'     => $oldData,
                    'new_data'     => $newData,
                    'created_at'   => $now,
                ]);
            }
        });
    }

    // ── Shortcut helpers ──────────────────────────────────────────────────────

    public static function create(
        string $module, string $desc,
        ?Model $subject = null, ?array $newData = null
    ): void {
        self::log($module, 'CREATE', $desc, $subject, null, $newData);
    }

    public static function update(
        string $module, string $desc,
        ?Model $subject = null, ?array $oldData = null, ?array $newData = null
    ): void {
        self::log($module, 'UPDATE', $desc, $subject, $oldData, $newData);
    }

    public static function delete(
        string $module, string $desc,
        ?Model $subject = null, ?array $oldData = null
    ): void {
        self::log($module, 'DELETE', $desc, $subject, $oldData);
    }

    public static function view(
        string $module, string $desc, ?Model $subject = null
    ): void {
        self::log($module, 'VIEW', $desc, $subject);
    }
}