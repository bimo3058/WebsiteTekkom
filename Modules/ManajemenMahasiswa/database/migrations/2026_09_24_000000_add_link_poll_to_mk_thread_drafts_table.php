<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_thread_drafts', function (Blueprint $table) {
            if (! Schema::hasColumn('mk_thread_drafts', 'link_url')) {
                $table->string('link_url', 2000)->nullable()->after('media_files');
            }
            if (! Schema::hasColumn('mk_thread_drafts', 'poll_data')) {
                $table->jsonb('poll_data')->nullable()->after('link_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_thread_drafts', function (Blueprint $table) {
            $table->dropColumn(['link_url', 'poll_data']);
        });
    }
};
