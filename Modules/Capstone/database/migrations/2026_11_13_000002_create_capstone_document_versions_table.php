<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capstone_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('capstone_documents')->cascadeOnDelete();
            $table->integer('version');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capstone_document_versions');
    }
};
