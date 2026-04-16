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
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kegiatan', 'kepengurusan_id')) {
                $table->unsignedBigInteger('kepengurusan_id')->nullable()->after('bidang_id');
                $table->foreign('kepengurusan_id')->references('id')->on('mk_kepengurusan')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['kepengurusan_id']);
            $table->dropColumn('kepengurusan_id');
        });
    }
};
