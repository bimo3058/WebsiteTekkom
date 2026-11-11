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
            $table->boolean('sso_enabled')->default(false)->index();
            $table->timestamp('sso_enabled_at')->nullable();
            $table->foreignId('sso_enabled_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('user_sso_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 32)->default('microsoft');
            $table->string('tenant_id', 64);
            $table->string('object_id', 191);
            $table->string('email');
            $table->timestamps();

            $table->unique(['provider', 'tenant_id', 'object_id'], 'user_sso_identity_provider_object_unique');
            $table->unique(['user_id', 'provider', 'tenant_id'], 'user_sso_identity_user_provider_unique');
            $table->index('email');
        });

        // Preserve access for existing accounts during the SSO cutover.
        // New accounts still require an explicit grant (the default remains false).
        DB::table('users')->update([
            'sso_enabled' => true,
            'sso_enabled_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sso_identities');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sso_enabled_by']);
            $table->dropIndex(['sso_enabled']);
            $table->dropColumn(['sso_enabled', 'sso_enabled_at', 'sso_enabled_by']);
        });
    }
};
