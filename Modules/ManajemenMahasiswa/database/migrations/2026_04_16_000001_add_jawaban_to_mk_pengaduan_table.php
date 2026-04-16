<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->text('jawaban')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->unsignedBigInteger('answered_by')->nullable();

            $table->foreign('answered_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            $table->dropForeign(['answered_by']);
            $table->dropColumn(['jawaban', 'answered_at', 'answered_by']);
        });
    }
};
