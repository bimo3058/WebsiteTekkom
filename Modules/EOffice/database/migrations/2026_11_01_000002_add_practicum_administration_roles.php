<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        foreach (['admin_praktikum', 'koordinator_cerc'] as $name) {
            DB::table('roles')->updateOrInsert(['name'=>$name, 'guard_name'=>'web'], ['module'=>'eoffice', 'is_academic'=>false]);
        }
        $roleId = (int) DB::table('roles')->where('name','koordinator_cerc')->where('guard_name','web')->value('id');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS one_active_cerc_coordinator ON model_has_roles (role_id) WHERE role_id = '.$roleId);
        $permissions = DB::table('permissions')->where('name','eoffice.view')->where('guard_name','web')->pluck('id');
        foreach (DB::table('roles')->whereIn('name',['admin_praktikum','koordinator_cerc'])->where('guard_name','web')->pluck('id') as $role) {
            foreach ($permissions as $permission) DB::table('role_has_permissions')->insertOrIgnore(['role_id'=>$role,'permission_id'=>$permission]);
        }
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS one_active_cerc_coordinator');
        // Keep roles and assignments: rolling back must not silently revoke real accounts.
    }
};
