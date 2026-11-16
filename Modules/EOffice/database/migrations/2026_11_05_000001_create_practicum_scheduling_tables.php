<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eo_prak_schedule_periods',function(Blueprint $t){
            $t->uuid('id')->primary();$t->string('tahun_ajaran',9);$t->string('semester',10);$t->date('starts_on');$t->date('ends_on');$t->unsignedInteger('version')->default(0);$t->timestamps();$t->unique(['tahun_ajaran','semester']);
        });
        Schema::create('eo_prak_booking_requests',function(Blueprint $t){
            $t->uuid('id')->primary();$t->string('code')->unique();$t->uuid('period_id')->index();$t->string('kind',10)->default('new');$t->string('status',12)->default('draft')->index();$t->unsignedBigInteger('requested_by');$t->unsignedBigInteger('decided_by')->nullable();$t->text('reason')->nullable();$t->text('decision_reason')->nullable();$t->json('plan')->nullable();$t->json('history')->nullable();$t->string('fingerprint',64)->nullable();$t->unsignedInteger('version')->default(0);$t->timestamp('submitted_at')->nullable();$t->timestamp('decided_at')->nullable();$t->timestamps();$t->foreign('period_id')->references('id')->on('eo_prak_schedule_periods');
        });
        Schema::create('eo_prak_booking_meetings',function(Blueprint $t){
            $t->uuid('id')->primary();$t->uuid('request_id')->index();$t->uuid('praktikum_id')->index();$t->string('module_id',64);$t->string('module_name');$t->unsignedInteger('module_order');$t->string('shift',50);$t->date('date');$t->time('starts_at');$t->time('ends_at');$t->unsignedBigInteger('room_id')->index();$t->unsignedInteger('participants')->default(0);$t->uuid('replaces_id')->nullable()->index();$t->unsignedBigInteger('booking_id')->nullable()->index();$t->string('state',12)->default('draft')->index();$t->timestamps();$t->foreign('request_id')->references('id')->on('eo_prak_booking_requests')->cascadeOnDelete();
        });
        Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->uuid('practicum_meeting_id')->nullable()->unique());
        DB::statement("CREATE UNIQUE INDEX one_approved_practicum_session ON eo_prak_booking_meetings (praktikum_id,module_id,shift) WHERE state = 'approved'");
    }
    public function down(): void
    {
        // Refuse to erase official bookings through a casual rollback.
        if(DB::table('eo_prak_booking_meetings')->whereNotNull('booking_id')->exists())throw new RuntimeException('Arsipkan booking praktikum secara eksplisit sebelum rollback.');
        Schema::table('eo_mr_peminjamans',fn(Blueprint $t)=>$t->dropColumn('practicum_meeting_id'));
        Schema::dropIfExists('eo_prak_booking_meetings');Schema::dropIfExists('eo_prak_booking_requests');Schema::dropIfExists('eo_prak_schedule_periods');
    }
};
