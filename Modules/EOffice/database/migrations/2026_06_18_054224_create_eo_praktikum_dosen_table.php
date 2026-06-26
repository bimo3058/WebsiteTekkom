<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create pivot table
        Schema::create('eo_praktikum_dosen', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('dosen_id');
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['praktikum_id', 'dosen_id']);
        });

        // 2. Migrate existing data
        $praktikums = DB::table('eo_praktikum')->whereNotNull('dosen_id')->get();
        foreach ($praktikums as $p) {
            DB::table('eo_praktikum_dosen')->insert([
                'praktikum_id' => $p->id,
                'dosen_id'     => $p->dosen_id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // 3. Drop existing foreign key & column from eo_praktikum
        Schema::table('eo_praktikum', function (Blueprint $table) {
            // Drop foreign key first. The constraint name is usually <table_name>_<column_name>_foreign
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 3: Add column back
        Schema::table('eo_praktikum', function (Blueprint $table) {
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('set null');
        });

        // Revert 2: Move data back. Since we only had one before, we pick the first dosen for each praktikum
        $pivotData = DB::table('eo_praktikum_dosen')->get();
        foreach ($pivotData as $pd) {
            DB::table('eo_praktikum')
                ->where('id', $pd->praktikum_id)
                ->whereNull('dosen_id')
                ->update(['dosen_id' => $pd->dosen_id]);
        }

        // Revert 1: Drop pivot table
        Schema::dropIfExists('eo_praktikum_dosen');
    }
};
