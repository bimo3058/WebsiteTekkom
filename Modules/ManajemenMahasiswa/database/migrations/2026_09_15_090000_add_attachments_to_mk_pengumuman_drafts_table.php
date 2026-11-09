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
        Schema::table('mk_pengumuman_drafts', function (Blueprint $table) {
            $table->unsignedBigInteger('poster_repo_id')->nullable()->after('konten');
            $table->json('lampiran_repo_ids')->nullable()->after('poster_repo_id');

            $table->foreign('poster_repo_id')
                ->references('id')
                ->on('mk_repo_mulmed')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mk_pengumuman_drafts', function (Blueprint $table) {
            $table->dropForeign(['poster_repo_id']);
            $table->dropColumn(['poster_repo_id', 'lampiran_repo_ids']);
        });
    }
};
