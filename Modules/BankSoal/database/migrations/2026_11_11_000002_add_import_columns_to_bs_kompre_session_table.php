<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->string('source')->default('cbt')->index();
            $table->foreignId('import_batch_id')
                ->nullable()
                ->constrained('bs_import_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bs_kompre_session', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
            $table->dropColumn(['source', 'import_batch_id']);
        });
    }
};
