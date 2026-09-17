<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEXES = [
        'manprak_periode_pendaftaran' => ['eo_periode_active_lookup_idx', ['praktikum_id', 'is_aktif', 'jenis', 'created_at']],
        'pendaftaran_asprak' => ['eo_asprak_student_latest_idx', ['user_id', 'praktikum_id', 'created_at']],
        'pendaftaran_koordinator' => ['eo_koor_student_latest_idx', ['user_id', 'praktikum_id', 'created_at']],
        'asprak_praktikum' => ['eo_asprak_student_role_idx', ['user_id', 'praktikum_id', 'deleted_at', 'role']],
        'daftar_praktikan' => ['eo_praktikan_student_idx', ['user_id']],
        'pengumpulan_tugas' => ['eo_submission_student_task_idx', ['daftar_praktikan_id', 'tugas_id']],
        'absensi_praktikum' => ['eo_attendance_student_status_idx', ['daftar_praktikan_id', 'status']],
        'tugas_praktikum' => ['eo_task_module_deadline_idx', ['modul_id', 'is_published', 'deadline']],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => [$name, $columns]) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($columns, $name));
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::INDEXES, true) as $table => [$name]) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
        }
    }
};
