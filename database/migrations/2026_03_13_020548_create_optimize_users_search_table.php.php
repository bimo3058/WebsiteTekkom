<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Diperlukan karena CREATE INDEX CONCURRENTLY tidak boleh dalam transaction di Postgres
    public $withinTransaction = false;

    public function up(): void
    {
        // HANYA UNTUK POSTGRESQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // 1. Aktifkan pg_trgm extension
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

            // 2. GIN trigram index untuk search name
            DB::statement('
                CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_name_trgm 
                ON users USING GIN (name gin_trgm_ops) 
                WHERE deleted_at IS NULL
            ');

            // 3. GIN trigram index untuk search email
            DB::statement('
                CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_email_trgm 
                ON users USING GIN (email gin_trgm_ops) 
                WHERE deleted_at IS NULL
            ');

            // 4. Index untuk default load dashboard
            DB::statement('
                CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_active_created 
                ON users (created_at DESC) 
                WHERE deleted_at IS NULL
            ');

            // 6. Index deleted_at
            DB::statement('
                CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_users_deleted_at 
                ON users (deleted_at) 
                WHERE deleted_at IS NOT NULL
            ');
        }

        // BISA JALAN DI SQLITE & POSTGRES (Standard Laravel)
        // 5. Index pivot table user_roles
        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                $table->index(['role_id', 'user_id'], 'idx_user_roles_role_user');
                $table->index(['user_id', 'role_id'], 'idx_user_roles_user_role');
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_users_name_trgm');
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_users_email_trgm');
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_users_active_created');
            DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_users_deleted_at');
        }

        if (Schema::hasTable('user_roles')) {
            Schema::table('user_roles', function (Blueprint $table) {
                $table->dropIndex('idx_user_roles_role_user');
                $table->dropIndex('idx_user_roles_user_role');
            });
        }
    }
};