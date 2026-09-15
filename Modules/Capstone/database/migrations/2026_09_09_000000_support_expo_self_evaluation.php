<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('capstone_expo_self_evaluations', function (Blueprint $table) {
            // Keep self-reflection separate from the existing lecturer grade calculation.
            $table->id();
            $table->foreignId('expo_registration_id')->constrained('capstone_expo_registrations')->restrictOnDelete();
            $table->foreignId('group_id')->constrained('capstone_groups')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('period_component_id')->constrained('capstone_period_assessment_components')->restrictOnDelete();
            $table->decimal('score', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['expo_registration_id','student_id','period_component_id'], 'cap_expo_self_component_unique');
        });
    }

    public function down(): void
    {
        // Fail instead of erasing the identity of existing student evaluations.
        if (\Illuminate\Support\Facades\DB::table('capstone_expo_self_evaluations')->exists()) {
            throw new RuntimeException('Existing Expo self-evaluations must be preserved before rolling back this schema.');
        }
        Schema::dropIfExists('capstone_expo_self_evaluations');
    }
};
