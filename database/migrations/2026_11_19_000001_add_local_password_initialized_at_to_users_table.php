<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('local_password_initialized_at')->nullable();
        });

        if (Schema::hasTable('audit_logs')) {
            // Existing form logins are recorded separately from Microsoft logins.
            $existingFormUsers = DB::table('audit_logs')->select('user_id')
                ->where('module', 'auth')
                ->where('action', 'LOGIN')
                ->where('description', 'like', 'Login ke sistem sebagai %');

            DB::table('users')->whereNotNull('password')->whereIn('id', $existingFormUsers)
                ->update(['local_password_initialized_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('local_password_initialized_at');
        });
    }
};
