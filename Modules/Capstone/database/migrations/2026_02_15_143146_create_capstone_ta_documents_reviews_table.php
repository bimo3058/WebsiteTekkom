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
        Schema::create('capstone_ta_document_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_id')
                ->constrained('capstone_ta_documents')
                ->onDelete('cascade');

            $table->foreignId('lecturer_id')
                ->constrained('lecturers')
                ->onDelete('cascade');

            $table->text('comment')->nullable();

            $table->enum('status', ['DRAFT','REVISION','APPROVED']);

            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capstone_ta_documents_reviews');
    }
};
