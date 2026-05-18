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
        Schema::create('eo_kp_template', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('phase', ['pra_kp', 'saat_kp', 'pasca_kp']);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->boolean('is_required')->default(false);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            
            $table->timestamps();
            
            // Note: Not setting foreign key constraint yet to prevent issues if users table doesn't match
            // $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_template');
    }
};
