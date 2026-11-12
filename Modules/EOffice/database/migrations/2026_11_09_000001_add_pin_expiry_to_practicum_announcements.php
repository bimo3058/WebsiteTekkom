<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eo_prak_pengumuman_praktikum', function (Blueprint $table) {
            $table->timestamp('pinned_until')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('eo_prak_pengumuman_praktikum',fn(Blueprint $table)=>$table->dropColumn('pinned_until'));
    }
};
