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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module');        // 'bank_soal' | 'capstone' | 'eoffice' | 'manajemen_mahasiswa' | 'user_management'
            $table->string('action');        // 'CREATE' | 'UPDATE' | 'DELETE' | 'VIEW' | 'LOGIN'
            $table->string('subject_type')->nullable(); // nama entitas, e.g. 'Pertanyaan', 'CapstoneGroup'
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');   // human-readable
            $table->timestamp('created_at')->useCurrent();

            // Index untuk performa filter di superadmin
            $table->index(['module', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
