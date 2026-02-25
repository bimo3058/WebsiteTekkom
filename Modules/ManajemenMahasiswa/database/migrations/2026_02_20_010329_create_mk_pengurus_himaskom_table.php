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
        Schema::create('mk_pengurus_himaskom', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('kepengurusan_id')
                ->constrained('mk_kepengurusan')
                ->cascadeOnDelete();

            $table->string('divisi', 50);
            $table->string('jabatan', 50);
            $table->string('status_keaktifan', 20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_pengurus_himaskom');
    }
};
