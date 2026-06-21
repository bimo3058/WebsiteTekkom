<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('eo_kp_template', function (Blueprint $table) {
            $table->string('approver_role')->default('koordinator')->comment('koordinator, dosen_pembimbing, or tanpa_review');
        });
    }

    public function down(): void
    {
        Schema::table('eo_kp_template', function (Blueprint $table) {
            $table->dropColumn('approver_role');
        });
    }
};
