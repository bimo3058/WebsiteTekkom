<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_statuses', function (Blueprint $table) {
            $table->string('kind', 32)->default('users')->index();
            $table->json('result_summary')->nullable();
        });

        Schema::create('import_status_rows', function (Blueprint $table) {
            $table->id();
            // The connected Supabase schema has an import_statuses.id column without
            // a primary/unique constraint, so PostgreSQL cannot accept a foreign key.
            $table->unsignedBigInteger('import_status_id');
            $table->unsignedInteger('row_number');
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 24);
            $table->text('message')->nullable();
            $table->boolean('previous_sso_enabled')->nullable();
            $table->boolean('requested_sso_enabled')->nullable();
            $table->timestamps();

            $table->unique(['import_status_id', 'row_number']);
            $table->index(['import_status_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_status_rows');
        Schema::table('import_statuses', function (Blueprint $table) {
            $table->dropIndex(['kind']);
            $table->dropColumn(['kind', 'result_summary']);
        });
    }
};
