<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new string column
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->string('tahun_ajaran_new')->nullable();
        });

        // 2. Migrate data based on user logic: 
        // "misal saat ini yang tersimpan 2025 ganjil maka jadinya nanti 2025/2026 ganjil"
        // "tetapi jika 2025 genap maka jadinya 2024/2025 genap"
        $praktikums = DB::table('eo_praktikum')->get();
        foreach ($praktikums as $p) {
            $year = (int)$p->tahun_ajaran;
            $semester = strtolower($p->semester);
            
            if ($semester === 'ganjil') {
                $newFormat = $year . '/' . ($year + 1);
            } else { // genap or others
                $newFormat = ($year - 1) . '/' . $year;
            }

            DB::table('eo_praktikum')->where('id', $p->id)->update([
                'tahun_ajaran_new' => $newFormat
            ]);
        }

        // 3. Drop old column
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran');
        });

        // 4. Rename new column to original name
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->renameColumn('tahun_ajaran_new', 'tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse logic
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->integer('tahun_ajaran_old')->nullable();
        });

        $praktikums = DB::table('eo_praktikum')->get();
        foreach ($praktikums as $p) {
            $str = $p->tahun_ajaran;
            // e.g. "2025/2026"
            $parts = explode('/', $str);
            $year = (int)($parts[0] ?? date('Y'));
            $semester = strtolower($p->semester);

            if ($semester === 'genap') {
                $year = $year + 1; // 2024/2025 Genap -> 2025
            } else {
                $year = $year; // 2025/2026 Ganjil -> 2025
            }

            DB::table('eo_praktikum')->where('id', $p->id)->update([
                'tahun_ajaran_old' => $year
            ]);
        }

        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->renameColumn('tahun_ajaran_old', 'tahun_ajaran');
        });
    }
};
