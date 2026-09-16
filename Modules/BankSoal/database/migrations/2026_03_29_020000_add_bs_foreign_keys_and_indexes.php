<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function foreignKeys(): array
    {
        return [
            'bs_mata_kuliah_cpl' => [
                ['mk_id', 'bs_mata_kuliah', 'cascade'],
                ['cpl_id', 'bs_cpl', 'cascade'],
            ],
            'bs_pertanyaan' => [
                ['cpl_id', 'bs_cpl', 'cascade'],
                ['mk_id', 'bs_mata_kuliah', 'cascade'],
            ],
            'bs_jawaban' => [
                ['soal_id', 'bs_pertanyaan', 'cascade'],
            ],
            'bs_dosen_pengampu_mk' => [
                ['user_id', 'users', 'cascade'],
                ['mk_id', 'bs_mata_kuliah', 'cascade'],
            ],
            'bs_rps_detail' => [
                ['mk_id', 'bs_mata_kuliah', 'cascade'],
            ],
            'bs_rps_cpl' => [
                ['rps_id', 'bs_rps_detail', 'cascade'],
                ['cpl_id', 'bs_cpl', 'cascade'],
            ],
            'bs_rps_cpmk' => [
                ['rps_id', 'bs_rps_detail', 'cascade'],
                ['cpmk_id', 'bs_cpmk', 'cascade'],
            ],
            'bs_rps_dosen' => [
                ['rps_id', 'bs_rps_detail', 'cascade'],
                ['dosen_id', 'users', 'cascade'],
            ],
            'bs_hasil_review_rps' => [
                ['rps_detail_id', 'bs_rps_detail', 'cascade'],
                ['parameter_id', 'bs_parameter', 'restrict'],
            ],
            'bs_kompre_session' => [
                ['user_id', 'users', 'cascade'],
            ],
            'bs_kompre_jawaban' => [
                ['kompre_session_id', 'bs_kompre_session', 'cascade'],
                ['pertanyaan_id', 'bs_pertanyaan', 'cascade'],
                ['jawaban_dipilih', 'bs_jawaban', 'cascade'],
            ],
            'bs_review' => [
                ['pertanyaan_id', 'bs_pertanyaan', 'cascade'],
            ],
        ];
    }

    public function up(): void
    {
        Schema::create('bs_cpl_cpmk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('bs_mata_kuliah')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('cpl_id')->constrained('bs_cpl')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('cpmk_id')->constrained('bs_cpmk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['mk_id', 'cpl_id', 'cpmk_id'], 'bs_cpl_cpmk_unique');
        });

        foreach ($this->foreignKeys() as $tableName => $foreignKeys) {
            Schema::table($tableName, function (Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as [$column, $target, $onDelete]) {
                    $foreign = $table->foreign($column)
                        ->references('id')
                        ->on($target)
                        ->cascadeOnUpdate();

                    $onDelete === 'restrict'
                        ? $foreign->restrictOnDelete()
                        : $foreign->cascadeOnDelete();
                }
            });
        }

        Schema::table('bs_audit_logs', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id'], 'bs_audit_subject_index');
            $table->index('created_at', 'bs_audit_created_index');
        });

        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->index('status', 'bs_periode_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->dropIndex('bs_periode_status_index');
        });

        Schema::table('bs_audit_logs', function (Blueprint $table) {
            $table->dropIndex('bs_audit_subject_index');
            $table->dropIndex('bs_audit_created_index');
        });

        foreach (array_reverse($this->foreignKeys(), true) as $tableName => $foreignKeys) {
            Schema::table($tableName, function (Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as [$column]) {
                    $table->dropForeign([$column]);
                }
            });
        }

        Schema::dropIfExists('bs_cpl_cpmk');
    }
};
