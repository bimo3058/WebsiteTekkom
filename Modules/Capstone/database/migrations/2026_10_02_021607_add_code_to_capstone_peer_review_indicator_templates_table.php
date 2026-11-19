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
        Schema::table('capstone_peer_review_indicator_templates', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
        });

        DB::table('capstone_peer_review_indicator_templates')
            ->whereNull('code')
            ->orderBy('id')
            ->eachById(function ($row) {
                DB::table('capstone_peer_review_indicator_templates')
                    ->where('id', $row->id)
                    ->update(['code' => 'PR-'.$row->id]);
            });

        Schema::table('capstone_peer_review_indicator_templates', function (Blueprint $table) {
            $table->unique('code');
        });

        match (Schema::getConnection()->getDriverName()) {
            'mysql' => DB::statement('ALTER TABLE capstone_peer_review_indicator_templates MODIFY code VARCHAR(255) NOT NULL'),
            'pgsql' => DB::statement('ALTER TABLE capstone_peer_review_indicator_templates ALTER COLUMN code SET NOT NULL'),
            default => null,
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capstone_peer_review_indicator_templates', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
