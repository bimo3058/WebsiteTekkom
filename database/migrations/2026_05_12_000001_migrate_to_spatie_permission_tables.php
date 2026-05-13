<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tambah guard_name ke tabel roles dan permissions yang sudah ada ──
        Schema::table('roles', function (Blueprint $table) {
            $table->string('guard_name')->default('web')->after('name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('guard_name')->default('web')->after('name');
        });

        DB::table('roles')->update(['guard_name' => 'web']);
        DB::table('permissions')->update(['guard_name' => 'web']);

        // ── 2. Buat Spatie pivot tables ──────────────────────────────────────────
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $table->foreign('permission_id')
                  ->references('id')->on('permissions')
                  ->onDelete('cascade');
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')
                  ->references('id')->on('permissions')
                  ->onDelete('cascade');
            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('cascade');
            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        // ── 3. Migrasi data dari tabel lama ke tabel Spatie baru ─────────────────
        $modelType = 'App\\Models\\User';

        // user_roles → model_has_roles
        DB::table('user_roles')->orderBy('user_id')->orderBy('role_id')
            ->chunk(500, function ($rows) use ($modelType) {
                foreach ($rows as $row) {
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id'    => $row->role_id,
                        'model_type' => $modelType,
                        'model_id'   => $row->user_id,
                    ]);
                }
            });

        // user_permissions → model_has_permissions
        DB::table('user_permissions')->orderBy('user_id')->orderBy('permission_id')
            ->chunk(500, function ($rows) use ($modelType) {
                foreach ($rows as $row) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $row->permission_id,
                        'model_type'    => $modelType,
                        'model_id'      => $row->user_id,
                    ]);
                }
            });

        // role_permissions → role_has_permissions
        DB::table('role_permissions')->orderBy('role_id')->orderBy('permission_id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $row->permission_id,
                        'role_id'       => $row->role_id,
                    ]);
                }
            });

        // ── 4. Drop tabel pivot lama ─────────────────────────────────────────────
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
    }

    public function down(): void
    {
        // Rollback: recreate old tables and migrate data back
        Schema::create('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->primary(['user_id', 'permission_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->primary(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        $modelType = 'App\\Models\\User';

        DB::table('model_has_roles')->where('model_type', $modelType)->each(function ($row) {
            DB::table('user_roles')->insertOrIgnore([
                'user_id' => $row->model_id,
                'role_id' => $row->role_id,
            ]);
        });

        DB::table('model_has_permissions')->where('model_type', $modelType)->each(function ($row) {
            DB::table('user_permissions')->insertOrIgnore([
                'user_id'       => $row->model_id,
                'permission_id' => $row->permission_id,
            ]);
        });

        DB::table('role_has_permissions')->each(function ($row) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id'       => $row->role_id,
                'permission_id' => $row->permission_id,
            ]);
        });

        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('guard_name');
        });
    }
};
