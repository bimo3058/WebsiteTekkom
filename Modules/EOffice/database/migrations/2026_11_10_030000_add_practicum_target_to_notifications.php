<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('eo_notifikasi',fn(Blueprint $t)=>$t->json('practicum_target')->nullable()); }
    public function down(): void { Schema::table('eo_notifikasi',fn(Blueprint $t)=>$t->dropColumn('practicum_target')); }
};
