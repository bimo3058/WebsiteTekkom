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
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_pengaduan', 'anon_token')) {
                $table->string('anon_token')->nullable()->unique()->after('is_anonim');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            if (Schema::hasColumn('mk_pengaduan', 'anon_token')) {
                $table->dropColumn('anon_token');
            }
        });
    }
};
