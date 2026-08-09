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
        $columns = [
            'cv_domisili' => 'string',
            'cv_portfolio' => 'string',
            'kegiatan_organisasi' => 'json',
            'proyek' => 'json',
            'bahasa' => 'json',
        ];

        $missing = array_filter(
            $columns,
            fn (string $type, string $column) => ! Schema::hasColumn('cv_profiles', $column),
            ARRAY_FILTER_USE_BOTH
        );

        if ($missing === []) {
            return;
        }

        Schema::table('cv_profiles', function (Blueprint $table) use ($missing) {
            foreach ($missing as $column => $type) {
                $table->{$type}($column)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kolom utama dimiliki migration global dengan timestamp yang sama.
    }
};
