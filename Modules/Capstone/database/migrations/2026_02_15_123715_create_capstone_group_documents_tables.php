<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('capstone_group_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')
                ->constrained('capstone_groups')
                ->onDelete('cascade');

            $table->enum('doc_type', ['C100','C200','C300','C400','C500']);

            $table->string('file_path');
            $table->integer('version')->default(1);

            $table->enum('status', ['DRAFT','REVISION','APPROVED'])
                ->default('DRAFT');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamp('uploaded_at')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_group_documents');
    }
};
