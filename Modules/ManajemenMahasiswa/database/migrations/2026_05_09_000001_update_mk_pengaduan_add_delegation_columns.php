<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_pengaduan', 'reopen_count')) {
                $table->unsignedTinyInteger('reopen_count')->default(0)->after('answered_by');
            }
            if (!Schema::hasColumn('mk_pengaduan', 'reopen_reason')) {
                $table->text('reopen_reason')->nullable()->after('reopen_count');
            }
            if (!Schema::hasColumn('mk_pengaduan', 'auto_close_at')) {
                $table->timestamp('auto_close_at')->nullable()->after('reopen_reason');
            }
            if (!Schema::hasColumn('mk_pengaduan', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('auto_close_at');
            }
            if (!Schema::hasColumn('mk_pengaduan', 'closed_by')) {
                $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');
                $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_pengaduan', function (Blueprint $table) {
            if (Schema::hasColumn('mk_pengaduan', 'closed_by')) {
                $table->dropForeign(['closed_by']);
            }
            $cols = ['reopen_count', 'reopen_reason', 'auto_close_at', 'closed_at', 'closed_by'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('mk_pengaduan', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
