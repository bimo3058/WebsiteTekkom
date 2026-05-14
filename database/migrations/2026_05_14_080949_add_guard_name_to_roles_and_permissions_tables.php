<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('guard_name')->default('web')->after('name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('guard_name')->default('web')->after('name');
        });

        // isi data lama
        DB::table('roles')->update([
            'guard_name' => 'web'
        ]);

        DB::table('permissions')->update([
            'guard_name' => 'web'
        ]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });
    }
};