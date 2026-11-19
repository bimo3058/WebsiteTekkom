<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Multi-file attachments are stored as a JSON string. A varchar(255)
     * cannot reliably hold the paths and original names of up to three files.
     */
    public function up(): void
    {
        // Fresh installations still use historical names at this point, while
        // restored databases may already have the standardized eo_prak_ names.
        $targets = [];
        foreach ([
            'pengumpulan_tugas' => true,
            'riwayat_pengumpulan_tugas' => false,
            'tugas_praktikum' => true,
        ] as $legacy => $nullable) {
            $current = 'eo_prak_'.$legacy;
            $hasLegacy = Schema::hasTable($legacy);
            $hasCurrent = Schema::hasTable($current);

            if ($hasLegacy === $hasCurrent) {
                throw new RuntimeException("Expected exactly one table: {$legacy} or {$current}.");
            }

            $name = $hasCurrent ? $current : $legacy;
            if (! Schema::hasColumn($name, 'file_path')) {
                throw new RuntimeException("Missing file_path column on {$name}.");
            }
            $targets[$name] = $nullable;
        }

        foreach ($targets as $name => $nullable) {
            Schema::table($name, function (Blueprint $table) use ($nullable) {
                $table->text('file_path')->nullable($nullable)->change();
            });
        }
    }

    /**
     * Keep these columns as text when rolling back. Once multi-file JSON has
     * been saved, narrowing them to varchar(255) could truncate user data.
     */
    public function down(): void
    {
        // Intentionally left unchanged to prevent attachment data loss.
    }
};
