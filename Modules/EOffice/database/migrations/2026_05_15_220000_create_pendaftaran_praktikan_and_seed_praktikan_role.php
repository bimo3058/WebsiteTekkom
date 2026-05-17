<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_praktikan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->string('irs_path');
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->text('alasan_penolakan')->nullable();
            $table->unsignedBigInteger('direview_oleh')->nullable();
            $table->timestamp('direview_pada')->nullable();
            $table->text('catatan_koor')->nullable();
            /** manual | otomatis_irs — untuk audit; otomatis bisa diisi integrasi KRS nanti */
            $table->string('validasi_mode')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
            $table->foreign('periode_id')->references('id')->on('manprak_periode_pendaftaran')->nullOnDelete();
            $table->foreign('direview_oleh')->references('id')->on('users')->nullOnDelete();
        });

        $this->seedPraktikanRole();
    }

    public function down(): void
    {
        $roleIds = DB::table('roles')
            ->where('name', 'praktikan')
            ->where('module', 'eoffice')
            ->pluck('id');

        if ($roleIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('role_id', $roleIds)->delete();
        }

        DB::table('permissions')
            ->where('name', 'praktikan.dashboard')
            ->where('module', 'eoffice')
            ->delete();

        DB::table('roles')
            ->where('name', 'praktikan')
            ->where('module', 'eoffice')
            ->delete();

        Schema::dropIfExists('pendaftaran_praktikan');
    }

    private function seedPraktikanRole(): void
    {
        $now = now();

        $exists = DB::table('roles')
            ->where('name', 'praktikan')
            ->where('module', 'eoffice')
            ->exists();

        if (! $exists) {
            DB::table('roles')->insert([
                'name'         => 'praktikan',
                'module'       => 'eoffice',
                'guard_name'   => 'web',
                'is_academic'  => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $permExists = DB::table('permissions')
            ->where('name', 'praktikan.dashboard')
            ->where('module', 'eoffice')
            ->exists();

        if (! $permExists) {
            DB::table('permissions')->insert([
                'name'         => 'praktikan.dashboard',
                'display_name' => 'Dashboard Praktikan',
                'module'       => 'eoffice',
                'guard_name'   => 'web',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $role  = DB::table('roles')->where('name', 'praktikan')->where('module', 'eoffice')->first();
        $perm  = DB::table('permissions')->where('name', 'praktikan.dashboard')->where('module', 'eoffice')->first();

        if ($role && $perm) {
            $linked = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->where('permission_id', $perm->id)
                ->exists();

            if (! $linked) {
                DB::table('role_has_permissions')->insert([
                    'role_id'       => $role->id,
                    'permission_id' => $perm->id,
                ]);
            }
        }
    }
};
