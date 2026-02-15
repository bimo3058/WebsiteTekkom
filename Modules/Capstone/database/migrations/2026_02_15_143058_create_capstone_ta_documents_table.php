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
        Schema::create('capstone_ta_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ta_id')
                ->constrained('capstone_individual_ta')
                ->onDelete('cascade');

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
        Schema::dropIfExists('capstone_ta_documents');
    }
};
